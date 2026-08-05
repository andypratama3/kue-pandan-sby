// Dedicated Dark Mode Toggle Script
// This script handles the dark/light mode toggle functionality for navbar and sidebar

document.addEventListener('DOMContentLoaded', function() {
    // Get both toggle elements
    const navToggle = document.getElementById('theme-toggle-checkbox-navbar');
    const sideToggle = document.getElementById('theme-toggle-checkbox-sidebar');
    const html = document.documentElement;
    
    if (!navToggle || !sideToggle) {
        console.error('Dark mode toggle elements not found!');
        return;
    }
    
    // Function to update theme
    function updateTheme(isDark) {
        if (isDark) {
            html.classList.add('dark');
            localStorage.setItem('color-theme', 'dark');
        } else {
            html.classList.remove('dark');
            localStorage.setItem('color-theme', 'light');
        }
        
        // Sync both toggles
        navToggle.checked = isDark;
        sideToggle.checked = isDark;
    }
    
    // Initialize theme
    function initTheme() {
        const savedTheme = localStorage.getItem('color-theme');
        const currentlyDark = html.classList.contains('dark');
        
        let isDark = savedTheme ? savedTheme === 'dark' : currentlyDark;
        
        // Set toggle states
        navToggle.checked = isDark;
        sideToggle.checked = isDark;
        
        // Ensure HTML class matches
        if (isDark) {
            html.classList.add('dark');
        } else {
            html.classList.remove('dark');
        }
        
        // Save current state if not saved
        if (!savedTheme) {
            localStorage.setItem('color-theme', isDark ? 'dark' : 'light');
        }
    }
    
    // Initialize
    initTheme();
    
    // Add event listeners for navbar toggle
    navToggle.addEventListener('change', function() {
        updateTheme(this.checked);
    });
    
    // Add event listeners for sidebar toggle
    sideToggle.addEventListener('change', function() {
        updateTheme(this.checked);
    });
});
