// Sidebar Toggle Functionality
document.addEventListener("DOMContentLoaded", function () {
    console.log("Sidebar toggle script loaded");
    // Get all necessary elements
    const sidebar = document.getElementById("sidebar");
    const mainContent = document.getElementById("main-content");
    const navbar = document.getElementById("navbar-main");
    const overlay = document.getElementById("sidebar-overlay");
    // Get toggle buttons
    const toggleButtons = document.querySelectorAll("[sidenav-trigger]");
    const closeButton = document.querySelector("[sidenav-close]");
    const desktopToggle = document.getElementById("desktop-toggle");
    const mobileToggle = document.getElementById("mobile-toggle");
    console.log("Elements found:", {
        sidebar: !!sidebar,
        mainContent: !!mainContent,
        navbar: !!navbar,
        overlay: !!overlay,
        toggleButtons: toggleButtons.length,
        desktopToggle: !!desktopToggle,
        mobileToggle: !!mobileToggle
    });
    // State management
    let sidebarVisible = true;

    // Function to show sidebar (expand)
    function showSidebar() {
        console.log("Showing sidebar");
        if (sidebar) {
            // Mobile behavior - show sidebar with overlay
            if (window.innerWidth < 1024) {
                sidebar.classList.remove("-translate-x-full");
                sidebar.classList.add("translate-x-0");
                if (overlay) {
                    overlay.classList.remove("hidden");
                }
                // Hide hamburger button
                if (mobileToggle) {
                    mobileToggle.classList.add("hidden");
                }
            } 
            // Desktop behavior - expand sidebar to full width
            else {
                sidebar.classList.remove("w-16", "sidebar-collapsed");
                sidebar.classList.add("w-64");
                // Show text elements
                document.querySelectorAll(".sidenav-text").forEach((text) => {
                    text.classList.remove("hidden", "opacity-0");
                    text.classList.add("opacity-100");
                });
                // Adjust main content margin
                if (mainContent) {
                    mainContent.classList.remove("sidebar-collapsed");
                }
            }
            sidebar.setAttribute("aria-expanded", "true");
            sidebarVisible = true;
            localStorage.setItem('sidebar-open', 'true');
            // Update desktop toggle icon
            if (desktopToggle) {
                const icon = desktopToggle.querySelector("i");
                if (icon) {
                    icon.classList.remove("fa-chevron-right");
                    icon.classList.add("fa-chevron-left");
                }
            }
        }
    }
    
    // Function to hide/collapse sidebar
    function hideSidebar() {
        console.log("Hiding sidebar");
        if (sidebar) {
            // Mobile behavior - hide sidebar completely
            if (window.innerWidth < 1024) {
                sidebar.classList.add("-translate-x-full");
                sidebar.classList.remove("translate-x-0");
                if (overlay) {
                    overlay.classList.add("hidden");
                }
                // Show hamburger button
                if (mobileToggle) {
                    mobileToggle.classList.remove("hidden");
                }
            } 
            // Desktop behavior - collapse sidebar to icon-only
            else {
                sidebar.classList.remove("w-64");
                sidebar.classList.add("w-16", "sidebar-collapsed");
                // Hide text elements
                document.querySelectorAll(".sidenav-text").forEach((text) => {
                    text.classList.add("hidden", "opacity-0");
                    text.classList.remove("opacity-100");
                });
                // Adjust main content margin for collapsed sidebar
                if (mainContent) {
                    mainContent.classList.add("sidebar-collapsed");
                }
            }
            sidebar.setAttribute("aria-expanded", "false");
            sidebarVisible = false;
            localStorage.setItem('sidebar-open', 'false');
            // Update desktop toggle icon
            if (desktopToggle) {
                const icon = desktopToggle.querySelector("i");
                if (icon) {
                    icon.classList.remove("fa-chevron-left");
                    icon.classList.add("fa-chevron-right");
                }
            }
        }
    }
    
    // Function to toggle sidebar
    function toggleSidebar() {
        console.log("Toggling sidebar, current state:", sidebarVisible);
        if (sidebarVisible) {
            hideSidebar();
        } else {
            showSidebar();
        }
    }
    
    // Add event listeners to all toggle buttons
    toggleButtons.forEach((button, index) => {
        console.log(`Adding event listener to toggle button ${index}`);
        button.addEventListener("click", function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log("Toggle button clicked");
            toggleSidebar();
        });
    });
    
    // Add event listener to close button
    if (closeButton) {
        console.log("Adding event listener to close button");
        closeButton.addEventListener("click", function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log("Close button clicked");
            hideSidebar();
        });
    }
    
    // Add event listener to overlay (mobile only)
    if (overlay) {
        console.log("Adding event listener to overlay");
        overlay.addEventListener("click", function() {
            console.log("Overlay clicked");
            hideSidebar();
        });
    }
    
    // Handle window resize
    window.addEventListener("resize", function() {
        console.log("Window resized, width:", window.innerWidth);
        // Reset sidebar state on resize
        if (window.innerWidth >= 1024) {
            // Desktop: show sidebar by default, hide overlay
            if (overlay) {
                overlay.classList.add("hidden");
            }
            if (!sidebarVisible) {
                showSidebar();
            }
        } else {
            // Mobile: hide sidebar by default
            if (sidebarVisible) {
                hideSidebar();
            }
        }
    });
    
    // Initialize sidebar state based on localStorage or screen size
    const savedSidebarState = localStorage.getItem('sidebar-open');
    if (savedSidebarState === 'false') {
        hideSidebar();
    } else if (savedSidebarState === 'true') {
        showSidebar();
    } else {
        // Fallback to default behavior
        if (window.innerWidth >= 1024) {
            // Desktop: show sidebar expanded by default, but ensure it's visible
            sidebar.classList.remove("-translate-x-full");
            sidebar.classList.add("translate-x-0", "w-64");
            
            // Show text elements
            document.querySelectorAll(".sidenav-text").forEach((text) => {
                text.classList.remove("hidden", "opacity-0");
                text.classList.add("opacity-100");
            });
            
            // Set proper main content margin for expanded sidebar
            if (mainContent) {
                mainContent.classList.remove("sidebar-collapsed");
            }
            sidebar.setAttribute("aria-expanded", "true");
            sidebarVisible = true;
            console.log("Desktop: Sidebar initialized as expanded");
        } else {
            // Mobile: hide sidebar by default
            sidebarVisible = false;
            hideSidebar();
        }
    }
});

document.addEventListener('DOMContentLoaded', function () {
    var ctaCloseBtn = document.querySelector('[data-dismiss-target="#dropdown-cta"]');
    if (ctaCloseBtn) {
        ctaCloseBtn.addEventListener('click', function(e) {
            e.preventDefault();
            var ctaBox = document.getElementById('dropdown-cta');
            if (ctaBox) ctaBox.style.display = 'none';
        });
    }
});
