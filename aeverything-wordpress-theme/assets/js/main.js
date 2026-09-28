/* ============================================================
   ÆVERYTHING — Interactions
   ============================================================ */
(function () {
  'use strict';

  const $  = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));
  const on = (el, ev, fn) => el && el.addEventListener(ev, fn);

  /* ---------- 1. STICKY HEADER ---------- */
  const hdr = $('.hdr');
  if (hdr) {
    const onScroll = () => hdr.classList.toggle('is-stuck', window.scrollY > 20);
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
  }

  /* ---------- 2. OVERLAYS (menu / search / cart) ---------- */
  const scrim = $('#scrim');
  let openPanel = null;

  function openIt(sel) {
    const el = $(sel);
    if (!el) return;
    closeAll();
    el.classList.add('is-open');
    scrim && scrim.classList.add('is-open');
    document.body.style.overflow = 'hidden';
    openPanel = el;
    const f = el.querySelector('input');
    if (f) setTimeout(() => f.focus(), 260);
  }
  function closeAll() {
    $$('.drawer.is-open, .searchbox.is-open, .lbox.is-open').forEach(e => e.classList.remove('is-open'));
    scrim && scrim.classList.remove('is-open');
    document.body.style.overflow = '';
    openPanel = null;
  }

  $$('[data-open]').forEach(b => on(b, 'click', e => { e.preventDefault(); openIt(b.dataset.open); }));
  $$('[data-close]').forEach(b => on(b, 'click', e => { e.preventDefault(); closeAll(); }));
  on(scrim, 'click', closeAll);
  on(document, 'keydown', e => { if (e.key === 'Escape') closeAll(); });

  /* ---------- 3. LIVE CLOCK ---------- */
  const clock = $('#clock');
  if (clock) {
    const hH = $('.clock-h', clock), mH = $('.clock-m', clock), sH = $('.clock-s', clock);
    const tEl = $('#clockTime'), apEl = $('#clockAp');
    const tick = () => {
      const d = new Date();
      const h = d.getHours(), m = d.getMinutes(), s = d.getSeconds();
      hH && (hH.style.transform = `rotate(${(h % 12) * 30 + m * 0.5}deg)`);
      mH && (mH.style.transform = `rotate(${m * 6 + s * 0.1}deg)`);
      sH && (sH.style.transform = `rotate(${s * 6}deg)`);
      const h12 = h % 12 || 12;
      tEl && (tEl.textContent = String(h12).padStart(2, '0') + ':' + String(m).padStart(2, '0'));
      apEl && (apEl.textContent = h < 12 ? 'AM' : 'PM');
    };
    tick();
    setInterval(tick, 1000);
  }

  /* ---------- 4. DROP COUNTDOWN ---------- */
  const cd = $('#countdown');
  if (cd) {
    // Target: next occurrence roughly 3d 14h out, so the mock always looks alive.
    let target = new Date(cd.dataset.target || '');
    if (isNaN(target) || target <= new Date()) {
      target = new Date(Date.now() + (3 * 86400 + 14 * 3600 + 27 * 60 + 18) * 1000);
    }
    const put = (id, v) => { const e = $('#' + id); if (e) e.textContent = String(v).padStart(2, '0'); };
    const tick = () => {
      let diff = Math.max(0, target - new Date());
      const d = Math.floor(diff / 864e5); diff -= d * 864e5;
      const h = Math.floor(diff / 36e5);  diff -= h * 36e5;
      const m = Math.floor(diff / 6e4);   diff -= m * 6e4;
      const s = Math.floor(diff / 1e3);
      put('cdD', d); put('cdH', h); put('cdM', m); put('cdS', s);
    };
    tick();
    setInterval(tick, 1000);
  }

  /* ---------- 5. HERO CAROUSEL ---------- */
  const car = $('#hero-car');
  if (car) {
    const slides = $$('[data-slide]', car);
    const dots   = $$('#hero-dots .dot');
    let i = 0, timer;
    const go = n => {
      i = (n + slides.length) % slides.length;
      slides.forEach((s, k) => {
        s.style.opacity = k === i ? '1' : '0';
        s.style.transform = k === i ? 'scale(1)' : 'scale(1.05)';
      });
      dots.forEach((d, k) => d.classList.toggle('is-on', k === i));
    };
    const play = () => { clearInterval(timer); timer = setInterval(() => go(i + 1), 5200); };
    dots.forEach((d, k) => on(d, 'click', () => { go(k); play(); }));
    go(0); play();
  }

  /* ---------- 6. FILTER PILLS ---------- */
  $$('[data-filter-group]').forEach(group => {
    const targetSel = group.dataset.filterTarget;
    const items = targetSel ? $$(targetSel + ' > *') : [];
    $$('.pill', group).forEach(pill => {
      on(pill, 'click', () => {
        $$('.pill', group).forEach(p => p.classList.remove('is-on'));
        pill.classList.add('is-on');
        const v = (pill.dataset.val || 'all').toLowerCase();
        items.forEach(it => {
          const tags = (it.dataset.tags || '').toLowerCase();
          const show = v === 'all' || tags.split(/\s+/).includes(v);
          it.style.display = show ? '' : 'none';
        });
      });
    });
  });

  /* ---------- 7. ACCORDIONS ---------- */
  $$('.acc').forEach(acc => {
    const hd = $('.acc-hd', acc), bd = $('.acc-bd', acc);
    on(hd, 'click', () => {
      const open = acc.classList.toggle('is-open');
      bd.style.maxHeight = open ? bd.scrollHeight + 'px' : '0px';
      hd.setAttribute('aria-expanded', open);
    });
  });

  /* ---------- 8. PDP: size / qty / thumbs ---------- */
  $$('.sizes').forEach(g => $$('.size', g).forEach(b =>
    on(b, 'click', () => { $$('.size', g).forEach(x => x.classList.remove('is-on')); b.classList.add('is-on'); })
  ));

  const qIn = $('#qty');
  if (qIn) {
    on($('#qMinus'), 'click', () => qIn.value = Math.max(1, (+qIn.value || 1) - 1));
    on($('#qPlus'),  'click', () => qIn.value = Math.min(99, (+qIn.value || 1) + 1));
  }

  $$('.pdp-thumb').forEach((t, k) => on(t, 'click', () => {
    $$('.pdp-thumb').forEach(x => x.classList.remove('is-on'));
    t.classList.add('is-on');
    const main = $('#pdpMain');
    if (main) { main.style.opacity = '0'; setTimeout(() => { main.style.opacity = '1'; }, 170); }
  }));

  /* ---------- 9. WISHLIST TOGGLES ---------- */
  $$('[data-wish]').forEach(b => on(b, 'click', e => {
    e.preventDefault(); e.stopPropagation();
    b.classList.toggle('is-on');
  }));

  /* ---------- 10. CART COUNT ---------- */
  const bump = n => $$('[data-cart-count]').forEach(e => e.textContent = n);
  let cartN = parseInt(($('[data-cart-count]') || {}).textContent, 10) || 2;
  $$('[data-add-cart]').forEach(b => on(b, 'click', e => {
    e.preventDefault();
    bump(++cartN);
    const old = b.textContent;
    b.textContent = 'Added ✓';
    b.style.pointerEvents = 'none';
    setTimeout(() => { b.textContent = old; b.style.pointerEvents = ''; }, 1500);
  }));

  /* ---------- 11. GALLERY LIGHTBOX ---------- */
  $$('[data-lbox]').forEach(it => on(it, 'click', e => {
    e.preventDefault();
    const box = $('#lightbox');
    if (!box) return;
    const ph = $('.ph', box);
    if (ph) ph.style.setProperty('--h', it.dataset.h || '210');
    const cap = $('#lboxTitle'), by = $('#lboxBy');
    cap && (cap.textContent = it.dataset.title || 'Artwork');
    by  && (by.textContent  = it.dataset.by || 'Æ Gallery');
    openIt('#lightbox');
  }));

  /* ---------- 12. PROGRESS ANIMATIONS ---------- */
  const animateProgress = root => {
    $$('.pbar i', root).forEach(b => { b.style.width = (b.dataset.p || 0) + '%'; });
    $$('.donut-fg', root).forEach(c => {
      const r = c.r.baseVal.value, C = 2 * Math.PI * r, p = +(c.dataset.p || 0);
      c.style.strokeDasharray = C;
      c.style.strokeDashoffset = C;
      requestAnimationFrame(() => { c.style.strokeDashoffset = C * (1 - p / 100); });
    });
  };

  /* ---------- 13. SCROLL REVEAL ---------- */
  const rv = $$('.rv');
  if ('IntersectionObserver' in window && rv.length) {
    const io = new IntersectionObserver((entries) => {
      entries.forEach(en => {
        if (!en.isIntersecting) return;
        en.target.classList.add('in');
        animateProgress(en.target);
        io.unobserve(en.target);
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px' });
    rv.forEach((el, k) => { el.style.transitionDelay = (k % 7) * 55 + 'ms'; io.observe(el); });
  } else {
    rv.forEach(el => el.classList.add('in'));
  }
  /* safety net — never leave content invisible if the observer is slow */
  setTimeout(() => {
    rv.forEach(el => el.classList.add('in'));
    animateProgress(document);
  }, 1200);

  /* ---------- 14. NEWSLETTER ---------- */
  $$('[data-newsletter]').forEach(f => on(f, 'submit', e => {
    e.preventDefault();
    const inp = $('input', f);
    if (!inp || !inp.value.includes('@')) { inp && inp.focus(); return; }
    f.innerHTML = '<p style="padding:14px 20px;font-size:13.5px;font-weight:600;color:inherit">Welcome to the movement. ✓</p>';
  }));

  /* ---------- 15. MARK ACTIVE NAV ---------- */
  const here = (location.pathname.split('/').pop() || 'index.html').toLowerCase();
  $$('.nav a, .mnav a').forEach(a => {
    const href = (a.getAttribute('href') || '').toLowerCase();
    if (href && href === here) a.classList.add('is-on');
  });
})();
