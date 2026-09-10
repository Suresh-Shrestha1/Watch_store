<?php
session_start();
require_once 'config/db.php';

// Fetch featured brands active only
$brands_sql = "SELECT id, name, slug, logo FROM brands WHERE is_active = 1 ORDER BY name ASC";
$brands_result = $conn->query($brands_sql);
$brands = [];
while ($row = $brands_result->fetch_assoc()) {
    $brands[] = $row;
}

// Fetch new arrivals (latest 8 active products with stock)
$new_arrivals_sql = "SELECT p.id, p.name, p.slug, p.price, p.gender, p.movement_type, p.stock_quantity, p.description, p.strap_adjustable, p.strap_size_options, b.name as brand_name,
    (SELECT image_url FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image
    FROM products p JOIN brands b ON p.brand_id = b.id
    WHERE p.is_active = 1 ORDER BY p.created_at DESC LIMIT 8";
$new_arrivals_result = $conn->query($new_arrivals_sql);
$new_arrivals = [];
while ($row = $new_arrivals_result->fetch_assoc()) {
    $new_arrivals[] = $row;
}

// Load wishlist product IDs for the logged in user so we can render heart state server-side
$wishlist_product_ids = [];
if (isset($_SESSION['user_id'])) {
    $uid = (int)$_SESSION['user_id'];
    $wl_res = $conn->query("SELECT product_id FROM wishlists WHERE user_id = $uid");
    if ($wl_res) {
        while ($r = $wl_res->fetch_assoc()) {
            $wishlist_product_ids[] = (int)$r['product_id'];
        }
    }
}

$page_title = "ChronoNest – Premium Multi-Brand Watch Store Nepal";

$categories = [
    [
        'name' => 'Unisex',
        'description' => 'Versatile pieces designed for everyday confidence.',
        'image' => 'assets/images/Unisex.jpeg',
        'href' => 'pages/shop.php?gender=Unisex',
    ],
    [
        'name' => 'Men',
        'description' => 'Bold essentials with a refined, modern edge.',
        'image' => 'assets/images/Men.png',
        'href' => 'pages/shop.php?gender=Men',
    ],
    [
        'name' => 'Women',
        'description' => 'Elegant styles crafted for standout presence.',
        'image' => 'assets/images/Women.png',
        'href' => 'pages/shop.php?gender=Women',
    ],
    [
        'name' => 'Kids',
        'description' => 'Playful, durable picks made for young style.',
        'image' => 'assets/images/Kids.jpeg',
        'href' => 'pages/shop.php?gender=Kids',
    ],
];

require_once 'includes/header.php';
?>

<!-- HERO SECTION - Full Width Auto-Scrolling Carousel -->
<section class="relative w-full overflow-hidden bg-[#1B2A4A]">
    <!-- Slider Container -->
    <div id="heroSlider" class="relative w-full h-[90vh]">

        <!-- Slides Wrapper -->
        <div id="heroSlidesWrapper" class="relative w-full h-full">

            <!-- SLIDE 1 - Luxury Watch -->
            <div class="hero-slide absolute inset-0 w-full h-full opacity-0 transition-opacity duration-1000 ease-in-out" data-slide="0">
                <div class="absolute inset-0">
                    <img src="assets/images/watch.jpg"
                        alt="Luxury Watch Collection"
                        class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-r from-black/75 via-black/50 to-black/20"></div>
                </div>

                <div class="relative z-10 h-full container mx-auto px-4 sm:px-6 lg:px-12 flex items-center">
                    <div class="max-w-xl slide-content">
                        <span class="inline-block font-['Inter'] font-semibold text-[11px] uppercase tracking-[2px] text-[#C9A84C] mb-4 slide-animate" data-delay="0">
                            New Collection 2025
                        </span>
                        <h1 class="font-['Playfair_Display'] font-bold text-[36px] md:text-[52px] lg:text-[60px] leading-[1.1] text-white mb-4 slide-animate" data-delay="150">
                            Discover Chrononest <span class="text-[#C9A84C]">Elegance</span>
                        </h1>
                        <p class="font-['Inter'] text-[15px] md:text-[17px] leading-[1.6] text-white/90 mb-8 max-w-md slide-animate" data-delay="300">
                            Browse premium watches from Rolex, Omega, Titan, Seiko & more.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-3 slide-animate" data-delay="450">
                            <a href="pages/shop.php"
                                class="inline-flex items-center justify-center gap-2 h-[48px] px-8 bg-[#C9A84C] hover:bg-[#B8953F] text-white font-['Inter'] font-semibold text-[14px] rounded-md transition-all duration-200 shadow-lg hover:shadow-xl">
                                Shop Now
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SLIDE 2 - Automatic Watch -->
            <div class="hero-slide absolute inset-0 w-full h-full opacity-0 transition-opacity duration-1000 ease-in-out" data-slide="1">
                <div class="absolute inset-0">
                    <img src="assets/images/watch-1.jpg"
                        alt="Luxury Automatic Watches"
                        class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-r from-black/75 via-black/50 to-black/20"></div>
                </div>

                <div class="relative z-10 h-full container mx-auto px-4 sm:px-6 lg:px-12 flex items-center">
                    <div class="max-w-xl slide-content">
                        <span class="inline-block font-['Inter'] font-semibold text-[11px] uppercase tracking-[2px] text-[#C9A84C] mb-4 slide-animate" data-delay="0">
                            Swiss Precision
                        </span>
                        <h1 class="font-['Playfair_Display'] font-bold text-[36px] md:text-[52px] lg:text-[60px] leading-[1.1] text-white mb-4 slide-animate" data-delay="150">
                            Automatic <span class="text-[#C9A84C]">Masterpieces</span>
                        </h1>
                        <p class="font-['Inter'] text-[15px] md:text-[17px] leading-[1.6] text-white/90 mb-8 max-w-md slide-animate" data-delay="300">
                            Handcrafted mechanical watches that stand the test of time.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-3 slide-animate" data-delay="450">
                            <a href="pages/shop.php?movement=Automatic"
                                class="inline-flex items-center justify-center gap-2 h-[48px] px-8 bg-[#C9A84C] hover:bg-[#B8953F] text-white font-['Inter'] font-semibold text-[14px] rounded-md transition-all duration-200 shadow-lg hover:shadow-xl">
                                Explore Collection
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SLIDE 3 - Women's Watch -->
            <div class="hero-slide absolute inset-0 w-full h-full opacity-0 transition-opacity duration-1000 ease-in-out" data-slide="2">
                <div class="absolute inset-0">
                    <img src="assets/images/watch-2.jpg"
                        alt="Women's Elegant Watches"
                        class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-r from-black/75 via-black/50 to-black/20"></div>
                </div>

                <div class="relative z-10 h-full container mx-auto px-4 sm:px-6 lg:px-12 flex items-center">
                    <div class="max-w-xl slide-content">
                        <span class="inline-block font-['Inter'] font-semibold text-[11px] uppercase tracking-[2px] text-[#C9A84C] mb-4 slide-animate" data-delay="0">
                            Women's Collection
                        </span>
                        <h1 class="font-['Playfair_Display'] font-bold text-[36px] md:text-[52px] lg:text-[60px] leading-[1.1] text-white mb-4 slide-animate" data-delay="150">
                            Elegance in <span class="text-[#C9A84C]">Every Second</span>
                        </h1>
                        <p class="font-['Inter'] text-[15px] md:text-[17px] leading-[1.6] text-white/90 mb-8 max-w-md slide-animate" data-delay="300">
                            Sophisticated designs crafted for the modern woman.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-3 slide-animate" data-delay="450">
                            <a href="pages/shop.php?gender=Women"
                                class="inline-flex items-center justify-center gap-2 h-[48px] px-8 bg-[#C9A84C] hover:bg-[#B8953F] text-white font-['Inter'] font-semibold text-[14px] rounded-md transition-all duration-200 shadow-lg hover:shadow-xl">
                                Shop Women's
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SLIDE 4 - Smartwatch -->
            <div class="hero-slide absolute inset-0 w-full h-full opacity-0 transition-opacity duration-1000 ease-in-out" data-slide="3">
                <div class="absolute inset-0">
                    <img src="assets/images/watch-3.jpg"
                        alt="Modern Smartwatches"
                        class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-r from-black/75 via-black/50 to-black/20"></div>
                </div>

                <div class="relative z-10 h-full container mx-auto px-4 sm:px-6 lg:px-12 flex items-center">
                    <div class="max-w-xl slide-content">
                        <span class="inline-block font-['Inter'] font-semibold text-[11px] uppercase tracking-[2px] text-[#C9A84C] mb-4 slide-animate" data-delay="0">
                            Smart Technology
                        </span>
                        <h1 class="font-['Playfair_Display'] font-bold text-[36px] md:text-[52px] lg:text-[60px] leading-[1.1] text-white mb-4 slide-animate" data-delay="150">
                            Future on Your <span class="text-[#C9A84C]">Wrist</span>
                        </h1>
                        <p class="font-['Inter'] text-[15px] md:text-[17px] leading-[1.6] text-white/90 mb-8 max-w-md slide-animate" data-delay="300">
                            Smartwatches from Apple, Samsung, Garmin & more.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-3 slide-animate" data-delay="450">
                            <a href="pages/shop.php?movement=Smartwatch"
                                class="inline-flex items-center justify-center gap-2 h-[48px] px-8 bg-[#C9A84C] hover:bg-[#B8953F] text-white font-['Inter'] font-semibold text-[14px] rounded-md transition-all duration-200 shadow-lg hover:shadow-xl">
                                View Smartwatches
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SLIDE 5 - Premium Luxury -->
            <div class="hero-slide absolute inset-0 w-full h-full opacity-0 transition-opacity duration-1000 ease-in-out" data-slide="4">
                <div class="absolute inset-0">
                    <img src="assets/images/watch-4.jpg"
                        alt="Premium Luxury Watches"
                        class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-r from-black/75 via-black/50 to-black/20"></div>
                </div>

                <div class="relative z-10 h-full container mx-auto px-4 sm:px-6 lg:px-12 flex items-center">
                    <div class="max-w-xl slide-content">
                        <span class="inline-block font-['Inter'] font-semibold text-[11px] uppercase tracking-[2px] text-[#C9A84C] mb-4 slide-animate" data-delay="0">
                            Luxury Edition
                        </span>
                        <h1 class="font-['Playfair_Display'] font-bold text-[36px] md:text-[52px] lg:text-[60px] leading-[1.1] text-white mb-4 slide-animate" data-delay="150">
                            Exclusive <span class="text-[#C9A84C]">Timepieces</span>
                        </h1>
                        <p class="font-['Inter'] text-[15px] md:text-[17px] leading-[1.6] text-white/90 mb-8 max-w-md slide-animate" data-delay="300">
                            Investment-grade watches from world's finest brands.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-3 slide-animate" data-delay="450">
                            <a href="pages/expensive.php"
                                class="inline-flex items-center justify-center gap-2 h-[48px] px-8 bg-[#C9A84C] hover:bg-[#B8953F] text-white font-['Inter'] font-semibold text-[14px] rounded-md transition-all duration-200 shadow-lg hover:shadow-xl">
                                View Luxury
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Slide Indicators (Pills) -->
        <div class="absolute bottom-6 md:bottom-8 left-1/2 -translate-x-1/2 z-20 flex items-center gap-2" id="heroDots">
            <button type="button" class="hero-dot h-1.5 w-8 rounded-full bg-[#C9A84C] transition-all duration-300" data-index="0" aria-label="Go to slide 1"></button>
            <button type="button" class="hero-dot h-1.5 w-8 rounded-full bg-white/30 hover:bg-white/50 transition-all duration-300" data-index="1" aria-label="Go to slide 2"></button>
            <button type="button" class="hero-dot h-1.5 w-8 rounded-full bg-white/30 hover:bg-white/50 transition-all duration-300" data-index="2" aria-label="Go to slide 3"></button>
            <button type="button" class="hero-dot h-1.5 w-8 rounded-full bg-white/30 hover:bg-white/50 transition-all duration-300" data-index="3" aria-label="Go to slide 4"></button>
            <button type="button" class="hero-dot h-1.5 w-8 rounded-full bg-white/30 hover:bg-white/50 transition-all duration-300" data-index="4" aria-label="Go to slide 5"></button>
        </div>

        <!-- Progress Bar -->
        <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-white/10 z-20">
            <div id="heroProgressBar" class="h-full bg-[#C9A84C] transition-none" style="width: 0%"></div>
        </div>

    </div>
</section>

<!-- NEW ARRIVALS -->
<section class="py-[60px] bg-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-10">
            <div>
                <span class="font-['Inter'] font-semibold text-[11px] uppercase tracking-[1.5px] text-[#C9A84C]">Just
                    Arrived</span>
                <h2 class="font-['Playfair_Display'] font-bold text-[28px] md:text-[32px] text-[#1A1A2E] mt-1">New
                    Arrivals</h2>
            </div>
            <a href="pages/shop.php"
                class="hidden sm:inline-flex items-center gap-2 font-['Inter'] font-semibold text-[14px] text-[#1B2A4A] hover:text-[#C9A84C] transition-colors duration-200">
                View All
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>

        <?php if (!empty($new_arrivals)): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
            <?php foreach ($new_arrivals as $product): ?>
            <div
                class="rounded-xl bg-white shadow-lg overflow-hidden group hover:shadow-[0_12px_32px_rgba(0,0,0,0.18)] hover:-translate-y-1 transition-all duration-300 border border-[#E0E2E7]">
                <!-- Image Section -->
                <div class="relative flex aspect-square items-center justify-center overflow-hidden bg-[#F7F8FA]">
                    <?php if ($product['main_image']): ?>
                    <a href="pages/product-detail.php?slug=<?= urlencode($product['slug']) ?>"
                        class="block w-full h-full flex items-center justify-center">
                        <img src="assets/uploads/products/<?= htmlspecialchars(basename($product['main_image'])) ?>"
                            alt="<?= htmlspecialchars($product['name']) ?>"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            loading="lazy">
                    </a>
                    <?php else: ?>
                    <a href="pages/product-detail.php?slug=<?= urlencode($product['slug']) ?>" class="block">
                        <svg class="w-16 h-16 text-[#E0E2E7]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke-width="1" />
                            <path stroke-linecap="round" stroke-width="1" d="M12 6v6l4 2" />
                        </svg>
                    </a>
                    <?php endif; ?>

                    <!-- Wishlist Button -->
                    <?php $is_in_wishlist = in_array((int)$product['id'], $wishlist_product_ids, true); ?>
                    <button type="button" onclick="toggleWishlist(this, <?= $product['id'] ?>)"
                        class="wishlist-btn absolute top-4 right-4 w-10 h-10 flex items-center justify-center bg-white hover:bg-red-50 shadow-md rounded-full transition-all duration-200 z-10"
                        aria-label="Add to wishlist" data-liked="<?= $is_in_wishlist ? '1' : '0' ?>">
                        <svg class="heart-icon w-4 h-4 transition-all duration-200 <?= $is_in_wishlist ? 'fill-red-500 stroke-red-500' : 'stroke-[#5A5F6D] fill-none' ?>"
                            stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </button>

                    <!-- Stock Badge -->
                    <div class="absolute top-4 left-4 z-10">
                        <?php if ($product['stock_quantity'] == 0): ?>
                        <span
                            class="inline-block px-2.5 py-1 rounded bg-[#FDEAEA] text-[#D64545] font-['Inter'] font-semibold text-[11px]">Out
                            of Stock</span>
                        <?php elseif ($product['stock_quantity'] <= 5): ?>
                        <span
                            class="inline-block px-2.5 py-1 rounded bg-[#FFF3E0] text-[#E65100] font-['Inter'] font-semibold text-[11px]">Low
                            Stock</span>
                        <?php else: ?>
                        <span
                            class="inline-block px-2.5 py-1 rounded bg-[#E8F5E9] text-[#2E7D32] font-['Inter'] font-semibold text-[11px]">In
                            Stock</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Card Content -->
                <div class="bg-white">
                    <div class="flex flex-col space-y-1.5 p-5 pb-3">
                        <a href="pages/product-detail.php?slug=<?= urlencode($product['slug']) ?>" class="block">
                            <h3
                                class="font-['Playfair_Display'] font-semibold text-[18px] text-[#1A1A2E] leading-tight tracking-tight line-clamp-1 hover:text-[#C9A84C] transition-colors duration-200">
                                <?= htmlspecialchars($product['name']) ?>
                            </h3>
                        </a>
                        <div class="flex items-center gap-2 flex-wrap pt-1">
                            <span
                                class="inline-flex items-center rounded-full border border-[#E0E2E7] px-2.5 py-0.5 text-[11px] font-semibold text-[#5A5F6D] font-['Inter']">
                                <?= htmlspecialchars($product['brand_name']) ?>
                            </span>
                            <span
                                class="inline-flex items-center rounded-full border border-[#E0E2E7] px-2.5 py-0.5 text-[11px] font-semibold text-[#5A5F6D] font-['Inter']">
                                <?= htmlspecialchars($product['gender']) ?>
                            </span>
                            <span
                                class="inline-flex items-center rounded-full border border-[#E0E2E7] px-2.5 py-0.5 text-[11px] font-semibold text-[#5A5F6D] font-['Inter']">
                                <?= htmlspecialchars($product['movement_type']) ?>
                            </span>
                        </div>
                    </div>

                    <!-- Description from DB -->
                    <div class="px-5 pb-3">
                        <p class="font-['Inter'] text-[13px] text-[#5A5F6D] leading-relaxed line-clamp-2">
                            <?= htmlspecialchars($product['description'] ?? 'No description available.') ?>
                        </p>
                    </div>

                    <!-- Footer -->
                    <div class="flex items-center justify-between gap-3 p-5 pt-2 max-sm:flex-col max-sm:items-stretch">
                        <div class="flex flex-col">
                            <span
                                class="text-[11px] font-medium uppercase text-[#5A5F6D] font-['Inter'] tracking-wider">Price</span>
                            <span class="text-[18px] font-bold text-[#1A1A2E] font-['Inter']">NPR
                                <?= number_format($product['price'], 0) ?></span>
                        </div>
                        <?php if ($product['stock_quantity'] > 0): ?>
                        <button type="button" onclick="addToCart(this, <?= $product['id'] ?>)"
                            data-strap-adjustable="<?= $product['strap_adjustable'] ? '1' : '0' ?>"
                            data-strap-sizes="<?= htmlspecialchars($product['strap_size_options'] ?? '') ?>"
                            class="add-to-cart-btn inline-flex items-center justify-center gap-2 h-10 px-5 bg-[#1A1A2E] hover:bg-[#C9A84C] text-white font-['Inter'] font-semibold text-[13px] rounded-md transition-all duration-200 shadow-md hover:shadow-lg whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Add to cart
                        </button>
                        <?php else: ?>
                        <button type="button" disabled
                            class="inline-flex items-center justify-center gap-2 h-10 px-5 bg-[#E0E2E7] text-[#5A5F6D] font-['Inter'] font-semibold text-[13px] rounded-md cursor-not-allowed whitespace-nowrap">
                            Sold Out
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-16 text-[#5A5F6D]">No products available yet.</div>
        <?php endif; ?>
    </div>

    <!-- Toast Notification Container -->
    <div id="toastContainer" class="fixed top-24 right-6 z-[9999] flex flex-col gap-2 pointer-events-none"></div>
</section>


<!-- Partnership BRAND -->
<section class="py-[60px] bg-white relative overflow-hidden">
    <!-- Radial gradient background effect -->
    <div aria-hidden="true"
        class="pointer-events-none absolute left-1/2 -top-1/2 -translate-x-1/2 h-[120vmin] w-[120vmin] rounded-b-full -z-0"
        style="background: radial-gradient(ellipse at center, rgba(201,168,76,0.08), transparent 50%); filter: blur(30px);">
    </div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-10">
            <h2 class="font-['Playfair_Display'] font-bold text-[28px] md:text-[32px] text-[#1A1A2E] mt-1">
                <span class="font-semibold">Partnership brands</span>
            </h2>
            <div class="mx-auto my-5 h-px max-w-sm"
                style="background: linear-gradient(to right, transparent, #E0E2E7, transparent);"></div>
        </div>

        <?php if (!empty($brands)): ?>
        <!-- Infinite scrolling brand logos -->
        <div class="overflow-hidden py-4 brand-slider" style="mask-image: linear-gradient(to right, transparent, black, transparent); -webkit-mask-image: linear-gradient(to right, transparent, black, transparent);">
            <div class="brand-slider-track flex items-center gap-[42px] w-max" id="brandSliderTrack">
                <?php 
                // Duplicate the brands array for seamless infinite loop
                $brand_loop = array_merge($brands, $brands);
                foreach ($brand_loop as $brand): 
                ?>
                <a href="pages/shop.php?brand=<?= urlencode($brand['slug']) ?>"
                    class="group flex flex-col items-center justify-center gap-2 flex-shrink-0 px-4 cursor-pointer">
                    <?php if ($brand['logo']): ?>
                    <img src="assets/uploads/brands/<?= htmlspecialchars(basename($brand['logo'])) ?>"
                        alt="<?= htmlspecialchars($brand['name']) ?>"
                        class="pointer-events-none h-8 md:h-10 w-auto select-none object-contain grayscale group-hover:grayscale-0 opacity-60 group-hover:opacity-100 transition-all duration-300"
                        loading="eager">
                    <?php else: ?>
                    <div
                        class="w-10 h-10 rounded-full bg-[#1B2A4A] flex items-center justify-center grayscale group-hover:grayscale-0 transition-all duration-300">
                        <span
                            class="font-['Inter'] font-bold text-[14px] text-white"><?= strtoupper(substr($brand['name'], 0, 1)) ?></span>
                    </div>
                    <?php endif; ?>
                    <span
                        class="font-['Inter'] font-medium text-[11px] text-[#5A5F6D] group-hover:text-[#C9A84C] transition-colors duration-200 whitespace-nowrap"><?= htmlspecialchars($brand['name']) ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="mx-auto mt-5 h-px max-w-sm"
            style="background: linear-gradient(to right, transparent, #E0E2E7, transparent);"></div>

        <?php else: ?>
        <div class="text-center py-12 text-[#5A5F6D]">No brands available yet.</div>
        <?php endif; ?>
    </div>
</section>

<!-- SHOP BY CATEGORY -->
<section class="py-[60px] bg-[#F7F8FA]">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mx-auto mb-10 max-w-2xl text-center">
            <span class="font-['Inter'] font-semibold text-[11px] uppercase tracking-[1.8px] text-[#C9A84C]">Browse
                By</span>
            <h2 class="font-['Playfair_Display'] font-bold text-[28px] md:text-[34px] text-[#1A1A2E] mt-2">Shop By
                Category</h2>
            <p class="mt-3 text-[15px] leading-7 text-[#5A5F6D]">
                Discover refined picks across every style, from versatile essentials to standout statement pieces.
            </p>
        </div>

        <div class="grid grid-cols-2 gap-4 sm:gap-5 lg:grid-cols-4">
            <?php foreach ($categories as $category): ?>
            <a href="<?= htmlspecialchars($category['href'], ENT_QUOTES, 'UTF-8') ?>"
                class="group relative overflow-hidden rounded-[20px] border border-white/80 bg-white shadow-[0_20px_45px_rgba(15,23,42,0.10)] transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_28px_70px_rgba(15,23,42,0.18)] active:scale-[0.97]">
                <div class="relative aspect-[4/5] overflow-hidden">
                    <img src="<?= htmlspecialchars($category['image'], ENT_QUOTES, 'UTF-8') ?>"
                        alt="<?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?> watches"
                        class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                        loading="lazy"
                        decoding="async">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#07111f]/95 via-[#07111f]/30 to-transparent"></div>
                    <div class="absolute inset-x-0 bottom-0 p-5 md:p-6">
                        <span class="inline-flex rounded-full border border-white/20 bg-white/10 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.28em] text-[#F6E7B6] backdrop-blur-sm">
                            Curated picks
                        </span>
                        <h3 class="mt-3 font-['Playfair_Display'] text-[22px] md:text-[24px] font-bold tracking-tight text-white">
                            <?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?>
                        </h3>
                        <p class="mt-1 text-sm leading-6 text-white/80">
                            <?= htmlspecialchars($category['description'], ENT_QUOTES, 'UTF-8') ?>
                        </p>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- MOVEMENT TYPE QUICK FILTER -->
<section class="py-[60px] bg-[#F7F8FA]">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <span class="font-['Inter'] font-semibold text-[11px] uppercase tracking-[1.5px] text-[#C9A84C]">Filter
                By</span>
            <h2 class="font-['Playfair_Display'] font-bold text-[28px] md:text-[32px] text-[#1A1A2E] mt-1">Movement
                Types</h2>
        </div>
        <div class="flex flex-wrap justify-center gap-3">
            <?php
            $movements = ['Automatic', 'Quartz', 'Mechanical', 'Solar', 'Kinetic', 'Digital', 'Smartwatch'];
            foreach ($movements as $mv):
            ?>
            <a href="pages/shop.php?movement=<?= urlencode($mv) ?>"
                class="inline-flex items-center gap-2 px-5 py-3 bg-white border border-[#E0E2E7] hover:border-[#C9A84C] hover:bg-[#FFF8E7] rounded-full font-['Inter'] font-medium text-[14px] text-[#1A1A2E] hover:text-[#C9A84C] transition-all duration-200 hover:shadow-md">
                <?= $mv ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- WHY CHOOSE US -->
<section class="py-[60px] bg-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <span class="font-['Inter'] font-semibold text-[11px] uppercase tracking-[1.5px] text-[#C9A84C]">Why
                ChronoNest</span>
            <h2 class="font-['Playfair_Display'] font-bold text-[28px] md:text-[32px] text-[#1A1A2E] mt-1">Built for
                Watch Lovers</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div
                class="p-8 bg-[#F7F8FA] rounded-xl border border-[#E0E2E7] text-center hover:shadow-[0_8px_28px_rgba(0,0,0,0.08)] transition-shadow duration-200">
                <div
                    class="w-14 h-14 rounded-xl bg-[#FFF8E7] border border-[#C9A84C]/20 flex items-center justify-center mx-auto mb-5">
                    <svg class="w-7 h-7 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <h3 class="font-['Playfair_Display'] font-semibold text-[20px] text-[#1A1A2E] mb-3">Smart Filtering</h3>
                <p class="font-['Inter'] text-[14px] leading-[1.6] text-[#5A5F6D]">Filter by brand, movement, material,
                    case size, dial color and more — find exactly what you're looking for in seconds.</p>
            </div>
            <div
                class="p-8 bg-[#F7F8FA] rounded-xl border border-[#E0E2E7] text-center hover:shadow-[0_8px_28px_rgba(0,0,0,0.08)] transition-shadow duration-200">
                <div
                    class="w-14 h-14 rounded-xl bg-[#FFF8E7] border border-[#C9A84C]/20 flex items-center justify-center mx-auto mb-5">
                    <svg class="w-7 h-7 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="font-['Playfair_Display'] font-semibold text-[20px] text-[#1A1A2E] mb-3">Multi-Angle Images
                </h3>
                <p class="font-['Inter'] text-[14px] leading-[1.6] text-[#5A5F6D]">View watches from multiple angles
                    with high-resolution zoom. Know exactly what you're buying before it arrives.</p>
            </div>
            <div
                class="p-8 bg-[#F7F8FA] rounded-xl border border-[#E0E2E7] text-center hover:shadow-[0_8px_28px_rgba(0,0,0,0.08)] transition-shadow duration-200">
                <div
                    class="w-14 h-14 rounded-xl bg-[#FFF8E7] border border-[#C9A84C]/20 flex items-center justify-center mx-auto mb-5">
                    <svg class="w-7 h-7 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h3 class="font-['Playfair_Display'] font-semibold text-[20px] text-[#1A1A2E] mb-3">Nationwide Delivery
                </h3>
                <p class="font-['Inter'] text-[14px] leading-[1.6] text-[#5A5F6D]">Shop from anywhere in Nepal —
                    Kathmandu to Humla. We deliver to your doorstep with care and reliability.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA STRIP -->
<section class="bg-[#FFF8E7] border-y border-[#C9A84C]/20 py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <div>
                <h2 class="font-['Playfair_Display'] font-bold text-[24px] text-[#1A1A2E]">Ready to find your perfect
                    watch?</h2>
                <p class="font-['Inter'] text-[15px] text-[#5A5F6D] mt-1">Join thousands of watch enthusiasts across
                    Nepal.</p>
            </div>
            <div class="flex items-center gap-4">
                <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="pages/register.php"
                    class="inline-flex items-center gap-2 h-[44px] px-6 bg-[#C9A84C] hover:bg-[#B8953F] text-white font-['Inter'] font-semibold text-[15px] rounded-lg transition-all duration-200">Create
                    Account</a>
                <a href="pages/shop.php"
                    class="inline-flex items-center gap-2 h-[44px] px-6 bg-transparent hover:bg-[#C9A84C]/10 text-[#1B2A4A] font-['Inter'] font-semibold text-[15px] rounded-lg border border-[#1B2A4A] transition-all duration-200">Browse
                    Watches</a>
                <?php else: ?>
                <a href="pages/shop.php"
                    class="inline-flex items-center gap-2 h-[44px] px-6 bg-[#C9A84C] hover:bg-[#B8953F] text-white font-['Inter'] font-semibold text-[15px] rounded-lg transition-all duration-200">Browse
                    Watches</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>

<style>
@keyframes brandScroll {
    0% {
        transform: translateX(0);
    }

    100% {
        transform: translateX(-50%);
    }
}

.brand-slider-track {
    animation: brandScroll 20s linear infinite;
    will-change: transform;
}

.brand-slider-track:hover {
    animation-play-state: paused;
}

.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

