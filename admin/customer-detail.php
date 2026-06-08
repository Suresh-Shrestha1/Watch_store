<?php
session_start();

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

require_once '../config/db.php';

$page_title = 'Customer Detail';

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['customer_error'] = 'Invalid customer.';
    header('Location: customers.php');
    exit();
}

// Fetch customer
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND role = 'customer'");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['customer_error'] = 'Customer not found.';
    $stmt->close();
    $conn->close();
    header('Location: customers.php');
    exit();
}

$customer = $result->fetch_assoc();
$stmt->close();

// Fetch customer orders
$order_stmt = $conn->prepare("
    SELECT * FROM orders
    WHERE user_id = ?
    ORDER BY ordered_at DESC
");
$order_stmt->bind_param("i", $id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();

$orders = [];
while ($row = $order_result->fetch_assoc()) {
    $orders[] = $row;
}
$order_stmt->close();

// Calculate stats
$total_orders = count($orders);
$total_spent  = 0;
$paid_orders  = 0;

foreach ($orders as $order) {
    if ($order['payment_status'] === 'paid') {
        $total_spent += $order['grand_total'];
        $paid_orders++;
    }
}

// Fetch wishlist count
$wish_stmt = $conn->prepare("SELECT COUNT(*) as total FROM wishlists WHERE user_id = ?");
$wish_stmt->bind_param("i", $id);
$wish_stmt->execute();
$wishlist_count = $wish_stmt->get_result()->fetch_assoc()['total'];
$wish_stmt->close();

// Fetch cart count
$cart_stmt = $conn->prepare("SELECT COUNT(*) as total FROM cart WHERE user_id = ?");
$cart_stmt->bind_param("i", $id);
$cart_stmt->execute();
$cart_count = $cart_stmt->get_result()->fetch_assoc()['total'];
$cart_stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($customer['name']); ?> — ChronoNest Admin</title>
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

            <!-- Back -->
            <div class="mb-6">
                <a href="customers.php" class="text-sm font-medium no-underline transition-all duration-200" style="color: #5A5F6D;" onmouseover="this.style.color='#1A1A2E';" onmouseout="this.style.color='#5A5F6D';">← Back to Customers</a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

                <!-- Left Column — Customer Info -->
                <div class="space-y-4">

                    <!-- Profile Card -->
                    <div class="bg-white rounded-lg p-5" style="border: 1px solid #E0E2E7;">

                        <!-- Avatar + Name -->
                        <div class="text-center mb-5">
                            <div class="w-16 h-16 rounded-full flex items-center justify-center text-xl font-bold text-white mx-auto mb-3" style="background-color: #1B2A4A;">
                                <?php echo strtoupper(substr($customer['name'], 0, 1)); ?>
                            </div>
                            <h2 class="font-bold text-lg" style="color: #1A1A2E;"><?php echo htmlspecialchars($customer['name']); ?></h2>
                            <p class="text-xs mt-1" style="color: #8A8F99;">Customer ID: #<?php echo $customer['id']; ?></p>
                        </div>

                        <!-- Info Rows -->
                        <div class="space-y-3" style="border-top: 1px solid #E0E2E7; padding-top: 16px;">

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider mb-0.5" style="color: #8A8F99;">Email</p>
                                <p class="text-sm" style="color: #1A1A2E;"><?php echo htmlspecialchars($customer['email']); ?></p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider mb-0.5" style="color: #8A8F99;">Phone</p>
                                <p class="text-sm" style="color: #1A1A2E;"><?php echo htmlspecialchars($customer['phone']); ?></p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider mb-0.5" style="color: #8A8F99;">Address</p>
                                <p class="text-sm" style="color: #1A1A2E;"><?php echo !empty($customer['address']) ? htmlspecialchars($customer['address']) : 'Not provided'; ?></p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider mb-0.5" style="color: #8A8F99;">Joined</p>
                                <p class="text-sm" style="color: #1A1A2E;"><?php echo date('M d, Y \a\t h:i A', strtotime($customer['created_at'])); ?></p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider mb-0.5" style="color: #8A8F99;">Last Login</p>
                                <p class="text-sm" style="color: #1A1A2E;">
                                    <?php echo $customer['last_login_at'] ? date('M d, Y \a\t h:i A', strtotime($customer['last_login_at'])) : 'Never'; ?>
                                </p>
                            </div>

                        </div>
                    </div>

                    <!-- Stats Card -->
                    <div class="bg-white rounded-lg p-5" style="border: 1px solid #E0E2E7;">

                        <h3 class="font-semibold text-base mb-4" style="color: #1A1A2E;">Summary</h3>

                        <div class="space-y-3">

                            <div class="flex items-center justify-between">
                                <span class="text-sm" style="color: #5A5F6D;">Total Orders</span>
                                <span class="text-sm font-semibold" style="color: #1A1A2E;"><?php echo $total_orders; ?></span>
                            </div>

                            <div class="flex items-center justify-between" style="border-top: 1px solid #F0F1F3; padding-top: 12px;">
                                <span class="text-sm" style="color: #5A5F6D;">Paid Orders</span>
                                <span class="text-sm font-semibold" style="color: #2E7D32;"><?php echo $paid_orders; ?></span>
                            </div>

                            <div class="flex items-center justify-between" style="border-top: 1px solid #F0F1F3; padding-top: 12px;">
                                <span class="text-sm" style="color: #5A5F6D;">Total Spent</span>
                                <span class="text-sm font-bold" style="color: #1A1A2E;">Rs. <?php echo number_format($total_spent, 2); ?></span>
                            </div>

                            <div class="flex items-center justify-between" style="border-top: 1px solid #F0F1F3; padding-top: 12px;">
                                <span class="text-sm" style="color: #5A5F6D;">Wishlist Items</span>
                                <span class="text-sm font-semibold" style="color: #1A1A2E;"><?php echo $wishlist_count; ?></span>
                            </div>

                            <div class="flex items-center justify-between" style="border-top: 1px solid #F0F1F3; padding-top: 12px;">
                                <span class="text-sm" style="color: #5A5F6D;">Cart Items</span>
                                <span class="text-sm font-semibold" style="color: #1A1A2E;"><?php echo $cart_count; ?></span>
                            </div>

                        </div>
                    </div>

                </div>

                <!-- Right Column — Orders -->
                <div class="lg:col-span-2">

                    <div class="bg-white rounded-lg overflow-hidden" style="border: 1px solid #E0E2E7;">

                        <!-- Orders Header -->
                        <div class="px-5 py-4" style="border-bottom: 1px solid #E0E2E7;">
                            <h3 class="font-semibold text-base" style="color: #1A1A2E;">Order History</h3>
                            <p class="text-xs mt-0.5" style="color: #8A8F99;"><?php echo $total_orders; ?> total orders</p>
                        </div>

                        <?php if (empty($orders)): ?>

                            <div class="p-8 text-center">
                                <p class="text-sm" style="color: #8A8F99;">This customer has no orders yet.</p>
                            </div>

                        <?php else: ?>

                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left">
                                    <thead>
                                        <tr style="background-color: #F7F8FA; border-bottom: 1px solid #E0E2E7;">
                                            <th class="px-4 py-3 font-semibold" style="color: #5A5F6D;">Order</th>
                                            <th class="px-4 py-3 font-semibold hidden sm:table-cell" style="color: #5A5F6D;">Date</th>
                                            <th class="px-4 py-3 font-semibold" style="color: #5A5F6D;">Total</th>
                                            <th class="px-4 py-3 font-semibold hidden sm:table-cell" style="color: #5A5F6D;">Payment</th>
                                            <th class="px-4 py-3 font-semibold" style="color: #5A5F6D;">Status</th>
                                            <th class="px-4 py-3 font-semibold text-right" style="color: #5A5F6D;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orders as $order): ?>
                                            <tr style="border-bottom: 1px solid #E0E2E7;">

                                                <!-- Order Number -->
                                                <td class="px-4 py-3">
                                                    <p class="font-medium" style="color: #1A1A2E;">#<?php echo htmlspecialchars($order['order_number']); ?></p>
                                                    <p class="text-xs mt-0.5 sm:hidden" style="color: #8A8F99;"><?php echo date('M d, Y', strtotime($order['ordered_at'])); ?></p>
                                                </td>

                                                <!-- Date -->
                                                <td class="px-4 py-3 hidden sm:table-cell" style="color: #5A5F6D;">
                                                    <?php echo date('M d, Y', strtotime($order['ordered_at'])); ?>
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
                                                    $pay_style = $pay_colors[$order['payment_status']] ?? '';
                                                    ?>
                                                    <span class="inline-block px-2.5 py-1 rounded text-xs font-semibold" style="<?php echo $pay_style; ?>">
                                                        <?php echo ucfirst($order['payment_status']); ?>
                                                    </span>
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
                                                    $order_style = $status_colors[$order['order_status']] ?? '';
                                                    ?>
                                                    <span class="inline-block px-2.5 py-1 rounded text-xs font-semibold" style="<?php echo $order_style; ?>">
                                                        <?php echo ucfirst($order['order_status']); ?>
                                                    </span>
                                                </td>

                                                <!-- View Order -->
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

            </div>

        </div>
    </main>

    <script src="../assets/js/admin.js"></script>

</body>
</html>