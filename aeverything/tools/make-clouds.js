/* ============================================================
   Generates assets/img/clouds.png — a seamless fractal cloud
   tile (white, variable alpha). Run once:  node tools/make-clouds.js
   Pre-rendering this beats an SVG feTurbulence background, which
   the browser has to re-rasterise for every tile on every page.
   ============================================================ */
const zlib = require('zlib');
const fs = require('fs');
const path = require('path');

const W = 1800, H = 1010;

/* --- seeded RNG so the tile is reproducible --- */
let seed = 1337;
const rnd = () => (seed = (seed * 1664525 + 1013904223) >>> 0) / 4294967296;

/* --- seamless value-noise octave: grid of size n x n, wrapped --- */
function octave(n) {
  const g = new Float32Array(n * n);
  for (let i = 0; i < g.length; i++) g[i] = rnd();
  const smooth = t => t * t * (3 - 2 * t);           // smoothstep
  return (x, y) => {                                  // x,y in [0,1)
    const fx = x * n, fy = y * n;
    const x0 = Math.floor(fx) % n, y0 = Math.floor(fy) % n;
    const x1 = (x0 + 1) % n, y1 = (y0 + 1) % n;       // wrap => seamless
    const tx = smooth(fx - Math.floor(fx)), ty = smooth(fy - Math.floor(fy));
    const a = g[y0 * n + x0], b = g[y0 * n + x1];
    const c = g[y1 * n + x0], d = g[y1 * n + x1];
    return (a + (b - a) * tx) + ((c + (d - c) * tx) - (a + (b - a) * tx)) * ty;
  };
}

/* fractal sum: big soft shapes + finer detail */
const octs = [
  { f: octave(2),  amp: 0.42 },
  { f: octave(4),  amp: 0.26 },
  { f: octave(8),  amp: 0.17 },
  { f: octave(16), amp: 0.09 },
  { f: octave(32), amp: 0.06 },
  { f: octave(64), amp: 0.035 },
];

const px = Buffer.alloc(W * H * 4);
for (let y = 0; y < H; y++) {
  for (let x = 0; x < W; x++) {
    const u = x / W, v = y / H;
    let n = 0;
    for (let k = 0; k < octs.length; k++) {
      const o = octs[k];
      const raw = o.f(u, v);
      // first two octaves stay smooth (the big banks); the rest billow
      n += ( k < 2 ? raw : Math.abs( raw * 2 - 1 ) ) * o.amp;
    }
    // broad ramp: most of the tile carries cloud, so the sky never reads as flat blue
    let a = ( n - 0.33 ) * 2.5;
    a = a < 0 ? 0 : a > 1 ? 1 : a;
    a = a * a * ( 3 - 2 * a ) * 0.35 + a * 0.65;  // only lightly soften the edges
    a = Math.pow( a, 1.08 );      // slight sharpen, keeps the cores bright
    const i = (y * W + x) * 4;
    px[i] = 255; px[i + 1] = 255; px[i + 2] = 255;    // white cloud
    px[i + 3] = Math.round(a * 255);
  }
}

/* --- minimal PNG encoder (RGBA, filter type 0) --- */
const raw = Buffer.alloc(H * (W * 4 + 1));
for (let y = 0; y < H; y++) {
  raw[y * (W * 4 + 1)] = 0;
  px.copy(raw, y * (W * 4 + 1) + 1, y * W * 4, (y + 1) * W * 4);
}

const CRC = (() => {
  const t = new Int32Array(256);
  for (let n = 0; n < 256; n++) {
    let c = n;
    for (let k = 0; k < 8; k++) c = c & 1 ? 0xedb88320 ^ (c >>> 1) : c >>> 1;
    t[n] = c;
  }
  return buf => {
    let c = -1;
    for (let i = 0; i < buf.length; i++) c = t[(c ^ buf[i]) & 0xff] ^ (c >>> 8);
    return (c ^ -1) >>> 0;
  };
})();

const chunk = (type, data) => {
  const len = Buffer.alloc(4); len.writeUInt32BE(data.length);
  const td = Buffer.concat([Buffer.from(type, 'ascii'), data]);
  const crc = Buffer.alloc(4); crc.writeUInt32BE(CRC(td));
  return Buffer.concat([len, td, crc]);
};

const ihdr = Buffer.alloc(13);
ihdr.writeUInt32BE(W, 0); ihdr.writeUInt32BE(H, 4);
ihdr[8] = 8; ihdr[9] = 6; ihdr[10] = 0; ihdr[11] = 0; ihdr[12] = 0;

const png = Buffer.concat([
  Buffer.from([137, 80, 78, 71, 13, 10, 26, 10]),
  chunk('IHDR', ihdr),
  chunk('IDAT', zlib.deflateSync(raw, { level: 9 })),
  chunk('IEND', Buffer.alloc(0)),
]);

const out = path.join(__dirname, '..', 'assets', 'img', 'clouds.png');
fs.writeFileSync(out, png);
console.log(`clouds.png written — ${W}x${H}, ${(png.length / 1024).toFixed(1)} KB`);
