<?php declare(strict_types=1); ?>
<style id="critical-css">
:root{--c-primary:#0B2545;--c-primary-deep:#050F22;--c-accent:#C2410C;--c-accent-soft:#E5734A;--c-ink:#0F1419;--c-text:#2B3340;--c-muted:#6B7785;--c-bg:#FFFFFF;--c-surface:#F4F2EE;--c-haze:#ECE9E2;--c-line:#E3E8EF;--font-headline:'Familjen Grotesk','Manrope','Inter',system-ui,-apple-system,'Segoe UI',sans-serif;--font-body:'Inter',system-ui,-apple-system,'Segoe UI',sans-serif;--content-max:1240px;--pad-x:clamp(1rem,4vw,2rem);--pad-y:clamp(4rem,9vw,8rem);--radius:8px;--radius-lg:16px}
*,*::before,*::after{box-sizing:border-box}
body,h1,h2,h3,h4,p,ul,ol{margin:0}
ul,ol{padding:0;list-style:none}
img,svg{display:block;max-width:100%;height:auto}
a{color:inherit;text-decoration:none}
button{font:inherit;color:inherit;background:0;border:0;cursor:pointer}
html{font-family:var(--font-body);color:var(--c-text);background:var(--c-bg);line-height:1.65;-webkit-text-size-adjust:100%}
body{min-height:100vh;font-size:1.05rem}
h1,h2,h3,h4{font-family:var(--font-headline);color:var(--c-ink);line-height:1.1;letter-spacing:-.025em;font-weight:700}
h1{font-size:clamp(2.4rem,4vw + 1rem,4.5rem);letter-spacing:-.035em}
h2{font-size:clamp(1.8rem,2vw + .6rem,2.6rem)}
.container{max-width:var(--content-max);margin-inline:auto;padding-inline:var(--pad-x)}
.container--narrow{max-width:880px}
.skip-link{position:absolute;left:-9999px;top:0;z-index:1000;background:var(--c-primary-deep);color:#fff;padding:.75rem 1rem;font-weight:600}
.skip-link:focus{left:1rem;top:1rem}
.eyebrow{display:inline-block;font-family:var(--font-body);font-weight:600;font-size:.78rem;letter-spacing:.18em;text-transform:uppercase;color:var(--c-accent);padding-bottom:.4rem;position:relative}
.eyebrow::after{content:'';position:absolute;left:0;bottom:0;width:28px;height:2px;background:var(--c-accent)}
.marquee{background:var(--c-primary-deep);color:#fff;overflow:hidden;border-bottom:1px solid rgba(255,255,255,.08)}
.marquee__track{display:flex;gap:2rem;white-space:nowrap;padding:.55rem 0;font-size:.78rem;letter-spacing:.18em;text-transform:uppercase;font-weight:500;animation:marquee 60s linear infinite}
@keyframes marquee{from{transform:translateX(0)}to{transform:translateX(-50%)}}
.topbar{background:var(--c-primary);color:#fff;font-size:.85rem}
.topbar__inner{display:flex;align-items:center;justify-content:space-between;min-height:38px;gap:1rem;flex-wrap:wrap}
.topbar__label{color:rgba(255,255,255,.7);letter-spacing:.05em}
.topbar__contact{display:flex;gap:1.5rem;flex-wrap:wrap}
.topbar__link{display:inline-flex;align-items:center;gap:.4rem;color:#fff;font-weight:500}
.site-header{position:sticky;top:0;z-index:50;background:rgba(255,255,255,.96);backdrop-filter:saturate(140%) blur(8px);border-bottom:1px solid var(--c-line);transition:padding .22s ease-out}
.site-header__inner{display:flex;align-items:center;justify-content:space-between;gap:1.5rem;padding-block:1rem}
.brand{flex-shrink:0}
.brand__logo{height:44px;width:auto}
.primary-nav{display:flex;align-items:center;gap:1.4rem;font-weight:500}
.primary-nav a{color:var(--c-ink);padding:.4rem 0;position:relative;font-size:.95rem}
.primary-nav__cta{background:var(--c-accent);color:#fff !important}
.btn{display:inline-flex;align-items:center;gap:.5rem;padding:.85rem 1.4rem;font-weight:600;font-size:.95rem;border-radius:var(--radius);white-space:nowrap;line-height:1}
.btn--primary{background:var(--c-accent);color:#fff !important}
.btn--ghost{border:1.5px solid var(--c-ink);color:var(--c-ink) !important}
.hero{position:relative;overflow:hidden}
.hero--home{display:grid;grid-template-columns:1fr;background:var(--c-surface);min-height:clamp(520px,70vh,720px)}
.hero--home .hero__panel{padding:clamp(3rem,6vw,5rem) var(--pad-x);display:flex;flex-direction:column;justify-content:center;max-width:720px;margin-inline:auto}
.hero--home .hero__media{position:relative;min-height:280px;background:var(--c-primary-deep) center/cover no-repeat;background-image:url('/assets/images/placeholders/hero-home.svg')}
.hero__headline{font-size:clamp(2.6rem,5vw,4.4rem);margin-block:1rem 1.2rem}
.hero__sub{font-size:clamp(1.05rem,1.4vw,1.25rem);color:var(--c-muted);max-width:50ch}
.hero__cta{display:flex;gap:.8rem;flex-wrap:wrap;margin-top:2rem}
.hero--inner{background:var(--c-primary-deep);color:#fff;padding:clamp(4rem,7vw,6rem) 0 clamp(3rem,5vw,4rem);position:relative}
.hero--inner::before{content:'';position:absolute;left:0;right:0;bottom:0;height:6px;background:var(--c-accent)}
.hero--inner .container{max-width:880px}
.hero--inner h1,.hero--inner h2,.hero--inner .hero__headline{color:#fff}
.hero--inner p,.hero--inner .hero__sub{color:rgba(255,255,255,.78);margin-top:1rem}
.nav-toggle{display:none;padding:.4rem}
@media(max-width:899px){.nav-toggle{display:inline-flex}.primary-nav{position:fixed;inset:0 0 0 auto;width:min(360px,86vw);background:var(--c-primary-deep);color:#fff;flex-direction:column;align-items:stretch;padding:6rem 2rem 2rem;gap:.5rem;transform:translateX(100%);transition:transform .22s ease-out;z-index:60}body.is-nav-open .primary-nav{transform:translateX(0)}body.is-nav-open{overflow:hidden}}
@media(min-width:900px){.hero--home{grid-template-columns:minmax(0,1fr) minmax(0,1.05fr)}}
@media(prefers-reduced-motion:reduce){*,*::before,*::after{animation-duration:.01ms !important;transition-duration:.01ms !important}.marquee__track{animation:none}}
</style>
