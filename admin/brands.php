<?php
session_start();

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

require_once '../config/db.php';

$page_title = 'Brands';

// Get success/error messages
$success = $_SESSION['brand_success'] ?? '';
$error = $_SESSION['brand_error'] ?? '';
unset($_SESSION['brand_success'], $_SESSION['brand_error']);

// Fetch all brands from database
$result = $conn->query("SELECT * FROM brands ORDER BY created_at DESC");
$brands = [];
while ($row = $result->fetch_assoc()) {
    $brands[] = $row;
}

// Count products per brand
$product_counts = [];
$count_result = $conn->query("SELECT brand_id, COUNT(*) as total FROM products GROUP BY brand_id");
while ($row = $count_result->fetch_assoc()) {
    $product_counts[$row['brand_id']] = $row['total'];
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

            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h2 class="font-bold text-2xl mb-1" style="font-family: 'Playfair Display', serif; color: #1A1A2E;">Brands</h2>
                    <p class="text-sm" style="color: #5A5F6D;"><?php echo count($brands); ?> total brands</p>
                </div>
                <a href="add-brand.php" class="inline-flex items-center h-10 px-5 rounded-lg text-sm font-semibold text-white no-underline transition-all duration-200" style="background-color: #1B2A4A;" onmouseover="this.style.backgroundColor='#2C4066';" onmouseout="this.style.backgroundColor='#1B2A4A';">+ Add Brand</a>
            </div>

            <!-- Success Message -->
            <?php if (!empty($success)): ?>
                <div class="rounded-lg px-4 py-3 mb-4 text-sm" style="background-color: #E8F5E9; color: #2E7D32; border: 1px solid rgba(46,125,50,0.2);">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <!-- Error Message -->
            <?php if (!empty($error)): ?>
                <div class="rounded-lg px-4 py-3 mb-4 text-sm" style="background-color: #FDEAEA; color: #D64545; border: 1px solid rgba(214,69,69,0.2);">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Brands Table -->
            <div class="bg-white rounded-lg overflow-hidden" style="border: 1px solid #E0E2E7;">

                <?php if (empty($brands)): ?>

                    <!-- Empty State -->
                    <div class="p-10 text-center">
                        <p class="text-base font-semibold mb-1" style="color: #1A1A2E;">No brands found</p>
                        <p class="text-sm mb-4" style="color: #5A5F6D;">Get started by adding your first brand.</p>
                        <a href="add-brand.php" class="inline-flex items-center h-10 px-5 rounded-lg text-sm font-semibold text-white no-underline" style="background-color: #1B2A4A;">+ Add Brand</a>
                    </div>

                <?php else: ?>

                    <!-- Table for desktop -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead>
                                <tr style="background-color: #F7F8FA; border-bottom: 1px solid #E0E2E7;">
                                    <th class="px-4 py-3 font-semibold" style="color: #5A5F6D;">Logo</th>
                                    <th class="px-4 py-3 font-semibold" style="color: #5A5F6D;">Brand Name</th>
                                    <th class="px-4 py-3 font-semibold hidden sm:table-cell" style="color: #5A5F6D;">Slug</th>
                                    <th class="px-4 py-3 font-semibold hidden md:table-cell" style="color: #5A5F6D;">Products</th>
                                    <th class="px-4 py-3 font-semibold" style="color: #5A5F6D;">Status</th>
                                    <th class="px-4 py-3 font-semibold text-right" style="color: #5A5F6D;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($brands as $brand): ?>
                                    <tr style="border-bottom: 1px solid #E0E2E7;">
                                        <!-- Logo -->
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-3">
                                                <?php if (!empty($brand['logo'])): ?>
                                                    <img src="../<?php echo htmlspecialchars($brand['logo']); ?>" alt="<?php echo htmlspecialchars($brand['name']); ?>" class="w-8 h-8 rounded object-contain" style="border: 1px solid #E0E2E7;">
                                                <?php else: ?>
                                                    <div class="w-8 h-8 rounded flex items-center justify-center text-xs font-bold" style="background-color: #F7F8FA; border: 1px solid #E0E2E7; color: #5A5F6D;">
                                                        <?php echo strtoupper(substr($brand['name'], 0, 1)); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                            

                                        <!-- Brand Name -->
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-3">
                                                <span class="font-medium" style="color: #1A1A2E;"><?php echo htmlspecialchars($brand['name']); ?></span>
                                            </div>
                                        </td>

                                        <!-- Slug -->
                                        <td class="px-4 py-3 hidden sm:table-cell" style="color: #5A5F6D;"><?php echo htmlspecialchars($brand['slug']); ?></td>

                                        <!-- Product Count -->
                                        <td class="px-4 py-3 hidden md:table-cell" style="color: #5A5F6D;">
                                            <?php echo $product_counts[$brand['id']] ?? 0; ?>
                                        </td>

                                        <!-- Status -->
                                        <td class="px-4 py-3">
                                            <?php if ($brand['is_active']): ?>
                                                <span class="inline-block px-2.5 py-1 rounded text-xs font-semibold" style="background-color: #E8F5E9; color: #2E7D32;">Active</span>
                                            <?php else: ?>
                                                <span class="inline-block px-2.5 py-1 rounded text-xs font-semibold" style="background-color: #FDEAEA; color: #D64545;">Inactive</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Actions -->
                                        <td class="px-4 py-3 text-right">
                                            <div class="flex items-center justify-end gap-2">

                                                <!-- Toggle Status -->
                                                <form method="POST" action="actions/brand-action.php" class="inline">
                                                    <input type="hidden" name="action" value="toggle_status">
                                                    <input type="hidden" name="id" value="<?php echo $brand['id']; ?>">
                                                    <input type="hidden" name="current_status" value="<?php echo $brand['is_active']; ?>">
                                                    <button type="submit" class="text-xs font-medium px-2.5 py-1 rounded transition-all duration-200" style="border: 1px solid #E0E2E7; color: #5A5F6D; background: none; cursor: pointer;" onmouseover="this.style.backgroundColor='#F7F8FA';" onmouseout="this.style.backgroundColor='transparent';">
                                                        <?php echo $brand['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                                    </button>
                                                </form>

                                                <!-- Edit -->
                                                <a href="edit-brand.php?id=<?php echo $brand['id']; ?>" class="text-xs font-medium px-2.5 py-1 rounded no-underline transition-all duration-200" style="border: 1px solid #E0E2E7; color: #1B2A4A;" onmouseover="this.style.backgroundColor='#F7F8FA';" onmouseout="this.style.backgroundColor='transparent';">Edit</a>

                                                <!-- Delete -->
                                                <form method="POST" action="actions/brand-action.php" class="inline" onsubmit="return confirm('Are you sure you want to delete this brand?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?php echo $brand['id']; ?>">
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

    <script src="../assets/js/admin.js"></script>
</body>
</html>