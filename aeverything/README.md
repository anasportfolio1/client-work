# æverything — static site

All 12 designed screens, built as a real multi-page website.
No frameworks, no npm install, no build tools to learn. Just HTML, CSS and one JS file.

---

## 1. See it

```bash
node serve.js
```

Then open **http://localhost:5173**

You can also just double-click `index.html` — it works from the file system too.

---

## 2. Pages

| File | Screen |
|---|---|
| `index.html` | Home — hero, countdown, Shop by World (5) |
| `shop.html` | Collection listing — sidebar, filters, 6 products |
| `product.html` | Product detail — sizes, quantity, 4 accordions |
| `magazine.html` | Æ Magazine hub — category filters, 3 articles |
| `article.html` | Article — "The Anima Within" |
| `gallery.html` | Gallery — 7 works, masonry + lightbox |
| `education.html` | Education hub — 4 disciplines + featured lesson |
| `lesson.html` | Lesson (dark theme) — 7 Hermetic Principles, progress, resources |
| `world.html` | World of Æ — animated globe, 5 pillars |
| `account.html` | My Account — orders, wishlist |
| `mentorship.html` | Mentorship — 4 programs, benefits, quote |
| `fitness.html` | Æverything Fitness — 6 programs, free resources |

Plus `sitemap.xml` and `robots.txt` (generated) so Google can index the site.

---

## 3. Folder structure

```
aeverything/
├─ index.html …          ← finished pages (generated — don't edit by hand)
├─ build.js              ← assembles pages from src/
├─ serve.js              ← local dev server
├─ sitemap.xml robots.txt
├─ assets/
│  ├─ css/
│  │  ├─ style.css       ← colours, fonts, sky, buttons  (design tokens)
│  │  ├─ components.css  ← header, footer, cards, widgets
│  │  └─ pages.css       ← per-page layouts + responsive
│  ├─ js/main.js         ← every interaction
│  └─ img/clouds.png     ← generated cloud texture
├─ src/
│  ├─ pages/*.html       ← EDIT THESE — page content only
│  └─ partials/          ← header, footers, icon sprite, widgets, garments
└─ tools/make-clouds.js  ← regenerates the cloud texture
```

**The workflow:** edit something in `src/`, then run:

```bash
node build.js
```

That rewrites all 12 finished pages. Edit the header once — it updates everywhere.

---

## 4. Adding real photos

Every photo slot is a placeholder that looks like this:

```html
<div class="ph" style="--h:338"><span class="ph-lb">Hero 1 — Model in Bubble Gum set</span></div>
```

Replace it with:

```html
<img class="ph-img" src="assets/img/hero-1.jpg" alt="Model wearing the Bubble Gum set">
```

Drop your files in `assets/img/`. The label text tells you what photo belongs in each slot.
`--h` is just the placeholder's hue — it does nothing once a real image is in.

The product illustrations (crop top, leggings, hoodie…) live in
`src/partials/garments.txt` — swap those for real product photos the same way.

---

## 5. Changing the brand

Everything is a CSS variable at the top of `assets/css/style.css`:

```css
--pink-600:#FF3D6E;   /* the Bubble Gum accent */
--sky-800:#1355B8;    /* sky blue */
--f-display:"Archivo" /* headings */
--f-body:"Inter"      /* body text */
```

Change one value, the whole site follows.

> **Fonts:** Archivo and Inter are free Google Fonts, chosen as the closest match
> to the mockups. If the brand has real licensed fonts, swap the `<link>` in
> `build.js` and these two variables.

---

## 6. Putting it online (free)

**Netlify** — easiest:
1. Go to app.netlify.com/drop
2. Drag the whole `aeverything` folder onto the page
3. You get a live URL in about 20 seconds

**GitHub Pages:**
1. Push this folder to a GitHub repo
2. Settings → Pages → Deploy from branch → `main` / root

Either way, add your domain afterwards, then update the `base` URL in
`build.js` (currently `https://aeverything.example/`) and re-run `node build.js`
so the canonical tags and sitemap point at the real domain.

---

## 7. Where the words come from

Every product name, price, article title, section heading, category, menu item,
order number and body paragraph is taken **directly from the design mockups**.
Nothing is invented — the counts match too (6 products, 3 magazine cards,
7 gallery works, 5 Shop by World tiles, 7 Hermetic Principles).

**One exception.** The four product accordions on `product.html` — Description,
Details, Shipping, Size Guide — are shown *collapsed* in the mockup, so there was
no copy to take. I wrote placeholder copy so the accordions have something to
open. Replace it in `src/pages/product.html` with your real product copy.

The gallery deliberately has **no artwork titles or artist credits**, because the
mockup shows none. Each tile just carries its category (Photo / Digital /
Painting / 3D), which comes from the real filter row in the design.

---

## 8. What's built and what isn't

**Built (12):** every screen from the design mockups, fully responsive, with working
navigation, cart drawer, search overlay, filters, accordions, lightbox, live clock,
live countdown, carousel, and animated progress rings.

**Not built yet (~20 screens that were never designed):** cart page, checkout,
search results, login/register, order detail, addresses, payment methods, settings,
topic hub, all-lessons index, mentorship application form, program detail,
World of Æ sub-pages, contact, FAQ, size guide, track order, 404, cookie banner.

The cart, prices and progress numbers are **front-end demo data** — there is no
backend. Real checkout, real accounts and real course progress come when this is
moved onto Shopify or WordPress.
