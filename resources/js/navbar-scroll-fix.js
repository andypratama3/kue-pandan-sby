(function() {
  'use strict';

  function initNavbarScroll() {
    setTimeout(() => {
      const navbar = document.getElementById('navbar-main');

      if (!navbar) {
        console.log('Navbar not found');
        return;
      }

      console.log('Navbar scroll initialized');

      // Add CSS styles for smooth green navbar transition
      const style = document.createElement('style');
      style.id = 'navbar-green-scroll-styles';
      style.textContent = `
        #navbar-main {
          transition: all 0.3s ease-out !important;
          /* background-color dihapus agar tidak override kelas Tailwind */
        }

        /* Light mode scrolled navbar */
        #navbar-main.scrolled:not(.dark) {
          box-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.08) !important;
          background-color: rgba(255, 255, 255, 0.95) !important;
        }

        /* Dark mode scrolled navbar */
        #navbar-main.scrolled.dark,
        .dark #navbar-main.scrolled {
          box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.4) !important;
          background-color: rgba(30, 41, 59, 0.95) !important;
        }

        /* Mobile styles */
        @media (max-width: 768px) {
          /* Light mode mobile */
          #navbar-main.scrolled:not(.dark) {
            box-shadow: 0 6px 25px rgba(15, 23, 42, 0.1) !important;
            background-color: #ffffff !important; /* solid white saat scroll */
          }
          
          /* Dark mode mobile */
          #navbar-main.scrolled.dark,
          .dark #navbar-main.scrolled {
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.4) !important;
            background-color: #1e293b !important; /* dark:bg-slate-800 */
          }
        }

        /* Ensure profile dropdown works in scrolled state */
        #navbar-main.scrolled .group:hover .absolute {
          opacity: 1 !important;
          transform: scale(1) !important;
          pointer-events: auto !important;
          z-index: 99999 !important;
        }
      `;

      // Remove existing style if any
      const existingStyle = document.getElementById('navbar-green-scroll-styles');
      if (existingStyle) {
        existingStyle.remove();
      }

      document.head.appendChild(style);

      function handleScroll() {
        const scrollY = window.scrollY || window.pageYOffset;
        const isDarkMode = document.documentElement.classList.contains('dark') || 
                          document.body.classList.contains('dark');

        // Tambahkan class scrolled untuk efek shadow yang lebih dalam
        if (scrollY > 0) {
          navbar.classList.add('scrolled');
          // Add dark class to navbar if in dark mode
          if (isDarkMode) {
            navbar.classList.add('dark');
          } else {
            navbar.classList.remove('dark');
          }
        } else {
          navbar.classList.remove('scrolled');
          navbar.classList.remove('dark');
        }
      }

      // Watch for dark mode changes
      function observeDarkMode() {
        const observer = new MutationObserver(function(mutations) {
          mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
              handleScroll(); // Re-run scroll handler to update navbar classes
            }
          });
        });

        // Observe changes to html and body class attributes
        observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
        observer.observe(document.body, { attributes: true, attributeFilter: ['class'] });
      }

      // Add scroll event listener
      window.addEventListener('scroll', handleScroll, { passive: true });

      // Watch for dark mode changes
      observeDarkMode();

      // Initial check
      handleScroll();

    }, 100);
  }

  // Initialize
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initNavbarScroll);
  } else {
    initNavbarScroll();
  }

  window.addEventListener('load', initNavbarScroll);

})();
