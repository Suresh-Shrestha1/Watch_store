<?php
session_start();

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

require_once '../config/db.php';

$page_title = 'Products';

$success = $_SESSION['product_success'] ?? '';
$error   = $_SESSION['product_error'] ?? '';
unset($_SESSION['product_success'], $_SESSION['product_error']);

// Search and filter
$search = trim($_GET['search'] ?? '');
$filter_brand = intval($_GET['brand'] ?? 0);
$filter_gender = trim($_GET['gender'] ?? '');
$filter_status = $_GET['status'] ?? '';

// Build query
$query = "
    SELECT p.*, b.name as brand_name,
    (SELECT image_url FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image
    FROM products p
    LEFT JOIN brands b ON p.brand_id = b.id
    WHERE 1=1
";
$params = [];
$types = "";

if (!empty($search)) {
    $query .= " AND (p.name LIKE ? OR p.model_number LIKE ?)";
    $search_param = "%" . $search . "%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}

if ($filter_brand > 0) {
    $query .= " AND p.brand_id = ?";
    $params[] = $filter_brand;
    $types .= "i";
}

if (!empty($filter_gender)) {
    $query .= " AND p.gender = ?";
    $params[] = $filter_gender;
    $types .= "s";
}

if ($filter_status !== '') {
    $query .= " AND p.is_active = ?";
    $params[] = intval($filter_status);
    $types .= "i";
}

$query .= " ORDER BY p.created_at DESC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}
$stmt->close();

// Get all brands for filter dropdown
$brands_result = $conn->query("SELECT id, name FROM brands ORDER BY name ASC");
$brands = [];
while ($row = $brands_result->fetch_assoc()) {
    $brands[] = $row;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> — ChronoNest Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen" style="background-color: #F7F8FA; font-family: 'Inter', sans-serif;">

    <?php include 'includes/sidebar.php'; ?>
    <?php include 'includes/topbar.php'; ?>

    <main class="lg:ml-64 pt-16">
        <div class="p-4 lg:p-6">

            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h2 class="font-bold text-2xl mb-1" style="font-family: 'Playfair Display', serif; color: #1A1A2E;">Products</h2>
                    <p class="text-sm" style="color: #5A5F6D;"><?php echo count($products); ?> products found</p>
                </div>
                <a href="add-product.php" class="inline-flex items-center h-10 px-5 rounded-lg text-sm font-semibold text-white no-underline transition-all duration-200" style="background-color: #1B2A4A;" onmouseover="this.style.backgroundColor='#2C4066';" onmouseout="this.style.backgroundColor='#1B2A4A';">+ Add Product</a>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg p-4 mb-4" style="border: 1px solid #E0E2E7;">
                <form method="GET" action="products.php" class="flex flex-col sm:flex-row flex-wrap gap-3">

                    <!-- Search -->
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by name or model..." class="h-10 px-4 rounded-lg text-sm flex-1 min-w-0 outline-none" style="border: 1px solid #E0E2E7; color: #1A1A2E;" onfocus="this.style.border='2px solid #C9A84C'; this.style.boxShadow='0 0 0 3px rgba(201,168,76,0.15)';" onblur="this.style.border='1px solid #E0E2E7'; this.style.boxShadow='none';">

                    <!-- Brand Filter -->
                    <select name="brand" class="h-10 px-3 rounded-lg text-sm outline-none" style="border: 1px solid #E0E2E7; color: #1A1A2E; min-width: 140px;">
                        <option value="0">All Brands</option>
                        <?php foreach ($brands as $b): ?>
                            <option value="<?php echo $b['id']; ?>" <?php echo ($filter_brand == $b['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($b['name']); ?></option>
                        <?php endforeach; ?>
                    </select>

                    <!-- Gender Filter -->
                    <select name="gender" class="h-10 px-3 rounded-lg text-sm outline-none" style="border: 1px solid #E0E2E7; color: #1A1A2E; min-width: 120px;">
                        <option value="">All Genders</option>
                        <option value="Men" <?php echo ($filter_gender === 'Men') ? 'selected' : ''; ?>>Men</option>
                        <option value="Women" <?php echo ($filter_gender === 'Women') ? 'selected' : ''; ?>>Women</option>
                        <option value="Unisex" <?php echo ($filter_gender === 'Unisex') ? 'selected' : ''; ?>>Unisex</option>
                        <option value="Kids" <?php echo ($filter_gender === 'Kids') ? 'selected' : ''; ?>>Kids</option>
                    </select>

                    <!-- Status Filter -->
                    <select name="status" class="h-10 px-3 rounded-lg text-sm outline-none" style="border: 1px solid #E0E2E7; color: #1A1A2E; min-width: 120px;">
                        <option value="">All Status</option>
                        <option value="1" <?php echo ($filter_status === '1') ? 'selected' : ''; ?>>Active</option>
                        <option value="0" <?php echo ($filter_status === '0') ? 'selected' : ''; ?>>Inactive</option>
                    </select>

                    <!-- Filter Button -->
                    <button type="submit" class="h-10 px-5 rounded-lg text-sm font-semibold text-white" style="background-color: #1B2A4A; border: none; cursor: pointer;">Filter</button>

                    <!-- Clear Filters -->
                    <?php if (!empty($search) || $filter_brand > 0 || !empty($filter_gender) || $filter_status !== ''): ?>
                        <a href="products.php" class="h-10 px-4 rounded-lg text-sm font-medium no-underline inline-flex items-center" style="color: #5A5F6D; border: 1px solid #E0E2E7;">Clear</a>
                    <?php endif; ?>

                </form>
            </div>

            <!-- Messages -->
            <?php if (!empty($success)): ?>
                <div class="rounded-lg px-4 py-3 mb-4 text-sm" style="background-color: #E8F5E9; color: #2E7D32; border: 1px solid rgba(46,125,50,0.2);">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="rounded-lg px-4 py-3 mb-4 text-sm" style="background-color: #FDEAEA; color: #D64545; border: 1px solid rgba(214,69,69,0.2);">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Products Table -->
            <div class="bg-white rounded-lg overflow-hidden" style="border: 1px solid #E0E2E7;">

                <?php if (empty($products)): ?>

                    <div class="p-10 text-center">
                        <p class="text-base font-semibold mb-1" style="color: #1A1A2E;">No products found</p>
                        <p class="text-sm mb-4" style="color: #5A5F6D;">Try adjusting your filters or add a new product.</p>
                        <a href="add-product.php" class="inline-flex items-center h-10 px-5 rounded-lg text-sm font-semibold text-white no-underline" style="background-color: #1B2A4A;">+ Add Product</a>
                    </div>

                <?php else: ?>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead>
                                <tr style="background-color: #F7F8FA; border-bottom: 1px solid #E0E2E7;">
                                    <th class="px-4 py-3 font-semibold" style="color: #5A5F6D;">Product</th>
                                    <th class="px-4 py-3 font-semibold hidden md:table-cell" style="color: #5A5F6D;">Brand</th>
                                    <th class="px-4 py-3 font-semibold hidden sm:table-cell" style="color: #5A5F6D;">Price</th>
                                    <th class="px-4 py-3 font-semibold hidden lg:table-cell" style="color: #5A5F6D;">Stock</th>
                                    <th class="px-4 py-3 font-semibold" style="color: #5A5F6D;">Status</th>
                                    <th class="px-4 py-3 font-semibold text-right" style="color: #5A5F6D;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                    <tr style="border-bottom: 1px solid #E0E2E7;">

                                        <!-- Product Name + Image + Model -->
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-3">
                                                <?php if (!empty($product['main_image'])): ?>
                                                    <img src="../assets/uploads/products/<?php echo htmlspecialchars(basename($product['main_image'])); ?>" alt="" class="w-10 h-10 rounded object-cover flex-shrink-0" style="border: 1px solid #E0E2E7;">
                                                <?php else: ?>
                                                    <div class="w-10 h-10 rounded flex items-center justify-center flex-shrink-0 text-xs" style="background-color: #F7F8FA; border: 1px solid #E0E2E7; color: #8A8F99;">No img</div>
                                                <?php endif; ?>
                                                <div>
                                                    <p class="font-medium leading-tight" style="color: #1A1A2E;"><?php echo htmlspecialchars($product['name']); ?></p>
                                                    <p class="text-xs mt-0.5" style="color: #8A8F99;"><?php echo htmlspecialchars($product['model_number']); ?></p>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Brand -->
                                        <td class="px-4 py-3 hidden md:table-cell" style="color: #5A5F6D;">
                                            <?php echo htmlspecialchars($product['brand_name'] ?? 'N/A'); ?>
                                        </td>

                                        <!-- Price -->
                                        <td class="px-4 py-3 hidden sm:table-cell font-semibold" style="color: #1A1A2E;">
                                            Rs. <?php echo number_format($product['price'], 2); ?>
                                        </td>

                                        <!-- Stock -->
                                        <td class="px-4 py-3 hidden lg:table-cell">
                                            <?php if ($product['stock_quantity'] <= 0): ?>
                                                <span class="inline-block px-2.5 py-1 rounded text-xs font-semibold" style="background-color: #FDEAEA; color: #D64545;">Out of Stock</span>
                                            <?php elseif ($product['stock_quantity'] <= 5): ?>
                                                <span class="inline-block px-2.5 py-1 rounded text-xs font-semibold" style="background-color: #FFF3E0; color: #E65100;">Low (<?php echo $product['stock_quantity']; ?>)</span>
                                            <?php else: ?>
                                                <span class="text-sm" style="color: #2E7D32;"><?php echo $product['stock_quantity']; ?> in stock</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Status -->
                                        <td class="px-4 py-3">
                                            <?php if ($product['is_active']): ?>
                                                <span class="inline-block px-2.5 py-1 rounded text-xs font-semibold" style="background-color: #E8F5E9; color: #2E7D32;">Active</span>
                                            <?php else: ?>
                                                <span class="inline-block px-2.5 py-1 rounded text-xs font-semibold" style="background-color: #FDEAEA; color: #D64545;">Inactive</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Actions -->
                                        <td class="px-4 py-3 text-right">
                                            <div class="flex items-center justify-end gap-2">

                                                <form method="POST" action="actions/product-action.php" class="inline">
                                                    <input type="hidden" name="action" value="toggle_status">
                                                    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                                    <input type="hidden" name="current_status" value="<?php echo $product['is_active']; ?>">
                                                    <button type="submit" class="text-xs font-medium px-2.5 py-1 rounded transition-all duration-200" style="border: 1px solid #E0E2E7; color: #5A5F6D; background: none; cursor: pointer;" onmouseover="this.style.backgroundColor='#F7F8FA';" onmouseout="this.style.backgroundColor='transparent';">
                                                        <?php echo $product['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                                    </button>
                                                </form>

                                                <a href="edit-product.php?id=<?php echo $product['id']; ?>" class="text-xs font-medium px-2.5 py-1 rounded no-underline transition-all duration-200" style="border: 1px solid #E0E2E7; color: #1B2A4A;" onmouseover="this.style.backgroundColor='#F7F8FA';" onmouseout="this.style.backgroundColor='transparent';">Edit</a>

                                                <form method="POST" action="actions/product-action.php" class="inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                                    <button type="submit" class="text-xs font-medium px-2.5 py-1 rounded transition-all duration-200" style="border: 1px solid rgba(214,69,69,0.3); color: #D64545; background: none; cursor: pointer;" onmouseover="this.style.backgroundColor='#FDEAEA';" onmouseout="this.style.backgroundColor='transparent';">Delete</button>
                                                </form>

                                            </div>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                <?php endif; ?>

            </div>

        </div>
    </main>

    <script src = "../assets/js/admin.js"></script>

</body>
</html>