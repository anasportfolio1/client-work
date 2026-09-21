/* ============================================================
   ÆVERYTHING — static site builder
   Run:  node build.js
   Reads src/pages/*.html + src/partials/*, writes finished
   standalone .html files to the project root.
   ============================================================ */
const fs = require('fs');
const path = require('path');

/* Where the site will be served from. Change this one line if it moves
   (e.g. to a custom domain) and re-run `node build.js`. Keep the trailing slash. */
const SITE = 'https://anasportfolio1.github.io/client-work/aeverything/';

const ROOT = __dirname;
const P = f => path.join(ROOT, 'src', 'partials', f);
const read = f => fs.readFileSync(f, 'utf8');

const sprite   = read(P('sprite.html'));
const header   = read(P('header.html'));
const footerA  = read(P('footer-a.html'));
const footerB  = read(P('footer-b.html'));
const widgets  = read(P('widgets.html'));

/* garments.txt -> { 'g-crop': '<svg>…</svg>', … } */
const garments = {};
read(P('garments.txt')).split(/^::(.+?)::$/m).forEach((chunk, i, arr) => {
  if (i % 2 === 1) garments[chunk.trim()] = arr[i + 1].trim();
});

const meta = (src, key, fallback) => {
  const m = src.match(new RegExp('<!--@' + key + ':([\\s\\S]*?)-->'));
  return m ? m[1].trim() : fallback;
};

function expand(html) {
  html = html.replace(/\{\{widgets\}\}/g, widgets);
  Object.keys(garments).forEach(k => {
    html = html.split('{{' + k + '}}').join(garments[k]);
  });
  return html;
}

const shell = (o) => `<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>${o.title}</title>
<meta name="description" content="${o.desc}">
<meta name="theme-color" content="#1355B8">
<link rel="canonical" href="${SITE}${o.file === 'index.html' ? '' : o.file}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="æverything">
<meta property="og:title" content="${o.title}">
<meta property="og:description" content="${o.desc}">
<meta name="twitter:card" content="summary_large_image">
<link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' rx='22' fill='%231355B8'/%3E%3Ctext x='50' y='72' font-size='62' font-family='Georgia,serif' fill='%23fff' text-anchor='middle'%3E%C3%A6%3C/text%3E%3C/svg%3E">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Archivo:wdth,wght@94..125,400..800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/components.css">
<link rel="stylesheet" href="assets/css/pages.css">
<noscript><style>.rv{opacity:1!important;transform:none!important}</style></noscript>
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"Organization","name":"æverything","slogan":"I am nothing, æverything.","url":"${SITE}"}
</script>
</head>
<body class="${o.body}">
${sprite}
${header}
<main>
${o.content}
</main>
${o.footer === 'b' ? footerB : footerA}
<script src="assets/js/main.js" defer></script>
</body>
</html>
`;

const dir = path.join(ROOT, 'src', 'pages');
const files = fs.readdirSync(dir).filter(f => f.endsWith('.html'));
let n = 0;

files.forEach(f => {
  const raw = read(path.join(dir, f));
  const content = raw.replace(/<!--@[\s\S]*?-->\s*/g, '');
  const out = shell({
    file: f,
    title: meta(raw, 'title', 'æverything'),
    desc: meta(raw, 'desc', 'æverything — mind, body, spirit, art.'),
    body: meta(raw, 'body', 'sky'),
    footer: meta(raw, 'footer', 'a'),
    content: content,
  });
  /* expand {{widgets}} / {{g-*}} across the whole document, header included */
  fs.writeFileSync(path.join(ROOT, f), expand(out));
  n++;
  console.log('  ✓ ' + f);
});

/* sitemap + robots for search engines */
const base = SITE;
fs.writeFileSync(path.join(ROOT, 'sitemap.xml'),
  '<?xml version="1.0" encoding="UTF-8"?>\n<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n' +
  files.map(f => `  <url><loc>${base}${f === 'index.html' ? '' : f}</loc></url>`).join('\n') +
  '\n</urlset>\n');
fs.writeFileSync(path.join(ROOT, 'robots.txt'), `User-agent: *\nAllow: /\nSitemap: ${base}sitemap.xml\n`);

console.log(`\nBuilt ${n} pages + sitemap.xml + robots.txt`);
