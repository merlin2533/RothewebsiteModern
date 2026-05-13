<?php declare(strict_types=1); ?>
<style id="critical-css">
:root{--c-primary:#1B5DB3;--c-primary-deep:#0D2547;--c-accent:#1B5DB3;--c-accent-soft:#2E7DD1;--c-ink:#0F1F3D;--c-text:#334155;--c-muted:#64748B;--c-bg:#FFFFFF;--c-surface:#EEF3F9;--c-haze:#E1EBF4;--c-line:#C8D9EA;--font-headline:'Familjen Grotesk','Manrope','Inter',system-ui,-apple-system,'Segoe UI',sans-serif;--font-body:'Inter',system-ui,-apple-system,'Segoe UI',sans-serif;--content-max:1240px;--pad-x:clamp(1rem,4vw,2rem);--pad-y:clamp(3rem,7vw,7rem);--radius:8px;--radius-lg:16px;--touch:44px;--header-h:68px}
*,*::before,*::after{box-sizing:border-box}
body,h1,h2,h3,h4,p,ul,ol{margin:0}
ul,ol{padding:0;list-style:none}
img,svg{display:block;max-width:100%;height:auto}
a{color:inherit;text-decoration:none}
button{font:inherit;color:inherit;background:0;border:0;cursor:pointer}
html{font-family:var(--font-body);color:var(--c-text);background:var(--c-bg);line-height:1.65;-webkit-text-size-adjust:100%;font-size:clamp(1rem,0.95rem + 0.25vw,1.125rem)}
body{min-height:100vh}
h1,h2,h3,h4{font-family:var(--font-headline);color:var(--c-ink);line-height:1.15;letter-spacing:-.02em;font-weight:700}
h1{font-size:clamp(2rem,1.4rem + 3.2vw,4rem);letter-spacing:-.03em;line-height:1.08}
h2{font-size:clamp(1.5rem,1.1rem + 1.8vw,2.4rem)}
.container{max-width:var(--content-max);margin-inline:auto;padding-inline:var(--pad-x)}
.container--narrow{max-width:880px}
.skip-link{position:absolute;left:-9999px;top:0;z-index:1000;background:var(--c-primary-deep);color:#fff;padding:.75rem 1rem;font-weight:600}
.skip-link:focus{left:1rem;top:1rem}
.eyebrow{display:inline-block;font-family:var(--font-body);font-weight:600;font-size:.78rem;letter-spacing:.18em;text-transform:uppercase;color:var(--c-accent);padding-bottom:.4rem;position:relative}
.eyebrow::after{content:'';position:absolute;left:0;bottom:0;width:28px;height:2px;background:var(--c-accent)}
.marquee{background:var(--c-accent);color:#fff;overflow:hidden}
.marquee__track{display:flex;gap:2.5rem;white-space:nowrap;padding:.5rem 0;font-size:.72rem;letter-spacing:.16em;text-transform:uppercase;font-weight:600;animation:marquee 55s linear infinite}
@keyframes marquee{from{transform:translateX(0)}to{transform:translateX(-50%)}}
.topbar{background:var(--c-surface);color:var(--c-text);font-size:.82rem;border-bottom:1px solid var(--c-line)}
.topbar__inner{display:flex;align-items:center;justify-content:space-between;min-height:36px;gap:1rem;flex-wrap:wrap}
.topbar__label{color:var(--c-muted);letter-spacing:.04em}
.topbar__contact{display:flex;gap:1.5rem;flex-wrap:wrap}
.topbar__link{display:inline-flex;align-items:center;gap:.4rem;color:var(--c-ink);font-weight:500}
.site-header{position:sticky;top:0;z-index:50;background:rgba(255,255,255,.96);backdrop-filter:saturate(140%) blur(8px);border-bottom:1px solid var(--c-line);transition:padding .22s ease-out}
.site-header__inner{display:flex;align-items:center;justify-content:space-between;gap:1.5rem;padding-block:1rem}
.brand{flex-shrink:0;min-width:0}
.brand__logo{height:40px;width:auto;max-width:160px;object-fit:contain;display:block}
.primary-nav{display:flex;align-items:center;gap:1.4rem;font-weight:500}
.primary-nav a{color:var(--c-ink);padding:.4rem 0;position:relative;font-size:.95rem}
.primary-nav__cta{background:var(--c-accent);color:#fff !important}
.nav-toggle{display:none;padding:.4rem;min-height:44px;min-width:44px;align-items:center;justify-content:center}
.nav-toggle__icon{stroke:currentColor;fill:none;stroke-width:2}
.nav-toggle__icon--close{display:none}
body.is-nav-open .nav-toggle__icon--menu{display:none}
body.is-nav-open .nav-toggle__icon--close{display:block}
.btn{display:inline-flex;align-items:center;gap:.5rem;padding:.85rem 1.4rem;font-weight:600;font-size:.95rem;border-radius:var(--radius);white-space:nowrap;line-height:1}
.btn--primary{background:var(--c-accent);color:#fff !important}
.btn--ghost{border:1.5px solid var(--c-ink);color:var(--c-ink) !important}
.hero{position:relative;overflow:hidden}
.hero--home{display:grid;grid-template-columns:1fr;background:var(--c-surface);min-height:clamp(520px,70vh,720px)}
.hero--home .hero__panel{padding:clamp(3rem,6vw,5rem) var(--pad-x);display:flex;flex-direction:column;justify-content:center;max-width:720px;margin-inline:auto}
.hero--home .hero__media{position:relative;min-height:280px;background:var(--c-primary-deep) center/cover no-repeat;background-image:url('/assets/images/placeholders/hero-home.svg')}
.hero__headline{margin-block:1rem 1.2rem}
.hero__sub{font-size:clamp(1.05rem,1.4vw,1.25rem);color:var(--c-muted);max-width:50ch}
.hero__cta{display:flex;gap:.8rem;flex-wrap:wrap;margin-top:2rem}
.hero--inner{background:var(--c-surface);color:var(--c-ink);padding:clamp(3.5rem,6vw,5rem) 0 clamp(2.5rem,4vw,3.5rem);position:relative;border-bottom:1px solid var(--c-line)}
.hero--inner::before{content:'';position:absolute;left:0;right:0;bottom:0;height:4px;background:var(--c-accent)}
.hero--inner .container{max-width:880px}
.hero--inner .hero__sub{color:var(--c-muted);margin-top:1rem}
@media(max-width:899px){
  .nav-toggle{display:inline-flex}
  .primary-nav{position:fixed;top:var(--header-h,68px);right:0;bottom:0;left:0;width:auto;background:#fff;color:var(--c-ink);flex-direction:column;align-items:stretch;padding:0 0 2rem;gap:0;border-top:3px solid var(--c-accent);transform:translateX(100%);transition:transform .22s ease-out;z-index:60;overflow-y:auto}
  .primary-nav a{padding:1rem 1.5rem;border-bottom:1px solid var(--c-line);display:flex;align-items:center;font-size:1.05rem;font-weight:600;min-height:var(--touch)}
  body.is-nav-open .primary-nav{transform:translateX(0)}
  body.is-nav-open{overflow:hidden}
}
@media(min-width:900px){.hero--home{grid-template-columns:minmax(0,1fr) minmax(0,1.05fr)}}
@media(max-width:640px){.marquee{display:none}.topbar__label{display:none}}
@media(prefers-reduced-motion:reduce){*,*::before,*::after{animation-duration:.01ms !important;transition-duration:.01ms !important}.marquee__track{animation:none}}
</style>
