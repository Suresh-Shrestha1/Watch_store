<?php
session_start();
require_once '../config/db.php';

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$page_title = "Search" . ($query ? ": $query" : "") . " – ChronoNest";
$products = [];

if ($query !== '') {
    $search_term = "%$query%";
    $stmt = $conn->prepare("SELECT p.id, p.name, p.slug, p.price, p.gender, p.movement_type, p.stock_quantity, b.name as brand_name,
        (SELECT image_url FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image
        FROM products p JOIN brands b ON p.brand_id = b.id
        WHERE p.is_active = 1 AND (p.name LIKE ? OR p.model_number LIKE ? OR b.name LIKE ?)
        ORDER BY p.created_at DESC LIMIT 40");
    $stmt->bind_param("sss", $search_term, $search_term, $search_term);
    $stmt->execute();
    $products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

require_once '../includes/header.php';
?>

<!-- HEADER -->
<section class="bg-[#1B2A4A] py-10">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="font-['Inter'] font-semibold text-[11px] uppercase tracking-[1.5px] text-[#C9A84C]">Search Results</span>
        <h1 class="font-['Playfair_Display'] font-bold text-[32px] text-white mt-1">
            <?php if ($query): ?>
                "<?= htmlspecialchars($query) ?>"
            <?php else: ?>
                Search Watches
            <?php endif; ?>
        </h1>
        <p class="font-['Inter'] text-[14px] text-[#B0B8C9] mt-2"><?= count($products) ?> result<?= count($products) !== 1 ? 's' : '' ?> found</p>
    </div>
</section>

<div class="bg-white border-b border-[#E0E2E7]">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-3">
        <nav class="flex items-center gap-2 font-['Inter'] text-[13px] text-[#5A5F6D]">
            <a href="../index.php" class="hover:text-[#C9A84C] transition-colors">Home</a>
            <svg class="w-3 h-3 text-[#8A8F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-[#1A1A2E] font-medium">Search</span>
        </nav>
    </div>
</div>

<!-- SEARCH BAR -->
<div class="bg-[#F7F8FA] border-b border-[#E0E2E7]">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <form method="GET" action="search.php" class="max-w-2xl mx-auto relative">
            <input type="text" name="q" value="<?= htmlspecialchars($query) ?>" placeholder="Search by name, model number, or brand..." autofocus class="w-full h-[48px] px-5 pr-14 bg-white border border-[#E0E2E7] rounded-xl font-['Inter'] text-[15px] text-[#1A1A2E] placeholder-[#8A8F99] focus:outline-none focus:border-2 focus:border-[#C9A84C] focus:shadow-[0_0_0_3px_rgba(201,168,76,0.15)] transition-all duration-150">
            <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 w-10 h-10 rounded-lg bg-[#C9A84C] hover:bg-[#B8953F] text-white flex items-center justify-center transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </button>
        </form>
    </div>
</div>

<section class="py-8 bg-[#F7F8FA] min-h-[50vh]">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <?php if (!$query): ?>
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <svg class="w-16 h-16 text-[#E0E2E7] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <h3 class="font-['Playfair_Display'] font-semibold text-[20px] text-[#1A1A2E] mb-2">Start searching</h3>
            <p class="font-['Inter'] text-[14px] text-[#5A5F6D]">Type a watch name, model number, or brand name above.</p>
        </div>
        <?php elseif (empty($products)): ?>
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <svg class="w-16 h-16 text-[#E0E2E7] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <h3 class="font-['Playfair_Display'] font-semibold text-[20px] text-[#1A1A2E] mb-2">No results found</h3>
            <p class="font-['Inter'] text-[14px] text-[#5A5F6D] mb-6">We couldn't find any watches matching "<?= htmlspecialchars($query) ?>"</p>
            <a href="shop.php" class="inline-flex items-center gap-2 h-[40px] px-6 bg-[#C9A84C] hover:bg-[#B8953F] text-white font-['Inter'] font-semibold text-[14px] rounded-lg transition-all duration-200">Browse All Watches</a>
        </div>
        <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php foreach ($products as $product): ?>
            <a href="product-detail.php?slug=<?= urlencode($product['slug']) ?>" class="group bg-white border border-[#E0E2E7] rounded-xl overflow-hidden hover:shadow-[0_8px_28px_rgba(0,0,0,0.14)] hover:-translate-y-1 transition-all duration-200 block">
                <div class="relative bg-[#F7F8FA] aspect-square overflow-hidden">
                    <?php if ($product['main_image']): ?>
                    <img src="../assets/uploads/products/<?= htmlspecialchars(basename($product['main_image'])) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
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
                    <h3 class="font-['Inter'] font-semibold text-[14px] text-[#1A1A2E] leading-[1.4] mb-2 line-clamp-2"><?= htmlspecialchars($product['name']) ?></h3>
                    <span class="font-['Inter'] font-bold text-[18px] text-[#1A1A2E]">NPR <?= number_format($product['price'], 0) ?></span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>            