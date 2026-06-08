function toggleMobileMenu() {
    document.getElementById('mobile-menu').classList.remove('-translate-x-full');
    document.getElementById('mobile-overlay').classList.remove('hidden');
}
function closeMobileMenu() {
    document.getElementById('mobile-menu').classList.add('-translate-x-full');
    document.getElementById('mobile-overlay').classList.add('hidden');
}
function toggleUserMenu(event) {
    event.stopPropagation();
    document.getElementById('user-menu').classList.toggle('hidden');
}
document.addEventListener('click', function (e) {
    var menu = document.getElementById('user-menu');
    if (menu && !menu.contains(e.target) && !e.target.closest('button[onclick^="toggleUserMenu"]')) {
        menu.classList.add('hidden');
    }
});


document.addEventListener('DOMContentLoaded', function () {

    // Mobile menu toggle
    const mobileToggle = document.getElementById('mobileMenuToggle');
    const mobileMenu = document.getElementById('mobileMenu');
    if (mobileToggle && mobileMenu) {
        mobileToggle.addEventListener('click', function () {
            mobileMenu.classList.toggle('hidden');
        });
    }

    // Auto-hide alerts after 5 seconds
    document.querySelectorAll('[data-auto-dismiss]').forEach(function (el) {
        setTimeout(function () {
            el.style.transition = 'opacity 0.3s ease';
            el.style.opacity = '0';
            setTimeout(function () { el.remove(); }, 300);
        }, 5000);
    });

    // Header scroll shadow
    const header = document.querySelector('header');
    if (header) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 10) {
                header.classList.add('shadow-[0_2px_8px_rgba(0,0,0,0.10)]');
            } else {
                header.classList.remove('shadow-[0_2px_8px_rgba(0,0,0,0.10)]');
            }
        });
    }
});