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

$stmt = mysqli_prepare($conn, "SELECT p.id, p.name, p.slug, p.price, p.gender, p.movement_type, p.stock_quantity, p.case_diameter_mm, p.water_resistance, b.name as brand_name,
    (SELECT image_url FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image
    FROM products p JOIN brands b ON p.brand_id = b.id
    WHERE p.is_active = 1 AND p.is_expensive = 1 ORDER BY $order_sql");
mysqli_stmt_execute($stmt);
$products = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

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
            <a href="product-detail.php?slug=<?= urlencode($product['slug']) ?>" class="group bg-white border border-[#E0E2E7] rounded-xl overflow-hidden hover:shadow-[0_8px_28px_rgba(0,0,0,0.14)] hover:-translate-y-1 transition-all duration-200 block relative">
                <div class="absolute top-3 right-3 z-10">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-[#FFF8E7] border border-[#C9A84C]/30 rounded-full font-['Inter'] font-semibold text-[10px] text-[#C9A84C]">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                        LUXURY
                    </span>
                </div>
                <div class="relative bg-[#F7F8FA] aspect-square overflow-hidden">
                    <?php if ($product['main_image']): ?>
                    <img src="../assets/uploads/products/<?= htmlspecialchars($product['main_image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                    <?php else: ?>
                    <div class="w-full h-full flex items-center justify-center"><svg class="w-16 h-16 text-[#E0E2E7]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="1"/><path stroke-linecap="round" stroke-width="1" d="M12 6v6l4 2"/></svg></div>
                    <?php endif; ?>
                    <div class="absolute top-3 left-3">
                        <?php if ($product['stock_quantity'] == 0): ?>
                        <span class="inline-block px-2.5 py-1 rounded bg-[#FDEAEA] text-[#D64545] font-['Inter'] font-semibold text-[11px]">Out of Stock</span>
                        <?php elseif ($product['stock_quantity'] <= 5): ?>
                        <span class="inline-block px-2.5 py-1 rounded bg-[#FFF3E0] text-[#E65100] font-['Inter'] font-semibold text-[11px]">Low Stock</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="p-4">
                    <div class="font-['Inter'] text-[12px] text-[#C9A84C] font-semibold mb-1"><?= htmlspecialchars($product['brand_name']) ?></div>
                    <h3 class="font-['Inter'] font-semibold text-[15px] text-[#1A1A2E] leading-[1.4] mb-2 line-clamp-2"><?= htmlspecialchars($product['name']) ?></h3>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="font-['Inter'] text-[11px] text-[#5A5F6D] bg-[#F7F8FA] px-2 py-0.5 rounded"><?= htmlspecialchars($product['movement_type']) ?></span>
                        <?php if ($product['water_resistance']): ?>
                        <span class="font-['Inter'] text-[11px] text-[#5A5F6D] bg-[#F7F8FA] px-2 py-0.5 rounded"><?= htmlspecialchars($product['water_resistance']) ?></span>
                        <?php endif; ?>
                    </div>
                    <span class="font-['Inter'] font-bold text-[20px] text-[#1A1A2E]">NPR <?= number_format($product['price'], 0) ?></span>
                </div>
            </a>
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

<?php require_once '../includes/footer.php'; ?>