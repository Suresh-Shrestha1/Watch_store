<?php
session_start();

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

require_once '../config/db.php';

$page_title = 'Edit Product';

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['product_error'] = 'Invalid product.';
    header('Location: products.php');
    exit();
}

// Fetch product
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['product_error'] = 'Product not found.';
    $stmt->close();
    $conn->close();
    header('Location: products.php');
    exit();
}

$product = $result->fetch_assoc();
$stmt->close();

// Fetch product images
$img_stmt = $conn->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order ASC");
$img_stmt->bind_param("i", $id);
$img_stmt->execute();
$img_result = $img_stmt->get_result();
$images = [];
while ($row = $img_result->fetch_assoc()) {
    $images[] = $row;
}
$img_stmt->close();

// Fetch brands
$brands_result = $conn->query("SELECT id, name FROM brands WHERE is_active = 1 ORDER BY name ASC");
$brands = [];
while ($row = $brands_result->fetch_assoc()) {
    $brands[] = $row;
}

$conn->close();

$success = $_SESSION['product_success'] ?? '';
$error   = $_SESSION['product_error'] ?? '';
unset($_SESSION['product_success'], $_SESSION['product_error']);
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

            <!-- Messages -->
            <?php if (!empty($success)): ?>
                <div class="rounded-lg px-4 py-3 mb-4 text-sm max-w-4xl" style="background-color: #E8F5E9; color: #2E7D32; border: 1px solid rgba(46,125,50,0.2);">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="rounded-lg px-4 py-3 mb-4 text-sm max-w-4xl" style="background-color: #FDEAEA; color: #D64545; border: 1px solid rgba(214,69,69,0.2);">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="actions/product-action.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" value="<?php echo $product['id']; ?>">

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

                    <!-- Left Column -->
                    <div class="lg:col-span-2 space-y-4">

                        <!-- Basic Info -->
                        <div class="bg-white rounded-lg p-5" style="border: 1px solid #E0E2E7;">
                            <h3 class="font-semibold text-base mb-4" style="color: #1A1A2E;">Basic Information</h3>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-semibold mb-1.5" style="color: #1A1A2E;">Product Name <span style="color: #D64545;">*</span></label>
                                    <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required class="w-full h-10 px-4 rounded-lg text-sm outline-none transition-all duration-200" style="border: 1px solid #E0E2E7; color: #1A1A2E;" onfocus="this.style.border='2px solid #C9A84C'; this.style.boxShadow='0 0 0 3px rgba(201,168,76,0.15)';" onblur="this.style.border='1px solid #E0E2E7'; this.style.boxShadow='none';">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold mb-1.5" style="color: #1A1A2E;">Model Number <span style="color: #D64545;">*</span></label>
                                    <input type="text" name="model_number" value="<?php echo htmlspecialchars($product['model_number']); ?>" required class="w-full h-10 px-4 rounded-lg text-sm outline-none" style="border: 1px solid #E0E2E7; color: #1A1A2E;">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold mb-1.5" style="color: #1A1A2E;">Brand <span style="color: #D64545;">*</span></label>
                                    <select name="brand_id" required class="w-full h-10 px-3 rounded-lg text-sm outline-none" style="border: 1px solid #E0E2E7; color: #1A1A2E;">
                                        <option value="">Select Brand</option>
                                        <?php foreach ($brands as $b): ?>
                                            <option value="<?php echo $b['id']; ?>" <?php echo ($product['brand_id'] == $b['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($b['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold mb-1.5" style="color: #1A1A2E;">Gender <span style="color: #D64545;">*</span></label>
                                    <select name="gender" required class="w-full h-10 px-3 rounded-lg text-sm outline-none" style="border: 1px solid #E0E2E7; color: #1A1A2E;">
                                        <?php foreach (['Men', 'Women', 'Unisex', 'Kids'] as $g): ?>
                                            <option value="<?php echo $g; ?>" <?php echo ($product['gender'] === $g) ? 'selected' : ''; ?>><?php echo $g; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold mb-1.5" style="color: #1A1A2E;">Movement Type</label>
                                    <select name="movement_type" class="w-full h-10 px-3 rounded-lg text-sm outline-none" style="border: 1px solid #E0E2E7; color: #1A1A2E;">
                                        <?php foreach (['-', 'Automatic', 'Quartz', 'Mechanical', 'Solar', 'Kinetic', 'Digital', 'Smartwatch'] as $m): ?>
                                            <option value="<?php echo $m; ?>" <?php echo ($product['movement_type'] === $m) ? 'selected' : ''; ?>><?php echo $m; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-semibold mb-1.5" style="color: #1A1A2E;">Description</label>
                                    <textarea name="description" rows="4" class="w-full px-4 py-3 rounded-lg text-sm outline-none resize-y" style="border: 1px solid #E0E2E7; color: #1A1A2E;"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                                </div>

                            </div>
                        </div>

                        <!-- Dial & Case -->
                        <div class="bg-white rounded-lg p-5" style="border: 1px solid #E0E2E7;">
                            <h3 class="font-semibold text-base mb-4" style="color: #1A1A2E;">Dial & Case</h3>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold mb-1.5" style="color: #1A1A2E;">Dial Shape</label>
                                    <select name="dial_shape" class="w-full h-10 px-3 rounded-lg text-sm outline-none" style="border: 1px solid #E0E2E7; color: #1A1A2E;">
                                        <?php foreach (['Round', 'Square', 'Rectangular', 'Oval', 'Tonneau'] as $ds): ?>
                                            <option value="<?php echo $ds; ?>" <?php echo ($product['dial_shape'] === $ds) ? 'selected' : ''; ?>><?php echo $ds; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold mb-1.5" style="color: #1A1A2E;">Dial Color</label>
                                    <input type="text" name="dial_color" value="<?php echo htmlspecialchars($product['dial_color'] ?? ''); ?>" class="w-full h-10 px-4 rounded-lg text-sm outline-none" style="border: 1px solid #E0E2E7; color: #1A1A2E;">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold mb-1.5" style="color: #1A1A2E;">Case Diameter (mm)</label>
                                    <input type="number" step="0.1" name="case_diameter_mm" value="<?php echo htmlspecialchars($product['case_diameter_mm'] ?? ''); ?>" class="w-full h-10 px-4 rounded-lg text-sm outline-none" style="border: 1px solid #E0E2E7; color: #1A1A2E;">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold mb-1.5" style="color: #1A1A2E;">Case Material</label>
                                    <input type="text" name="case_material" value="<?php echo htmlspecialchars($product['case_material'] ?? ''); ?>" class="w-full h-10 px-4 rounded-lg text-sm outline-none" style="border: 1px solid #E0E2E7; color: #1A1A2E;">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold mb-1.5" style="color: #1A1A2E;">Water Resistance</label>
                                    <input type="text" name="water_resistance" value="<?php echo htmlspecialchars($product['water_resistance'] ?? ''); ?>" class="w-full h-10 px-4 rounded-lg text-sm outline-none" style="border: 1px solid #E0E2E7; color: #1A1A2E;">
                                </div>
                            </div>
                        </div>

                        <!-- Strap -->
                        <div class="bg-white rounded-lg p-5" style="border: 1px solid #E0E2E7;">
                            <h3 class="font-semibold text-base mb-4" style="color: #1A1A2E;">Strap</h3>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold mb-1.5" style="color: #1A1A2E;">Strap Material</label>
                                    <input type="text" name="strap_material" value="<?php echo htmlspecialchars($product['strap_material'] ?? ''); ?>" class="w-full h-10 px-4 rounded-lg text-sm outline-none" style="border: 1px solid #E0E2E7; color: #1A1A2E;">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold mb-1.5" style="color: #1A1A2E;">Strap Color</label>
                                    <input type="text" name="strap_color" value="<?php echo htmlspecialchars($product['strap_color'] ?? ''); ?>" class="w-full h-10 px-4 rounded-lg text-sm outline-none" style="border: 1px solid #E0E2E7; color: #1A1A2E;">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold mb-1.5" style="color: #1A1A2E;">Strap Length (mm)</label>
                                    <input type="number" step="0.1" id="strap_length_mm" name="strap_length_mm" value="<?php echo htmlspecialchars($product['strap_length_mm'] ?? ''); ?>" class="w-full h-10 px-4 rounded-lg text-sm outline-none" style="border: 1px solid #E0E2E7; color: #1A1A2E;">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold mb-1.5" style="color: #1A1A2E;">Strap Size Options</label>
                                    <input type="text" id="strap_size_options" name="strap_size_options" value="<?php echo htmlspecialchars($product['strap_size_options'] ?? ''); ?>" placeholder="e.g. S, M, L, XL" class="w-full h-10 px-4 rounded-lg text-sm outline-none" style="border: 1px solid #E0E2E7; color: #1A1A2E;">
                                    <p class="text-xs mt-1" style="color: #8A8F99;">Comma separated values</p>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="checkbox" id="strap_adjustable" name="strap_adjustable" value="1" <?php echo ($product['strap_adjustable'] == 1) ? 'checked' : ''; ?> class="w-4 h-4 rounded cursor-pointer" style="accent-color: #1B2A4A;">
                                        <span class="text-sm font-medium" style="color: #1A1A2E;">Strap is adjustable</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Features -->
                        <div class="bg-white rounded-lg p-5" style="border: 1px solid #E0E2E7;">
                            <h3 class="font-semibold text-base mb-4" style="color: #1A1A2E;">Features</h3>
                            <textarea name="features" rows="3" class="w-full px-4 py-3 rounded-lg text-sm outline-none resize-y" style="border: 1px solid #E0E2E7; color: #1A1A2E;"><?php echo htmlspecialchars($product['features'] ?? ''); ?></textarea>
                        </div>

                    </div>

                    <!-- Right Column -->
                    <div class="space-y-4">

                        <!-- Pricing -->
                        <div class="bg-white rounded-lg p-5" style="border: 1px solid #E0E2E7;">
                            <h3 class="font-semibold text-base mb-4" style="color: #1A1A2E;">Pricing & Stock</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold mb-1.5" style="color: #1A1A2E;">Price (Rs.) <span style="color: #D64545;">*</span></label>
                                    <input type="number" step="0.01" name="price" value="<?php echo $product['price']; ?>" required class="w-full h-10 px-4 rounded-lg text-sm outline-none" style="border: 1px solid #E0E2E7; color: #1A1A2E;">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold mb-1.5" style="color: #1A1A2E;">Stock Quantity</label>
                                    <input type="number" name="stock_quantity" value="<?php echo $product['stock_quantity']; ?>" class="w-full h-10 px-4 rounded-lg text-sm outline-none" style="border: 1px solid #E0E2E7; color: #1A1A2E;">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold mb-1.5" style="color: #1A1A2E;">Warranty (Years)</label>
                                    <input type="number" name="warranty_years" value="<?php echo $product['warranty_years']; ?>" class="w-full h-10 px-4 rounded-lg text-sm outline-none" style="border: 1px solid #E0E2E7; color: #1A1A2E;">
                                </div>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="is_expensive" value="1" <?php echo ($product['is_expensive'] == 1) ? 'checked' : ''; ?> class="w-4 h-4 rounded cursor-pointer" style="accent-color: #1B2A4A;">
                                    <span class="text-sm font-medium" style="color: #1A1A2E;">Mark as expensive</span>
                                </label>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="bg-white rounded-lg p-5" style="border: 1px solid #E0E2E7;">
                            <h3 class="font-semibold text-base mb-4" style="color: #1A1A2E;">Status</h3>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" <?php echo ($product['is_active'] == 1) ? 'checked' : ''; ?> class="w-4 h-4 rounded cursor-pointer" style="accent-color: #1B2A4A;">
                                <span class="text-sm font-medium" style="color: #1A1A2E;">Active</span>
                            </label>
                        </div>

                        <!-- Existing Images -->
                        <div class="bg-white rounded-lg p-5" style="border: 1px solid #E0E2E7;">
                            <h3 class="font-semibold text-base mb-4" style="color: #1A1A2E;">Current Images</h3>

                            <?php if (empty($images)): ?>
                                <p class="text-sm" style="color: #8A8F99;">No images uploaded yet.</p>
                            <?php else: ?>
                                <div class="grid grid-cols-2 gap-3">
                                    <?php foreach ($images as $img): ?>
                                        <div class="relative rounded overflow-hidden" style="border: 1px solid #E0E2E7;">
                                            <img src="../<?php echo htmlspecialchars($img['image_url']); ?>" alt="" class="w-full h-24 object-cover">

                                            <!-- Main badge -->
                                            <?php if ($img['is_main']): ?>
                                                <span class="absolute top-1 left-1 px-1.5 py-0.5 rounded text-xs font-semibold" style="background-color: #C9A84C; color: white;">Main</span>
                                            <?php endif; ?>

                                            <!-- Actions below image -->
                                            <div class="flex" style="border-top: 1px solid #E0E2E7;">

                                                <!-- Set as Main -->
                                                <?php if (!$img['is_main']): ?>
                                                    <form method="POST" action="actions/product-action.php" class="flex-1">
                                                        <input type="hidden" name="action" value="set_main_image">
                                                        <input type="hidden" name="image_id" value="<?php echo $img['id']; ?>">
                                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                        <button type="submit" class="w-full py-1.5 text-xs font-medium transition-all duration-200" style="background: none; border: none; cursor: pointer; color: #1B2A4A;" onmouseover="this.style.backgroundColor='#F7F8FA';" onmouseout="this.style.backgroundColor='transparent';">Set Main</button>
                                                    </form>
                                                <?php endif; ?>

                                                <!-- Delete Image -->
                                                <form method="POST" action="actions/product-action.php" class="<?php echo $img['is_main'] ? 'flex-1' : ''; ?>" style="<?php echo !$img['is_main'] ? 'border-left: 1px solid #E0E2E7;' : ''; ?>" onsubmit="return confirm('Delete this image?');">
                                                    <input type="hidden" name="action" value="delete_image">
                                                    <input type="hidden" name="image_id" value="<?php echo $img['id']; ?>">
                                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                    <button type="submit" class="w-full py-1.5 text-xs font-medium transition-all duration-200" style="background: none; border: none; cursor: pointer; color: #D64545;" onmouseover="this.style.backgroundColor='#FDEAEA';" onmouseout="this.style.backgroundColor='transparent';">Delete</button>
                                                </form>

                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Upload New Images -->
                        <div class="bg-white rounded-lg p-5" style="border: 1px solid #E0E2E7;">
                            <h3 class="font-semibold text-base mb-4" style="color: #1A1A2E;">Add More Images</h3>
                            <p class="text-xs mb-3" style="color: #5A5F6D;">JPG, PNG, or WEBP. Max 5MB each.</p>
                            <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp" class="w-full text-sm rounded-lg cursor-pointer" style="border: 1px solid #E0E2E7; color: #5A5F6D; padding: 8px 12px;" onchange="previewImages(this)">
                            <div id="preview-container" class="grid grid-cols-3 gap-2 mt-3 hidden"></div>
                        </div>

                        <!-- Submit -->
                        <div class="flex items-center gap-3">
                            <button type="submit" class="h-10 px-6 rounded-lg text-sm font-semibold text-white transition-all duration-200" style="background-color: #1B2A4A; border: none; cursor: pointer;" onmouseover="this.style.backgroundColor='#2C4066';" onmouseout="this.style.backgroundColor='#1B2A4A';">Update Product</button>
                            <a href="products.php" class="h-10 px-5 rounded-lg text-sm font-medium no-underline inline-flex items-center transition-all duration-200" style="color: #5A5F6D; border: 1px solid #E0E2E7;" onmouseover="this.style.backgroundColor='#F7F8FA';" onmouseout="this.style.backgroundColor='transparent';">Cancel</a>
                        </div>

                    </div>

                </div>

            </form>

        </div>
    </main>

    <script src = "../assets/js/admin.js"></script>

</body>
</html>