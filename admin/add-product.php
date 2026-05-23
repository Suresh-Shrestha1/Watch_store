<?php
session_start();

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

require_once '../config/db.php';

$page_title = 'Add Product';

$error = $_SESSION['product_error'] ?? '';
unset($_SESSION['product_error']);

// Get saved form data after error
$form = $_SESSION['product_form'] ?? [];
unset($_SESSION['product_form']);

// Fetch brands for dropdown
$brands_result = $conn->query("SELECT id, name FROM brands WHERE is_active = 1 ORDER BY name ASC");
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
            <!-- Error -->
            <?php if (!empty($error)): ?>
                <div class="rounded-lg px-4 py-3 mb-4 text-sm max-w-4xl" style="background-color: #FDEAEA; color: #D64545; border: 1px solid rgba(214,69,69,0.2);">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="actions/product-action.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">

                <!-- Two Column Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

                    <!-- Left Column — Main Info (takes 2 cols on desktop) -->
                    <div class="lg:col-span-2 space-y-4">

                        <!-- Basic Info Card -->
                        <div class="bg-white rounded-lg p-5" style="border: 1px solid #E0E2E7;">
                            <h3 class="font-semibold text-base mb-4" style="color: #1A1A2E;">Basic Information</h3>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                                <!-- Product Name -->
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-semibold mb-1.5" style="color: #1A1A2E;">Product Name <span style="color: #D64545;">*</span></label>
                                    <input type="text" name="name" value="<?php echo htmlspecialchars($form['name'] ?? ''); ?>" placeholder="e.g. Submariner Date" required class="w-full h-10 px-4 rounded-lg text-sm outline-none transition-all duration-200" style="border: 1px solid #E0E2E7; color: #1A1A2E;" onfocus="this.style.border='2px solid #C9A84C'; this.style.boxShadow='0 0 0 3px rgba(201,168,76,0.15)';" onblur="this.style.border='1px solid #E0E2E7'; this.style.boxShadow='none';">
                                </div>

                                <!-- Model Number -->
                                <div>
                                    <label class="block text-sm font-semibold mb-1.5" style="color: #1A1A2E;">Model Number <span style="color: #D64545;">*</span></label>
                                    <input type="text" name="model_number" value="<?php echo htmlspecialchars($form['model_number'] ?? ''); ?>" placeholder="e.g. RLX-126610LN" required class="w-full h-10 px-4 rounded-lg text-sm outline-none transition-all duration-200" style="border: 1px solid #E0E2E7; color: #1A1A2E;" onfocus="this.style.border='2px solid #C9A84C'; this.style.boxShadow='0 0 0 3px rgba(201,168,76,0.15)';" onblur="this.style.border='1px solid #E0E2E7'; this.style.boxShadow='none';">
                                </div>

                                <!-- Brand -->
                                <div>
                                    <label class="block text-sm font-semibold mb-1.5" style="color: #1A1A2E;">Brand <span style="color: #D64545;">*</span></label>
                                    <select name="brand_id" required class="w-full h-10 px-3 rounded-lg text-sm outline-none" style="border: 1px solid #E0E2E7; color: #1A1A2E;">
                                        <option value="">Select Brand</option>
                                        <?php foreach ($brands as $b): ?>
                                            <option value="<?php echo $b['id']; ?>" <?php echo (($form['brand_id'] ?? '') == $b['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($b['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Gender -->
                                <div>
                                    <label class="block text-sm font-semibold mb-1.5" style="color: #1A1A2E;">Gender <span style="color: #D64545;">*</span></label>
                                    <select name="gender" required class="w-full h-10 px-3 rounded-lg text-sm outline-none" style="border: 1px solid #E0E2E7; color: #1A1A2E;">
                                        <option value="">Select Gender</option>
                                        <?php foreach (['Men', 'Women', 'Unisex', 'Kids'] as $g): ?>
                                            <option value="<?php echo $g; ?>" <?php echo (($form['gender'] ?? '') === $g) ? 'selected' : ''; ?>><?php echo $g; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Movement Type -->
                                <div>
                                    <label class="block text-sm font-semibold mb-1.5" style="color: #1A1A2E;">Movement Type</label>
                                    <select name="movement_type" class="w-full h-10 px-3 rounded-lg text-sm outline-none" style="border: 1px solid #E0E2E7; color: #1A1A2E;">
                                        <?php foreach (['-', 'Automatic', 'Quartz', 'Mechanical', 'Solar', 'Kinetic', 'Digital', 'Smartwatch'] as $m): ?>
                                            <option value="<?php echo $m; ?>" <?php echo (($form['movement_type'] ?? '-') === $m) ? 'selected' : ''; ?>><?php echo $m; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Description -->
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-semibold mb-1.5" style="color: #1A1A2E;">Description</label>
                                    <textarea name="description" rows="4" placeholder="Product description..." class="w-full px-4 py-3 rounded-lg text-sm outline-none transition-all duration-200 resize-y" style="border: 1px solid #E0E2E7; color: #1A1A2E;" onfocus="this.style.border='2px solid #C9A84C'; this.style.boxShadow='0 0 0 3px rgba(201,168,76,0.15)';" onblur="this.style.border='1px solid #E0E2E7'; this.style.boxShadow='none';"><?php echo htmlspecialchars($form['description'] ?? ''); ?></textarea>
                                </div>

                            </div>
                        </div>

                        <!-- Dial & Case Card -->
                        <div class="bg-white rounded-lg p-5" style="border: 1px solid #E0E2E7;">
                            <h3 class="font-semibold text-base mb-4" style="color: #1A1A2E;">Dial & Case</h3>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                                <div>
                                    <label class="block text-sm font-semibold mb-1.5" style="color: #1A1A2E;">Dial Shape</label>
                                    <select name="dial_shape" class="w-full h-10 px-3 rounded-lg text-sm outline-none" style="border: 1px solid #E0E2E7; color: #1A1A2E;">
                                        <?php foreach (['Round', 'Square', 'Rectangular', 'Oval', 'Tonneau'] as $ds): ?>
                                            <option value="<?php echo $ds; ?>" <?php echo (($form['dial_shape'] ?? 'Round') === $ds) ? 'selected' : ''; ?>><?php echo $ds; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold mb-1.5" style="color: #1A1A2E;">Dial Color</label>
                                    <input type="text" name="dial_color" value="<?php echo htmlspecialchars($form['dial_color'] ?? ''); ?>" placeholder="e.g. Black" class="w-full h-10 px-4 rounded-lg text-sm outline-none" style="border: 1px solid #E0E2E7; color: #1A1A2E;">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold mb-1.5" style="color: #1A1A2E;">Case Diameter (mm)</label>
                                    <input type="number" step="0.1" name="case_diameter_mm" value="<?php echo htmlspecialchars($form['case_diameter_mm'] ?? ''); ?>" placeholder="e.g. 41.0" class="w-full h-10 px-4 rounded-lg text-sm outline-none" style="border: 1px solid #E0E2E7; color: #1A1A2E;">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold mb-1.5" style="color: #1A1A2E;">Case Material</label>
                                    <input type="text" name="case_material" value="<?php echo htmlspecialchars($form['case_material'] ?? ''); ?>" placeholder="e.g. Stainless Steel" class="w-full h-10 px-4 rounded-lg text-sm outline-none" style="border: 1px solid #E0E2E7; color: #1A1A2E;">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold mb-1.5" style="color: #1A1A2E;">Water Resistance</label>
                                    <input type="text" name="water_resistance" value="<?php echo htmlspecialchars($form['water_resistance'] ?? ''); ?>" placeholder="e.g. 100m" class="w-full h-10 px-4 rounded-lg text-sm outline-none" style="border: 1px solid #E0E2E7; color: #1A1A2E;">
                                </div>

                            </div>
                        </div>

                        <!-- Strap Card -->
                        <div class="bg-white rounded-lg p-5" style="border: 1px solid #E0E2E7;">
                            <h3 class="font-semibold text-base mb-4" style="color: #1A1A2E;">Strap</h3>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                                <div>
                                    <label class="block text-sm font-semibold mb-1.5" style="color: #1A1A2E;">Strap Material</label>
                                    <input type="text" name="strap_material" value="<?php echo htmlspecialchars($form['strap_material'] ?? ''); ?>" placeholder="e.g. Leather" class="w-full h-10 px-4 rounded-lg text-sm outline-none" style="border: 1px solid #E0E2E7; color: #1A1A2E;">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold mb-1.5" style="color: #1A1A2E;">Strap Color</label>
                                    <input type="text" name="strap_color" value="<?php echo htmlspecialchars($form['strap_color'] ?? ''); ?>" placeholder="e.g. Brown" class="w-full h-10 px-4 rounded-lg text-sm outline-none" style="border: 1px solid #E0E2E7; color: #1A1A2E;">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold mb-1.5" style="color: #1A1A2E;">Strap Length (mm)</label>
                                    <input type="number" step="0.1" id="strap_length_mm" name="strap_length_mm" value="<?php echo htmlspecialchars($form['strap_length_mm'] ?? ''); ?>" placeholder="e.g. 200.0" class="w-full h-10 px-4 rounded-lg text-sm outline-none" style="border: 1px solid #E0E2E7; color: #1A1A2E;">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold mb-1.5" style="color: #1A1A2E;">Strap Size Options</label>
                                    <input type="text" id="strap_size_options" name="strap_size_options" value="<?php echo htmlspecialchars($form['strap_size_options'] ?? ''); ?>" placeholder="e.g. S, M, L, XL" class="w-full h-10 px-4 rounded-lg text-sm outline-none" style="border: 1px solid #E0E2E7; color: #1A1A2E;">
                                    <p class="text-xs mt-1" style="color: #8A8F99;">Comma separated values</p>
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="checkbox" id="strap_adjustable" name="strap_adjustable" value="1" <?php echo (($form['strap_adjustable'] ?? 1) == 1) ? 'checked' : ''; ?> class="w-4 h-4 rounded cursor-pointer" style="accent-color: #1B2A4A;">
                                        <span class="text-sm font-medium" style="color: #1A1A2E;">Strap is adjustable</span>
                                    </label>
                                </div>

                            </div>
                        </div>

                        <!-- Features Card -->
                        <div class="bg-white rounded-lg p-5" style="border: 1px solid #E0E2E7;">
                            <h3 class="font-semibold text-base mb-4" style="color: #1A1A2E;">Features</h3>
                            <textarea name="features" rows="3" placeholder="e.g. Chronograph, Date Display, Luminous Hands" class="w-full px-4 py-3 rounded-lg text-sm outline-none resize-y" style="border: 1px solid #E0E2E7; color: #1A1A2E;"><?php echo htmlspecialchars($form['features'] ?? ''); ?></textarea>
                            <p class="text-xs mt-1" style="color: #8A8F99;">Comma separated features</p>
                        </div>

                    </div>

                    <!-- Right Column — Price, Status, Images -->
                    <div class="space-y-4">

                        <!-- Pricing Card -->
                        <div class="bg-white rounded-lg p-5" style="border: 1px solid #E0E2E7;">
                            <h3 class="font-semibold text-base mb-4" style="color: #1A1A2E;">Pricing & Stock</h3>

                            <div class="space-y-4">

                                <div>
                                    <label class="block text-sm font-semibold mb-1.5" style="color: #1A1A2E;">Price (Rs.) <span style="color: #D64545;">*</span></label>
                                    <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($form['price'] ?? ''); ?>" placeholder="0.00" required class="w-full h-10 px-4 rounded-lg text-sm outline-none" style="border: 1px solid #E0E2E7; color: #1A1A2E;">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold mb-1.5" style="color: #1A1A2E;">Stock Quantity</label>
                                    <input type="number" name="stock_quantity" value="<?php echo htmlspecialchars($form['stock_quantity'] ?? '0'); ?>" class="w-full h-10 px-4 rounded-lg text-sm outline-none" style="border: 1px solid #E0E2E7; color: #1A1A2E;">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold mb-1.5" style="color: #1A1A2E;">Warranty (Years)</label>
                                    <input type="number" name="warranty_years" value="<?php echo htmlspecialchars($form['warranty_years'] ?? '2'); ?>" class="w-full h-10 px-4 rounded-lg text-sm outline-none" style="border: 1px solid #E0E2E7; color: #1A1A2E;">
                                </div>

                                <div>
                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="checkbox" name="is_expensive" value="1" <?php echo (($form['is_expensive'] ?? 0) == 1) ? 'checked' : ''; ?> class="w-4 h-4 rounded cursor-pointer" style="accent-color: #1B2A4A;">
                                        <span class="text-sm font-medium" style="color: #1A1A2E;">Mark as expensive</span>
                                    </label>
                                </div>

                            </div>
                        </div>

                        <!-- Status Card -->
                        <div class="bg-white rounded-lg p-5" style="border: 1px solid #E0E2E7;">
                            <h3 class="font-semibold text-base mb-4" style="color: #1A1A2E;">Status</h3>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" <?php echo (($form['is_active'] ?? 1) == 1) ? 'checked' : ''; ?> class="w-4 h-4 rounded cursor-pointer" style="accent-color: #1B2A4A;">
                                <span class="text-sm font-medium" style="color: #1A1A2E;">Active</span>
                            </label>
                            <p class="text-xs mt-1 ml-7" style="color: #8A8F99;">Product will be visible on the store</p>
                        </div>

                        <!-- Images Card -->
                        <div class="bg-white rounded-lg p-5" style="border: 1px solid #E0E2E7;">
                            <h3 class="font-semibold text-base mb-4" style="color: #1A1A2E;">Images</h3>
                            <p class="text-xs mb-3" style="color: #5A5F6D;">JPG, PNG, or WEBP. Max 5MB each. First image will be the main image.</p>

                            <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp" class="w-full text-sm rounded-lg cursor-pointer" style="border: 1px solid #E0E2E7; color: #5A5F6D; padding: 8px 12px;" onchange="previewImages(this)">

                            <div id="preview-container" class="grid grid-cols-3 gap-2 mt-3 hidden"></div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center gap-3">
                            <button type="submit" class="h-10 px-6 rounded-lg text-sm font-semibold text-white transition-all duration-200" style="background-color: #1B2A4A; border: none; cursor: pointer;" onmouseover="this.style.backgroundColor='#2C4066';" onmouseout="this.style.backgroundColor='#1B2A4A';">Save Product</button>
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