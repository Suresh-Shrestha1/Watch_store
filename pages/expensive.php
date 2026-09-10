<?php
session_start();
require_once '../config/db.php';

$page_title = "Luxury Collection – ChronoNest";

$sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'price_desc';
$order_sql = match($sort) {
    'price_asc' => 'p.price ASC',
    'newest' => 'p.created_at DESC',
    'name_asc' => 'p.name ASC',
    default => 'p.price DESC'
};

$stmt = $conn->prepare("SELECT p.id, p.name, p.slug, p.price, p.gender, p.movement_type, p.stock_quantity, p.case_diameter_mm, p.water_resistance, p.description, p.strap_adjustable, p.strap_size_options, b.name as brand_name,
    (SELECT image_url FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image
    FROM products p JOIN brands b ON p.brand_id = b.id
    WHERE p.is_active = 1 AND p.is_expensive = 1 ORDER BY $order_sql");
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Load wishlist product IDs for the logged in user (needed for rendering heart state)
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

require_once '../includes/header.php';
?>

<!-- HERO BANNER -->
<section class="bg-[#1B2A4A] py-16 relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-[#0F1B33] via-[#1B2A4A] to-[#2C4066]"></div>
    <div class="absolute top-0 right-0 w-80 h-80 bg-[#C9A84C]/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
    <div class="absolute bottom-0 left-0 w-60 h-60 bg-[#C9A84C]/5 rounded-full translate-y-1/3 -translate-x-1/4"></div>
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <span class="font-['Inter'] font-semibold text-[11px] uppercase tracking-[1.5px] text-[#C9A84C]">Exclusive Selection</span>
        <h1 class="font-['Playfair_Display'] font-bold text-[36px] md:text-[48px] text-white mt-2">Luxury <span class="text-[#C9A84C]">Collection</span></h1>
        <p class="font-['Inter'] text-[15px] text-[#B0B8C9] mt-3 max-w-lg mx-auto">Curated timepieces from the world's most prestigious watchmakers — crafted for those who appreciate the art of fine horology.</p>
        <p class="font-['Inter'] text-[14px] text-[#B0B8C9] mt-4"><?= count($products) ?> luxury timepiece<?= count($products) !== 1 ? 's' : '' ?></p>
    </div>
</section>

<div class="bg-white border-b border-[#E0E2E7]">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between">
        <nav class="flex items-center gap-2 font-['Inter'] text-[13px] text-[#5A5F6D]">
            <a href="../index.php" class="hover:text-[#C9A84C] transition-colors">Home</a>
            <svg class="w-3 h-3 text-[#8A8F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-[#1A1A2E] font-medium">Luxury Collection</span>
        </nav>
        <div class="flex items-center gap-2">
            <label class="font-['Inter'] text-[13px] text-[#5A5F6D]">Sort:</label>
            <select onchange="window.location.href='expensive.php?sort='+this.value" class="h-[36px] px-3 bg-white border border-[#E0E2E7] rounded-lg font-['Inter'] text-[13px] text-[#1A1A2E] focus:outline-none focus:border-[#C9A84C] appearance-none cursor-pointer">
                <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price: High → Low</option>
                <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price: Low → High</option>
                <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest First</option>
                <option value="name_asc" <?= $sort === 'name_asc' ? 'selected' : '' ?>>Name: A → Z</option>
            </select>
        </div>
    </div>
</div>

<section class="py-10 bg-[#F7F8FA] min-h-[50vh]">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <?php if (!empty($products)): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php foreach ($products as $product): ?>
            <div class="rounded-xl bg-white shadow-lg overflow-hidden group hover:shadow-[0_12px_32px_rgba(0,0,0,0.18)] hover:-translate-y-1 transition-all duration-300 border border-[#E0E2E7]">
                <!-- Image Section -->
                <div class="relative flex aspect-square items-center justify-center overflow-hidden bg-[#F7F8FA]">
                    <?php if ($product['main_image']): ?>
                    <a href="product-detail.php?slug=<?= urlencode($product['slug']) ?>" class="block w-full h-full flex items-center justify-center">
                        <img src="../assets/uploads/products/<?= htmlspecialchars(basename($product['main_image'])) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                    </a>
                    <?php else: ?>
                    <a href="product-detail.php?slug=<?= urlencode($product['slug']) ?>" class="block">
                        <svg class="w-16 h-16 text-[#E0E2E7]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="1" /><path stroke-linecap="round" stroke-width="1" d="M12 6v6l4 2" /></svg>
                    </a>
                    <?php endif; ?>

                    <!-- Wishlist Button -->
                    <?php $is_in_wishlist = in_array((int)$product['id'], $wishlist_product_ids, true); ?>
                    <button type="button" onclick="toggleWishlist(this, <?= $product['id'] ?>)" class="wishlist-btn absolute top-4 right-4 w-10 h-10 flex items-center justify-center bg-white hover:bg-red-50 shadow-md rounded-full transition-all duration-200 z-10" aria-label="Add to wishlist" data-liked="<?= $is_in_wishlist ? '1' : '0' ?>">
                        <svg class="heart-icon w-4 h-4 transition-all duration-200 <?= $is_in_wishlist ? 'fill-red-500 stroke-red-500' : 'stroke-[#5A5F6D] fill-none' ?>" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </button>

                    <!-- Luxury Badge -->
                    <div class="absolute top-4 left-4 z-10 flex flex-col gap-1">
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full font-['Inter'] font-semibold text-[11px] uppercase tracking-wider text-white shadow drop-shadow" style="background: linear-gradient(135deg, #C9A84C, #B8953F);">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.4 7.4H22l-6.2 4.5 2.4 7.4L12 17l-6.2 4.3 2.4-7.4L2 9.4h7.6z"/></svg>
                            Luxury
                        </span>
                        <?php if ($product['stock_quantity'] == 0): ?>
                        <span class="inline-block px-2.5 py-0.5 rounded bg-[#FDEAEA] text-[#D64545] font-['Inter'] font-semibold text-[10px]">Sold Out</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Card Content -->
                <div class="bg-white">
                    <div class="flex flex-col space-y-1.5 p-5 pb-3">
                        <a href="product-detail.php?slug=<?= urlencode($product['slug']) ?>" class="block">
                            <h3 class="font-['Playfair_Display'] font-semibold text-[18px] text-[#1A1A2E] leading-tight tracking-tight line-clamp-1 hover:text-[#C9A84C] transition-colors duration-200">
                                <?= htmlspecialchars($product['name']) ?>
                            </h3>
                        </a>
                        <div class="flex items-center gap-2 flex-wrap pt-1">
                            <span class="inline-flex items-center rounded-full border border-[#E0E2E7] px-2.5 py-0.5 text-[11px] font-semibold text-[#5A5F6D] font-['Inter']"><?= htmlspecialchars($product['brand_name']) ?></span>
                            <span class="inline-flex items-center rounded-full border border-[#E0E2E7] px-2.5 py-0.5 text-[11px] font-semibold text-[#5A5F6D] font-['Inter']"><?= htmlspecialchars($product['movement_type']) ?></span>
                            <?php if ($product['water_resistance']): ?>
                            <span class="inline-flex items-center rounded-full border border-[#E0E2E7] px-2.5 py-0.5 text-[11px] font-semibold text-[#5A5F6D] font-['Inter']"><?= htmlspecialchars($product['water_resistance']) ?></span>
                            <?php endif; ?>
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
                            <span class="text-[11px] font-medium uppercase text-[#5A5F6D] font-['Inter'] tracking-wider">Price</span>
                            <span class="text-[18px] font-bold text-[#1A1A2E] font-['Inter']">NPR <?= number_format($product['price'], 0) ?></span>
                        </div>
                        <?php if ($product['stock_quantity'] > 0): ?>
                        <button type="button" onclick="addToCart(this, <?= $product['id'] ?>)" data-strap-adjustable="<?= $product['strap_adjustable'] ? '1' : '0' ?>" data-strap-sizes="<?= htmlspecialchars($product['strap_size_options'] ?? '') ?>" class="add-to-cart-btn inline-flex items-center justify-center gap-2 h-10 px-5 bg-[#1A1A2E] hover:bg-[#C9A84C] text-white font-['Inter'] font-semibold text-[13px] rounded-md transition-all duration-200 shadow-md hover:shadow-lg whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Add to cart
                        </button>
                        <?php else: ?>
                        <button type="button" disabled class="inline-flex items-center justify-center gap-2 h-10 px-5 bg-[#E0E2E7] text-[#5A5F6D] font-['Inter'] font-semibold text-[13px] rounded-md cursor-not-allowed whitespace-nowrap">Sold Out</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <svg class="w-20 h-20 text-[#E0E2E7] mb-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
            <h3 class="font-['Playfair_Display'] font-semibold text-[22px] text-[#1A1A2E] mb-2">No luxury watches yet</h3>
            <p class="font-['Inter'] text-[14px] text-[#5A5F6D] mb-6">Check back soon for our premium collection.</p>
            <a href="shop.php" class="inline-flex items-center gap-2 h-[40px] px-6 bg-[#C9A84C] hover:bg-[#B8953F] text-white font-['Inter'] font-semibold text-[14px] rounded-lg transition-all duration-200">Browse All Watches</a>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Toast Notification Container -->
<div id="toastContainer" class="fixed top-24 right-6 z-[9999] flex flex-col gap-2 pointer-events-none"></div>

<?php require_once '../includes/footer.php'; ?>