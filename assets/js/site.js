// ══ AUTH STATE (from PHP) ══
var WF_LOGGED_IN = document.body.dataset.loggedIn === 'true';
var WF_LOGIN_URL = document.body.dataset.loginUrl;

// ══ PAGE LOADER ══
(function () {
  var loader = document.getElementById('wf-loader');
  function hideLoader() {
    if (loader) loader.classList.add('hidden');
  }
  // Keep the animated loader, but never wait for offscreen images or third parties.
  var hero = document.querySelector('.wf-hero__reveal-img--1');
  if (!hero || hero.complete) requestAnimationFrame(hideLoader);
  else hero.addEventListener('load', hideLoader, { once: true });
  setTimeout(hideLoader, 700);
})();

// ══ LAZY CARD ENTRANCE (IntersectionObserver) ══
(function () {
  // Tag all product/category cards as lazy-card
  document.querySelectorAll(
    '.product-card, .wf-prod-card, .wf-cat-card, .category-card, .wf-benefit'
  ).forEach(function (el) {
    el.classList.add('lazy-card');
  });

  if (!('IntersectionObserver' in window)) {
    // Fallback: show all immediately
    document.querySelectorAll('.lazy-card').forEach(function (el) {
      el.classList.add('visible');
    });
    return;
  }

  var cardObserver = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        // Stagger siblings in same grid row
        var siblings = Array.from(entry.target.parentElement.children);
        var idx = siblings.indexOf(entry.target);
        entry.target.style.transitionDelay = (idx % 4 * 80) + 'ms';
        entry.target.classList.add('visible');
        cardObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });

  document.querySelectorAll('.lazy-card').forEach(function (el) {
    cardObserver.observe(el);
  });
})();

// ══ LAZY IMAGE LOADING (native + fallback) ══
(function () {
  document.querySelectorAll('img').forEach(function (img) {
    // Skip tiny icons, logos, hero images already visible
    if (img.closest('.site-navbar, #wf-hero, .wf-hero, #wf-loader') || img.loading === 'eager' || img.fetchPriority === 'high' || img.dataset.src) return;
    if (img.width < 40 && img.height < 40) return;

    // Use native lazy if supported
    img.loading = 'lazy';
    img.classList.add('lazy-img');

    // Mark as loaded once decoded
    if (img.complete) {
      img.classList.add('loaded');
    } else {
      img.addEventListener('load', function () { img.classList.add('loaded'); }, { once: true });
    }
  });
})();

// ══ MOBILE NAV ══
const toggler = document.getElementById('navToggler');
const navLinks = document.getElementById('navLinks');

function closeNav() {
  if (!navLinks) return;
  navLinks.classList.remove('open');
  const icon = toggler ? toggler.querySelector('i') : null;
  if (icon) icon.className = 'bi bi-list';
}

if (toggler && navLinks) {
  toggler.addEventListener('click', (e) => {
    e.stopPropagation();
    const isOpen = navLinks.classList.toggle('open');
    const icon = toggler.querySelector('i');
    icon.className = isOpen ? 'bi bi-x' : 'bi bi-list';
  });
  navLinks.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => closeNav());
  });
  document.addEventListener('click', (e) => {
    if (!navLinks.contains(e.target) && !toggler.contains(e.target)) closeNav();
  });
}

// ══ AUTO-DISMISS FLASH ══
setTimeout(() => {
  document.querySelectorAll('.flash').forEach(el => el.remove());
}, 5000);

// ══ LOGIN PROMPT MODAL ══
(function () {
  var modal    = document.getElementById('wf-login-modal');
  var backdrop = document.getElementById('wf-modal-backdrop');
  var cancelBtn = document.getElementById('wf-modal-cancel');
  if (!modal) return;

  function showModal() {
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
  }
  function hideModal() {
    modal.style.display = 'none';
    document.body.style.overflow = '';
  }

  if (cancelBtn)  cancelBtn.addEventListener('click',  hideModal);
  if (backdrop)   backdrop.addEventListener('click',   hideModal);
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') hideModal();
  });

  // Intercept ANY add-to-cart form submit when not logged in
  if (!WF_LOGGED_IN) {
    document.addEventListener('submit', function (e) {
      var form = e.target;
      // Check if it's a cart action form
      var actionInput = form.querySelector('input[name="action"]');
      if (!actionInput) return;
      if (actionInput.value !== 'add' && actionInput.value !== 'buy') return;
      e.preventDefault();
      showModal();
    }, true); // capture phase

    // Also intercept anchor/button clicks with data-require-login
    document.addEventListener('click', function (e) {
      var el = e.target.closest('[data-require-login]');
      if (!el) return;
      e.preventDefault();
      showModal();
    });
  }
})();
