<?php
session_start();

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

require_once '../config/db.php';

$page_title = 'Customers';

$success = $_SESSION['customer_success'] ?? '';
$error   = $_SESSION['customer_error'] ?? '';
unset($_SESSION['customer_success'], $_SESSION['customer_error']);

// Search
$search = trim($_GET['search'] ?? '');

// Build query — only get customers (not admins)
$query = "
    SELECT u.*,
        (SELECT COUNT(*) FROM orders WHERE user_id = u.id) as order_count,
        (SELECT COALESCE(SUM(grand_total), 0) FROM orders WHERE user_id = u.id AND payment_status = 'paid') as total_spent
    FROM users u
    WHERE u.role = 'customer'
";
$params = [];
$types  = "";

if (!empty($search)) {
    $query .= " AND (u.name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)";
    $search_param = "%" . $search . "%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

$query .= " ORDER BY u.created_at DESC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$customers = [];
while ($row = $result->fetch_assoc()) {
    $customers[] = $row;
}
$stmt->close();
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
            <div class="mb-6">
                <h2 class="font-bold text-2xl mb-1" style="font-family: 'Playfair Display', serif; color: #1A1A2E;">Customers</h2>
                <p class="text-sm" style="color: #5A5F6D;"><?php echo count($customers); ?> registered customers</p>
            </div>

            <!-- Search -->
            <div class="bg-white rounded-lg p-4 mb-4" style="border: 1px solid #E0E2E7;">
                <form method="GET" action="customers.php" class="flex flex-col sm:flex-row gap-3">
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by name, email, or phone..." class="h-10 px-4 rounded-lg text-sm flex-1 min-w-0 outline-none" style="border: 1px solid #E0E2E7; color: #1A1A2E;" onfocus="this.style.border='2px solid #C9A84C'; this.style.boxShadow='0 0 0 3px rgba(201,168,76,0.15)';" onblur="this.style.border='1px solid #E0E2E7'; this.style.boxShadow='none';">
                    <button type="submit" class="h-10 px-5 rounded-lg text-sm font-semibold text-white" style="background-color: #1B2A4A; border: none; cursor: pointer;">Search</button>
                    <?php if (!empty($search)): ?>
                        <a href="customers.php" class="h-10 px-4 rounded-lg text-sm font-medium no-underline inline-flex items-center" style="color: #5A5F6D; border: 1px solid #E0E2E7;">Clear</a>
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

            <!-- Customers Table -->
            <div class="bg-white rounded-lg overflow-hidden" style="border: 1px solid #E0E2E7;">

                <?php if (empty($customers)): ?>

                    <div class="p-10 text-center">
                        <p class="text-base font-semibold mb-1" style="color: #1A1A2E;">No customers found</p>
                        <p class="text-sm" style="color: #5A5F6D;">
                            <?php echo !empty($search) ? 'Try a different search term.' : 'Customers will appear here when they register.'; ?>
                        </p>
                    </div>

                <?php else: ?>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead>
                                <tr style="background-color: #F7F8FA; border-bottom: 1px solid #E0E2E7;">
                                    <th class="px-4 py-3 font-semibold" style="color: #5A5F6D;">Customer</th>
                                    <th class="px-4 py-3 font-semibold hidden sm:table-cell" style="color: #5A5F6D;">Phone</th>
                                    <th class="px-4 py-3 font-semibold hidden md:table-cell" style="color: #5A5F6D;">Orders</th>
                                    <th class="px-4 py-3 font-semibold hidden md:table-cell" style="color: #5A5F6D;">Total Spent</th>
                                    <th class="px-4 py-3 font-semibold hidden lg:table-cell" style="color: #5A5F6D;">Joined</th>
                                    <th class="px-4 py-3 font-semibold text-right" style="color: #5A5F6D;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($customers as $customer): ?>
                                    <tr style="border-bottom: 1px solid #E0E2E7;">

                                        <!-- Name + Email -->
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-3">
                                                <!-- Avatar with first letter -->
                                                <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0" style="background-color: #1B2A4A;">
                                                    <?php echo strtoupper(substr($customer['name'], 0, 1)); ?>
                                                </div>
                                                <div>
                                                    <p class="font-medium leading-tight" style="color: #1A1A2E;"><?php echo htmlspecialchars($customer['name']); ?></p>
                                                    <p class="text-xs mt-0.5" style="color: #8A8F99;"><?php echo htmlspecialchars($customer['email']); ?></p>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Phone -->
                                        <td class="px-4 py-3 hidden sm:table-cell" style="color: #5A5F6D;">
                                            <?php echo htmlspecialchars($customer['phone']); ?>
                                        </td>

                                        <!-- Order Count -->
                                        <td class="px-4 py-3 hidden md:table-cell" style="color: #1A1A2E;">
                                            <?php echo $customer['order_count']; ?>
                                        </td>

                                        <!-- Total Spent -->
                                        <td class="px-4 py-3 hidden md:table-cell font-semibold" style="color: #1A1A2E;">
                                            Rs. <?php echo number_format($customer['total_spent'], 2); ?>
                                        </td>

                                        <!-- Joined Date -->
                                        <td class="px-4 py-3 hidden lg:table-cell" style="color: #5A5F6D;">
                                            <?php echo date('M d, Y', strtotime($customer['created_at'])); ?>
                                        </td>

                                        <!-- Actions -->
                                        <td class="px-4 py-3 text-right">
                                            <div class="flex items-center justify-end gap-2">

                                                <!-- View Details -->
                                                <a href="customer-detail.php?id=<?php echo $customer['id']; ?>" class="text-xs font-medium px-2.5 py-1 rounded no-underline transition-all duration-200" style="border: 1px solid #E0E2E7; color: #1B2A4A;" onmouseover="this.style.backgroundColor='#F7F8FA';" onmouseout="this.style.backgroundColor='transparent';">View</a>

                                                <!-- Delete -->
                                                <form method="POST" action="actions/customer-action.php" class="inline" onsubmit="return confirm('Are you sure you want to delete this customer? This cannot be undone.');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?php echo $customer['id']; ?>">
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