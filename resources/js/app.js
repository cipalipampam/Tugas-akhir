import './bootstrap';

// --- SIDEBAR COLOR ---
window.sidebarColor = function(element) {
    var color = element.getAttribute('data-color');
    localStorage.setItem('sidebarColor', color);
    // Terapkan class pada sidebar
    var sidebar = document.getElementById('sidenav-main');
    if (sidebar) {
        // Hapus semua class bg-gradient-*
        sidebar.className = sidebar.className.replace(/bg-gradient-\w+/g, '').trim();
        // Tambahkan class baru
        sidebar.classList.add('bg-gradient-' + color);
    }
    // Update badge active
    document.querySelectorAll('.badge-colors .badge').forEach(function(badge) {
        badge.classList.remove('active');
    });
    if (element) element.classList.add('active');
};

// --- SIDENAV TYPE ---
window.sidebarType = function(element) {
    var type = element.getAttribute('data-class');
    localStorage.setItem('sidebarType', type);
    var sidebar = document.getElementById('sidenav-main');
    if (sidebar) {
        // Hapus class bg-gradient-dark, bg-transparent, bg-white
        sidebar.classList.remove('bg-gradient-dark', 'bg-transparent', 'bg-white');
        sidebar.classList.add(type);
    }
    // Update button active
    document.querySelectorAll('.d-flex > button[data-class]').forEach(function(btn) {
        btn.classList.remove('active');
    });
    if (element) element.classList.add('active');
};

// --- NAVBAR FIXED ---
window.navbarFixed = function(element) {
    var isFixed = element.checked;
    localStorage.setItem('navbarFixed', isFixed ? 'true' : 'false');
    var navbar = document.getElementById('navbarBlur');
    if (navbar) {
        if (isFixed) {
            navbar.classList.add('position-sticky', 'top-1');
        } else {
            navbar.classList.remove('position-sticky', 'top-1');
        }
    }
};

// --- DARK MODE ---
window.darkMode = function(element) {
    var isDark = element.checked;
    localStorage.setItem('darkMode', isDark ? 'true' : 'false');
    var body = document.body;
    if (body) {
        if (isDark) {
            body.classList.add('dark-version');
        } else {
            body.classList.remove('dark-version');
        }
    }
};

// --- ON PAGE LOAD: Terapkan preferensi dari localStorage ---
document.addEventListener('DOMContentLoaded', function() {
    // Sidebar Color
    var sidebarColorValue = localStorage.getItem('sidebarColor');
    if (sidebarColorValue) {
        var colorElem = document.querySelector('[data-color="' + sidebarColorValue + '"]');
        if (colorElem) window.sidebarColor(colorElem);
    }

    // Sidenav Type
    var sidebarTypeValue = localStorage.getItem('sidebarType');
    if (sidebarTypeValue) {
        var typeElem = document.querySelector('[data-class="' + sidebarTypeValue + '"]');
        if (typeElem) window.sidebarType(typeElem);
    }

    // Navbar Fixed
    var navbarFixed = localStorage.getItem('navbarFixed');
    if (navbarFixed !== null) {
        var navbarFixedCheckbox = document.getElementById('navbarFixed');
        if (navbarFixedCheckbox) {
            navbarFixedCheckbox.checked = (navbarFixed === 'true');
            window.navbarFixed(navbarFixedCheckbox);
        }
    }

    // Dark Mode
    var darkMode = localStorage.getItem('darkMode');
    if (darkMode !== null) {
        var darkModeCheckbox = document.getElementById('dark-version');
        if (darkModeCheckbox) {
            darkModeCheckbox.checked = (darkMode === 'true');
            window.darkMode(darkModeCheckbox);
        }
    }
});
