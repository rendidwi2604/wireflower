(function () {
  if (!window.gsap || !window.ScrollTrigger) {
    document.documentElement.classList.add('wf-animation-fallback');
    return;
  }
  gsap.registerPlugin(ScrollTrigger);

  function loadRevealImage(img) {
    if (!img || !img.dataset.src) return;
    var picture = img.closest('picture');
    if (picture) picture.querySelectorAll('source[data-srcset]').forEach(function(source) {
      source.srcset = source.dataset.srcset;
      delete source.dataset.srcset;
    });
    if (img.dataset.srcset) img.srcset = img.dataset.srcset;
    img.src = img.dataset.src;
    delete img.dataset.src;
    delete img.dataset.srcset;
  }

  /* ══ HERO entrance — langsung animate ke opacity:1
        TIDAK pakai gs- class agar tidak ada konflik ══ */
  var heroTl = gsap.timeline({ defaults:{ ease:'power3.out' } });
  heroTl
    .to('.wf-hero__tag',     { opacity:1, y:0, duration:.7 }, .15)
    .to('.wf-hero__title',   { opacity:1, y:0, duration:.8 }, .28)
    .to('.wf-hero__sub',     { opacity:1, y:0, duration:.7 }, .45)
    .to('.wf-hero__actions', { opacity:1, y:0, duration:.6 }, .60)
    .to('.wf-stat-card',     { opacity:1, x:0, duration:.6, stagger:.15 }, .72);

  /* Set dari state */
  gsap.set('.wf-hero__tag',     { y:30 });
  gsap.set('.wf-hero__title',   { y:40 });
  gsap.set('.wf-hero__sub',     { y:30 });
  gsap.set('.wf-hero__actions', { y:25 });
  gsap.set('.wf-stat-card',     { x:40 });

  /* ── Parallax hero bg ── */
  gsap.to('#wf-hero .wf-hero__bg', {
    yPercent:20, ease:'none',
    scrollTrigger:{ trigger:'#wf-hero', start:'top top', end:'bottom top', scrub:true }
  });

  /* ══ ScrollTrigger: gs-fade-up ══ */
  gsap.utils.toArray('.gs-fade-up').forEach(function(el) {
    gsap.to(el, {
      opacity:1, y:0, duration:.7, ease:'power3.out',
      scrollTrigger:{ trigger:el, start:'top 88%', toggleActions:'play none none none' }
    });
  });

  /* ══ ScrollTrigger: gs-fade-in ══ */
  gsap.utils.toArray('.gs-fade-in').forEach(function(el) {
    gsap.to(el, {
      opacity:1, duration:.6, ease:'power2.out',
      scrollTrigger:{ trigger:el, start:'top 90%', toggleActions:'play none none none' }
    });
  });

  /* ══ ScrollTrigger: gs-scale (stagger per row) ══ */
  gsap.utils.toArray('.gs-scale').forEach(function(el, i) {
    // skip jika sudah dihandle oleh lazy-card IntersectionObserver
    if (el.classList.contains('wf-prod-card') || el.classList.contains('wf-cat-card')) return;
    gsap.to(el, {
      opacity:1, scale:1, duration:.65, ease:'power3.out',
      delay:(i % 4) * .08,
      scrollTrigger:{ trigger:el, start:'top 90%', toggleActions:'play none none none' }
    });
  });

  /* ══ Benefit stagger ══ */
  gsap.utils.toArray('.wf-benefit.gs-fade-up').forEach(function(el, i) {
    gsap.to(el, {
      opacity:1, y:0, duration:.6, ease:'power3.out', delay:i * .1,
      scrollTrigger:{ trigger:el, start:'top 90%', toggleActions:'play none none none' }
    });
  });

  /* ══ Micro: add-to-cart elastic ══ */
  document.querySelectorAll('.wf-prod-card__add').forEach(function(btn) {
    btn.addEventListener('click', function() {
      gsap.fromTo(btn, { scale:1.35 }, { scale:1, duration:.4, ease:'elastic.out(1,.5)' });
    });
  });

  /* ══ Micro: wishlist heart ══ */
  document.querySelectorAll('.wf-prod-card__wish').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var icon = btn.querySelector('i');
      var filled = icon.classList.contains('bi-heart-fill');
      icon.classList.toggle('bi-heart-fill', !filled);
      icon.classList.toggle('bi-heart', filled);
      gsap.fromTo(btn,
        { scale: filled ? 1 : .75 },
        { scale:1, duration:.45, ease:'elastic.out(1,.4)' }
      );
      gsap.to(btn, { color: filled ? '' : '#c9516f', duration:.2 });
    });
  });

  /* ══ Category reveal — cursor-tracked spotlight per card ══ */
  document.querySelectorAll('.wf-cat-card').forEach(function(card) {
    var reveal  = card.querySelector('.wf-cat-card__reveal');
    var name    = card.querySelector('.wf-cat-card__name');
    var desc    = card.querySelector('.wf-cat-card__desc');
    var radius  = 0;
    var cx = 50, cy = 50; // percent

    function applyClip() {
      reveal.style.clipPath = 'circle(' + radius + 'px at ' + cx + '% ' + cy + '%)';
    }

    card.addEventListener('mouseenter', function() {
      loadRevealImage(reveal.querySelector('img'));
      // Tween radius via gsap
      var obj = {r:0};
      gsap.to(obj, { r:120, duration:.5, ease:'power2.out',
        onUpdate: function(){ radius = obj.r; applyClip(); }
      });
      gsap.to([name, desc], { y:-4, duration:.3, ease:'power2.out', stagger:.05 });
    });

    card.addEventListener('touchstart', function() { loadRevealImage(reveal.querySelector('img')); }, {passive: true});

    card.addEventListener('mousemove', function(e) {
      var rect = card.getBoundingClientRect();
      cx = ((e.clientX - rect.left) / rect.width)  * 100;
      cy = ((e.clientY - rect.top)  / rect.height) * 100;
      applyClip();
    });

    card.addEventListener('mouseleave', function() {
      var obj = {r:radius};
      gsap.to(obj, { r:0, duration:.4, ease:'power2.in',
        onUpdate: function(){ radius = obj.r; applyClip(); }
      });
      gsap.to([name, desc], { y:0, duration:.25, ease:'power2.in', stagger:.03 });
    });
  });

  /* ══ Hero spotlight reveal — cursor follows mouse over hero ══ */
  (function(){
    var hero    = document.getElementById('wf-hero');
    var img2    = document.getElementById('heroRevealImg2');
    if (!hero || !img2) return;

    var radius  = 0;
    var cx = 50, cy = 50;

    function applyHeroClip() {
      img2.style.clipPath = 'circle(' + radius + 'px at ' + cx + '% ' + cy + '%)';
    }

    hero.addEventListener('mouseenter', function() {
      loadRevealImage(img2);
      var obj = {r:0};
      gsap.to(obj, { r:220, duration:.7, ease:'power2.out',
        onUpdate: function(){ radius = obj.r; applyHeroClip(); }
      });
    });

    hero.addEventListener('touchstart', function() { loadRevealImage(img2); }, {passive: true});

    hero.addEventListener('mousemove', function(e) {
      var rect = hero.getBoundingClientRect();
      cx = ((e.clientX - rect.left) / rect.width)  * 100;
      cy = ((e.clientY - rect.top)  / rect.height) * 100;
      applyHeroClip();
    });

    hero.addEventListener('mouseleave', function() {
      var obj = {r:radius};
      gsap.to(obj, { r:0, duration:.5, ease:'power2.in',
        onUpdate: function(){ radius = obj.r; applyHeroClip(); }
      });
    });
  })();

})();
