'use strict';

(() => {
  const reduceMotion = matchMedia('(prefers-reduced-motion: reduce)').matches;

  // ── Header condense on scroll + sync --header-h for mobile nav ──────────
  const header = document.querySelector('[data-header]');
  const syncHeaderH = () => {
    if (header) {
      document.documentElement.style.setProperty('--header-h', header.offsetHeight + 'px');
    }
  };
  const onScroll = () => {
    document.documentElement.classList.toggle('is-scrolled', window.scrollY > 32);
    syncHeaderH();
  };
  document.addEventListener('scroll', onScroll, { passive: true });
  window.addEventListener('resize', syncHeaderH);
  onScroll();
  syncHeaderH();

  // ── Mobile nav toggle ────────────────────────────────────────────────────
  const toggle = document.querySelector('[data-nav-toggle]');
  const closeNav = () => {
    document.body.classList.remove('is-nav-open');
    toggle?.setAttribute('aria-expanded', 'false');
    toggle?.setAttribute('aria-label', 'Menü öffnen');
  };
  if (toggle) {
    toggle.addEventListener('click', () => {
      const open = document.body.classList.toggle('is-nav-open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      toggle.setAttribute('aria-label', open ? 'Menü schließen' : 'Menü öffnen');
    });
  }
  // Close nav when clicking the backdrop (body::after pseudo-element area)
  document.addEventListener('click', (e) => {
    if (!document.body.classList.contains('is-nav-open')) return;
    const nav = document.getElementById('primary-nav');
    const btn = document.querySelector('[data-nav-toggle]');
    if (nav && !nav.contains(e.target) && btn && !btn.contains(e.target)) {
      closeNav();
    }
  });
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeNav();
  });

  // ── Reveal sections on scroll ────────────────────────────────────────────
  const reveals = document.querySelectorAll('[data-reveal]');
  if (reveals.length) {
    if (reduceMotion || !('IntersectionObserver' in window)) {
      reveals.forEach((el) => el.classList.add('is-revealed'));
    } else {
      const io = new IntersectionObserver((entries, obs) => {
        for (const entry of entries) {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-revealed');
            obs.unobserve(entry.target);
          }
        }
      }, { rootMargin: '0px 0px -10% 0px', threshold: 0 });
      reveals.forEach((el) => io.observe(el));
    }
  }

  // ── Stat counters ────────────────────────────────────────────────────────
  const counters = document.querySelectorAll('[data-counter]');
  const animateCounter = (el) => {
    const target = parseFloat(el.dataset.counter);
    if (Number.isNaN(target)) return;
    if (reduceMotion) {
      el.textContent = String(target);
      return;
    }
    const duration = 1200;
    const start = performance.now();
    const ease = (t) => 1 - Math.pow(1 - t, 3);
    const tick = (now) => {
      const t = Math.min(1, (now - start) / duration);
      const v = target * ease(t);
      el.textContent = target % 1 === 0 ? Math.round(v).toString() : v.toFixed(1);
      if (t < 1) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
  };
  if (counters.length && 'IntersectionObserver' in window) {
    const io = new IntersectionObserver((entries, obs) => {
      for (const entry of entries) {
        if (entry.isIntersecting) {
          animateCounter(entry.target);
          obs.unobserve(entry.target);
        }
      }
    }, { threshold: 0.4 });
    counters.forEach((el) => io.observe(el));
  } else {
    counters.forEach(animateCounter);
  }

  // ── Consent banner (categorised: necessary / statistics / marketing) ─────
  const banner = document.getElementById('consent-banner');
  if (banner) {
    banner.addEventListener('click', (e) => {
      const btn = e.target.closest('[data-consent]');
      if (!btn) return;
      const mode = btn.dataset.consent;
      let cats = ['necessary'];
      if (mode === 'all') {
        cats = ['necessary', 'statistics', 'marketing'];
      } else if (mode === 'custom') {
        banner.querySelectorAll('[data-cat]:checked').forEach(c => cats.push(c.dataset.cat));
      } // 'denied' → only necessary
      const secure = location.protocol === 'https:' ? '; Secure' : '';
      document.cookie = `rt_consent_v2=${cats.join(',')}; Max-Age=31536000; Path=/; SameSite=Lax${secure}`;
      banner.style.display = 'none';
      // Reload only when at least one analytics/marketing tag was granted, so
      // the head can boot the corresponding scripts.
      if (cats.length > 1) location.reload();
    });
  }

  // ── Conversion-tracking dataLayer events (vendor neutral) ────────────────
  // Pushed regardless of analytics setup; tag managers may consume them.
  // Also forwarded to /api/track for server-side tagging (consent-gated server-side).
  const push = (payload) => {
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push(payload);
    if (window._paq) {
      window._paq.push(['trackEvent', payload.event, payload.label || '', payload.value || '']);
    }
    try {
      if (navigator.sendBeacon) {
        navigator.sendBeacon('/api/track', new Blob([JSON.stringify({
          event: payload.event,
          label: payload.label || '',
          value: payload.value || null,
          page:  location.pathname,
        })], { type: 'application/json' }));
      }
    } catch (_) { /* ignore */ }
  };

  // ── Initial page_view + click-IDs / UTM ins dataLayer ────────────────────
  // Wird VOR allen User-Aktionen gepushed, damit Tag-Manager das Event
  // beim ersten "consent.granted" zur Verfuegung haben.
  (function () {
    var p = new URLSearchParams(location.search);
    var attr = {};
    ['utm_source','utm_medium','utm_campaign','utm_term','utm_content','gclid','fbclid','msclkid'].forEach(function (k) {
      var v = p.get(k);
      if (v) attr[k] = v.slice(0, 200);
    });
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push(Object.assign({
      event: 'page_view_enriched',
      page:  location.pathname,
      lang:  document.documentElement.lang || 'de'
    }, attr));
  })();

  // ── Web Vitals via PerformanceObserver → /api/track ──────────────────────
  // Sammelt LCP, INP (oder FID Fallback), CLS und sendet sie via sendBeacon
  // wenn der Tab in den Hintergrund geht (zuverlaessigster Zeitpunkt).
  (function () {
    if (!('PerformanceObserver' in window)) return;
    var vitals = { lcp: 0, cls: 0, inp: 0, ttfb: 0 };

    try { vitals.ttfb = performance.getEntriesByType('navigation')[0]?.responseStart || 0; } catch (_) {}

    var lcpObs;
    try {
      lcpObs = new PerformanceObserver(function (list) {
        var entries = list.getEntries();
        var last = entries[entries.length - 1];
        if (last && last.renderTime) vitals.lcp = last.renderTime;
        else if (last && last.loadTime) vitals.lcp = last.loadTime;
      });
      lcpObs.observe({ type: 'largest-contentful-paint', buffered: true });
    } catch (_) {}

    try {
      new PerformanceObserver(function (list) {
        list.getEntries().forEach(function (e) {
          if (!e.hadRecentInput) vitals.cls += e.value;
        });
      }).observe({ type: 'layout-shift', buffered: true });
    } catch (_) {}

    try {
      new PerformanceObserver(function (list) {
        list.getEntries().forEach(function (e) {
          if (e.duration && e.duration > vitals.inp) vitals.inp = e.duration;
        });
      }).observe({ type: 'event', buffered: true, durationThreshold: 16 });
    } catch (_) {}

    function flush() {
      try {
        if (lcpObs) lcpObs.takeRecords();
      } catch (_) {}
      var payload = {
        event: 'web_vitals',
        page:  location.pathname,
        lcp:   Math.round(vitals.lcp),
        cls:   Math.round(vitals.cls * 1000) / 1000,
        inp:   Math.round(vitals.inp),
        ttfb:  Math.round(vitals.ttfb)
      };
      window.dataLayer = window.dataLayer || [];
      window.dataLayer.push(payload);
      try {
        if (navigator.sendBeacon) {
          navigator.sendBeacon('/api/track', new Blob([JSON.stringify(payload)], { type: 'application/json' }));
        }
      } catch (_) {}
    }
    addEventListener('visibilitychange', function () {
      if (document.visibilityState === 'hidden') flush();
    });
    addEventListener('pagehide', flush);
  })();

  document.addEventListener('click', (e) => {
    const a = e.target.closest && e.target.closest('a[href]');
    if (!a) return;
    const href = a.getAttribute('href') || '';
    if (href.startsWith('tel:')) {
      push({ event: 'phone_click', label: href.replace('tel:', ''), source: a.dataset.source || a.closest('section')?.id || 'page' });
    } else if (href.startsWith('mailto:')) {
      push({ event: 'email_click', label: href.replace('mailto:', '').split('?')[0], source: a.dataset.source || a.closest('section')?.id || 'page' });
    } else if (a.matches('.btn--primary, .primary-nav__cta')) {
      push({ event: 'cta_click', label: (a.textContent || '').trim().slice(0, 60) });
    }
  }, { passive: true });

  // Scroll-depth milestones (25/50/75/100)
  if (!reduceMotion) {
    const milestones = [25, 50, 75, 100];
    const fired = new Set();
    const scrollDepth = () => {
      const h = document.documentElement;
      const scrollable = h.scrollHeight - h.clientHeight;
      if (scrollable <= 0) return;
      const pct = (window.scrollY / scrollable) * 100;
      for (const m of milestones) {
        if (pct >= m && !fired.has(m)) {
          fired.add(m);
          push({ event: 'scroll_depth', value: m });
        }
      }
    };
    document.addEventListener('scroll', scrollDepth, { passive: true });
  }
})();
