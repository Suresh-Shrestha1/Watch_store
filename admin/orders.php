<?php
session_start();

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

require_once '../config/db.php';

$page_title = 'Orders';

$success = $_SESSION['order_success'] ?? '';
$error   = $_SESSION['order_error'] ?? '';
unset($_SESSION['order_success'], $_SESSION['order_error']);

// Filters
$search         = trim($_GET['search'] ?? '');
$filter_status  = trim($_GET['status'] ?? '');
$filter_payment = trim($_GET['payment'] ?? '');

// Build query
$query = "SELECT * FROM orders WHERE 1=1";
$params = [];
$types  = "";

if (!empty($search)) {
    $query .= " AND (order_number LIKE ? OR customer_name LIKE ? OR customer_email LIKE ? OR customer_phone LIKE ?)";
    $search_param = "%" . $search . "%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ssss";
}

if (!empty($filter_status)) {
    $query .= " AND order_status = ?";
    $params[] = $filter_status;
    $types .= "s";
}

if (!empty($filter_payment)) {
    $query .= " AND payment_status = ?";
    $params[] = $filter_payment;
    $types .= "s";
}

$query .= " ORDER BY ordered_at DESC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}
$stmt->close();

// Get order counts by status for quick filter tabs
$count_query = $conn->query("
    SELECT
        COUNT(*) as total,
        SUM(order_status = 'pending') as pending_count,
        SUM(order_status = 'confirmed') as confirmed_count,
        SUM(order_status = 'processing') as processing_count,
        SUM(order_status = 'shipped') as shipped_count,
        SUM(order_status = 'delivered') as delivered_count
    FROM orders
");
$counts = $count_query->fetch_assoc();

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
                <h2 class="font-bold text-2xl mb-1" style="font-family: 'Playfair Display', serif; color: #1A1A2E;">Orders</h2>
                <p class="text-sm" style="color: #5A5F6D;"><?php echo count($orders); ?> orders found</p>
            </div>

            <!-- Status Tabs -->
            <div class="flex flex-wrap gap-2 mb-4">
                <?php
                $tabs = [
                    ''           => ['label' => 'All',        'count' => $counts['total']],
                    'pending'    => ['label' => 'Pending',    'count' => $counts['pending_count']],
                    'confirmed'  => ['label' => 'Confirmed',  'count' => $counts['confirmed_count']],
                    'processing' => ['label' => 'Processing', 'count' => $counts['processing_count']],
                    'shipped'    => ['label' => 'Shipped',    'count' => $counts['shipped_count']],
                    'delivered'  => ['label' => 'Delivered',  'count' => $counts['delivered_count']],
                ];
                foreach ($tabs as $tab_value => $tab_data):
                    $is_active_tab = ($filter_status === $tab_value);
                    // Build URL keeping other filters
                    $tab_url = 'orders.php?status=' . $tab_value;
                    if (!empty($search)) $tab_url .= '&search=' . urlencode($search);
                    if (!empty($filter_payment)) $tab_url .= '&payment=' . urlencode($filter_payment);
                ?>
                    <a href="<?php echo $tab_url; ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold no-underline transition-all duration-200" style="<?php echo $is_active_tab ? 'background-color: #1B2A4A; color: #FFFFFF;' : 'background-color: #FFFFFF; color: #5A5F6D; border: 1px solid #E0E2E7;'; ?>">
                        <?php echo $tab_data['label']; ?>
                        <span class="px-1.5 py-0.5 rounded text-xs" style="<?php echo $is_active_tab ? 'background-color: rgba(255,255,255,0.2); color: #FFFFFF;' : 'background-color: #F7F8FA; color: #8A8F99;'; ?>">
                            <?php echo $tab_data['count']; ?>
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Search + Payment Filter -->
            <div class="bg-white rounded-lg p-4 mb-4" style="border: 1px solid #E0E2E7;">
                <form method="GET" action="orders.php" class="flex flex-col sm:flex-row gap-3">

                    <!-- Keep current status filter -->
                    <input type="hidden" name="status" value="<?php echo htmlspecialchars($filter_status); ?>">

                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by order number, name, email, or phone..." class="h-10 px-4 rounded-lg text-sm flex-1 min-w-0 outline-none" style="border: 1px solid #E0E2E7; color: #1A1A2E;" onfocus="this.style.border='2px solid #C9A84C'; this.style.boxShadow='0 0 0 3px rgba(201,168,76,0.15)';" onblur="this.style.border='1px solid #E0E2E7'; this.style.boxShadow='none';">

                    <select name="payment" class="h-10 px-3 rounded-lg text-sm outline-none" style="border: 1px solid #E0E2E7; color: #1A1A2E; min-width: 140px;">
                        <option value="">All Payments</option>
                        <option value="pending" <?php echo ($filter_payment === 'pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="paid" <?php echo ($filter_payment === 'paid') ? 'selected' : ''; ?>>Paid</option>
                        <option value="failed" <?php echo ($filter_payment === 'failed') ? 'selected' : ''; ?>>Failed</option>
                    </select>

                    <button type="submit" class="h-10 px-5 rounded-lg text-sm font-semibold text-white" style="background-color: #1B2A4A; border: none; cursor: pointer;">Search</button>

                    <?php if (!empty($search) || !empty($filter_payment) || !empty($filter_status)): ?>
                        <a href="orders.php" class="h-10 px-4 rounded-lg text-sm font-medium no-underline inline-flex items-center" style="color: #5A5F6D; border: 1px solid #E0E2E7;">Clear All</a>
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

            <!-- Orders Table -->
            <div class="bg-white rounded-lg overflow-hidden" style="border: 1px solid #E0E2E7;">

                <?php if (empty($orders)): ?>

                    <div class="p-10 text-center">
                        <p class="text-base font-semibold mb-1" style="color: #1A1A2E;">No orders found</p>
                        <p class="text-sm" style="color: #5A5F6D;">
                            <?php echo (!empty($search) || !empty($filter_status) || !empty($filter_payment)) ? 'Try adjusting your filters.' : 'Orders will appear here when customers place them.'; ?>
                        </p>
                    </div>

                <?php else: ?>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead>
                                <tr style="background-color: #F7F8FA; border-bottom: 1px solid #E0E2E7;">
                                    <th class="px-4 py-3 font-semibold" style="color: #5A5F6D;">Order</th>
                                    <th class="px-4 py-3 font-semibold hidden sm:table-cell" style="color: #5A5F6D;">Customer</th>
                                    <th class="px-4 py-3 font-semibold hidden md:table-cell" style="color: #5A5F6D;">Date</th>
                                    <th class="px-4 py-3 font-semibold" style="color: #5A5F6D;">Total</th>
                                    <th class="px-4 py-3 font-semibold hidden sm:table-cell" style="color: #5A5F6D;">Payment</th>
                                    <th class="px-4 py-3 font-semibold" style="color: #5A5F6D;">Status</th>
                                    <th class="px-4 py-3 font-semibold text-right" style="color: #5A5F6D;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr style="border-bottom: 1px solid #E0E2E7;">

                                        <!-- Order Number + mobile date -->
                                        <td class="px-4 py-3">
                                            <p class="font-medium" style="color: #1A1A2E;">#<?php echo htmlspecialchars($order['order_number']); ?></p>
                                            <p class="text-xs mt-0.5 md:hidden" style="color: #8A8F99;"><?php echo date('M d, Y', strtotime($order['ordered_at'])); ?></p>
                                        </td>

                                        <!-- Customer -->
                                        <td class="px-4 py-3 hidden sm:table-cell">
                                            <p class="font-medium leading-tight" style="color: #1A1A2E;"><?php echo htmlspecialchars($order['customer_name']); ?></p>
                                            <p class="text-xs mt-0.5" style="color: #8A8F99;"><?php echo htmlspecialchars($order['customer_phone']); ?></p>
                                        </td>

                                        <!-- Date -->
                                        <td class="px-4 py-3 hidden md:table-cell" style="color: #5A5F6D;">
                                            <?php echo date('M d, Y', strtotime($order['ordered_at'])); ?>
                                            <p class="text-xs mt-0.5" style="color: #8A8F99;"><?php echo date('h:i A', strtotime($order['ordered_at'])); ?></p>
                                        </td>

                                        <!-- Total -->
                                        <td class="px-4 py-3 font-semibold" style="color: #1A1A2E;">
                                            Rs. <?php echo number_format($order['grand_total'], 2); ?>
                                        </td>

                                        <!-- Payment Status -->
                                        <td class="px-4 py-3 hidden sm:table-cell">
                                            <?php
                                            $pay_colors = [
                                                'pending' => 'background-color: #FFF3E0; color: #E65100;',
                                                'paid'    => 'background-color: #E8F5E9; color: #2E7D32;',
                                                'failed'  => 'background-color: #FDEAEA; color: #D64545;'
                                            ];
                                            ?>
                                            <span class="inline-block px-2.5 py-1 rounded text-xs font-semibold" style="<?php echo $pay_colors[$order['payment_status']] ?? ''; ?>">
                                                <?php echo ucfirst($order['payment_status']); ?>
                                            </span>
                                            <p class="text-xs mt-0.5" style="color: #8A8F99;"><?php echo $order['payment_method']; ?></p>
                                        </td>

                                        <!-- Order Status -->
                                        <td class="px-4 py-3">
                                            <?php
                                            $status_colors = [
                                                'pending'    => 'background-color: #E3F2FD; color: #1565C0;',
                                                'confirmed'  => 'background-color: #FFF3E0; color: #E65100;',
                                                'processing' => 'background-color: #FFF3E0; color: #E65100;',
                                                'shipped'    => 'background-color: #E3F2FD; color: #1565C0;',
                                                'delivered'  => 'background-color: #E8F5E9; color: #2E7D32;'
                                            ];
                                            ?>
                                            <span class="inline-block px-2.5 py-1 rounded text-xs font-semibold" style="<?php echo $status_colors[$order['order_status']] ?? ''; ?>">
                                                <?php echo ucfirst($order['order_status']); ?>
                                            </span>
                                        </td>

                                        <!-- View -->
                                        <td class="px-4 py-3 text-right">
                                            <a href="order-detail.php?id=<?php echo $order['id']; ?>" class="text-xs font-medium px-2.5 py-1 rounded no-underline transition-all duration-200" style="border: 1px solid #E0E2E7; color: #1B2A4A;" onmouseover="this.style.backgroundColor='#F7F8FA';" onmouseout="this.style.backgroundColor='transparent';">View</a>
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