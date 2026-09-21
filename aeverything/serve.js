/* Tiny zero-dependency dev server.  Run:  node serve.js  */
const http = require('http'), fs = require('fs'), path = require('path');
const PORT = 5173, ROOT = __dirname;
const TYPES = { '.html':'text/html; charset=utf-8', '.css':'text/css; charset=utf-8',
  '.js':'text/javascript; charset=utf-8', '.json':'application/json', '.svg':'image/svg+xml',
  '.png':'image/png', '.jpg':'image/jpeg', '.jpeg':'image/jpeg', '.webp':'image/webp',
  '.woff2':'font/woff2', '.xml':'application/xml', '.txt':'text/plain; charset=utf-8' };

http.createServer((req, res) => {
  let p = decodeURIComponent(req.url.split('?')[0]);
  if (p === '/') p = '/index.html';
  const file = path.join(ROOT, p);
  if (!file.startsWith(ROOT)) { res.writeHead(403).end('Forbidden'); return; }
  fs.readFile(file, (err, buf) => {
    if (err) { res.writeHead(404, {'Content-Type':'text/html'}).end('<h1>404</h1><a href="/">Home</a>'); return; }
    res.writeHead(200, { 'Content-Type': TYPES[path.extname(file).toLowerCase()] || 'application/octet-stream',
      'Cache-Control': 'no-cache' });
    res.end(buf);
  });
}).listen(PORT, () => console.log('æverything running → http://localhost:' + PORT));
