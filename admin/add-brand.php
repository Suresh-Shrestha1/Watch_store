<?php
session_start();

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

$page_title = 'Add Brand';

// Get error message and saved form data
$error = $_SESSION['brand_error'] ?? '';
unset($_SESSION['brand_error']);

$form = $_SESSION['brand_form'] ?? ['name' => '', 'is_active' => 1];
unset($_SESSION['brand_form']);
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

            <!-- Form Card -->
            <div class="bg-white rounded-lg p-6 max-w-xl" style="border: 1px solid #E0E2E7;">

                <h2 class="font-bold text-xl mb-1" style="font-family: 'Playfair Display', serif; color: #1A1A2E;">Add New Brand</h2>
                <p class="text-sm mb-6" style="color: #5A5F6D;">Fill in the details to create a new brand.</p>

                <!-- Error -->
                <?php if (!empty($error)): ?>
                    <div class="rounded-lg px-4 py-3 mb-5 text-sm" style="background-color: #FDEAEA; color: #D64545; border: 1px solid rgba(214,69,69,0.2);">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <!-- enctype="multipart/form-data" is required for file uploads -->
                <form method="POST" action="actions/brand-action.php" enctype="multipart/form-data">

                    <!-- Hidden action field tells brand_action.php what to do -->
                    <input type="hidden" name="action" value="add">

                    <!-- Brand Name -->
                    <div class="mb-5">
                        <label for="name" class="block text-sm font-semibold mb-1.5" style="color: #1A1A2E;">Brand Name <span style="color: #D64545;">*</span></label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($form['name']); ?>" placeholder="e.g. Rolex" required class="w-full h-11 px-4 rounded-lg text-sm outline-none transition-all duration-200" style="border: 1px solid #E0E2E7; color: #1A1A2E;" onfocus="this.style.border='2px solid #C9A84C'; this.style.boxShadow='0 0 0 3px rgba(201,168,76,0.15)';" onblur="this.style.border='1px solid #E0E2E7'; this.style.boxShadow='none';">
                    </div>

                    <!-- Logo Upload -->
                    <div class="mb-5">
                        <label for="logo" class="block text-sm font-semibold mb-1.5" style="color: #1A1A2E;">Brand Logo</label>
                        <p class="text-xs mb-2" style="color: #5A5F6D;">JPG, PNG, WEBP, or SVG. Max 2MB.</p>

                        <!-- File Input -->
                        <input type="file" id="logo" name="logo" accept="image/jpeg,image/png,image/webp,image/svg+xml" class="w-full text-sm rounded-lg cursor-pointer" style="border: 1px solid #E0E2E7; color: #5A5F6D; padding: 8px 12px;" onchange="previewImage(this)">

                        <!-- Image Preview -->
                        <div id="preview-container" class="hidden mt-3">
                            <img id="preview-image" src="" alt="Preview" class="w-16 h-16 rounded object-contain" style="border: 1px solid #E0E2E7;">
                        </div>
                    </div>

                    <!-- Status Toggle -->
                    <div class="mb-6">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" <?php echo $form['is_active'] ? 'checked' : ''; ?> class="w-4 h-4 rounded cursor-pointer" style="accent-color: #1B2A4A;">
                            <span class="text-sm font-medium" style="color: #1A1A2E;">Active</span>
                        </label>
                        <p class="text-xs mt-1 ml-7" style="color: #5A5F6D;">Active brands are visible on the store.</p>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center gap-3">
                        <button type="submit" class="h-10 px-6 rounded-lg text-sm font-semibold text-white transition-all duration-200" style="background-color: #1B2A4A; border: none; cursor: pointer;" onmouseover="this.style.backgroundColor='#2C4066';" onmouseout="this.style.backgroundColor='#1B2A4A';">Save Brand</button>
                        <a href="brands.php" class="h-10 px-6 rounded-lg text-sm font-semibold no-underline inline-flex items-center transition-all duration-200" style="color: #5A5F6D; border: 1px solid #E0E2E7;" onmouseover="this.style.backgroundColor='#F7F8FA';" onmouseout="this.style.backgroundColor='transparent';">Cancel</a>
                    </div>

                </form>

            </div>

        </div>
    </main>

    <script src="../assets/js/admin.js"></script>
</body>
</html>
