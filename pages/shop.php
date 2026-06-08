<?php
session_start();
require_once '../config/db.php';

$page_title = "Shop Watches – ChronoNest";

// Get filter values
$brand_filter = isset($_GET['brand']) ? trim($_GET['brand']) : '';
$gender_filter = isset($_GET['gender']) ? trim($_GET['gender']) : '';
$movement_filter = isset($_GET['movement']) ? trim($_GET['movement']) : '';
$strap_filter = isset($_GET['strap_material']) ? trim($_GET['strap_material']) : '';
$dial_color_filter = isset($_GET['dial_color']) ? trim($_GET['dial_color']) : '';
$water_filter = isset($_GET['water_resistance']) ? trim($_GET['water_resistance']) : '';
$min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 0;
$sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'newest';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = 12;
$offset = ($page - 1) * $per_page;

// Build query
$where = ["p.is_active = 1"];
$params = [];
$types = "";

if ($brand_filter) {
    $where[] = "b.slug = ?";
    $params[] = $brand_filter;
    $types .= "s";
}
if ($gender_filter) {
    $where[] = "p.gender = ?";
    $params[] = $gender_filter;
    $types .= "s";
}
if ($movement_filter) {
    $where[] = "p.movement_type = ?";
    $params[] = $movement_filter;
    $types .= "s";
}
if ($strap_filter) {
    $where[] = "p.strap_material = ?";
    $params[] = $strap_filter;
    $types .= "s";
}
if ($dial_color_filter) {
    $where[] = "p.dial_color = ?";
    $params[] = $dial_color_filter;
    $types .= "s";
}
if ($water_filter) {
    $where[] = "p.water_resistance = ?";
    $params[] = $water_filter;
    $types .= "s";
}
if ($min_price > 0) {
    $where[] = "p.price >= ?";
    $params[] = $min_price;
    $types .= "d";
}
if ($max_price > 0) {
    $where[] = "p.price <= ?";
    $params[] = $max_price;
    $types .= "d";
}

$where_sql = implode(" AND ", $where);

$order_sql = match($sort) {
    'price_asc' => 'p.price ASC',
    'price_desc' => 'p.price DESC',
    'name_asc' => 'p.name ASC',
    'name_desc' => 'p.name DESC',
    'oldest' => 'p.created_at ASC',
    default => 'p.created_at DESC'
};

// Count total
$count_sql = "SELECT COUNT(*) as total FROM products p JOIN brands b ON p.brand_id = b.id WHERE $where_sql";
$count_stmt = mysqli_prepare($conn, $count_sql);
if ($types) mysqli_stmt_bind_param($count_stmt, $types, ...$params);
mysqli_stmt_execute($count_stmt);
$total_products = mysqli_fetch_assoc(mysqli_stmt_get_result($count_stmt))['total'];
$total_pages = ceil($total_products / $per_page);

// Fetch products
$products_sql = "SELECT p.id, p.name, p.slug, p.price, p.gender, p.movement_type, p.stock_quantity, p.case_diameter_mm, p.water_resistance, b.name as brand_name, b.slug as brand_slug,
    (SELECT image_url FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image
    FROM products p JOIN brands b ON p.brand_id = b.id
    WHERE $where_sql ORDER BY $order_sql LIMIT ? OFFSET ?";
$products_stmt = mysqli_prepare($conn, $products_sql);
$p_types = $types . "ii";
$p_params = array_merge($params, [$per_page, $offset]);
mysqli_stmt_bind_param($products_stmt, $p_types, ...$p_params);
mysqli_stmt_execute($products_stmt);
$products = mysqli_fetch_all(mysqli_stmt_get_result($products_stmt), MYSQLI_ASSOC);

// Fetch filter options
$brands_list = mysqli_fetch_all(mysqli_query($conn, "SELECT name, slug FROM brands WHERE is_active = 1 ORDER BY name ASC"), MYSQLI_ASSOC);
$strap_materials = mysqli_fetch_all(mysqli_query($conn, "SELECT DISTINCT strap_material FROM products WHERE is_active = 1 AND strap_material IS NOT NULL AND strap_material != '' ORDER BY strap_material ASC"), MYSQLI_ASSOC);
$dial_colors = mysqli_fetch_all(mysqli_query($conn, "SELECT DISTINCT dial_color FROM products WHERE is_active = 1 AND dial_color IS NOT NULL AND dial_color != '' ORDER BY dial_color ASC"), MYSQLI_ASSOC);
$water_options = mysqli_fetch_all(mysqli_query($conn, "SELECT DISTINCT water_resistance FROM products WHERE is_active = 1 AND water_resistance IS NOT NULL AND water_resistance != '' ORDER BY water_resistance ASC"), MYSQLI_ASSOC);

// Build current query string for pagination
function buildQuery($overrides = []) {
    $params = $_GET;
    foreach ($overrides as $k => $v) {
        if ($v === '' || $v === null) unset($params[$k]);
        else $params[$k] = $v;
    }
    return http_build_query($params);
}

// Active filter label
$brand_name_display = '';
if ($brand_filter) {
    foreach ($brands_list as $bl) {
        if ($bl['slug'] === $brand_filter) { $brand_name_display = $bl['name']; break; }
    }
}

require_once '../includes/header.php';
?>

<!-- PAGE HEADER -->
<section class="bg-[#1B2A4A] py-10">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
            <div>
                <span class="font-['Inter'] font-semibold text-[11px] uppercase tracking-[1.5px] text-[#C9A84C]">Browse Collection</span>
                <h1 class="font-['Playfair_Display'] font-bold text-[32px] md:text-[40px] text-white mt-1">
                    <?php if ($brand_name_display): ?>
                        <?= htmlspecialchars($brand_name_display) ?> Watches
                    <?php elseif ($gender_filter): ?>
                        <?= htmlspecialchars($gender_filter) ?>'s Watches
                    <?php elseif ($movement_filter): ?>
                        <?= htmlspecialchars($movement_filter) ?> Watches
                    <?php else: ?>
                        All Watches
                    <?php endif; ?>
                </h1>
                <p class="font-['Inter'] text-[14px] text-[#B0B8C9] mt-1"><?= number_format($total_products) ?> product<?= $total_products !== 1 ? 's' : '' ?> found</p>
            </div>
            <div class="flex items-center gap-3">
                <label class="font-['Inter'] text-[13px] text-[#B0B8C9]">Sort by:</label>
                <select onchange="window.location.href='shop.php?<?= buildQuery(['sort' => '']) ?>&sort='+this.value" class="h-[38px] px-3 bg-white/10 border border-white/20 rounded-lg font-['Inter'] text-[13px] text-white focus:outline-none focus:border-[#C9A84C] appearance-none cursor-pointer">
                    <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?> class="text-[#1A1A2E]">Newest First</option>
                    <option value="oldest" <?= $sort === 'oldest' ? 'selected' : '' ?> class="text-[#1A1A2E]">Oldest First</option>
                    <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?> class="text-[#1A1A2E]">Price: Low → High</option>
                    <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?> class="text-[#1A1A2E]">Price: High → Low</option>
                    <option value="name_asc" <?= $sort === 'name_asc' ? 'selected' : '' ?> class="text-[#1A1A2E]">Name: A → Z</option>
                    <option value="name_desc" <?= $sort === 'name_desc' ? 'selected' : '' ?> class="text-[#1A1A2E]">Name: Z → A</option>
                </select>
            </div>
        </div>
    </div>
</section>

<!-- BREADCRUMB -->
<div class="bg-white border-b border-[#E0E2E7]">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-3">
        <nav class="flex items-center gap-2 font-['Inter'] text-[13px] text-[#5A5F6D]">
            <a href="../index.php" class="hover:text-[#C9A84C] transition-colors">Home</a>
            <svg class="w-3 h-3 text-[#8A8F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-[#1A1A2E] font-medium">Shop</span>
            <?php if ($brand_name_display): ?>
            <svg class="w-3 h-3 text-[#8A8F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-[#1A1A2E] font-medium"><?= htmlspecialchars($brand_name_display) ?></span>
            <?php endif; ?>
        </nav>
    </div>
</div>

<section class="py-8 bg-[#F7F8FA] min-h-screen">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-8">

            <!-- SIDEBAR FILTERS -->
            <aside class="w-full lg:w-[260px] flex-shrink-0">
                <div class="bg-white rounded-xl border border-[#E0E2E7] shadow-[0_2px_12px_rgba(0,0,0,0.08)] p-5 sticky top-[100px]">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="font-['Inter'] font-semibold text-[16px] text-[#1A1A2E]">Filters</h3>
                        <?php if ($brand_filter || $gender_filter || $movement_filter || $strap_filter || $dial_color_filter || $water_filter || $min_price || $max_price): ?>
                        <a href="shop.php" class="font-['Inter'] text-[12px] text-[#D64545] hover:underline">Clear All</a>
                        <?php endif; ?>
                    </div>

                    <form method="GET" action="shop.php">
                        <?php if ($sort !== 'newest'): ?><input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>"><?php endif; ?>

                        <!-- Brand -->
                        <div class="mb-5">
                            <label class="block font-['Inter'] font-semibold text-[13px] text-[#1A1A2E] mb-2">Brand</label>
                            <select name="brand" class="w-full h-[38px] px-3 bg-white border border-[#E0E2E7] rounded-lg font-['Inter'] text-[13px] text-[#1A1A2E] focus:outline-none focus:border-[#C9A84C] appearance-none cursor-pointer">
                                <option value="">All Brands</option>
                                <?php foreach ($brands_list as $bl): ?>
                                <option value="<?= htmlspecialchars($bl['slug']) ?>" <?= $brand_filter === $bl['slug'] ? 'selected' : '' ?>><?= htmlspecialchars($bl['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Gender -->
                        <div class="mb-5">
                            <label class="block font-['Inter'] font-semibold text-[13px] text-[#1A1A2E] mb-2">Gender</label>
                            <select name="gender" class="w-full h-[38px] px-3 bg-white border border-[#E0E2E7] rounded-lg font-['Inter'] text-[13px] text-[#1A1A2E] focus:outline-none focus:border-[#C9A84C] appearance-none cursor-pointer">
                                <option value="">All</option>
                                <?php foreach (['Men','Women','Unisex','Kids'] as $g): ?>
                                <option value="<?= $g ?>" <?= $gender_filter === $g ? 'selected' : '' ?>><?= $g ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Movement -->
                        <div class="mb-5">
                            <label class="block font-['Inter'] font-semibold text-[13px] text-[#1A1A2E] mb-2">Movement</label>
                            <select name="movement" class="w-full h-[38px] px-3 bg-white border border-[#E0E2E7] rounded-lg font-['Inter'] text-[13px] text-[#1A1A2E] focus:outline-none focus:border-[#C9A84C] appearance-none cursor-pointer">
                                <option value="">All</option>
                                <?php foreach (['Automatic','Quartz','Mechanical','Solar','Kinetic','Digital','Smartwatch'] as $m): ?>
                                <option value="<?= $m ?>" <?= $movement_filter === $m ? 'selected' : '' ?>><?= $m ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Strap Material -->
                        <div class="mb-5">
                            <label class="block font-['Inter'] font-semibold text-[13px] text-[#1A1A2E] mb-2">Strap Material</label>
                            <select name="strap_material" class="w-full h-[38px] px-3 bg-white border border-[#E0E2E7] rounded-lg font-['Inter'] text-[13px] text-[#1A1A2E] focus:outline-none focus:border-[#C9A84C] appearance-none cursor-pointer">
                                <option value="">All</option>
                                <?php foreach ($strap_materials as $sm): ?>
                                <option value="<?= htmlspecialchars($sm['strap_material']) ?>" <?= $strap_filter === $sm['strap_material'] ? 'selected' : '' ?>><?= htmlspecialchars($sm['strap_material']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Dial Color -->
                        <div class="mb-5">
                            <label class="block font-['Inter'] font-semibold text-[13px] text-[#1A1A2E] mb-2">Dial Color</label>
                            <select name="dial_color" class="w-full h-[38px] px-3 bg-white border border-[#E0E2E7] rounded-lg font-['Inter'] text-[13px] text-[#1A1A2E] focus:outline-none focus:border-[#C9A84C] appearance-none cursor-pointer">
                                <option value="">All</option>
                                <?php foreach ($dial_colors as $dc): ?>
                                <option value="<?= htmlspecialchars($dc['dial_color']) ?>" <?= $dial_color_filter === $dc['dial_color'] ? 'selected' : '' ?>><?= htmlspecialchars($dc['dial_color']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Water Resistance -->
                        <div class="mb-5">
                            <label class="block font-['Inter'] font-semibold text-[13px] text-[#1A1A2E] mb-2">Water Resistance</label>
                            <select name="water_resistance" class="w-full h-[38px] px-3 bg-white border border-[#E0E2E7] rounded-lg font-['Inter'] text-[13px] text-[#1A1A2E] focus:outline-none focus:border-[#C9A84C] appearance-none cursor-pointer">
                                <option value="">All</option>
                                <?php foreach ($water_options as $wo): ?>
                                <option value="<?= htmlspecialchars($wo['water_resistance']) ?>" <?= $water_filter === $wo['water_resistance'] ? 'selected' : '' ?>><?= htmlspecialchars($wo['water_resistance']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Price Range -->
                        <div class="mb-5">
                            <label class="block font-['Inter'] font-semibold text-[13px] text-[#1A1A2E] mb-2">Price Range (NPR)</label>
                            <div class="flex items-center gap-2">
                                <input type="number" name="min_price" value="<?= $min_price > 0 ? $min_price : '' ?>" placeholder="Min" class="w-full h-[38px] px-3 bg-white border border-[#E0E2E7] rounded-lg font-['Inter'] text-[13px] text-[#1A1A2E] placeholder-[#8A8F99] focus:outline-none focus:border-[#C9A84C]">
                                <span class="text-[#8A8F99]">–</span>
                                <input type="number" name="max_price" value="<?= $max_price > 0 ? $max_price : '' ?>" placeholder="Max" class="w-full h-[38px] px-3 bg-white border border-[#E0E2E7] rounded-lg font-['Inter'] text-[13px] text-[#1A1A2E] placeholder-[#8A8F99] focus:outline-none focus:border-[#C9A84C]">
                            </div>
                        </div>

                        <button type="submit" class="w-full h-[40px] bg-[#1B2A4A] hover:bg-[#2C4066] text-white font-['Inter'] font-semibold text-[14px] rounded-lg transition-all duration-200">Apply Filters</button>
                    </form>
                </div>
            </aside>

            <!-- PRODUCT GRID -->
            <div class="flex-1">
                <!-- Active Filters -->
                <?php
                $active_filters = [];
                if ($brand_name_display) $active_filters[] = ['label' => "Brand: $brand_name_display", 'key' => 'brand'];
                if ($gender_filter) $active_filters[] = ['label' => "Gender: $gender_filter", 'key' => 'gender'];
                if ($movement_filter) $active_filters[] = ['label' => "Movement: $movement_filter", 'key' => 'movement'];
                if ($strap_filter) $active_filters[] = ['label' => "Strap: $strap_filter", 'key' => 'strap_material'];
                if ($dial_color_filter) $active_filters[] = ['label' => "Dial: $dial_color_filter", 'key' => 'dial_color'];
                if ($water_filter) $active_filters[] = ['label' => "Water: $water_filter", 'key' => 'water_resistance'];
                if ($min_price > 0) $active_filters[] = ['label' => "Min: NPR " . number_format($min_price, 0), 'key' => 'min_price'];
                if ($max_price > 0) $active_filters[] = ['label' => "Max: NPR " . number_format($max_price, 0), 'key' => 'max_price'];
                ?>
                <?php if (!empty($active_filters)): ?>
                <div class="flex flex-wrap items-center gap-2 mb-6">
                    <span class="font-['Inter'] text-[12px] text-[#5A5F6D]">Active:</span>
                    <?php foreach ($active_filters as $af): ?>
                    <a href="shop.php?<?= buildQuery([$af['key'] => '']) ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#FFF8E7] border border-[#C9A84C]/30 rounded-full font-['Inter'] text-[12px] text-[#C9A84C] hover:bg-[#C9A84C] hover:text-white transition-all duration-150">
                        <?= htmlspecialchars($af['label']) ?>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php if (!empty($products)): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    <?php foreach ($products as $product): ?>
                    <a href="product-detail.php?slug=<?= urlencode($product['slug']) ?>" class="group bg-white border border-[#E0E2E7] rounded-xl overflow-hidden hover:shadow-[0_8px_28px_rgba(0,0,0,0.14)] hover:-translate-y-1 transition-all duration-200 block">
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
                                <?php else: ?>
                                <span class="inline-block px-2.5 py-1 rounded bg-[#E8F5E9] text-[#2E7D32] font-['Inter'] font-semibold text-[11px]">In Stock</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="font-['Inter'] text-[12px] text-[#C9A84C] font-semibold mb-1"><?= htmlspecialchars($product['brand_name']) ?></div>
                            <h3 class="font-['Inter'] font-semibold text-[15px] text-[#1A1A2E] leading-[1.4] mb-2 line-clamp-2 group-hover:text-[#1B2A4A]"><?= htmlspecialchars($product['name']) ?></h3>
                            <div class="flex items-center gap-2 mb-3 flex-wrap">
                                <span class="font-['Inter'] text-[11px] text-[#5A5F6D] bg-[#F7F8FA] px-2 py-0.5 rounded"><?= htmlspecialchars($product['gender']) ?></span>
                                <span class="font-['Inter'] text-[11px] text-[#5A5F6D] bg-[#F7F8FA] px-2 py-0.5 rounded"><?= htmlspecialchars($product['movement_type']) ?></span>
                                <?php if ($product['water_resistance']): ?>
                                <span class="font-['Inter'] text-[11px] text-[#5A5F6D] bg-[#F7F8FA] px-2 py-0.5 rounded"><?= htmlspecialchars($product['water_resistance']) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="font-['Inter'] font-bold text-[20px] text-[#1A1A2E]">NPR <?= number_format($product['price'], 0) ?></span>
                                <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-[#1B2A4A] group-hover:bg-[#C9A84C] transition-colors duration-200"><svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg></span>
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>

                <!-- PAGINATION -->
                <?php if ($total_pages > 1): ?>
                <div class="flex items-center justify-center gap-2 mt-10">
                    <?php if ($page > 1): ?>
                    <a href="shop.php?<?= buildQuery(['page' => $page - 1]) ?>" class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-[#E0E2E7] bg-white hover:border-[#C9A84C] text-[#5A5F6D] hover:text-[#C9A84C] transition-all duration-150"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></a>
                    <?php endif; ?>
                    <?php
                    $start_pg = max(1, $page - 2);
                    $end_pg = min($total_pages, $page + 2);
                    if ($start_pg > 1): ?>
                    <a href="shop.php?<?= buildQuery(['page' => 1]) ?>" class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-[#E0E2E7] bg-white hover:border-[#C9A84C] font-['Inter'] text-[14px] text-[#5A5F6D] hover:text-[#C9A84C] transition-all duration-150">1</a>
                    <?php if ($start_pg > 2): ?><span class="font-['Inter'] text-[14px] text-[#8A8F99] px-1">...</span><?php endif; ?>
                    <?php endif; ?>
                    <?php for ($i = $start_pg; $i <= $end_pg; $i++): ?>
                    <a href="shop.php?<?= buildQuery(['page' => $i]) ?>" class="inline-flex items-center justify-center w-10 h-10 rounded-lg border font-['Inter'] text-[14px] transition-all duration-150 <?= $i === $page ? 'bg-[#C9A84C] border-[#C9A84C] text-white font-semibold' : 'border-[#E0E2E7] bg-white text-[#5A5F6D] hover:border-[#C9A84C] hover:text-[#C9A84C]' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                    <?php if ($end_pg < $total_pages): ?>
                    <?php if ($end_pg < $total_pages - 1): ?><span class="font-['Inter'] text-[14px] text-[#8A8F99] px-1">...</span><?php endif; ?>
                    <a href="shop.php?<?= buildQuery(['page' => $total_pages]) ?>" class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-[#E0E2E7] bg-white hover:border-[#C9A84C] font-['Inter'] text-[14px] text-[#5A5F6D] hover:text-[#C9A84C] transition-all duration-150"><?= $total_pages ?></a>
                    <?php endif; ?>
                    <?php if ($page < $total_pages): ?>
                    <a href="shop.php?<?= buildQuery(['page' => $page + 1]) ?>" class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-[#E0E2E7] bg-white hover:border-[#C9A84C] text-[#5A5F6D] hover:text-[#C9A84C] transition-all duration-150"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php else: ?>
                <div class="flex flex-col items-center justify-center py-20 text-center">
                    <svg class="w-20 h-20 text-[#E0E2E7] mb-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="1"/><path stroke-linecap="round" stroke-width="1" d="M12 6v6l4 2"/></svg>
                    <h3 class="font-['Playfair_Display'] font-semibold text-[22px] text-[#1A1A2E] mb-2">No watches found</h3>
                    <p class="font-['Inter'] text-[14px] text-[#5A5F6D] mb-6 max-w-sm">Try adjusting your filters or browse our entire collection.</p>
                    <a href="shop.php" class="inline-flex items-center gap-2 h-[40px] px-6 bg-[#C9A84C] hover:bg-[#B8953F] text-white font-['Inter'] font-semibold text-[14px] rounded-lg transition-all duration-200">Clear All Filters</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>