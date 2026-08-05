// Dedicated Dark Mode Toggle Script
// This script handles the dark/light mode toggle functionality for navbar and sidebar

document.addEventListener('DOMContentLoaded', function() {
    // Get both toggle elements (sidebar toggle is optional)
    const navToggle = document.getElementById('theme-toggle-checkbox-navbar');
    const sideToggle = document.getElementById('theme-toggle-checkbox-sidebar');
    const html = document.documentElement;
    const toggles = [navToggle, sideToggle].filter(Boolean);
    
    if (!toggles.length) {
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
        
        // Sync all available toggles
        toggles.forEach(toggle => { toggle.checked = isDark; });
    }
    
    // Initialize theme
    function initTheme() {
        const savedTheme = localStorage.getItem('color-theme');
        const currentlyDark = html.classList.contains('dark');
        
        let isDark = savedTheme ? savedTheme === 'dark' : currentlyDark;
        
        // Set toggle states
        toggles.forEach(toggle => { toggle.checked = isDark; });
        
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
    
    // Add event listeners for all toggles
    toggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            updateTheme(this.checked);
        });
    });
});
