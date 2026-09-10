<?php
session_start();
require_once '../config/db.php';

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
if (!$slug) { header('Location: shop.php'); exit; }

$stmt = $conn->prepare("SELECT p.*, b.name as brand_name, b.slug as brand_slug FROM products p JOIN brands b ON p.brand_id = b.id WHERE p.slug = ? AND p.is_active = 1 LIMIT 1");
$stmt->bind_param("s", $slug);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) { header('Location: shop.php'); exit; }

$img_result = $conn->query("SELECT * FROM product_images WHERE product_id = {$product['id']} ORDER BY is_main DESC, sort_order ASC");
$images = $img_result->fetch_all(MYSQLI_ASSOC);

$in_wishlist = false;
if (isset($_SESSION['user_id'])) {
    $wl_stmt = $conn->prepare("SELECT id FROM wishlists WHERE user_id = ? AND product_id = ?");
    $wl_stmt->bind_param("ii", $_SESSION['user_id'], $product['id']);
    $wl_stmt->execute();
    $in_wishlist = $wl_stmt->get_result()->num_rows > 0;
}

// Strap size → length mapping
$strap_size_length_map = [
    'XS' => 145,
    'S'  => 160,
    'M'  => 175,
    'L'  => 190,
    'XL' => 205,
];

// STRAP LOGIC
$strap_sizes = [];
$needs_strap_selection = false;
if ($product['strap_adjustable'] && $product['strap_size_options']) {
    $strap_sizes = array_map('trim', explode(',', $product['strap_size_options']));
    $needs_strap_selection = true;
}

$features_list = [];
if ($product['features']) {
    $features_list = array_map('trim', explode(',', $product['features']));
}

$related_sql = "SELECT p.id, p.name, p.slug, p.price, p.stock_quantity, b.name as brand_name,
    (SELECT image_url FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image
    FROM products p JOIN brands b ON p.brand_id = b.id
    WHERE p.is_active = 1 AND p.id != ? AND (p.brand_id = ? OR p.gender = ?) ORDER BY RAND() LIMIT 4";
$rel_stmt = $conn->prepare($related_sql);
$rel_stmt->bind_param("iis", $product['id'], $product['brand_id'], $product['gender']);
$rel_stmt->execute();
$related = $rel_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$page_title = htmlspecialchars($product['name']) . " – ChronoNest";
$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
require_once '../includes/header.php';
?>

<!-- BREADCRUMB -->
<div class="bg-white border-b border-[#E0E2E7]">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-3">
        <nav class="flex items-center gap-2 font-['Inter'] text-[13px] text-[#5A5F6D] flex-wrap">
            <a href="../index.php" class="hover:text-[#C9A84C] transition-colors">Home</a>
            <svg class="w-3 h-3 text-[#8A8F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="shop.php" class="hover:text-[#C9A84C] transition-colors">Shop</a>
            <svg class="w-3 h-3 text-[#8A8F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="shop.php?brand=<?= urlencode($product['brand_slug']) ?>" class="hover:text-[#C9A84C] transition-colors"><?= htmlspecialchars($product['brand_name']) ?></a>
            <svg class="w-3 h-3 text-[#8A8F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-[#1A1A2E] font-medium line-clamp-1"><?= htmlspecialchars($product['name']) ?></span>
        </nav>
    </div>
</div>

<!-- ALERTS -->
<div class="container mx-auto px-4 sm:px-6 lg:px-8 mt-4">
    <?php if ($success): ?>
    <div class="flex items-center gap-3 bg-[#E8F5E9] border border-[#2E7D32]/20 text-[#2E7D32] rounded-lg px-4 py-3 mb-4 font-['Inter'] text-[14px]"><svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
    <div class="flex items-center gap-3 bg-[#FDEAEA] border border-[#D64545]/20 text-[#D64545] rounded-lg px-4 py-3 mb-4 font-['Inter'] text-[14px]"><svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
</div>

<!-- PRODUCT DETAIL -->
<section class="py-8 bg-[#F7F8FA]">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

            <!-- IMAGES -->
            <div>
                <div class="bg-white rounded-xl border border-[#E0E2E7] overflow-hidden">
                    <?php if (!empty($images)): ?>
                    <div id="mainImageWrap" class="aspect-square overflow-hidden cursor-zoom-in relative">
                        <img id="mainImage" src="../assets/uploads/products/<?= htmlspecialchars(basename($images[0]['image_url'])) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-full object-cover transition-transform duration-300 hover:scale-150" style="transform-origin: center center;" onmousemove="const r=this.getBoundingClientRect();const x=((event.clientX-r.left)/r.width)*100;const y=((event.clientY-r.top)/r.height)*100;this.style.transformOrigin=x+'% '+y+'%'" onmouseleave="this.style.transformOrigin='center center';this.style.transform='scale(1)'" onmouseenter="this.style.transform='scale(1.8)'">
                    </div>
                    <?php if (count($images) > 1): ?>
                    <div class="flex gap-2 p-3 overflow-x-auto">
                        <?php foreach ($images as $i => $img): ?>
                        <button onclick="document.getElementById('mainImage').src='../assets/uploads/products/<?= htmlspecialchars(basename($img['image_url'])) ?>';document.querySelectorAll('.thumb-btn').forEach(b=>b.classList.remove('ring-2','ring-[#C9A84C]'));this.classList.add('ring-2','ring-[#C9A84C]')" class="thumb-btn w-16 h-16 rounded-lg border border-[#E0E2E7] overflow-hidden flex-shrink-0 <?= $i === 0 ? 'ring-2 ring-[#C9A84C]' : '' ?> hover:border-[#C9A84C] transition-all duration-150">
                            <img src="../assets/uploads/products/<?= htmlspecialchars(basename($img['image_url'])) ?>" alt="" class="w-full h-full object-cover">
                        </button>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <?php else: ?>
                    <div class="aspect-square flex items-center justify-center bg-[#F7F8FA]">
                        <svg class="w-24 h-24 text-[#E0E2E7]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="1"/><path stroke-linecap="round" stroke-width="1" d="M12 6v6l4 2"/></svg>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- DETAILS -->
            <div>
                <div class="mb-3">
                    <a href="shop.php?brand=<?= urlencode($product['brand_slug']) ?>" class="font-['Inter'] font-semibold text-[13px] text-[#C9A84C] hover:underline"><?= htmlspecialchars($product['brand_name']) ?></a>
                </div>
                <h1 class="font-['Playfair_Display'] font-bold text-[28px] md:text-[36px] leading-[1.2] text-[#1A1A2E] mb-2"><?= htmlspecialchars($product['name']) ?></h1>
                <p class="font-['Inter'] text-[13px] text-[#8A8F99] mb-4">Model: <?= htmlspecialchars($product['model_number']) ?></p>

                <div class="mb-5">
                    <?php if ($product['stock_quantity'] == 0): ?>
                    <span class="inline-block px-3 py-1.5 rounded bg-[#FDEAEA] text-[#D64545] font-['Inter'] font-semibold text-[12px]">Out of Stock</span>
                    <?php elseif ($product['stock_quantity'] <= 5): ?>
                    <span class="inline-block px-3 py-1.5 rounded bg-[#FFF3E0] text-[#E65100] font-['Inter'] font-semibold text-[12px]">Only <?= $product['stock_quantity'] ?> left in stock</span>
                    <?php else: ?>
                    <span class="inline-block px-3 py-1.5 rounded bg-[#E8F5E9] text-[#2E7D32] font-['Inter'] font-semibold text-[12px]">In Stock (<?= $product['stock_quantity'] ?> available)</span>
                    <?php endif; ?>
                </div>

                <div class="mb-6">
                    <div class="font-['Inter'] font-bold text-[32px] text-[#1A1A2E]">NPR <?= number_format($product['price'], 0) ?></div>
                    <p class="font-['Inter'] text-[12px] text-[#8A8F99] mt-1">Inclusive of all taxes</p>
                </div>

                <!-- Quick Specs -->
                <div class="grid grid-cols-2 gap-3 mb-6">
                    <?php if ($product['gender']): ?>
                    <div class="flex items-center gap-2 p-3 bg-[#F7F8FA] rounded-lg"><svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg><div><div class="font-['Inter'] text-[11px] text-[#8A8F99]">Gender</div><div class="font-['Inter'] font-semibold text-[13px] text-[#1A1A2E]"><?= htmlspecialchars($product['gender']) ?></div></div></div>
                    <?php endif; ?>
                    <?php if ($product['movement_type'] && $product['movement_type'] !== '-'): ?>
                    <div class="flex items-center gap-2 p-3 bg-[#F7F8FA] rounded-lg"><svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg><div><div class="font-['Inter'] text-[11px] text-[#8A8F99]">Movement</div><div class="font-['Inter'] font-semibold text-[13px] text-[#1A1A2E]"><?= htmlspecialchars($product['movement_type']) ?></div></div></div>
                    <?php endif; ?>
                    <?php if ($product['case_diameter_mm']): ?>
                    <div class="flex items-center gap-2 p-3 bg-[#F7F8FA] rounded-lg"><svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/></svg><div><div class="font-['Inter'] text-[11px] text-[#8A8F99]">Case Size</div><div class="font-['Inter'] font-semibold text-[13px] text-[#1A1A2E]"><?= $product['case_diameter_mm'] ?> mm</div></div></div>
                    <?php endif; ?>
                    <?php if ($product['water_resistance']): ?>
                    <div class="flex items-center gap-2 p-3 bg-[#F7F8FA] rounded-lg"><svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg><div><div class="font-['Inter'] text-[11px] text-[#8A8F99]">Water Resistance</div><div class="font-['Inter'] font-semibold text-[13px] text-[#1A1A2E]"><?= htmlspecialchars($product['water_resistance']) ?></div></div></div>
                    <?php endif; ?>
                </div>

                <!-- ===== STRAP SECTION ===== -->
                <?php if ($needs_strap_selection): ?>
                <!-- ADJUSTABLE STRAP: Customer selects size, length updates dynamically -->
                <div class="mb-6 p-4 bg-[#F7F8FA] border border-[#E0E2E7] rounded-xl">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                        <label class="font-['Inter'] font-semibold text-[14px] text-[#1A1A2E]">Select Strap Size <span class="text-[#D64545]">*</span></label>
                    </div>
                    <p class="font-['Inter'] text-[12px] text-[#5A5F6D] mb-3">This watch has an adjustable strap. Select your preferred size — strap length adjusts accordingly.</p>

                    <!-- Size Guide Table -->
                    <div class="mb-3 bg-white border border-[#E0E2E7] rounded-lg overflow-hidden">
                        <div class="grid grid-cols-5 text-center divide-x divide-[#E0E2E7]">
                            <?php foreach ($strap_size_length_map as $sz => $len): ?>
                            <div class="py-1.5 <?= in_array($sz, $strap_sizes) ? '' : 'opacity-30' ?>">
                                <div class="font-['Inter'] font-semibold text-[11px] text-[#1A1A2E]"><?= $sz ?></div>
                                <div class="font-['Inter'] text-[10px] text-[#8A8F99]"><?= $len ?> mm</div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Size Buttons -->
                    <div id="strapSizeOptions" class="flex flex-wrap gap-2">
                        <?php foreach ($strap_sizes as $ss):
                            $size_length = $strap_size_length_map[$ss] ?? '–';
                        ?>
                        <button type="button" onclick="document.querySelectorAll('#strapSizeOptions button').forEach(b=>{b.classList.remove('bg-[#1B2A4A]','text-white','border-[#1B2A4A]');b.classList.add('bg-white','text-[#1A1A2E]','border-[#E0E2E7]')});this.classList.remove('bg-white','text-[#1A1A2E]','border-[#E0E2E7]');this.classList.add('bg-[#1B2A4A]','text-white','border-[#1B2A4A]');document.getElementById('selectedStrapSize').value='<?= htmlspecialchars($ss) ?>';document.getElementById('strapLengthDisplay').textContent='Strap length: <?= $size_length ?> mm';document.getElementById('strapLengthDisplay').classList.remove('hidden')" class="px-5 py-2.5 rounded-lg border border-[#E0E2E7] bg-white text-[#1A1A2E] font-['Inter'] font-medium text-[13px] hover:border-[#1B2A4A] transition-all duration-150 cursor-pointer flex flex-col items-center gap-0.5">
                            <span class="font-semibold"><?= htmlspecialchars($ss) ?></span>
                            <span class="text-[10px] text-[#8A8F99] font-normal"><?= $size_length ?> mm</span>
                        </button>
                        <?php endforeach; ?>
                    </div>

                    <!-- Dynamic Length Display -->
                    <p id="strapLengthDisplay" class="font-['Inter'] text-[12px] text-[#C9A84C] font-semibold mt-3 hidden flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span></span>
                    </p>
                </div>

                <?php elseif (!$product['strap_adjustable']): ?>
                <!-- NON-ADJUSTABLE / FIXED STRAP -->
                <div class="mb-6 p-4 bg-[#F7F8FA] border border-[#E0E2E7] rounded-xl">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-4 h-4 text-[#5A5F6D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="font-['Inter'] font-semibold text-[14px] text-[#1A1A2E]">Strap Information</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-[#E0E2E7] rounded-lg font-['Inter'] text-[13px] text-[#1A1A2E]">
                            <svg class="w-3.5 h-3.5 text-[#8A8F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            Fixed Size
                        </span>
                        <?php if ($product['strap_length_mm']): ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-[#E0E2E7] rounded-lg font-['Inter'] text-[13px] text-[#1A1A2E]">Length: <?= $product['strap_length_mm'] ?> mm</span>
                        <?php endif; ?>
                    </div>
                    <p class="font-['Inter'] text-[11px] text-[#8A8F99] mt-2">This watch has a fixed (non-adjustable) strap. No size selection required.</p>
                </div>
                <?php endif; ?>

                <!-- ACTIONS -->
                <div class="flex flex-col sm:flex-row gap-3 mb-6">
                    <?php if ($product['stock_quantity'] > 0): ?>
                    <form action="actions/cart-action.php" method="POST" class="flex-1" onsubmit="<?= $needs_strap_selection ? "if(!document.getElementById('selectedStrapSize').value){alert('Please select a strap size');return false;}" : '' ?>">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <input type="hidden" name="selected_strap_size" value="" id="selectedStrapSize">
                        <input type="hidden" name="redirect" value="product-detail.php?slug=<?= urlencode($slug) ?>">
                        <button type="submit" class="w-full h-[48px] bg-[#C9A84C] hover:bg-[#B8953F] text-white font-['Inter'] font-semibold text-[15px] rounded-lg transition-all duration-200 hover:shadow-md inline-flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                            Add to Cart
                        </button>
                    </form>
                    <?php else: ?>
                    <button disabled class="flex-1 h-[48px] bg-[#C5C9D1] text-[#8A8F99] font-['Inter'] font-semibold text-[15px] rounded-lg cursor-not-allowed inline-flex items-center justify-center gap-2">Out of Stock</button>
                    <?php endif; ?>

                    <form action="actions/wishlist-action.php" method="POST">
                        <input type="hidden" name="action" value="<?= $in_wishlist ? 'remove' : 'add' ?>">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <input type="hidden" name="redirect" value="product-detail.php?slug=<?= urlencode($slug) ?>">
                        <button type="submit" class="h-[48px] px-5 border rounded-lg font-['Inter'] font-semibold text-[14px] transition-all duration-200 inline-flex items-center gap-2 <?= $in_wishlist ? 'bg-[#FDEAEA] border-[#D64545]/30 text-[#D64545] hover:bg-[#D64545] hover:text-white' : 'bg-white border-[#E0E2E7] text-[#5A5F6D] hover:border-[#C9A84C] hover:text-[#C9A84C]' ?>">
                            <svg class="w-5 h-5" fill="<?= $in_wishlist ? 'currentColor' : 'none' ?>" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            <?= $in_wishlist ? 'Remove' : 'Wishlist' ?>
                        </button>
                    </form>
                </div>

                <!-- Warranty & Delivery -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
                    <div class="flex items-center gap-2 p-3 bg-[#E8F5E9] rounded-lg"><svg class="w-4 h-4 text-[#2E7D32]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg><span class="font-['Inter'] font-medium text-[12px] text-[#2E7D32]"><?= $product['warranty_years'] ?>-Year Warranty</span></div>
                    <div class="flex items-center gap-2 p-3 bg-[#E3F2FD] rounded-lg"><svg class="w-4 h-4 text-[#1565C0]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 12a2 2 0 002 2h8a2 2 0 002-2L19 8"/></svg><span class="font-['Inter'] font-medium text-[12px] text-[#1565C0]">Free Delivery</span></div>
                    <div class="flex items-center gap-2 p-3 bg-[#FFF8E7] rounded-lg"><svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span class="font-['Inter'] font-medium text-[12px] text-[#C9A84C]">100% Authentic</span></div>
                </div>

                <?php if ($product['description']): ?>
                <div class="mb-6">
                    <h3 class="font-['Playfair_Display'] font-semibold text-[18px] text-[#1A1A2E] mb-3">Description</h3>
                    <div class="font-['Inter'] text-[14px] leading-[1.7] text-[#5A5F6D]"><?= nl2br(htmlspecialchars($product['description'])) ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- SPECIFICATIONS -->
        <div class="mt-12 bg-white rounded-xl border border-[#E0E2E7] shadow-[0_2px_12px_rgba(0,0,0,0.08)] overflow-hidden">
            <div class="px-6 py-4 bg-[#F7F8FA] border-b border-[#E0E2E7]">
                <h2 class="font-['Playfair_Display'] font-bold text-[22px] text-[#1A1A2E]">Specifications</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2">
                <?php
                // Build strap length display for specs
                $strap_length_display = null;
                if ($product['strap_adjustable'] && $product['strap_size_options']) {
                    // Show range based on available sizes
                    $sizes_arr = array_map('trim', explode(',', $product['strap_size_options']));
                    $lengths = [];
                    foreach ($sizes_arr as $sz) {
                        if (isset($strap_size_length_map[$sz])) $lengths[] = $strap_size_length_map[$sz];
                    }
                    if (!empty($lengths)) {
                        $strap_length_display = min($lengths) . ' – ' . max($lengths) . ' mm (varies by size)';
                    }
                } elseif (!$product['strap_adjustable'] && $product['strap_length_mm']) {
                    $strap_length_display = $product['strap_length_mm'] . ' mm (fixed)';
                }

                $specs = [
                    ['label' => 'Brand', 'value' => $product['brand_name']],
                    ['label' => 'Model Number', 'value' => $product['model_number']],
                    ['label' => 'Gender', 'value' => $product['gender']],
                    ['label' => 'Movement Type', 'value' => $product['movement_type'] !== '-' ? $product['movement_type'] : null],
                    ['label' => 'Case Diameter', 'value' => $product['case_diameter_mm'] ? $product['case_diameter_mm'] . ' mm' : null],
                    ['label' => 'Case Material', 'value' => $product['case_material']],
                    ['label' => 'Dial Shape', 'value' => $product['dial_shape']],
                    ['label' => 'Dial Color', 'value' => $product['dial_color']],
                    ['label' => 'Strap Material', 'value' => $product['strap_material']],
                    ['label' => 'Strap Color', 'value' => $product['strap_color']],
                    ['label' => 'Strap Type', 'value' => $product['strap_adjustable'] ? 'Adjustable (select size)' : 'Fixed Size'],
                    ['label' => 'Strap Length', 'value' => $strap_length_display],
                    ['label' => 'Available Sizes', 'value' => ($product['strap_adjustable'] && $product['strap_size_options']) ? $product['strap_size_options'] : null],
                    ['label' => 'Water Resistance', 'value' => $product['water_resistance']],
                    ['label' => 'Warranty', 'value' => $product['warranty_years'] . ' Year(s)'],
                ];
                foreach ($specs as $spec):
                    if (!$spec['value']) continue;
                ?>
                <div class="flex border-b border-[#F0F1F3] last:border-b-0">
                    <div class="w-[45%] px-6 py-3.5 bg-[#F7F8FA] font-['Inter'] font-semibold text-[13px] text-[#5A5F6D]"><?= $spec['label'] ?></div>
                    <div class="w-[55%] px-6 py-3.5 font-['Inter'] text-[13px] text-[#1A1A2E]"><?= htmlspecialchars($spec['value']) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if (!empty($features_list)): ?>
        <div class="mt-6 bg-white rounded-xl border border-[#E0E2E7] shadow-[0_2px_12px_rgba(0,0,0,0.08)] p-6">
            <h3 class="font-['Playfair_Display'] font-semibold text-[18px] text-[#1A1A2E] mb-4">Features</h3>
            <div class="flex flex-wrap gap-2">
                <?php foreach ($features_list as $feat): ?>
                <span class="inline-block px-3 py-1.5 bg-[#FFF8E7] border border-[#C9A84C]/20 rounded-full font-['Inter'] text-[12px] text-[#C9A84C] font-medium"><?= htmlspecialchars($feat) ?></span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($related)): ?>
        <div class="mt-12">
            <h2 class="font-['Playfair_Display'] font-bold text-[24px] text-[#1A1A2E] mb-6">You May Also Like</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                <?php foreach ($related as $rp): ?>
                <a href="product-detail.php?slug=<?= urlencode($rp['slug']) ?>" class="group bg-white border border-[#E0E2E7] rounded-xl overflow-hidden hover:shadow-[0_8px_28px_rgba(0,0,0,0.14)] hover:-translate-y-1 transition-all duration-200 block">
                    <div class="relative bg-[#F7F8FA] aspect-square overflow-hidden">
                        <?php if ($rp['main_image']): ?>
                        <img src="../assets/uploads/products/<?= htmlspecialchars(basename($rp['main_image'])) ?>" alt="<?= htmlspecialchars($rp['name']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                        <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center"><svg class="w-12 h-12 text-[#E0E2E7]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="1"/><path stroke-linecap="round" stroke-width="1" d="M12 6v6l4 2"/></svg></div>
                        <?php endif; ?>
                    </div>
                    <div class="p-4">
                        <div class="font-['Inter'] text-[12px] text-[#C9A84C] font-semibold mb-1"><?= htmlspecialchars($rp['brand_name']) ?></div>
                        <h3 class="font-['Inter'] font-semibold text-[14px] text-[#1A1A2E] leading-[1.4] mb-2 line-clamp-2"><?= htmlspecialchars($rp['name']) ?></h3>
                        <span class="font-['Inter'] font-bold text-[18px] text-[#1A1A2E]">NPR <?= number_format($rp['price'], 0) ?></span>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>