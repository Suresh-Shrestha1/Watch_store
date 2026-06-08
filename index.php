<?php
session_start();
require_once 'config/db.php';

// Fetch featured brands (active only, limit 8)
$brands_sql = "SELECT id, name, slug, logo FROM brands WHERE is_active = 1 ORDER BY name ASC LIMIT 8";
$brands_result = mysqli_query($conn, $brands_sql);
$brands = [];
while ($row = mysqli_fetch_assoc($brands_result)) {
    $brands[] = $row;
}

// Fetch new arrivals (latest 8 active products with stock)
$new_arrivals_sql = "SELECT p.id, p.name, p.slug, p.price, p.gender, p.movement_type, p.stock_quantity, b.name as brand_name,
    (SELECT image_url FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image
    FROM products p JOIN brands b ON p.brand_id = b.id
    WHERE p.is_active = 1 ORDER BY p.created_at DESC LIMIT 8";
$new_arrivals_result = mysqli_query($conn, $new_arrivals_sql);
$new_arrivals = [];
while ($row = mysqli_fetch_assoc($new_arrivals_result)) {
    $new_arrivals[] = $row;
}

// Fetch expensive/luxury watches (is_expensive = 1, limit 4)
$luxury_sql = "SELECT p.id, p.name, p.slug, p.price, p.gender, p.movement_type, p.stock_quantity, b.name as brand_name,
    (SELECT image_url FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image
    FROM products p JOIN brands b ON p.brand_id = b.id
    WHERE p.is_active = 1 AND p.is_expensive = 1 ORDER BY p.price DESC LIMIT 4";
$luxury_result = mysqli_query($conn, $luxury_sql);
$luxury_watches = [];
while ($row = mysqli_fetch_assoc($luxury_result)) {
    $luxury_watches[] = $row;
}

// Fetch stats
$total_brands = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM brands WHERE is_active = 1"))['cnt'];
$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM products WHERE is_active = 1"))['cnt'];
$total_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM orders"))['cnt'];

$page_title = "ChronoNest – Premium Multi-Brand Watch Store Nepal";
require_once 'includes/header.php';
?>

<!-- HERO SECTION -->
<section class="relative bg-[#1B2A4A] min-h-[92vh] flex items-center overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-[#0F1B33] via-[#1B2A4A] to-[#2C4066] opacity-95"></div>
    <div class="absolute inset-0 opacity-5 bg-[url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23C9A84C\' fill-opacity=\'1\'%3E%3Ccircle cx=\'30\' cy=\'30\' r=\'2\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10 py-20">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="text-center lg:text-left">
                <span class="inline-block font-['Inter'] font-semibold text-[11px] uppercase tracking-[1.5px] text-[#C9A84C] mb-4">Nepal's Premier Watch Destination</span>
                <h1 class="font-['Playfair_Display'] font-bold text-[36px] md:text-[52px] leading-[1.15] text-white mb-6">Discover Your <span class="text-[#C9A84C]">Perfect</span> Timepiece</h1>
                <p class="font-['Inter'] text-[16px] leading-[1.7] text-[#B0B8C9] mb-8 max-w-lg mx-auto lg:mx-0">From iconic Swiss luxury to modern smartwatches — explore hundreds of authentic watches from the world's finest brands, delivered anywhere in Nepal.</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="pages/shop.php" class="inline-flex items-center justify-center gap-2 h-[48px] px-8 bg-[#C9A84C] hover:bg-[#B8953F] text-white font-['Inter'] font-semibold text-[15px] rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        Shop Now
                    </a>
                    <a href="pages/expensive.php" class="inline-flex items-center justify-center gap-2 h-[48px] px-8 bg-transparent hover:bg-white/10 text-white font-['Inter'] font-semibold text-[15px] rounded-lg border border-white/30 transition-all duration-200">
                        <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                        Luxury Collection
                    </a>
                </div>
                <div class="flex items-center gap-8 mt-10 justify-center lg:justify-start">
                    <div class="text-center">
                        <div class="font-['Inter'] font-bold text-[28px] text-white"><?= $total_products ?>+</div>
                        <div class="font-['Inter'] text-[12px] text-[#B0B8C9] mt-0.5">Watches</div>
                    </div>
                    <div class="w-px h-10 bg-white/20"></div>
                    <div class="text-center">
                        <div class="font-['Inter'] font-bold text-[28px] text-white"><?= $total_brands ?>+</div>
                        <div class="font-['Inter'] text-[12px] text-[#B0B8C9] mt-0.5">Brands</div>
                    </div>
                    <div class="w-px h-10 bg-white/20"></div>
                    <div class="text-center">
                        <div class="font-['Inter'] font-bold text-[28px] text-white"><?= $total_orders ?>+</div>
                        <div class="font-['Inter'] text-[12px] text-[#B0B8C9] mt-0.5">Orders</div>
                    </div>
                </div>
            </div>
            <div class="hidden lg:flex justify-center items-center relative">
                <div class="w-[420px] h-[420px] rounded-full bg-[#C9A84C]/10 border border-[#C9A84C]/20 flex items-center justify-center relative">
                    <div class="w-[320px] h-[320px] rounded-full bg-[#C9A84C]/10 border border-[#C9A84C]/20 flex items-center justify-center">
                        <div class="w-[220px] h-[220px] rounded-full bg-[#C9A84C]/15 border-2 border-[#C9A84C]/40 flex items-center justify-center">
                            <svg class="w-24 h-24 text-[#C9A84C] opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="1.5"/><path stroke-linecap="round" stroke-width="1.5" d="M12 6v6l4 2"/><path stroke-linecap="round" stroke-width="1" d="M8.5 3.5l1 2M15.5 3.5l-1 2M8.5 20.5l1-2M15.5 20.5l-1-2M3.5 8.5l2 1M3.5 15.5l2-1M20.5 8.5l-2 1M20.5 15.5l-2-1"/></svg>
                        </div>
                    </div>
                    <div class="absolute top-8 right-8 bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl px-4 py-3">
                        <div class="font-['Inter'] text-[11px] text-[#B0B8C9]">Free Delivery</div>
                        <div class="font-['Inter'] font-semibold text-[13px] text-white">Nationwide</div>
                    </div>
                    <div class="absolute bottom-12 left-4 bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl px-4 py-3">
                        <div class="font-['Inter'] text-[11px] text-[#B0B8C9]">Warranty</div>
                        <div class="font-['Inter'] font-semibold text-[13px] text-white">Up to 2 Years</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="absolute bottom-0 left-0 right-0 h-16 bg-gradient-to-t from-[#F7F8FA] to-transparent"></div>
</section>

<!-- FEATURES STRIP -->
<section class="bg-[#F7F8FA] border-b border-[#E0E2E7]">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 lg:grid-cols-4 divide-x divide-[#E0E2E7]">
            <div class="flex items-center gap-4 py-6 px-4 lg:px-8">
                <div class="w-10 h-10 rounded-lg bg-[#FFF8E7] flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 12a2 2 0 002 2h8a2 2 0 002-2L19 8"/></svg>
                </div>
                <div>
                    <div class="font-['Inter'] font-semibold text-[14px] text-[#1A1A2E]">Free Delivery</div>
                    <div class="font-['Inter'] text-[12px] text-[#5A5F6D]">Across Nepal</div>
                </div>
            </div>
            <div class="flex items-center gap-4 py-6 px-4 lg:px-8">
                <div class="w-10 h-10 rounded-lg bg-[#FFF8E7] flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <div>
                    <div class="font-['Inter'] font-semibold text-[14px] text-[#1A1A2E]">100% Authentic</div>
                    <div class="font-['Inter'] text-[12px] text-[#5A5F6D]">Genuine products</div>
                </div>
            </div>
            <div class="flex items-center gap-4 py-6 px-4 lg:px-8">
                <div class="w-10 h-10 rounded-lg bg-[#FFF8E7] flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <div>
                    <div class="font-['Inter'] font-semibold text-[14px] text-[#1A1A2E]">Secure Payment</div>
                    <div class="font-['Inter'] text-[12px] text-[#5A5F6D]">COD & eSewa</div>
                </div>
            </div>
            <div class="flex items-center gap-4 py-6 px-4 lg:px-8">
                <div class="w-10 h-10 rounded-lg bg-[#FFF8E7] flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="font-['Inter'] font-semibold text-[14px] text-[#1A1A2E]">Warranty Support</div>
                    <div class="font-['Inter'] text-[12px] text-[#5A5F6D]">Up to 2 years</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SHOP BY BRAND -->
<section class="py-[60px] bg-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <span class="font-['Inter'] font-semibold text-[11px] uppercase tracking-[1.5px] text-[#C9A84C]">Explore By</span>
            <h2 class="font-['Playfair_Display'] font-bold text-[28px] md:text-[32px] text-[#1A1A2E] mt-1">Featured Brands</h2>
            <p class="font-['Inter'] text-[15px] text-[#5A5F6D] mt-2 max-w-lg mx-auto">Shop from the world's most trusted and iconic watch brands — all in one place.</p>
        </div>
        <?php if (!empty($brands)): ?>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-4">
            <?php foreach ($brands as $brand): ?>
            <a href="pages/shop.php?brand=<?= urlencode($brand['slug']) ?>" class="group flex flex-col items-center justify-center gap-3 p-6 bg-[#F7F8FA] hover:bg-white border border-transparent hover:border-[#C9A84C] rounded-xl transition-all duration-200 hover:shadow-[0_8px_28px_rgba(0,0,0,0.14)] cursor-pointer">
                <?php if ($brand['logo']): ?>
                <img src="assets/uploads/brands/<?= htmlspecialchars($brand['logo']) ?>" alt="<?= htmlspecialchars($brand['name']) ?>" class="h-12 w-auto object-contain grayscale group-hover:grayscale-0 transition-all duration-200">
                <?php else: ?>
                <div class="w-12 h-12 rounded-full bg-[#1B2A4A] flex items-center justify-center">
                    <span class="font-['Inter'] font-bold text-[16px] text-white"><?= strtoupper(substr($brand['name'], 0, 1)) ?></span>
                </div>
                <?php endif; ?>
                <span class="font-['Inter'] font-semibold text-[13px] text-[#1A1A2E] group-hover:text-[#C9A84C] transition-colors duration-200"><?= htmlspecialchars($brand['name']) ?></span>
            </a>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-8">
            <a href="pages/shop.php" class="inline-flex items-center gap-2 font-['Inter'] font-semibold text-[14px] text-[#1B2A4A] hover:text-[#C9A84C] transition-colors duration-200">
                View All Brands
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
        <?php else: ?>
        <div class="text-center py-12 text-[#5A5F6D]">No brands available yet.</div>
        <?php endif; ?>
    </div>
</section>

<!-- SHOP BY CATEGORY -->
<section class="py-[60px] bg-[#F7F8FA]">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <span class="font-['Inter'] font-semibold text-[11px] uppercase tracking-[1.5px] text-[#C9A84C]">Browse By</span>
            <h2 class="font-['Playfair_Display'] font-bold text-[28px] md:text-[32px] text-[#1A1A2E] mt-1">Shop By Category</h2>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="pages/shop.php?gender=Men" class="relative group overflow-hidden rounded-xl bg-[#1B2A4A] h-[200px] flex flex-col items-center justify-center cursor-pointer hover:shadow-[0_8px_28px_rgba(0,0,0,0.14)] transition-all duration-200">
                <div class="absolute inset-0 bg-gradient-to-t from-[#0F1B33] to-transparent opacity-60"></div>
                <svg class="relative z-10 w-16 h-auto text-[#C9A84C] mb-3" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 115.65 122.88"><path fill-rule="evenodd" clip-rule="evenodd" d="M23.41,21.8C39.39,2,57.82-8.7,71.65,8.87,88.32,9.75,95,36.23,81.71,47.38c0,.22,0,.44-.08.66a7.22,7.22,0,0,1,1.54-.3,5.05,5.05,0,0,1,2.72.51,3.9,3.9,0,0,1,1.9,2.3,7.06,7.06,0,0,1-.09,4l-.06.17-2,5.73a4.35,4.35,0,0,1-1.32,2.16,3.85,3.85,0,0,1-2.82.68l-.58-.06c-.13,6.57-3.34,9.59-7.53,13.53,3.31,11.14,11.76,12.87,20,14.57,11.28,2.31,22.25,3.09,22.25,24.81v5.19a1.59,1.59,0,0,1-1.59,1.59H1.59A1.59,1.59,0,0,1,0,121.29V116.6C0,93.91,11.69,93.71,23.57,92c8.61-1.22,17.35-2.46,20.58-13.55L44,78.33,42.4,77l0,0c-4.11-3.54-7.54-6.51-8.19-13.72H33.8a4.87,4.87,0,0,1-2.42-.64,6.61,6.61,0,0,1-2.67-3.23,14.75,14.75,0,0,1-1.07-4.87v0c0-.51,0-1.5,0-2.44s.07-1.64.13-2.09a1.25,1.25,0,0,1,.11-.39c.86-2.41,2.18-3.19,3.92-3.14l-1.15-.76c-.62-7.78,1.2-21.28-7.24-23.83ZM36.62,92.23a38.83,38.83,0,0,0,21.54,5.71A37.41,37.41,0,0,0,80,90.43a19.45,19.45,0,0,1-9.08-11,17,17,0,0,1-5.41,3.23,17.1,17.1,0,0,1-12.43,1,17.8,17.8,0,0,1-5.55-2.18c-.28-.17-.56-.36-.84-.55-2.15,6.14-5.78,9.41-10.08,11.32Zm46.58-.34-.17.17a40.34,40.34,0,0,1-24.8,9,41.73,41.73,0,0,1-25.36-7.56A72.38,72.38,0,0,1,24,95.18C13.5,96.66,3.18,96.66,3.18,116.6v3.1H112.47v-3.6c0-19.14-9.71-19.67-19.7-21.71a62,62,0,0,1-9.57-2.5ZM70.4,75.16l.19-.18.58-.54c4.07-3.82,7.06-6.63,6.48-13.49a1.6,1.6,0,0,1,.27-1,1.58,1.58,0,0,1,2.19-.45,4.06,4.06,0,0,0,.85.44,2.2,2.2,0,0,0,.68.15,3.15,3.15,0,0,0,.69,0,3.41,3.41,0,0,0,.3-.74l2-5.71a3.91,3.91,0,0,0,.13-2.12.73.73,0,0,0-.37-.47,1.83,1.83,0,0,0-1-.14,5.75,5.75,0,0,0-2.82,1.17,1.55,1.55,0,0,1-1.24.32,1.58,1.58,0,0,1-1.3-1.83c1.5-8.72.81-14.4-1-18.27a15.57,15.57,0,0,0-7-7c-6.25,4.78-10.64,5.33-15,5.87-3.63.45-7.25.9-12,4.21a11.59,11.59,0,0,0-4.58,5.71,14.39,14.39,0,0,0-.2,7.79,1.58,1.58,0,0,1-2.07,1.93l-.23-.08c-.43-.16-.85-.32-1.21-.44-1.88-.66-3.21-1-3.72.21,0,.39-.07,1-.08,1.57,0,.86,0,1.75,0,2.22v0a11.53,11.53,0,0,0,.83,3.77,3.61,3.61,0,0,0,1.31,1.74,2,2,0,0,0,.9.23,4.6,4.6,0,0,0,1.3-.22,1.5,1.5,0,0,1,.47-.08,1.58,1.58,0,0,1,1.62,1.54c.17,7.16,3.34,9.91,7.19,13.23l0,0L46.08,76a21.11,21.11,0,0,0,7.83,4.62,18.66,18.66,0,0,0,6.19.2,19.53,19.53,0,0,0,4.07-1A20.33,20.33,0,0,0,69.53,76l.87-.83ZM67.75,46.74a2.63,2.63,0,1,1-2.62,2.63,2.63,2.63,0,0,1,2.62-2.63ZM55.11,62.43a1.17,1.17,0,0,1-.4-.78,1.11,1.11,0,0,1,.27-.84,1.14,1.14,0,0,1,1.63-.15,1.93,1.93,0,0,0,.57.33,2,2,0,0,0,.62.13,2.14,2.14,0,0,0,.64-.12,2.61,2.61,0,0,0,.58-.33l0,0a1.17,1.17,0,0,1,1.6.16l0,.05a1.15,1.15,0,0,1,.24.83,1.17,1.17,0,0,1-.42.78,4.48,4.48,0,0,1-1.3.74,4,4,0,0,1-1.44.26,4.47,4.47,0,0,1-1.4-.28,4.14,4.14,0,0,1-1.25-.73ZM47.88,46.74a2.63,2.63,0,1,1-2.63,2.63,2.63,2.63,0,0,1,2.63-2.63ZM49.3,66.32h16c1.49,0,1.88.73,1.38,1.82-4.29,9.68-18.43,4.91-18.61-.13a1.5,1.5,0,0,1,1.19-1.69ZM73.74,45.45a1.15,1.15,0,0,1-.24,1.6,1.13,1.13,0,0,1-1.59-.23A5.17,5.17,0,0,0,69,44.71,6.16,6.16,0,0,0,65.6,45a1.14,1.14,0,1,1-.72-2.17,8.28,8.28,0,0,1,4.62-.37,7.5,7.5,0,0,1,4.24,3Zm-27.59-3a8.28,8.28,0,0,1,4.62.37A1.14,1.14,0,1,1,50.05,45a6.06,6.06,0,0,0-3.38-.31,5.17,5.17,0,0,0-2.93,2.11,1.13,1.13,0,0,1-1.59.23,1.15,1.15,0,0,1-.24-1.6,7.43,7.43,0,0,1,4.24-3Zm5.1,27.27-.72-1.88H64.47l-.63,1.88Z"/></svg><span class="relative z-10 font-['Playfair_Display'] font-semibold text-[20px] text-white">Men</span>
                <span class="relative z-10 font-['Inter'] text-[12px] text-[#B0B8C9] mt-1">Classic & Sport</span>
            </a>
            <a href="pages/shop.php?gender=Women" class="relative group overflow-hidden rounded-xl bg-[#2C1B3A] h-[200px] flex flex-col items-center justify-center cursor-pointer hover:shadow-[0_8px_28px_rgba(0,0,0,0.14)] transition-all duration-200">
                <div class="absolute inset-0 bg-gradient-to-t from-[#1a0f24] to-transparent opacity-60"></div>
                <svg class="relative z-10 w-16 h-auto text-[#C9A84C] mb-3" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 122.55 122.88"><path fill-rule="evenodd" clip-rule="evenodd" d="M28,86.8c10.87,10.56,21.81,16.46,32.81,16.79s22.32-4.94,33.86-16.72c-1.67-.4-3.46-.78-5.35-1.16l-.27,0c-1.63-.33-3.93-.79-6.3-1.45a22.36,22.36,0,0,1-3.44-1.11,20.44,20.44,0,0,1-3.42-1.63,4.57,4.57,0,0,1-1.45-1.89,7.2,7.2,0,0,1-1.46-4.14c-.24-.85-.43-1.58-.56-2-7.39,4.84-15.57,5-22.92.07,0,2-1.12,7.45-3.15,8.68C43,84.16,37.8,85,34,85.64c-.91.15-1.75.29-2.49.43-1.22.24-2.39.48-3.52.73Zm28.47-21a1.27,1.27,0,1,1,.86-2.38,11.07,11.07,0,0,0,8-.1,1.27,1.27,0,0,1,1.63.74,1.26,1.26,0,0,1-.73,1.63,13.65,13.65,0,0,1-9.75.11Zm-.6-26.68c1.94.73,2.34,2.35,1.26,3-1.27.72-2.87-.34-4.12-.74-3.24-1-7.09-1.15-9.89.59a15.78,15.78,0,0,0-2.27,1.8A7.8,7.8,0,0,1,42.18,41c2.81-3.54,9.88-3.75,13.64-1.82Zm10.92,0c-1.94.73-2.35,2.35-1.27,3,1.28.72,2.88-.34,4.12-.74,3.24-1,7.09-1.15,9.89.59a15.83,15.83,0,0,1,2.28,1.8A8.14,8.14,0,0,0,80.38,41c-2.82-3.54-9.89-3.75-13.64-1.82Zm-21.48,7a.83.83,0,0,1-.55-.58.94.94,0,0,1,.5-1.15,13.36,13.36,0,0,1,9.66,0,.93.93,0,0,1,.52,1.13.77.77,0,0,1-1,.59c-.53-.19-1.07-.35-1.59-.48a3,3,0,0,1,.07.6,2.24,2.24,0,1,1-4.47,0,2.2,2.2,0,0,1,.17-.86,11.58,11.58,0,0,0-2.41.58,1.39,1.39,0,0,1-.91.17Zm25.06-.55a2.39,2.39,0,0,0-.1.66,2.24,2.24,0,1,0,4.47,0,2.11,2.11,0,0,0-.23-1,13.68,13.68,0,0,1,3,.64.79.79,0,0,0,1-.63.94.94,0,0,0-.55-1.12A15.39,15.39,0,0,0,73,43.35a14.86,14.86,0,0,0-4.77.79.94.94,0,0,0-.55,1.13.79.79,0,0,0,1,.61,14.36,14.36,0,0,1,1.58-.43c.15,0,.11-.07.06.11ZM10.79,77.86C22,77,26.06,65.35,27.43,53.33c2.1-18.41-.81-42.09,21.35-51C66.37-4.83,89.12,4.75,91.6,30.09c-.1,17.22,4.74,46.52,20.15,47.77-1.13,3.2-6.48,5.58-12.86,6.65,7.5,2,12.87,4.56,16.62,8.69,5.12,5.65,7,14,7,28a1.67,1.67,0,0,1-1.67,1.67H1.67A1.67,1.67,0,0,1,0,121.21c0-13.6,1.81-22,6.64-27.69,3.5-4.13,8.48-6.7,15.41-8.68-5.56-1.25-10.13-3.8-11.26-7ZM76,68.06a47.45,47.45,0,0,0,8.64-13,52,52,0,0,0,0-21.23C77.8,30.71,73.32,23.72,71,13.23,68.28,33.21,43.75,32.35,37.58,36c0,9.08-1.19,15.43,2.81,23.71h0a49.78,49.78,0,0,0,2.78,5,25,25,0,0,0,2.73,3.81c8.24,8.88,22.33,7.77,30.12-.48Zm23,20C86.14,101.81,73.34,108,60.65,107.58s-25.09-7.31-37.24-19.66C16.88,89.73,12.27,92,9.18,95.67c-4.06,4.79-5.69,12.09-5.83,23.87H119.2c-.14-12.07-1.86-19.37-6.16-24.1-3.1-3.41-7.64-5.62-14-7.43Z"/></svg>
                <span class="relative z-10 font-['Playfair_Display'] font-semibold text-[20px] text-white">Women</span>
                <span class="relative z-10 font-['Inter'] text-[12px] text-[#B0B8C9] mt-1">Elegant & Stylish</span>
            </a>
            <a href="pages/shop.php?gender=Unisex" class="relative group overflow-hidden rounded-xl bg-[#1B3A2C] h-[200px] flex flex-col items-center justify-center cursor-pointer hover:shadow-[0_8px_28px_rgba(0,0,0,0.14)] transition-all duration-200">
                <div class="absolute inset-0 bg-gradient-to-t from-[#0f2419] to-transparent opacity-60"></div>
                <svg class="relative z-10 w-16 h-auto text-[#C9A84C] mb-3" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 512 335.09"shape-rendering="geometricPrecision" text-rendering="geometricPrecision" image-rendering="optimizeQuality"><path fill-rule="evenodd" clip-rule="evenodd" d="M252 223.98c12.02-.99 20.25-7.75 25.85-17.5l.04-.07c5.67-9.89 8.68-22.89 10.19-36.11.79-6.99 1.26-14.3 1.74-21.7 2.35-36.56 4.9-76.02 46.42-92.81a70.634 70.634 0 0 1 19.66-4.81c14.35-1.4 29 1.55 41.64 8.79 12.65 7.24 23.3 18.77 29.66 34.48 2.92 7.23 4.93 15.34 5.81 24.32v.16c-.12 22.05 3.41 53.02 12.99 75.74 6.9 16.34 16.92 28.37 30.93 29.51.57.04 1 .55.96 1.12l-.06.27c-2.6 7.34-14.6 12.99-28.95 15.74 21.34 5.19 36.15 11.45 46.18 22.5C507.4 277.2 512 297.44 512 331.06c0 2.22-1.8 4.02-4.02 4.02H340.75c.28-1.38.44-2.81.44-4.28v-13.99c0-12.25-3.13-24.11-8.54-34.95 10.16 4.16 20.36 6.46 30.57 6.77 26.58.8 53.74-11.89 81.55-40.27-4.04-.95-8.34-1.87-12.89-2.78l-.65-.14c-3.89-.77-9.37-1.86-15.02-3.43-1.95-.3-3.84-.66-5.66-1.11-5.66-1.37-10.65-3.48-14.38-6.41-5.7-4.46-8.53-10.76-6.63-19.19-17.06 10.65-34.98 10.61-51.95-.36l-1.09-.61c-.09 5-3.26 19.8-8.1 22.75-7.36 4.47-18.91 6.56-27.93 8.04-11.14-7.78-23.71-13.33-36.61-15.82-4.1-.79-8.22-1.59-12.17-2.52-.23-.46-.44-.93-.61-1.41-.19-.55.1-1.15.64-1.34l.28-.05zm-56.14-21.31c.21-.25.44-.47.7-.67l1.48-1.4c10.95-10.28 19.02-17.86 17.46-36.34-.05-.92.17-1.87.71-2.69a4.268 4.268 0 0 1 5.9-1.22c.76.5 1.53.9 2.31 1.18.64.24 1.26.38 1.83.41 1.11.06 1.68.06 1.83.02.15-.12.43-.86.83-2l5.44-15.4c.68-2.61.74-4.47.34-5.71-.17-.54-.46-.94-.83-1.18l-.16-.09c-.66-.35-1.6-.47-2.68-.38-2.39.18-5.18 1.33-7.66 3.21-.9.68-2.08 1.01-3.29.8a4.284 4.284 0 0 1-3.5-4.93c4.03-23.5 2.19-38.8-2.82-49.24-4.38-9.15-11.38-14.7-18.96-18.84-16.83 12.9-28.68 14.37-40.49 15.83-9.78 1.2-19.54 2.42-32.46 11.35-6.15 4.26-10.23 9.4-12.32 15.39-2.1 6.03-2.26 13.03-.56 20.95.26.86.25 1.8-.08 2.7-.81 2.22-3.27 3.35-5.49 2.54-1.28-.47-2.61-1.01-3.9-1.42-5.05-1.76-8.63-2.58-10.01.6-.11 1.01-.18 2.58-.23 4.2v.03c-.06 2.3-.06 4.71 0 5.96v.06c.15 2.96.8 6.88 2.22 10.17.89 2.08 2.07 3.83 3.54 4.67.73.41 1.56.6 2.42.61 1.06.02 2.24-.2 3.48-.57a4.273 4.273 0 0 1 5.66 3.93c.46 19.35 9.05 26.75 19.49 35.74l4.35 3.78c6.86 6.1 13.96 10.28 21.08 12.43 5.48 1.14 11.21 1.26 16.71.55 3.83-.5 7.52-1.4 10.94-2.63 4.94-2.33 9.79-5.74 14.48-10.27l2.24-2.13zm8.09 4.1c8.99 30 33.07 34.68 56.71 39.24 32.07 6.2 63.48 36.49 63.48 70.8v13.99c0 2.36-1.93 4.29-4.28 4.29H4.28c-2.36 0-4.28-1.93-4.28-4.29v-12.65c0-41.24 33.35-65.6 67.05-70.11 24.59-3.28 49.4-6.62 58.16-36.5l-.48-.43-4.26-3.72c-11.1-9.56-20.39-17.56-22.16-37.03l-.98.01c-2.27-.03-4.47-.55-6.53-1.72-3.31-1.88-5.63-5.09-7.19-8.71-1.86-4.31-2.69-9.33-2.89-13.12v-.08c-.06-1.39-.06-4.05 0-6.6v-.04c.07-2.21.18-4.36.35-5.58.05-.37.15-.71.28-1.04 2.33-6.48 5.89-8.58 10.59-8.45l-3.09-2.05c-1.68-20.97 3.23-57.35-19.54-64.23 43.08-53.24 92.73-82.19 130.02-34.84 44.92 2.36 62.93 73.74 27.11 103.79l-.21 1.76c1.41-.42 2.81-.69 4.15-.79 2.66-.21 5.19.22 7.33 1.35l.41.25c2.16 1.27 3.82 3.25 4.7 5.97.89 2.77.92 6.32-.25 10.66l-5.6 15.9c-.9 2.57-1.73 4.38-3.55 5.83l-.35.25c-1.8 1.28-3.99 1.76-7.25 1.59l-1.56-.17c-.36 17.68-9 25.84-20.31 36.47zm-6.42 6.75c-13.09 18.08-51.29 18.47-65.56 4.12-3.56 14.83-12.8 25.08-26.47 31.75 39.2 23.9 77.83 23.19 115.85-5.01-14.62-6.63-22.01-17.22-23.82-30.86zm198.82-13.45c18.17-20.1 23.01-33.65 20.61-60.73-.39-4.33-1.03-8.73-1.92-13.19-14.82-6.87-24.52-21.97-29.48-44.66-5.93 43.2-58.98 41.34-72.32 49.2 0 3.64-.09 7.07-.17 10.37-.61 22.23 1.04 34.06 12.23 51.81 1.87 2.97 3.7 5.84 5.91 8.22 17.82 19.22 48.29 16.82 65.14-1.02z"/></svg>
                <span class="relative z-10 font-['Playfair_Display'] font-semibold text-[20px] text-white">Unisex</span>
                <span class="relative z-10 font-['Inter'] text-[12px] text-[#B0B8C9] mt-1">For Everyone</span>
            </a>
            <a href="pages/shop.php?gender=Kids" class="relative group overflow-hidden rounded-xl bg-[#3A2A1B] h-[200px] flex flex-col items-center justify-center cursor-pointer hover:shadow-[0_8px_28px_rgba(0,0,0,0.14)] transition-all duration-200">
                <div class="absolute inset-0 bg-gradient-to-t from-[#241a0f] to-transparent opacity-60"></div>
                <svg class="relative z-10 w-16 h-auto text-[#C9A84C] mb-3" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 512 358.44"shape-rendering="geometricPrecision" text-rendering="geometricPrecision" image-rendering="optimizeQuality"><path fill-rule="evenodd" clip-rule="evenodd" d="M354.96 139.89c6.72 0 12.18 5.45 12.18 12.17 0 6.73-5.46 12.18-12.18 12.18-6.73 0-12.18-5.45-12.18-12.18 0-6.72 5.45-12.17 12.18-12.17zm14.87 77.56a5.745 5.745 0 0 1-1.44-8c1.81-2.61 5.4-3.25 8.01-1.44 5.84 4.07 11.58 6.08 17.19 6.07 5.63-.01 11.38-2.04 17.22-6.07 2.61-1.8 6.19-1.15 7.98 1.46a5.733 5.733 0 0 1-1.46 7.98c-7.8 5.37-15.71 8.09-23.74 8.1-8.03.01-15.96-2.67-23.76-8.1zm65.06-77.56c6.72 0 12.17 5.45 12.17 12.17 0 6.73-5.45 12.18-12.17 12.18-6.73 0-12.18-5.45-12.18-12.18 0-6.72 5.45-12.17 12.18-12.17zM279.17 358.44c9.77-35.13 22.63-45.77 48.8-67.4 3.96-3.28 8.24-6.82 12.35-10.3.08-.07.17-.13.25-.2.92-1.19 1.55-1.98 2.14-2.74 3.05-3.88 5.58-7.09 7.61-11.37-12.89-6.9-24.89-17.23-35.15-31.22-7.69 1.93-14.21 3.64-19.67 5.06-5.3 1.39-9.64 2.53-12.78 3.28-1.82.43-3.13.84-4.99.11-2.15-.86-3.67-3-3.61-5.47l3.29-127.63c0-.21 0-.42.02-.63.82-8.22 2.68-16.13 5.46-23.77 2.79-7.65 6.48-14.99 10.96-22.04 12.17-19.14 27.33-32.76 45.18-41.47 17.72-8.65 38.01-12.39 60.55-11.83 19.37.47 38.13 5.28 54.82 14.85 15.59 8.94 29.37 22.03 40.12 39.61 3.97 6.49 7.39 13.31 10.28 20.45 2.84 7.02 5.15 14.31 6.96 21.86.15.52.23 1.07.23 1.64l-.01 122.51c0 1.8.19 3.47-.75 5.09-1.92 3.33-7.09 4.14-9.69.44-2.6-3.7-5.97-6.81-9.95-9.4-3.01-1.96-6.4-3.63-10.1-5.06-11.91 19.37-27.49 33.85-44.72 42.96 1.55 4.48 3.7 7.88 6.37 12.09l1.15 1.82c9.07 4.11 17.93 9.35 26.29 17.07 8.67 8 16.74 18.57 23.85 33.17 4.56 9.35 8.01 18.88 10.91 28.52H493.3c-2.54-8.01-5.5-15.86-9.21-23.48-6.44-13.22-13.63-22.69-21.29-29.76-6.38-5.88-13.15-10.16-20.13-13.58a65.03 65.03 0 0 1-15.1 13.72c-10.69 7-23.15 10.62-35.59 10.65-12.47.03-24.92-3.55-35.55-10.93a61.876 61.876 0 0 1-5.91-4.68 71.364 71.364 0 0 1-7.26-7.1c-2.89 2.42-5.49 4.57-7.96 6.62-23.32 19.27-35.25 29.14-44.12 58.54h-12.01zM422.46 253.5c1.31-.59 2.63-1.21 3.97-1.86.17-.08.35-.15.52-.22 19.56-9.78 36.83-27.81 47.71-52.62 3.82-8.72 5.58-16.68 6.31-24.56 2.28-24.34.18-46.22.18-71.49-19.99 3.08-28.32-10.3-32-30.24-40.85 37.24-88.43 45.45-141.75 28.98 0 25.01-1.99 45.37-.03 69.56.59 7.31 2.03 14.7 5.15 22.89 23.09 60.49 70.44 76.51 109.94 59.56zm-61.64 17.74c-2.44 5.22-5.43 9.04-9.04 13.63a50.92 50.92 0 0 0 6.8 7.29c.43.35.87.7 1.31 1.04 9.43 7.29 20.49 11.06 31.55 11.23 11.04.16 22.13-3.27 31.64-10.37 3.74-2.8 7.24-6.14 10.37-10.05l-.02-.03c-2.95-4.66-5.37-8.49-7.2-13.38-21.21 8.26-44.16 8.75-65.41.64zm-123.15 87.2c-1.08-4.22-2.34-7.98-3.77-11.35-8.17-19.25-28.53-28.02-48.51-36.63-3.38-1.46-6.75-2.91-10.29-4.52-.45.57-.91 1.13-1.38 1.68-2.74 3.36-5.96 6.35-9.54 8.94-9.93 7.19-22.75 11.32-35.96 11.97-13.16.64-26.79-2.16-38.42-8.83-5.78-3.32-11.07-7.58-15.58-12.85-3.19 1.53-6.5 3.03-9.87 4.57-19.2 8.76-40.85 18.63-48.77 35.11-1.74 3.63-3.13 7.6-4.17 11.91H0c.1-.5.21-1 .33-1.5 1.24-5.46 2.98-10.54 5.22-15.21 9.68-20.13 33.27-30.89 54.18-40.43 4.64-2.11 9.16-4.17 13.2-6.19 2.76-3.62 5.02-7.53 6.78-11.72 1.5-3.58 2.63-7.36 3.37-11.33a80.802 80.802 0 0 1-12.11-8.88c-15.69-13.92-26.66-25.28-33.72-38.41-7.17-13.34-10.18-28.14-9.79-48.7l1.13-64.9c.01-.64.13-1.25.33-1.81l-13.86-4.37c-2.01-43.88 23.4-70.14 54.85-82.75 11.46-4.6-7.73-29.69 18.63-20.06 35.21 12.87 113.36 30.45 131.85 52.56 15.03 17.96 15.63 27.72 15.29 51.16-.1 7.6-1.3 14.29-4.81 16.78-1.6 1.13-3.56 1.6-5.81 1.41v56.29c0 28.22-10.08 51.78-25.57 69.23a101.747 101.747 0 0 1-30.47 23.41c.67 3.63 1.61 7.09 2.8 10.39 1.42 3.94 3.24 7.69 5.44 11.27 3.81 1.77 8.16 3.65 12.53 5.53 22.01 9.48 44.42 19.14 54.36 42.56 1.94 4.59 3.61 9.78 4.95 15.67h-11.43zm-133.74-77.67a81.5 81.5 0 0 1-10.54-3.53c-.85 3.62-1.98 7.12-3.39 10.47-1.77 4.23-4.01 8.25-6.7 12.07 2.97 3.29 6.33 6.11 9.98 8.46 9.84 6.35 21.7 9.35 33.36 9.06 11.59-.28 22.93-3.83 31.8-10.53 2.36-1.79 4.53-3.8 6.49-6.03.59-.71 1.14-1.44 1.67-2.19-2.05-3.6-3.8-7.39-5.25-11.39a76.96 76.96 0 0 1-2.73-9.43c-11.35 4.06-22.83 6.08-34.09 5.91-6.99-.1-13.88-1.05-20.6-2.87zm-11.7-16.49c3.68 1.94 7.45 3.53 11.29 4.78l.16.04c7.1 2.2 14.5 3.26 21.97 3.26 11.94 0 24.04-2.71 35.28-7.83a90.55 90.55 0 0 0 30.23-22.28c13.77-15.53 22.73-36.57 22.73-61.87v-56.93h.02c-.13-3.55-.64-6.82-1.6-9.77-.93-2.84-2.26-5.35-4.08-7.48-6.83-8.02-13.32-7.69-22.56-7.22-.83.04-1.65.08-2.84.13-36.58 1.61-60.56 1.57-79.99-.13-17.93-1.56-32.01-4.5-48.39-8.83A82.054 82.054 0 0 1 49 100.78a88.09 88.09 0 0 1-9.33 12.66l-1.09 62.8c-.35 18.53 2.25 31.68 8.48 43.25 6.33 11.77 16.55 22.3 31.27 35.36 3.89 3.44 8.05 6.36 12.4 8.76.54.15 1.04.38 1.5.67zm8.98-34a5.57 5.57 0 0 1-1.64-7.7c1.67-2.58 5.12-3.31 7.7-1.64 6.35 4.13 12.43 6.36 18.23 6.44 5.6.07 11.16-1.91 16.66-6.17a5.572 5.572 0 0 1 7.82.98 5.584 5.584 0 0 1-.98 7.83c-7.56 5.87-15.45 8.59-23.63 8.48-8.01-.11-16.06-2.95-24.16-8.22zm-20.79-69.82c6.65 0 12.04 5.39 12.04 12.03 0 6.65-5.39 12.04-12.04 12.04-6.64 0-12.03-5.39-12.03-12.04 0-6.64 5.39-12.03 12.03-12.03zm90.8 0c6.65 0 12.04 5.39 12.04 12.03 0 6.65-5.39 12.04-12.04 12.04-6.65 0-12.03-5.39-12.03-12.04 0-6.64 5.38-12.03 12.03-12.03z"/></svg>
                <span class="relative z-10 font-['Playfair_Display'] font-semibold text-[20px] text-white">Kids</span>
                <span class="relative z-10 font-['Inter'] text-[12px] text-[#B0B8C9] mt-1">Fun & Durable</span>
            </a>
        </div>
    </div>
</section>

<!-- NEW ARRIVALS -->
<section class="py-[60px] bg-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-10">
            <div>
                <span class="font-['Inter'] font-semibold text-[11px] uppercase tracking-[1.5px] text-[#C9A84C]">Just Arrived</span>
                <h2 class="font-['Playfair_Display'] font-bold text-[28px] md:text-[32px] text-[#1A1A2E] mt-1">New Arrivals</h2>
            </div>
            <a href="pages/shop.php" class="hidden sm:inline-flex items-center gap-2 font-['Inter'] font-semibold text-[14px] text-[#1B2A4A] hover:text-[#C9A84C] transition-colors duration-200">
                View All
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
        <?php if (!empty($new_arrivals)): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php foreach ($new_arrivals as $product): ?>
            <a href="pages/product-detail.php?slug=<?= urlencode($product['slug']) ?>" class="group bg-white border border-[#E0E2E7] rounded-xl overflow-hidden hover:shadow-[0_8px_28px_rgba(0,0,0,0.14)] hover:-translate-y-1 transition-all duration-200 cursor-pointer block">
                <div class="relative bg-[#F7F8FA] aspect-square overflow-hidden">
                    <?php if ($product['main_image']): ?>
                    <img src="assets/uploads/products/<?= htmlspecialchars($product['main_image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    <?php else: ?>
                    <div class="w-full h-full flex items-center justify-center">
                        <svg class="w-16 h-16 text-[#E0E2E7]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="1"/><path stroke-linecap="round" stroke-width="1" d="M12 6v6l4 2"/></svg>
                    </div>
                    <?php endif; ?>
                    <div class="absolute top-3 left-3">
                        <?php if ($product['stock_quantity'] == 0): ?>
                        <span class="inline-block px-2.5 py-1 rounded bg-[#FDEAEA] text-[#D64545] font-['Inter'] font-semibold text-[11px]">Out of Stock</span>
                        <?php elseif ($product['stock_quantity'] <= 5): ?>
                        <span class="inline-block px-2.5 py-1 rounded bg-[#FFF3E0] text-[#E65100] font-['Inter'] font-semibold text-[11px]">Low Stock</span>
                        <?php else: ?>
                        <span class="inline-block px-2.5 py-1 rounded bg-[#E8F5E9] text-[#2E7D32] font-['Inter'] font-semibold text-[11px]">In Stock</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="p-4">
                    <div class="font-['Inter'] text-[12px] text-[#C9A84C] font-semibold mb-1"><?= htmlspecialchars($product['brand_name']) ?></div>
                    <h3 class="font-['Inter'] font-semibold text-[15px] text-[#1A1A2E] leading-[1.4] mb-2 line-clamp-2 group-hover:text-[#1B2A4A]"><?= htmlspecialchars($product['name']) ?></h3>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="font-['Inter'] text-[11px] text-[#5A5F6D] bg-[#F7F8FA] px-2 py-0.5 rounded"><?= htmlspecialchars($product['gender']) ?></span>
                        <span class="font-['Inter'] text-[11px] text-[#5A5F6D] bg-[#F7F8FA] px-2 py-0.5 rounded"><?= htmlspecialchars($product['movement_type']) ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="font-['Inter'] font-bold text-[20px] text-[#1A1A2E]">NPR <?= number_format($product['price'], 0) ?></span>
                        <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-[#1B2A4A] group-hover:bg-[#C9A84C] transition-colors duration-200">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </span>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-16 text-[#5A5F6D]">No products available yet.</div>
        <?php endif; ?>
    </div>
</section>

<!-- MOVEMENT TYPE QUICK FILTER -->
<section class="py-[60px] bg-[#F7F8FA]">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <span class="font-['Inter'] font-semibold text-[11px] uppercase tracking-[1.5px] text-[#C9A84C]">Filter By</span>
            <h2 class="font-['Playfair_Display'] font-bold text-[28px] md:text-[32px] text-[#1A1A2E] mt-1">Movement Types</h2>
        </div>
        <div class="flex flex-wrap justify-center gap-3">
            <?php
            $movements = ['Automatic', 'Quartz', 'Mechanical', 'Solar', 'Kinetic', 'Digital', 'Smartwatch'];
            foreach ($movements as $mv):
            ?>
            <a href="pages/shop.php?movement=<?= urlencode($mv) ?>" class="inline-flex items-center gap-2 px-5 py-3 bg-white border border-[#E0E2E7] hover:border-[#C9A84C] hover:bg-[#FFF8E7] rounded-full font-['Inter'] font-medium text-[14px] text-[#1A1A2E] hover:text-[#C9A84C] transition-all duration-200 hover:shadow-md">
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
            <span class="font-['Inter'] font-semibold text-[11px] uppercase tracking-[1.5px] text-[#C9A84C]">Why ChronoNest</span>
            <h2 class="font-['Playfair_Display'] font-bold text-[28px] md:text-[32px] text-[#1A1A2E] mt-1">Built for Watch Lovers</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="p-8 bg-[#F7F8FA] rounded-xl border border-[#E0E2E7] text-center hover:shadow-[0_8px_28px_rgba(0,0,0,0.08)] transition-shadow duration-200">
                <div class="w-14 h-14 rounded-xl bg-[#FFF8E7] border border-[#C9A84C]/20 flex items-center justify-center mx-auto mb-5">
                    <svg class="w-7 h-7 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <h3 class="font-['Playfair_Display'] font-semibold text-[20px] text-[#1A1A2E] mb-3">Smart Filtering</h3>
                <p class="font-['Inter'] text-[14px] leading-[1.6] text-[#5A5F6D]">Filter by brand, movement, material, case size, dial color and more — find exactly what you're looking for in seconds.</p>
            </div>
            <div class="p-8 bg-[#F7F8FA] rounded-xl border border-[#E0E2E7] text-center hover:shadow-[0_8px_28px_rgba(0,0,0,0.08)] transition-shadow duration-200">
                <div class="w-14 h-14 rounded-xl bg-[#FFF8E7] border border-[#C9A84C]/20 flex items-center justify-center mx-auto mb-5">
                    <svg class="w-7 h-7 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="font-['Playfair_Display'] font-semibold text-[20px] text-[#1A1A2E] mb-3">Multi-Angle Images</h3>
                <p class="font-['Inter'] text-[14px] leading-[1.6] text-[#5A5F6D]">View watches from multiple angles with high-resolution zoom. Know exactly what you're buying before it arrives.</p>
            </div>
            <div class="p-8 bg-[#F7F8FA] rounded-xl border border-[#E0E2E7] text-center hover:shadow-[0_8px_28px_rgba(0,0,0,0.08)] transition-shadow duration-200">
                <div class="w-14 h-14 rounded-xl bg-[#FFF8E7] border border-[#C9A84C]/20 flex items-center justify-center mx-auto mb-5">
                    <svg class="w-7 h-7 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="font-['Playfair_Display'] font-semibold text-[20px] text-[#1A1A2E] mb-3">Nationwide Delivery</h3>
                <p class="font-['Inter'] text-[14px] leading-[1.6] text-[#5A5F6D]">Shop from anywhere in Nepal — Kathmandu to Humla. We deliver to your doorstep with care and reliability.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA STRIP -->
<section class="bg-[#FFF8E7] border-y border-[#C9A84C]/20 py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <div>
                <h2 class="font-['Playfair_Display'] font-bold text-[24px] text-[#1A1A2E]">Ready to find your perfect watch?</h2>
                <p class="font-['Inter'] text-[15px] text-[#5A5F6D] mt-1">Join thousands of watch enthusiasts across Nepal.</p>
            </div>
            <div class="flex items-center gap-4">
                <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="pages/register.php" class="inline-flex items-center gap-2 h-[44px] px-6 bg-[#C9A84C] hover:bg-[#B8953F] text-white font-['Inter'] font-semibold text-[15px] rounded-lg transition-all duration-200">Create Account</a>
                <a href="pages/shop.php" class="inline-flex items-center gap-2 h-[44px] px-6 bg-transparent hover:bg-[#C9A84C]/10 text-[#1B2A4A] font-['Inter'] font-semibold text-[15px] rounded-lg border border-[#1B2A4A] transition-all duration-200">Browse Watches</a>
                <?php else: ?>
                <a href="pages/shop.php" class="inline-flex items-center gap-2 h-[44px] px-6 bg-[#C9A84C] hover:bg-[#B8953F] text-white font-['Inter'] font-semibold text-[15px] rounded-lg transition-all duration-200">Browse Watches</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>