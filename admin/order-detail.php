<?php
session_start();

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

require_once '../config/db.php';

$page_title = 'Order Detail';

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['order_error'] = 'Invalid order.';
    header('Location: orders.php');
    exit();
}

// Fetch order
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['order_error'] = 'Order not found.';
    $stmt->close();
    $conn->close();
    header('Location: orders.php');
    exit();
}

$order = $result->fetch_assoc();
$stmt->close();

// Fetch order items
$items_stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
$items_stmt->bind_param("i", $id);
$items_stmt->execute();
$items_result = $items_stmt->get_result();

$items = [];
while ($row = $items_result->fetch_assoc()) {
    $items[] = $row;
}
$items_stmt->close();

$conn->close();

$success = $_SESSION['order_success'] ?? '';
$error   = $_SESSION['order_error'] ?? '';
unset($_SESSION['order_success'], $_SESSION['order_error']);

// Status flow for progress tracker
$all_statuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered'];
$current_index = array_search($order['order_status'], $all_statuses);

// Status badge colors
$status_colors = [
    'pending'    => 'background-color: #E3F2FD; color: #1565C0;',
    'confirmed'  => 'background-color: #FFF3E0; color: #E65100;',
    'processing' => 'background-color: #FFF3E0; color: #E65100;',
    'shipped'    => 'background-color: #E3F2FD; color: #1565C0;',
    'delivered'  => 'background-color: #E8F5E9; color: #2E7D32;'
];

$pay_colors = [
    'pending' => 'background-color: #FFF3E0; color: #E65100;',
    'paid'    => 'background-color: #E8F5E9; color: #2E7D32;',
    'failed'  => 'background-color: #FDEAEA; color: #D64545;'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #<?php echo htmlspecialchars($order['order_number']); ?> — ChronoNest Admin</title>
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

            <!-- Back + Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <a href="orders.php" class="text-sm font-medium no-underline transition-all duration-200 inline-block mb-2" style="color: #5A5F6D;" onmouseover="this.style.color='#1A1A2E';" onmouseout="this.style.color='#5A5F6D';">← Back to Orders</a>
                    <h2 class="font-bold text-2xl" style="font-family: 'Playfair Display', serif; color: #1A1A2E;">Order #<?php echo htmlspecialchars($order['order_number']); ?></h2>
                    <p class="text-sm mt-0.5" style="color: #5A5F6D;">Placed on <?php echo date('M d, Y \a\t h:i A', strtotime($order['ordered_at'])); ?></p>
                </div>

                <!-- Delete Button — only for pending unpaid orders -->
                <?php if ($order['order_status'] === 'pending' && $order['payment_status'] !== 'paid'): ?>
                    <form method="POST" action="actions/order-action.php" onsubmit="return confirm('Are you sure you want to delete this order?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <button type="submit" class="h-10 px-5 rounded-lg text-sm font-semibold transition-all duration-200" style="background: none; border: 1px solid rgba(214,69,69,0.3); color: #D64545; cursor: pointer;" onmouseover="this.style.backgroundColor='#FDEAEA';" onmouseout="this.style.backgroundColor='transparent';">Delete Order</button>
                    </form>
                <?php endif; ?>
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

            <!-- Status Progress Tracker -->
            <div class="bg-white rounded-lg p-5 mb-4" style="border: 1px solid #E0E2E7;">
                <h3 class="font-semibold text-base mb-4" style="color: #1A1A2E;">Order Progress</h3>

                <div class="flex items-center justify-between">
                    <?php foreach ($all_statuses as $i => $status): ?>

                        <?php
                        $is_completed = ($i <= $current_index);
                        $is_current   = ($i === $current_index);

                        // Circle colors
                        if ($is_current) {
                            $circle_bg    = '#1B2A4A';
                            $circle_color = '#FFFFFF';
                        } elseif ($is_completed) {
                            $circle_bg    = '#2E7D32';
                            $circle_color = '#FFFFFF';
                        } else {
                            $circle_bg    = '#E0E2E7';
                            $circle_color = '#8A8F99';
                        }

                        $label_color = $is_current ? '#1A1A2E' : ($is_completed ? '#2E7D32' : '#8A8F99');
                        ?>

                        <div class="flex flex-col items-center flex-1">
                            <!-- Circle -->
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold" style="background-color: <?php echo $circle_bg; ?>; color: <?php echo $circle_color; ?>;">
                                <?php if ($is_completed && !$is_current): ?>
                                    ✓
                                <?php else: ?>
                                    <?php echo $i + 1; ?>
                                <?php endif; ?>
                            </div>
                            <!-- Label -->
                            <p class="text-xs font-medium mt-1.5 text-center hidden sm:block" style="color: <?php echo $label_color; ?>;"><?php echo ucfirst($status); ?></p>
                        </div>

                        <!-- Connector line between circles -->
                        <?php if ($i < count($all_statuses) - 1): ?>
                            <div class="flex-1 h-0.5 -mt-4 sm:-mt-6" style="background-color: <?php echo ($i < $current_index) ? '#2E7D32' : '#E0E2E7'; ?>;"></div>
                        <?php endif; ?>

                    <?php endforeach; ?>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

                <!-- Left Column — Items + Summary -->
                <div class="lg:col-span-2 space-y-4">

                    <!-- Order Items -->
                    <div class="bg-white rounded-lg overflow-hidden" style="border: 1px solid #E0E2E7;">
                        <div class="px-5 py-4" style="border-bottom: 1px solid #E0E2E7;">
                            <h3 class="font-semibold text-base" style="color: #1A1A2E;">Items (<?php echo count($items); ?>)</h3>
                        </div>

                        <div class="divide-y" style="border-color: #E0E2E7;">
                            <?php foreach ($items as $item): ?>
                                <div class="flex items-center gap-4 px-5 py-4">

                                    <!-- Product Image -->
                                    <?php if (!empty($item['product_image'])): ?>
                                        <img src="../assets/uploads/products/<?php echo htmlspecialchars(basename($item['product_image'])); ?>" alt="" class="w-14 h-14 rounded object-cover flex-shrink-0" style="border: 1px solid #E0E2E7;">
                                    <?php else: ?>
                                        <div class="w-14 h-14 rounded flex items-center justify-center flex-shrink-0 text-xs" style="background-color: #F7F8FA; border: 1px solid #E0E2E7; color: #8A8F99;">No img</div>
                                    <?php endif; ?>

                                    <!-- Product Info -->
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-sm leading-tight" style="color: #1A1A2E;"><?php echo htmlspecialchars($item['product_name']); ?></p>
                                        <p class="text-xs mt-0.5" style="color: #8A8F99;">Model: <?php echo htmlspecialchars($item['product_model_number']); ?></p>
                                        <?php if (!empty($item['selected_strap_size'])): ?>
                                            <p class="text-xs mt-0.5" style="color: #8A8F99;">Strap: <?php echo htmlspecialchars($item['selected_strap_size']); ?></p>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Quantity + Price -->
                                    <div class="text-right flex-shrink-0">
                                        <p class="text-sm font-semibold" style="color: #1A1A2E;">Rs. <?php echo number_format($item['total'], 2); ?></p>
                                        <p class="text-xs mt-0.5" style="color: #8A8F99;"><?php echo $item['quantity']; ?> × Rs. <?php echo number_format($item['price'], 2); ?></p>
                                    </div>

                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Order Summary -->
                        <div class="px-5 py-4" style="background-color: #F7F8FA; border-top: 1px solid #E0E2E7;">

                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm" style="color: #5A5F6D;">Subtotal</span>
                                <span class="text-sm" style="color: #1A1A2E;">Rs. <?php echo number_format($order['total_amount'], 2); ?></span>
                            </div>

                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm" style="color: #5A5F6D;">Shipping</span>
                                <span class="text-sm" style="color: #1A1A2E;">
                                    <?php echo ($order['shipping_charge'] > 0) ? 'Rs. ' . number_format($order['shipping_charge'], 2) : 'Free'; ?>
                                </span>
                            </div>

                            <div class="flex items-center justify-between pt-2" style="border-top: 1px solid #E0E2E7;">
                                <span class="text-base font-bold" style="color: #1A1A2E;">Grand Total</span>
                                <span class="text-base font-bold" style="color: #1A1A2E;">Rs. <?php echo number_format($order['grand_total'], 2); ?></span>
                            </div>

                        </div>
                    </div>

                    <!-- Admin Notes -->
                    <div class="bg-white rounded-lg p-5" style="border: 1px solid #E0E2E7;">
                        <h3 class="font-semibold text-base mb-3" style="color: #1A1A2E;">Admin Notes</h3>

                        <form method="POST" action="actions/order-action.php">
                            <input type="hidden" name="action" value="update_notes">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">

                            <textarea name="admin_notes" rows="3" placeholder="Add internal notes about this order..." class="w-full px-4 py-3 rounded-lg text-sm outline-none resize-y mb-3" style="border: 1px solid #E0E2E7; color: #1A1A2E;" onfocus="this.style.border='2px solid #C9A84C'; this.style.boxShadow='0 0 0 3px rgba(201,168,76,0.15)';" onblur="this.style.border='1px solid #E0E2E7'; this.style.boxShadow='none';"><?php echo htmlspecialchars($order['admin_notes'] ?? ''); ?></textarea>

                            <button type="submit" class="h-9 px-4 rounded-lg text-xs font-semibold text-white" style="background-color: #1B2A4A; border: none; cursor: pointer;">Save Notes</button>
                        </form>
                    </div>

                </div>

                <!-- Right Column — Customer, Payment, Status Update -->
                <div class="space-y-4">

                    <!-- Customer Info -->
                    <div class="bg-white rounded-lg p-5" style="border: 1px solid #E0E2E7;">
                        <h3 class="font-semibold text-base mb-4" style="color: #1A1A2E;">Customer</h3>

                        <div class="space-y-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider mb-0.5" style="color: #8A8F99;">Name</p>
                                <p class="text-sm" style="color: #1A1A2E;"><?php echo htmlspecialchars($order['customer_name']); ?></p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider mb-0.5" style="color: #8A8F99;">Email</p>
                                <p class="text-sm" style="color: #1A1A2E;"><?php echo htmlspecialchars($order['customer_email']); ?></p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider mb-0.5" style="color: #8A8F99;">Phone</p>
                                <p class="text-sm" style="color: #1A1A2E;"><?php echo htmlspecialchars($order['customer_phone']); ?></p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider mb-0.5" style="color: #8A8F99;">Shipping Address</p>
                                <p class="text-sm" style="color: #1A1A2E;"><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                            </div>

                            <!-- Link to customer profile if user_id exists -->
                            <?php if (!empty($order['user_id'])): ?>
                                <a href="customer-detail.php?id=<?php echo $order['user_id']; ?>" class="inline-block text-xs font-medium no-underline mt-1" style="color: #1565C0;">View Customer Profile</a>
                            <?php else: ?>
                                <p class="text-xs mt-1" style="color: #8A8F99;">Guest checkout (no account)</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Payment Info -->
                    <div class="bg-white rounded-lg p-5" style="border: 1px solid #E0E2E7;">
                        <h3 class="font-semibold text-base mb-4" style="color: #1A1A2E;">Payment</h3>

                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm" style="color: #5A5F6D;">Method</span>
                                <span class="text-sm font-medium" style="color: #1A1A2E;"><?php echo $order['payment_method']; ?></span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-sm" style="color: #5A5F6D;">Status</span>
                                <span class="inline-block px-2.5 py-1 rounded text-xs font-semibold" style="<?php echo $pay_colors[$order['payment_status']] ?? ''; ?>">
                                    <?php echo ucfirst($order['payment_status']); ?>
                                </span>
                            </div>

                            <?php if (!empty($order['transaction_id'])): ?>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm" style="color: #5A5F6D;">Transaction ID</span>
                                    <span class="text-xs font-medium" style="color: #1A1A2E;"><?php echo htmlspecialchars($order['transaction_id']); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if (strtolower($order['payment_method']) === 'cod'): ?>

                        <!-- Update Payment Status -->
                        <form method="POST" action="actions/order-action.php" class="mt-4 pt-4" style="border-top: 1px solid #E0E2E7;">
                            <input type="hidden" name="action" value="update_payment">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">

                            <label class="block text-xs font-semibold mb-1.5" style="color: #1A1A2E;">Update Payment</label>
                            <div class="flex gap-2">
                                <select name="payment_status" class="h-9 px-3 rounded-lg text-sm outline-none flex-1" style="border: 1px solid #E0E2E7; color: #1A1A2E;">
                                    <option value="pending" <?php echo ($order['payment_status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="paid" <?php echo ($order['payment_status'] === 'paid') ? 'selected' : ''; ?>>Paid</option>
                                    <option value="failed" <?php echo ($order['payment_status'] === 'failed') ? 'selected' : ''; ?>>Failed</option>
                                </select>
                                <button type="submit" class="h-9 px-4 rounded-lg text-xs font-semibold text-white flex-shrink-0" style="background-color: #1B2A4A; border: none; cursor: pointer;">Update</button>
                            </div>
                        </form>
                        <?php else: ?>

<div class="mt-4 pt-4" style="border-top: 1px solid #E0E2E7;">
    <p class="text-xs" style="color:#8A8F99;">
        Payment status is managed automatically by eSewa after successful payment verification.
    </p>
</div>

<?php endif; ?>
                    </div>

                    <!-- Update Order Status -->
                    <div class="bg-white rounded-lg p-5" style="border: 1px solid #E0E2E7;">
                        <h3 class="font-semibold text-base mb-3" style="color: #1A1A2E;">Update Status</h3>

                        <div class="flex items-center gap-2 mb-4">
                            <span class="text-sm" style="color: #5A5F6D;">Current:</span>
                            <span class="inline-block px-2.5 py-1 rounded text-xs font-semibold" style="<?php echo $status_colors[$order['order_status']] ?? ''; ?>">
                                <?php echo ucfirst($order['order_status']); ?>
                            </span>
                        </div>

                        <?php if ($order['order_status'] !== 'delivered'): ?>

                            <!-- Show next possible status -->
                            <?php
                            $next_index  = $current_index + 1;
                            $next_status = $all_statuses[$next_index] ?? null;
                            ?>

                            <?php if ($next_status): ?>
                                <form method="POST" action="actions/order-action.php">
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <input type="hidden" name="new_status" value="<?php echo $next_status; ?>">

                                    <button type="submit" class="w-full h-10 rounded-lg text-sm font-semibold text-white transition-all duration-200" style="background-color: #1B2A4A; border: none; cursor: pointer;" onmouseover="this.style.backgroundColor='#2C4066';" onmouseout="this.style.backgroundColor='#1B2A4A';" onclick="return confirm('Move order to <?php echo ucfirst($next_status); ?>?');">
                                        Move to <?php echo ucfirst($next_status); ?>
                                    </button>
                                </form>
                            <?php endif; ?>

                        <?php else: ?>
                            <p class="text-sm" style="color: #2E7D32;">This order has been delivered.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Timeline -->
                    <div class="bg-white rounded-lg p-5" style="border: 1px solid #E0E2E7;">
                        <h3 class="font-semibold text-base mb-4" style="color: #1A1A2E;">Timeline</h3>

                        <div class="space-y-4">

                            <!-- Ordered -->
                            <div class="flex items-start gap-3">
                                <div class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0" style="background-color: #2E7D32;"></div>
                                <div>
                                    <p class="text-sm font-medium" style="color: #1A1A2E;">Order Placed</p>
                                    <p class="text-xs" style="color: #8A8F99;"><?php echo date('M d, Y \a\t h:i A', strtotime($order['ordered_at'])); ?></p>
                                </div>
                            </div>

                            <!-- Confirmed -->
                            <?php if ($order['confirmed_at']): ?>
                                <div class="flex items-start gap-3">
                                    <div class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0" style="background-color: #2E7D32;"></div>
                                    <div>
                                        <p class="text-sm font-medium" style="color: #1A1A2E;">Confirmed</p>
                                        <p class="text-xs" style="color: #8A8F99;"><?php echo date('M d, Y \a\t h:i A', strtotime($order['confirmed_at'])); ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Shipped -->
                            <?php if ($order['shipped_at']): ?>
                                <div class="flex items-start gap-3">
                                    <div class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0" style="background-color: #2E7D32;"></div>
                                    <div>
                                        <p class="text-sm font-medium" style="color: #1A1A2E;">Shipped</p>
                                        <p class="text-xs" style="color: #8A8F99;"><?php echo date('M d, Y \a\t h:i A', strtotime($order['shipped_at'])); ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Delivered -->
                            <?php if ($order['delivered_at']): ?>
                                <div class="flex items-start gap-3">
                                    <div class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0" style="background-color: #2E7D32;"></div>
                                    <div>
                                        <p class="text-sm font-medium" style="color: #1A1A2E;">Delivered</p>
                                        <p class="text-xs" style="color: #8A8F99;"><?php echo date('M d, Y \a\t h:i A', strtotime($order['delivered_at'])); ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>

                </div>

            </div>

        </div>
    </main>

    <script src="../assets/js/admin.js"></script>

</body>
</html>