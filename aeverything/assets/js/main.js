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
    /* Render a two-digit value as two rolling columns. The strip of 0-9 is
       built once; after that a change is one transform per digit. */
    const roll = (e, str) => {
      if (e.dataset.odo !== '1') {
        e.textContent = '';
        for (let i = 0; i < str.length; i++) {
          const win = document.createElement('span');
          win.className = 'odo';
          const strip = document.createElement('span');
          strip.className = 'odo-r';
          for (let d = 0; d <= 9; d++) {
            const b = document.createElement('b');
            b.textContent = String(d);
            strip.appendChild(b);
          }
          win.appendChild(strip);
          e.appendChild(win);
        }
        e.dataset.odo = '1';
      }
      const strips = e.querySelectorAll('.odo-r');
      for (let i = 0; i < strips.length; i++) {
        strips[i].style.transform = 'translateY(-' + (+str[i] || 0) + 'em)';
      }
      e.dataset.v = str;
    };
    const put = (id, v) => { const e = $('#' + id); if (e) roll(e, String(v).padStart(2, '0')); };
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

  /* ---------- 5. HERO CAROUSEL ----------
     The headline, sub-line, button and cut-out all change together, so
     each slide reads as a different part of the brand. */
  const heroData = $('#heroSlides');
  if (heroData) {
    let slides = [];
    try { slides = JSON.parse(heroData.textContent) || []; } catch (e) { slides = []; }

    const title = $('#heroTitle'), sub = $('#heroSub'),
          btn   = $('#heroBtn'),   model = $('#heroModel'),
          slot  = $('.hero-r > .ph'),
          copy  = $('#heroCopy');
    const dots = $$('#hero-dots .dot');

    if (slides.length && title) {
      let i = 0, timer;

      /* Write a slide's text and image into the DOM. */
      const paint = s => {
        title.innerHTML = '<span></span>' + (s.l2 ? '<br><span></span>' : '');
        const spans = title.querySelectorAll('span');
        spans[0].textContent = s.l1;
        if (spans[1]) spans[1].textContent = s.l2;
        title.dataset.ln = '';   /* innerHTML was just replaced — allow a re-split */
        title.classList.remove('typed');
        lineSplit(title);
        retype(title);
        if (sub) sub.textContent = s.sub || '';
        if (btn) { btn.textContent = s.btn || ''; btn.href = s.url || '#'; }
        /* Show the cut-out when this slide has one, otherwise the empty
           slot — a slide without a photo never borrows another's. */
        if (model) {
          if (s.img) {
            if (model.getAttribute('src') !== s.img) model.src = s.img;
            model.classList.remove('is-off');
          } else {
            model.classList.add('is-off');
          }
        }
        if (slot) slot.classList.toggle('is-off', !!s.img);
      };

      /* Have the next image decoded before the swipe starts, so the
         incoming frame is never blank. Always settles. */
      const preload = src => new Promise(res => {
        if (!src) { res(); return; }
        const im = new Image();
        im.onload = im.onerror = res;
        im.src = src;
        if (im.complete) res();
        setTimeout(res, 700);
      });

      const els  = [copy, model, slot].filter(Boolean);
      let busy = false;

      /* Swipe left: what's on screen exits to the left, the next slide
         enters from the right. Copy and cut-out move together. */
      const go = async n => {
        if (busy) return;
        busy = true;
        i = (n + slides.length) % slides.length;
        dots.forEach((d, k) => d.classList.toggle('is-on', k === i));

        await preload(slides[i].img);

        els.forEach(el => { el.classList.remove('in'); el.classList.add('sw-out'); });
        await new Promise(r => setTimeout(r, 360));

        paint(slides[i]);

        /* jump to the right with the transition suppressed, then release */
        els.forEach(el => { el.classList.remove('sw-out'); el.classList.add('sw-in'); });
        await new Promise(r => setTimeout(r, 40));
        els.forEach(el => { el.classList.remove('sw-in'); el.classList.add('in'); });

        setTimeout(() => { busy = false; }, 420);
      };

      if (copy) copy.style.transition = 'opacity .38s var(--e), transform .55s var(--e)';

      /* Start clean and visible. If a transition is ever interrupted the
         element must not be left stranded in a swipe state. */
      const settle = () => els.forEach(el => {
        el.classList.remove('sw-out', 'sw-in');
        el.classList.add('in');
      });
      setTimeout(settle, 20);
      on(document, 'visibilitychange', () => { if (!document.hidden && !busy) settle(); });

      const play = () => { clearInterval(timer); timer = setInterval(() => go(i + 1), 6000); };
      dots.forEach((d, k) => on(d, 'click', () => { go(k); play(); }));

      const hero = $('.hero');
      on(hero, 'mouseenter', () => clearInterval(timer));
      on(hero, 'mouseleave', play);

      if (slides.length > 1) play();
    }
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

  /* A category in the address applies the matching filter on arrival, so
     a link like shop.html?c=headwear actually lands on headwear instead
     of the full catalogue. */
  (() => {
    const group = $('[data-filter-group]');
    if (!group) return;
    const want = new URLSearchParams(location.search).get('c');
    if (!want) return;
    const pill = group.querySelector('.pill[data-val="' + want + '"]');
    if (pill) pill.click();
  })();

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

  /* ---------- 13. HEADING LINE REVEAL ----------
     A heading's hard line breaks become separate clipping boxes, so a
     two-line headline arrives one line after the other. Only headings
     inside a .rv container are touched — that container is what flips
     them visible, so nothing can end up stranded hidden. */
  const lineSplit = el => {
    if (!el || el.dataset.ln === '1') return;
    /* Hard breaks give the lines; each line is then split into words. Built
       with textContent rather than innerHTML so entities such as &aelig;
       survive and nothing can be injected through a heading. */
    const probe = document.createElement('div');
    const lines = el.innerHTML.split(/<br\s*\/?>/i)
      .map(s => { probe.innerHTML = s; return probe.textContent.replace(/\s+/g, ' ').trim(); })
      .filter(Boolean);
    if (!lines.length) return;

    const STEP = 55;   /* ms per character — unhurried on purpose */
    const GAP  = 340;  /* pause before the next line starts */
    let at = 0;

    el.textContent = '';
    lines.forEach(line => {
      const ln = document.createElement('span');
      ln.className = 'ln';

      const type = (parent, text) => {
        for (const c of text) {          /* of, not index — keeps æ in one piece */
          const ch = document.createElement('span');
          ch.className = 'ch';
          ch.textContent = c;
          ch.style.transitionDelay = at + 'ms';
          at += STEP;
          parent.appendChild(ch);
        }
      };

      line.split(' ').forEach((word, wi) => {
        /* the space lives outside the word so a long line can still wrap */
        if (wi) type(ln, ' ');
        const wd = document.createElement('span');
        wd.className = 'wd';
        type(wd, word);
        ln.appendChild(wd);
      });

      at += GAP;
      el.appendChild(ln);
    });
    el.dataset.ln = '1';
  };
  /* Replay: drop the class, force the removal to land, put it back. A
     reflow rather than rAF, because rAF is throttled in a hidden tab. */
  function retype(el) {           /* declaration, so it is hoisted: the hero
                                     carousel calls this from further up */
    el.classList.remove('typed');
    void el.offsetWidth;
    el.classList.add('typed');
  }

  /* The hero headline, and nothing else. Typing every heading on a five
     second timer turned the page into a flicker, and because the replay
     did not actually restart the character animation, all that showed was
     the caret winking on and off like a stray cursor. One heading, typed
     once on arrival and again when the carousel brings in a new slide. */
  /* The first big heading on whatever page this is: the homepage hero,
     a service page headline, or a page title like Magazine. */
  const heroHead = $('main #heroTitle') || $('main .h-hero') || $('main .h-page');
  if (heroHead) {
    if (window.matchMedia('(prefers-reduced-motion:reduce)').matches) {
      heroHead.classList.add('typed');
    } else {
      lineSplit(heroHead);
      retype(heroHead);
    }
  }

  /* ---------- 13b. SCROLL REVEAL ----------
     Only a handful of elements were marked by hand, so most of a page
     arrived already assembled. Everything inside a content section now
     takes part, which is what makes a page feel like it is being built
     as you scroll rather than simply being there. The hero is left out:
     it has its own choreography, and the header, drawers and overlays
     are not part of the page flow. */
  const REVEAL = [
    'main .wrap > *',
    'main .phead .wrap > *',
    'main .worlds > *', 'main .worldc > *', 'main .prods > *',
    'main .bene-grid > *', 'main .pkg-grid > *', 'main .edu-grid > *',
    'main .gal-masonry > *', 'main .prog-grid-4 > *', 'main .prog-grid-6 > *',
    'main .acards > *', 'main .tiles > *', 'main .dtiles > *',
  ].join(',');
  $$(REVEAL).forEach(el => {
    if (el.closest('.hero')) return;
    el.classList.add('rv');
  });

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
    /* Stagger by position within the element's own group rather than by
       global document order. Counting across the whole page meant a
       heading, its sub-line and its button could land on 340ms, 395ms and
       0ms — which reads as random instead of choreographed. */
    const seen = new Map();
    rv.forEach(el => {
      const group = el.closest('section, footer') || document.body;
      const n = seen.get(group) || 0;
      seen.set(group, n + 1);
      el.style.transitionDelay = Math.min(n, 7) * 105 + 'ms';
      io.observe(el);
    });
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

  /* ---------- 15. AI VIDEO PACKAGES ---------- */
  const pkgs = $$('.pkg');
  if (pkgs.length) {
    const hidden = $('#ae_package');
    const chip   = $('#pkgChosen');
    const wrap   = $('#briefWrap');

    pkgs.forEach(p => on(p, 'click', () => {
      pkgs.forEach(x => x.classList.remove('is-on'));
      p.classList.add('is-on');

      const name = p.dataset.pkg || '';
      if (hidden) hidden.value = name;
      if (chip) { chip.textContent = name; chip.classList.add('on'); }
      if (wrap) wrap.classList.add('ready');

      const first = $('#ae_name');
      if (wrap) {
        wrap.scrollIntoView({ behavior: 'smooth', block: 'start' });
        setTimeout(() => first && first.focus({ preventScroll: true }), 650);
      }
    }));
  }

  /* ---------- 16. LIVE FEEL ---------- */

  /* Light follows the cursor across cards and glass panels. */
  const lit = $$('.pcard, .icard, .acard, .edu-card, .wcard, .tile, .dtile, .glass, .glass-dark, .w-clock, .w-drop');
  if (lit.length && window.matchMedia('(hover:hover)').matches) {
    lit.forEach(el => {
      on(el, 'pointermove', e => {
        const r = el.getBoundingClientRect();
        el.style.setProperty('--mx', ((e.clientX - r.left) / r.width * 100) + '%');
        el.style.setProperty('--my', ((e.clientY - r.top) / r.height * 100) + '%');
      });
    });
    document.body.classList.add('has-cursor-light');
  }

  /* Countdown seconds tick visibly. */
  const cdS = $('#cdS');
  if (cdS) {
    let last = cdS.dataset.v || '';
    setInterval(() => {
      if (cdS.dataset.v !== last) {
        last = cdS.dataset.v;
        cdS.classList.add('tick');
        setTimeout(() => cdS.classList.remove('tick'), 220);
      }
    }, 200);
  }

  /* Parallax drift on the hero cut-out. */
  const hm = $('#heroModel');
  if (hm && window.matchMedia('(hover:hover)').matches && !window.matchMedia('(prefers-reduced-motion:reduce)').matches) {
    on(document, 'pointermove', e => {
      const x = (e.clientX / window.innerWidth - 0.5) * 12;
      const y = (e.clientY / window.innerHeight - 0.5) * 8;
      hm.style.setProperty('--px', x.toFixed(2) + 'px');
      hm.style.setProperty('--py', y.toFixed(2) + 'px');
    });
  }


  /* ---------- 17. PLAN CHOICE -> BOOKING LINK ----------
     Choosing a package does not navigate anywhere. It marks the choice and
     writes it onto the booking button, so whoever takes the call already
     knows which plan the enquiry is about. */
  const bookBtn = $("[data-book]");
  if (bookBtn) {
    const base = bookBtn.dataset.bookUrl || bookBtn.getAttribute("href") || "";
    const label = $("#planChosen");

    const setPlan = name => {
      if (label) { label.textContent = name; label.classList.add("on"); }
      if (!base || base === "#") return;          /* no calendar link set yet */
      const url = new URL(base, location.href);
      url.searchParams.set("utm_content", name);  /* the booking tool passes this through */
      bookBtn.setAttribute("href", url.toString());
    };

    $$("#planGrid .pkg").forEach(p =>
      on(p, "click", () => {
        document.body.classList.add("plan-picked");
        setPlan(p.dataset.pkg || "");
      }));
  }

  /* ---------- 15b. SHOP IS A MENU, NOT A LINK ----------
     Clicking a nav item that owns a drop-down should reveal the
     drop-down, not navigate. On a pointer device the menu is already
     open on hover, so the click is simply swallowed; on touch the first
     tap opens it. The children still navigate normally. */
  $$('.nav .menu-item-has-children > a, .mnav .menu-item-has-children > a').forEach(a => {
    on(a, 'click', e => {
      e.preventDefault();
      const li = a.parentElement;
      li.classList.toggle('is-open');
    });
  });

  /* ---------- 16. MARK ACTIVE NAV ---------- */
  const here = (location.pathname.split('/').pop() || 'index.html').toLowerCase();
  $$('.nav a, .mnav a').forEach(a => {
    const href = (a.getAttribute('href') || '').toLowerCase();
    if (href && href === here) a.classList.add('is-on');
  });
})();
