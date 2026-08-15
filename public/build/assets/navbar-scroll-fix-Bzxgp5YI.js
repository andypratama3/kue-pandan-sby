(function(){function t(){setTimeout(()=>{const a=document.getElementById("navbar-main");if(!a){console.log("Navbar not found");return}console.log("Navbar scroll initialized");const o=document.createElement("style");o.id="navbar-green-scroll-styles",o.textContent=`
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
      `;const s=document.getElementById("navbar-green-scroll-styles");s&&s.remove(),document.head.appendChild(o);function r(){const e=window.scrollY||window.pageYOffset,n=document.documentElement.classList.contains("dark")||document.body.classList.contains("dark");e>0?(a.classList.add("scrolled"),n?a.classList.add("dark"):a.classList.remove("dark")):(a.classList.remove("scrolled"),a.classList.remove("dark"))}function i(){const e=new MutationObserver(function(n){n.forEach(function(l){l.type==="attributes"&&l.attributeName==="class"&&r()})});e.observe(document.documentElement,{attributes:!0,attributeFilter:["class"]}),e.observe(document.body,{attributes:!0,attributeFilter:["class"]})}window.addEventListener("scroll",r,{passive:!0}),i(),r()},100)}document.readyState==="loading"?document.addEventListener("DOMContentLoaded",t):t(),window.addEventListener("load",t)})();
