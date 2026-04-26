'use strict';

(() => {
  const reduceMotion = matchMedia('(prefers-reduced-motion: reduce)').matches;

  // ── Header condense on scroll ─────────────────────────────────────────────
  const onScroll = () => {
    document.documentElement.classList.toggle('is-scrolled', window.scrollY > 32);
  };
  document.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  // ── Mobile nav toggle ────────────────────────────────────────────────────
  const toggle = document.querySelector('[data-nav-toggle]');
  if (toggle) {
    toggle.addEventListener('click', () => {
      const open = document.body.classList.toggle('is-nav-open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      toggle.setAttribute('aria-label', open ? 'Menü schließen' : 'Menü öffnen');
    });
  }
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && document.body.classList.contains('is-nav-open')) {
      document.body.classList.remove('is-nav-open');
      toggle?.setAttribute('aria-expanded', 'false');
    }
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

  // ── Conversion-tracking dataLayer events (vendor neutral) ────────────────
  // Pushed regardless of analytics setup; tag managers may consume them.
  const push = (payload) => {
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push(payload);
    if (window._paq) {
      window._paq.push(['trackEvent', payload.event, payload.label || '', payload.value || '']);
    }
  };

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
