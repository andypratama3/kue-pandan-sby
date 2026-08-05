// Navbar stick on scroll ++ styles
// DISABLED - Using new navbar-scroll-fix.js instead

/*
// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
  var navbar = document.querySelector("[navbar-main]");
  
  if (!navbar) {
    console.log("Navbar not found!");
    return;
  }

  const white_elements = navbar.querySelectorAll(".text-white");
  const white_bg_elements = navbar.querySelectorAll("[sidenav-trigger] i.bg-white");
  const white_before_elements = navbar.querySelectorAll(".before\\:text-white");

  window.onscroll = function () {
    let blur = navbar.getAttribute("navbar-scroll");
    if (blur == "true") stickyNav();
  };

  function stickyNav() {
    if (window.scrollY >= 5) {
      // Add solid background with important styles
      navbar.style.backgroundColor = "rgba(255, 255, 255, 1) !important";
      navbar.style.backdropFilter = "blur(10px)";
      navbar.style.boxShadow = "0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)";
      navbar.style.borderBottom = "1px solid rgba(229, 231, 235, 1)";
      navbar.style.zIndex = "50";
      navbar.classList.remove("shadow-none");
      
      // Update text colors for better contrast on white background
      white_elements.forEach(element => {
        element.classList.remove("text-white");
        element.classList.add("text-slate-700");
        element.style.color = "#334155 !important";
      });
      white_bg_elements.forEach(element => {
        element.classList.remove("bg-white");
        element.classList.add("bg-slate-700");
        element.style.backgroundColor = "#374151 !important";
      });
      white_before_elements.forEach(element => {
        element.classList.add("before:text-slate-700");
        element.classList.remove("before:text-white");
      });
    } else {
      // Remove background effects when at top
      navbar.style.backgroundColor = "";
      navbar.style.backdropFilter = "";
      navbar.style.boxShadow = "";
      navbar.style.borderBottom = "";
      navbar.style.zIndex = "";
      navbar.classList.add("shadow-none");
      
      // Restore original white text colors
      white_elements.forEach(element => {
        element.classList.add("text-white");
        element.classList.remove("text-slate-700");
        element.style.color = "";
      });
      white_bg_elements.forEach(element => {
        element.classList.add("bg-white");
        element.classList.remove("bg-slate-700");
        element.style.backgroundColor = "";
      });
      white_before_elements.forEach(element => {
        element.classList.remove("before:text-slate-700");
        element.classList.add("before:text-white");
      });
    }
  }
});
*/
