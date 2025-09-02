document.addEventListener('DOMContentLoaded', () => {
  // Countdown Timer
  function updateCountdown() {
      const eventDate = new Date('September 18, 2025 09:00:00').getTime();
      const now = new Date().getTime();
      const distance = eventDate - now;

      const daysEl = document.getElementById('days');
      const hoursEl = document.getElementById('hours');
      const minutesEl = document.getElementById('minutes');
      const secondsEl = document.getElementById('seconds');

      if (!daysEl || !hoursEl || !minutesEl || !secondsEl) return;

      const days = Math.floor(distance / (1000 * 60 * 60 * 24));
      const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      const seconds = Math.floor((distance % (1000 * 60)) / 1000);

      daysEl.textContent = String(days).padStart(2, '0');
      hoursEl.textContent = String(hours).padStart(2, '0');
      minutesEl.textContent = String(minutes).padStart(2, '0');
      secondsEl.textContent = String(seconds).padStart(2, '0');

      if (distance < 0) {
          clearInterval(countdownInterval);
          const c = document.getElementById('countdown');
          if (c) c.innerHTML = "EVENT STARTED!";
      }
  }
  const countdownInterval = setInterval(updateCountdown, 1000);
  updateCountdown();

  // Smooth Scrolling
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
          const href = this.getAttribute('href');
          if (!href || href === '#') return;
          const target = document.querySelector(href);
          if (target) {
              e.preventDefault();
              target.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }
      });
  });

  // Navbar Scroll Effect
  let lastScrollTop = 0;
  const navbar = document.getElementById('navbar');
  const scrollThreshold = 50;

  window.addEventListener('scroll', function () {
      const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
      if (scrollTop > scrollThreshold) {
          navbar.classList.add('nav-scroll');
          if (scrollTop > lastScrollTop) {
              navbar.classList.remove('nav-visible');
          } else {
              navbar.classList.add('nav-visible');
          }
      } else {
          navbar.classList.remove('nav-scroll', 'nav-visible');
      }
      lastScrollTop = scrollTop;
  });

  // Mobile Menu Toggle
  const mobileMenuBtn = document.querySelector('.md\\:hidden');
  const mobileMenu = document.getElementById('mobileMenu');
  if (mobileMenuBtn && mobileMenu) {
    mobileMenuBtn.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
    });
    // Hide menu when a link is clicked
    mobileMenu.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            mobileMenu.classList.add('hidden');
        });
    });
  }

  // ===== Modal Logic (event delegation) =====
  document.body.addEventListener('click', (e) => {
    // Open
    const openBtn = e.target.closest('.open-modal');
    if (openBtn) {
      const id = openBtn.dataset.modal;
      const modal = id && document.getElementById(id);
      if (modal) modal.classList.remove('hidden');
      return;
    }
    // Close by clicking X or outside
    if (e.target.closest('.close-modal') || e.target.classList.contains('modal')) {
      const modal = e.target.closest('.modal') || document.querySelector('.modal:not(.hidden)');
      if (modal) modal.classList.add('hidden');
    }
  });
});
