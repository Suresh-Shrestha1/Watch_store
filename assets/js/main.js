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

// ── Wishlist ──────────────────────────────────────────────────────────────────
function toggleWishlist(button, productId) {
    const heartIcon = button.querySelector('.heart-icon');
    const isLiked = button.dataset.liked === '1';

    fetch(window.AJAX_URL || 'ajax_handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'ajax_action=toggle_wishlist&product_id=' + productId
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (data.action === 'added') {
                    button.dataset.liked = '1';
                    heartIcon.classList.remove('stroke-[#5A5F6D]', 'fill-none');
                    heartIcon.classList.add('fill-red-500', 'stroke-red-500');
                    showToast('Added to wishlist', 'success');
                } else {
                    button.dataset.liked = '0';
                    heartIcon.classList.remove('fill-red-500', 'stroke-red-500');
                    heartIcon.classList.add('stroke-[#5A5F6D]', 'fill-none');
                    showToast('Removed from wishlist', 'info');
                }
            } else if (data.redirect) {
                window.location.href = data.redirect; // not logged in → go to login
            } else {
                showToast(data.message || 'Something went wrong', 'error');
            }
        })
        .catch(() => showToast('Network error. Please try again.', 'error'));
}

// ── Add to Cart ───────────────────────────────────────────────────────────────
function addToCart(button, productId, strapSize) {
    // If this product needs a strap size and none was supplied yet, show the picker first.
    if (!strapSize && button.dataset.strapAdjustable === '1' && button.dataset.strapSizes) {
        showStrapSizeModal(button, productId);
        return;
    }

    const originalHTML = button.innerHTML;
    button.disabled = true;
    button.innerHTML = `<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg> Adding...`;

    let body = 'ajax_action=add_to_cart&product_id=' + productId + '&quantity=1';
    if (strapSize) {
        body += '&selected_strap_size=' + encodeURIComponent(strapSize);
    }

    fetch(window.AJAX_URL || 'ajax_handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: body
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                button.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg> Added!`;
                button.classList.replace('bg-[#1A1A2E]', 'bg-green-600');
                button.classList.remove('hover:bg-[#C9A84C]');
                showToast('Added to cart successfully!', 'success');

                // update header cart counter
                const cartCounter = document.querySelector('.cart-count');
                if (cartCounter && data.cart_count !== undefined) {
                    cartCounter.textContent = data.cart_count;
                }

                setTimeout(() => {
                    button.innerHTML = originalHTML;
                    button.classList.replace('bg-green-600', 'bg-[#1A1A2E]');
                    button.classList.add('hover:bg-[#C9A84C]');
                    button.disabled = false;
                }, 2000);

            } else if (data.requires_strap_size) {
                // Product needs a strap size selection — show modal instead of failing silently
                button.innerHTML = originalHTML;
                button.disabled = false;
                showStrapSizeModal(button, productId);
            } else if (data.redirect) {
                window.location.href = data.redirect; // not logged in
            } else {
                showToast(data.message || 'Failed to add to cart', 'error');
                button.innerHTML = originalHTML;
                button.disabled = false;
            }
        })
        .catch(() => {
            showToast('Network error. Please try again.', 'error');
            button.innerHTML = originalHTML;
            button.disabled = false;
        });
}

// ── Strap Size Modal ─────────────────────────────────────────────────────────
function showStrapSizeModal(button, productId) {
    const sizes = button.dataset.strapSizes.split(',').map(s => s.trim()).filter(Boolean);
    if (sizes.length === 0) return;

    const existing = document.getElementById('strapSizeModal');
    if (existing) existing.remove();

    const overlay = document.createElement('div');
    overlay.id = 'strapSizeModal';
    overlay.className = 'fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4';

    const sizeButtons = sizes.map(sz =>
        `<button type="button" data-size="${sz}" class="strap-size-option px-5 py-2.5 rounded-lg border border-[#E0E2E7] bg-white text-[#1A1A2E] font-['Inter'] font-medium text-[13px] hover:border-[#1B2A4A] transition-all duration-150 cursor-pointer">${sz}</button>`
    ).join('');

    overlay.innerHTML = `
        <div class="bg-white rounded-xl shadow-xl max-w-sm w-full p-6">
            <h3 class="font-['Inter'] font-semibold text-[16px] text-[#1A1A2E] mb-1">Select Strap Size</h3>
            <p class="font-['Inter'] text-[13px] text-[#5A5F6D] mb-4">Please select a strap size to continue.</p>
            <div class="flex flex-wrap gap-2 mb-5">${sizeButtons}</div>
            <button type="button" id="strapSizeModalClose" class="w-full h-10 rounded-lg border border-[#E0E2E7] font-['Inter'] text-[13px] text-[#5A5F6D] hover:border-[#C9A84C] transition-colors duration-150">Cancel</button>
        </div>`;

    document.body.appendChild(overlay);

    overlay.querySelectorAll('.strap-size-option').forEach(opt => {
        opt.addEventListener('click', function () {
            const size = this.dataset.size;
            overlay.remove();
            addToCart(button, productId, size);
        });
    });

    document.getElementById('strapSizeModalClose').addEventListener('click', () => overlay.remove());
    overlay.addEventListener('click', (e) => { if (e.target === overlay) overlay.remove(); });
}

// ── Toast ─────────────────────────────────────────────────────────────────────
function showToast(message, type = 'success') {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');

    // Match existing server-side alert styles (colors, font, border radius)
    let bgClass = 'bg-[#E8F5E9] border border-[#2E7D32]/20 text-[#2E7D32]';
    let icon = `<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`;

    if (type === 'error') {
        bgClass = 'bg-[#FDEAEA] border border-[#D64545]/20 text-[#D64545]';
        icon = `<svg class="w-4 h-4 text-[#D64545] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01"/></svg>`;
    } else if (type === 'info') {
        bgClass = 'bg-[#F7F8FA] border border-[#E0E2E7] text-[#5A5F6D]';
        icon = `<svg class="w-4 h-4 text-[#5A5F6D] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21S3 15.75 3 8.25C3 5.35 5.35 3 8.25 3c1.74 0 3.41.84 4.5 2.25C13.84 3.84 15.51 3 17.25 3 20.15 3 22.5 5.35 22.5 8.25c0 1.5-.36 2.85-.95 4.05"/><circle cx="17" cy="17" r="4.5" fill="white" stroke="currentColor" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.75 17h4.5"/></svg> `;
    }

    toast.className = `toast-enter pointer-events-auto flex items-center gap-3 ${bgClass} px-4 py-3 rounded-lg shadow-md min-w-[280px] max-w-sm font-['Inter'] text-[14px]`;
    toast.innerHTML = `${icon}<div class="ml-2">${message}</div>`;

    container.appendChild(toast);

    setTimeout(() => {
        toast.classList.replace('toast-enter', 'toast-exit');
        setTimeout(() => toast.remove(), 300);
    }, 2800);
}

// Hero Slider
(function() {
    function initSlider() {
        console.log("Hero Slider initSlider executed");
        const slides = document.querySelectorAll('.hero-slide');
        const dots = document.querySelectorAll('.hero-dot');
        const progressBar = document.getElementById('heroProgressBar');
        const slider = document.getElementById('heroSlider');

        console.log("slides found:", slides.length);
        console.log("slider found:", slider);

        if (!slides.length) return;

        let currentSlide = 0;
        let slideInterval;
        let progressInterval;
        const slideDuration = 5000;
        const progressStep = 50;

        function animateSlideContent(slideIndex) {
            const activeSlide = slides[slideIndex];
            if (!activeSlide) return;
            const elements = activeSlide.querySelectorAll('.slide-animate');
            elements.forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                const delay = parseInt(el.dataset.delay) || 0;
                setTimeout(() => {
                    el.style.transition = 'opacity 0.7s ease-out, transform 0.7s ease-out';
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                }, delay);
            });
        }

        function goToSlide(index) {
            if (!slides[index]) return;
            slides.forEach((slide) => {
                slide.style.opacity = '0';
                slide.style.zIndex = '1';
            });
            slides[index].style.opacity = '1';
            slides[index].style.zIndex = '2';

            dots.forEach((dot, i) => {
                if (i === index) {
                    dot.classList.remove('bg-white/30', 'hover:bg-white/50');
                    dot.classList.add('bg-[#C9A84C]');
                } else {
                    dot.classList.remove('bg-[#C9A84C]');
                    dot.classList.add('bg-white/30', 'hover:bg-white/50');
                }
            });

            currentSlide = index;
            animateSlideContent(index);
            resetProgress();
        }

        function nextSlide() {
            goToSlide((currentSlide + 1) % slides.length);
        }

        function prevSlide() {
            goToSlide((currentSlide - 1 + slides.length) % slides.length);
        }

        function resetProgress() {
            if (!progressBar) return;
            if (progressInterval) clearInterval(progressInterval);
            let width = 0;
            progressBar.style.width = '0%';
            progressInterval = setInterval(() => {
                width += (progressStep / slideDuration) * 100;
                if (width >= 100) {
                    width = 100;
                    clearInterval(progressInterval);
                }
                progressBar.style.width = width + '%';
            }, progressStep);
        }

        function startAutoSlide() {
            if (slideInterval) clearInterval(slideInterval);
            slideInterval = setInterval(nextSlide, slideDuration);
        }

        function stopAutoSlide() {
            if (slideInterval) clearInterval(slideInterval);
            if (progressInterval) clearInterval(progressInterval);
        }

        // Dots click navigation
        dots.forEach(dot => {
            dot.addEventListener('click', () => {
                goToSlide(parseInt(dot.dataset.index));
                startAutoSlide();
            });
        });

        // Pause on hover (desktop)
        if (slider) {
            slider.addEventListener('mouseenter', stopAutoSlide);
            slider.addEventListener('mouseleave', startAutoSlide);
        }

        // Touch/Swipe support (mobile)
        if (slider) {
            let touchStartX = 0;
            slider.addEventListener('touchstart', (e) => {
                touchStartX = e.changedTouches[0].screenX;
            }, { passive: true });

            slider.addEventListener('touchend', (e) => {
                const touchEndX = e.changedTouches[0].screenX;
                const diff = touchStartX - touchEndX;
                if (Math.abs(diff) > 50) {
                    if (diff > 0) nextSlide();
                    else prevSlide();
                    startAutoSlide();
                }
            }, { passive: true });
        }

        // Initialize
        goToSlide(0);
        startAutoSlide();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSlider);
    } else {
        initSlider();
    }
})();