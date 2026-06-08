<?php
session_start();

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

require_once '../config/db.php';

$page_title = 'Dashboard';

// =============================================
// FETCH ALL DASHBOARD DATA
// =============================================

// --- Product Stats ---
$product_result = $conn->query("
    SELECT
        COUNT(*) as total,
        SUM(is_active = 1) as active,
        SUM(is_active = 0) as inactive,
        SUM(stock_quantity <= 0) as out_of_stock,
        SUM(stock_quantity > 0 AND stock_quantity <= 5) as low_stock
    FROM products
");
$product_stats = $product_result->fetch_assoc();

// --- Brand Stats ---
$brand_result = $conn->query("SELECT COUNT(*) as total FROM brands");
$brand_stats = $brand_result->fetch_assoc();

// --- Customer Stats ---
$customer_result = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'customer'");
$customer_stats = $customer_result->fetch_assoc();

// --- Order Stats ---
$order_result = $conn->query("
    SELECT
        COUNT(*) as total,
        SUM(order_status = 'pending') as pending,
        SUM(order_status = 'confirmed') as confirmed,
        SUM(order_status = 'processing') as processing,
        SUM(order_status = 'shipped') as shipped,
        SUM(order_status = 'delivered') as delivered,
        COALESCE(SUM(CASE WHEN payment_status = 'paid' THEN grand_total ELSE 0 END), 0) as total_revenue,
        SUM(payment_status = 'paid') as paid_orders,
        SUM(payment_status = 'pending') as unpaid_orders,
        SUM(payment_method = 'COD') as cod_orders,
        SUM(payment_method = 'eSewa') as esewa_orders
    FROM orders
");
$order_stats = $order_result->fetch_assoc();

// --- Recent Orders (last 10) ---
$recent_orders_result = $conn->query("
    SELECT id, order_number, customer_name, grand_total, order_status, payment_status, payment_method, ordered_at
    FROM orders
    ORDER BY ordered_at DESC
    LIMIT 10
");
$recent_orders = [];
while ($row = $recent_orders_result->fetch_assoc()) {
    $recent_orders[] = $row;
}

// --- Recent Customers (last 5) ---
$recent_customers_result = $conn->query("
    SELECT id, name, email, phone, created_at
    FROM users
    WHERE role = 'customer'
    ORDER BY created_at DESC
    LIMIT 5
");
$recent_customers = [];
while ($row = $recent_customers_result->fetch_assoc()) {
    $recent_customers[] = $row;
}

// --- Low Stock Products ---
$low_stock_result = $conn->query("
    SELECT p.id, p.name, p.model_number, p.stock_quantity, b.name as brand_name,
        (SELECT image_url FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image
    FROM products p
    LEFT JOIN brands b ON p.brand_id = b.id
    WHERE p.stock_quantity <= 5 AND p.is_active = 1
    ORDER BY p.stock_quantity ASC
    LIMIT 10
");
$low_stock_products = [];
while ($row = $low_stock_result->fetch_assoc()) {
    $low_stock_products[] = $row;
}

$conn->close();

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

            <!-- Welcome -->
            <div class="mb-6">
                <h2 class="font-bold text-2xl mb-1" style="font-family: 'Playfair Display', serif; color: #1A1A2E;">Welcome back, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></h2>
                <p class="text-sm" style="color: #5A5F6D;">Here's what's happening with your store.</p>
            </div>

            <!-- ================================ -->
            <!-- MAIN STAT CARDS                  -->
            <!-- ================================ -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

                <!-- Revenue -->
                <div class="bg-white rounded-lg p-5" style="border: 1px solid #E0E2E7;">
                    <p class="text-xs font-semibold uppercase tracking-wider mb-3" style="color: #5A5F6D;">Total Revenue</p>
                    <p class="text-2xl font-bold" style="color: #1A1A2E;">Rs. <?php echo number_format($order_stats['total_revenue'], 2); ?></p>
                    <p class="text-xs mt-2" style="color: #8A8F99;">From <?php echo $order_stats['paid_orders']; ?> paid orders</p>
                </div>

                <!-- Orders -->
                <div class="bg-white rounded-lg p-5" style="border: 1px solid #E0E2E7;">
                    <p class="text-xs font-semibold uppercase tracking-wider mb-3" style="color: #5A5F6D;">Total Orders</p>
                    <p class="text-2xl font-bold" style="color: #1A1A2E;"><?php echo $order_stats['total']; ?></p>
                    <p class="text-xs mt-2" style="color: #8A8F99;"><?php echo $order_stats['pending']; ?> pending</p>
                </div>

                <!-- Products -->
                <div class="bg-white rounded-lg p-5" style="border: 1px solid #E0E2E7;">
                    <p class="text-xs font-semibold uppercase tracking-wider mb-3" style="color: #5A5F6D;">Total Products</p>
                    <p class="text-2xl font-bold" style="color: #1A1A2E;"><?php echo $product_stats['total']; ?></p>
                    <p class="text-xs mt-2" style="color: #8A8F99;"><?php echo $product_stats['active']; ?> active</p>
                </div>

                <!-- Customers -->
                <div class="bg-white rounded-lg p-5" style="border: 1px solid #E0E2E7;">
                    <p class="text-xs font-semibold uppercase tracking-wider mb-3" style="color: #5A5F6D;">Customers</p>
                    <p class="text-2xl font-bold" style="color: #1A1A2E;"><?php echo $customer_stats['total']; ?></p>
                    <p class="text-xs mt-2" style="color: #8A8F99;">Registered accounts</p>
                </div>

            </div>

            <!-- ================================ -->
            <!-- ORDER STATUS BREAKDOWN           -->
            <!-- ================================ -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mb-6">

                <?php
                $order_status_cards = [
                    ['label' => 'Pending',    'count' => $order_stats['pending'],    'bg' => '#E3F2FD', 'color' => '#1565C0'],
                    ['label' => 'Confirmed',  'count' => $order_stats['confirmed'],  'bg' => '#FFF3E0', 'color' => '#E65100'],
                    ['label' => 'Processing', 'count' => $order_stats['processing'], 'bg' => '#FFF3E0', 'color' => '#E65100'],
                    ['label' => 'Shipped',    'count' => $order_stats['shipped'],    'bg' => '#E3F2FD', 'color' => '#1565C0'],
                    ['label' => 'Delivered',  'count' => $order_stats['delivered'],  'bg' => '#E8F5E9', 'color' => '#2E7D32'],
                ];

                foreach ($order_status_cards as $card):
                ?>
                    <a href="orders.php?status=<?php echo strtolower($card['label']); ?>" class="rounded-lg p-4 no-underline transition-all duration-200" style="background-color: <?php echo $card['bg']; ?>; border: 1px solid transparent;" onmouseover="this.style.opacity='0.85';" onmouseout="this.style.opacity='1';">
                        <p class="text-2xl font-bold" style="color: <?php echo $card['color']; ?>;"><?php echo $card['count']; ?></p>
                        <p class="text-xs font-semibold mt-1" style="color: <?php echo $card['color']; ?>;"><?php echo $card['label']; ?></p>
                    </a>
                <?php endforeach; ?>

            </div>

            <!-- ================================ -->
            <!-- TWO COLUMN SECTION               -->
            <!-- ================================ -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">

                <!-- Recent Orders (2 cols) -->
                <div class="lg:col-span-2 bg-white rounded-lg overflow-hidden" style="border: 1px solid #E0E2E7;">

                    <div class="flex items-center justify-between px-5 py-4" style="border-bottom: 1px solid #E0E2E7;">
                        <div>
                            <h3 class="font-semibold text-base" style="color: #1A1A2E;">Recent Orders</h3>
                            <p class="text-xs mt-0.5" style="color: #8A8F99;">Last 10 orders</p>
                        </div>
                        <a href="orders.php" class="text-xs font-semibold no-underline" style="color: #1565C0;">View All</a>
                    </div>

                    <?php if (empty($recent_orders)): ?>
                        <div class="p-6 text-center">
                            <p class="text-sm" style="color: #8A8F99;">No orders yet.</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead>
                                    <tr style="background-color: #F7F8FA; border-bottom: 1px solid #E0E2E7;">
                                        <th class="px-4 py-2.5 font-semibold" style="color: #5A5F6D;">Order</th>
                                        <th class="px-4 py-2.5 font-semibold hidden sm:table-cell" style="color: #5A5F6D;">Customer</th>
                                        <th class="px-4 py-2.5 font-semibold" style="color: #5A5F6D;">Total</th>
                                        <th class="px-4 py-2.5 font-semibold hidden md:table-cell" style="color: #5A5F6D;">Payment</th>
                                        <th class="px-4 py-2.5 font-semibold" style="color: #5A5F6D;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_orders as $order): ?>
                                        <tr style="border-bottom: 1px solid #E0E2E7;" class="cursor-pointer" onclick="window.location='order-detail.php?id=<?php echo $order['id']; ?>';" onmouseover="this.style.backgroundColor='#F7F8FA';" onmouseout="this.style.backgroundColor='transparent';">

                                            <td class="px-4 py-2.5">
                                                <p class="font-medium" style="color: #1A1A2E;">#<?php echo htmlspecialchars($order['order_number']); ?></p>
                                                <p class="text-xs mt-0.5 sm:hidden" style="color: #8A8F99;"><?php echo htmlspecialchars($order['customer_name']); ?></p>
                                            </td>

                                            <td class="px-4 py-2.5 hidden sm:table-cell" style="color: #5A5F6D;">
                                                <?php echo htmlspecialchars($order['customer_name']); ?>
                                            </td>

                                            <td class="px-4 py-2.5 font-semibold" style="color: #1A1A2E;">
                                                Rs. <?php echo number_format($order['grand_total'], 2); ?>
                                            </td>

                                            <td class="px-4 py-2.5 hidden md:table-cell">
                                                <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold" style="<?php echo $pay_colors[$order['payment_status']] ?? ''; ?>">
                                                    <?php echo ucfirst($order['payment_status']); ?>
                                                </span>
                                            </td>

                                            <td class="px-4 py-2.5">
                                                <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold" style="<?php echo $status_colors[$order['order_status']] ?? ''; ?>">
                                                    <?php echo ucfirst($order['order_status']); ?>
                                                </span>
                                            </td>

                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                </div>

                <!-- Right Side Cards -->
                <div class="space-y-4">

                    <!-- Payment Breakdown -->
                    <div class="bg-white rounded-lg p-5" style="border: 1px solid #E0E2E7;">
                        <h3 class="font-semibold text-base mb-4" style="color: #1A1A2E;">Payments</h3>

                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm" style="color: #5A5F6D;">Paid Orders</span>
                                <span class="text-sm font-semibold" style="color: #2E7D32;"><?php echo $order_stats['paid_orders']; ?></span>
                            </div>

                            <div class="flex items-center justify-between" style="border-top: 1px solid #F0F1F3; padding-top: 12px;">
                                <span class="text-sm" style="color: #5A5F6D;">Unpaid Orders</span>
                                <span class="text-sm font-semibold" style="color: #E65100;"><?php echo $order_stats['unpaid_orders']; ?></span>
                            </div>

                            <div class="flex items-center justify-between" style="border-top: 1px solid #F0F1F3; padding-top: 12px;">
                                <span class="text-sm" style="color: #5A5F6D;">COD Orders</span>
                                <span class="text-sm font-semibold" style="color: #1A1A2E;"><?php echo $order_stats['cod_orders']; ?></span>
                            </div>

                            <div class="flex items-center justify-between" style="border-top: 1px solid #F0F1F3; padding-top: 12px;">
                                <span class="text-sm" style="color: #5A5F6D;">eSewa Orders</span>
                                <span class="text-sm font-semibold" style="color: #1A1A2E;"><?php echo $order_stats['esewa_orders']; ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Product Health -->
                    <div class="bg-white rounded-lg p-5" style="border: 1px solid #E0E2E7;">
                        <h3 class="font-semibold text-base mb-4" style="color: #1A1A2E;">Inventory</h3>

                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm" style="color: #5A5F6D;">Active Products</span>
                                <span class="text-sm font-semibold" style="color: #2E7D32;"><?php echo $product_stats['active']; ?></span>
                            </div>

                            <div class="flex items-center justify-between" style="border-top: 1px solid #F0F1F3; padding-top: 12px;">
                                <span class="text-sm" style="color: #5A5F6D;">Inactive Products</span>
                                <span class="text-sm font-semibold" style="color: #8A8F99;"><?php echo $product_stats['inactive']; ?></span>
                            </div>

                            <div class="flex items-center justify-between" style="border-top: 1px solid #F0F1F3; padding-top: 12px;">
                                <span class="text-sm" style="color: #5A5F6D;">Low Stock</span>
                                <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold" style="background-color: #FFF3E0; color: #E65100;"><?php echo $product_stats['low_stock']; ?></span>
                            </div>

                            <div class="flex items-center justify-between" style="border-top: 1px solid #F0F1F3; padding-top: 12px;">
                                <span class="text-sm" style="color: #5A5F6D;">Out of Stock</span>
                                <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold" style="background-color: #FDEAEA; color: #D64545;"><?php echo $product_stats['out_of_stock']; ?></span>
                            </div>

                            <div class="flex items-center justify-between" style="border-top: 1px solid #F0F1F3; padding-top: 12px;">
                                <span class="text-sm" style="color: #5A5F6D;">Total Brands</span>
                                <span class="text-sm font-semibold" style="color: #1A1A2E;"><?php echo $brand_stats['total']; ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Customers -->
                    <div class="bg-white rounded-lg overflow-hidden" style="border: 1px solid #E0E2E7;">
                        <div class="flex items-center justify-between px-5 py-4" style="border-bottom: 1px solid #E0E2E7;">
                            <h3 class="font-semibold text-base" style="color: #1A1A2E;">New Customers</h3>
                            <a href="customers.php" class="text-xs font-semibold no-underline" style="color: #1565C0;">View All</a>
                        </div>

                        <?php if (empty($recent_customers)): ?>
                            <div class="p-5 text-center">
                                <p class="text-sm" style="color: #8A8F99;">No customers yet.</p>
                            </div>
                        <?php else: ?>
                            <div>
                                <?php foreach ($recent_customers as $cust): ?>
                                    <a href="customer-detail.php?id=<?php echo $cust['id']; ?>" class="flex items-center gap-3 px-5 py-3 no-underline transition-all duration-200" style="border-bottom: 1px solid #F0F1F3;" onmouseover="this.style.backgroundColor='#F7F8FA';" onmouseout="this.style.backgroundColor='transparent';">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0" style="background-color: #1B2A4A;">
                                            <?php echo strtoupper(substr($cust['name'], 0, 1)); ?>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium leading-tight" style="color: #1A1A2E;"><?php echo htmlspecialchars($cust['name']); ?></p>
                                            <p class="text-xs" style="color: #8A8F99;"><?php echo htmlspecialchars($cust['email']); ?></p>
                                        </div>
                                        <span class="text-xs flex-shrink-0" style="color: #8A8F99;"><?php echo date('M d', strtotime($cust['created_at'])); ?></span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>

            </div>

            <!-- ================================ -->
            <!-- LOW STOCK ALERT                  -->
            <!-- ================================ -->
            <?php if (!empty($low_stock_products)): ?>
                <div class="bg-white rounded-lg overflow-hidden mb-6" style="border: 1px solid #E0E2E7;">

                    <div class="flex items-center justify-between px-5 py-4" style="border-bottom: 1px solid #E0E2E7;">
                        <div>
                            <h3 class="font-semibold text-base" style="color: #1A1A2E;">Low Stock Alert</h3>
                            <p class="text-xs mt-0.5" style="color: #8A8F99;">Products with 5 or fewer items in stock</p>
                        </div>
                        <a href="products.php" class="text-xs font-semibold no-underline" style="color: #1565C0;">View All Products</a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead>
                                <tr style="background-color: #F7F8FA; border-bottom: 1px solid #E0E2E7;">
                                    <th class="px-4 py-2.5 font-semibold" style="color: #5A5F6D;">Product</th>
                                    <th class="px-4 py-2.5 font-semibold hidden sm:table-cell" style="color: #5A5F6D;">Brand</th>
                                    <th class="px-4 py-2.5 font-semibold" style="color: #5A5F6D;">Stock</th>
                                    <th class="px-4 py-2.5 font-semibold text-right" style="color: #5A5F6D;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($low_stock_products as $product): ?>
                                    <tr style="border-bottom: 1px solid #E0E2E7;">

                                        <td class="px-4 py-2.5">
                                            <div class="flex items-center gap-3">
                                                <?php if (!empty($product['main_image'])): ?>
                                                    <img src="../<?php echo htmlspecialchars($product['main_image']); ?>" alt="" class="w-8 h-8 rounded object-cover flex-shrink-0" style="border: 1px solid #E0E2E7;">
                                                <?php else: ?>
                                                    <div class="w-8 h-8 rounded flex items-center justify-center flex-shrink-0 text-xs" style="background-color: #F7F8FA; border: 1px solid #E0E2E7; color: #8A8F99;">—</div>
                                                <?php endif; ?>
                                                <div>
                                                    <p class="font-medium leading-tight" style="color: #1A1A2E;"><?php echo htmlspecialchars($product['name']); ?></p>
                                                    <p class="text-xs mt-0.5" style="color: #8A8F99;"><?php echo htmlspecialchars($product['model_number']); ?></p>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-4 py-2.5 hidden sm:table-cell" style="color: #5A5F6D;">
                                            <?php echo htmlspecialchars($product['brand_name'] ?? '—'); ?>
                                        </td>

                                        <td class="px-4 py-2.5">
                                            <?php if ($product['stock_quantity'] <= 0): ?>
                                                <span class="inline-block px-2.5 py-1 rounded text-xs font-semibold" style="background-color: #FDEAEA; color: #D64545;">Out of Stock</span>
                                            <?php else: ?>
                                                <span class="inline-block px-2.5 py-1 rounded text-xs font-semibold" style="background-color: #FFF3E0; color: #E65100;"><?php echo $product['stock_quantity']; ?> left</span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="px-4 py-2.5 text-right">
                                            <a href="edit-product.php?id=<?php echo $product['id']; ?>" class="text-xs font-medium px-2.5 py-1 rounded no-underline transition-all duration-200" style="border: 1px solid #E0E2E7; color: #1B2A4A;" onmouseover="this.style.backgroundColor='#F7F8FA';" onmouseout="this.style.backgroundColor='transparent';">Edit</a>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            <?php endif; ?>

            <!-- ================================ -->
            <!-- QUICK ACTIONS                    -->
            <!-- ================================ -->
            <div class="bg-white rounded-lg p-5" style="border: 1px solid #E0E2E7;">
                <h3 class="font-semibold text-base mb-4" style="color: #1A1A2E;">Quick Actions</h3>
                <div class="flex flex-wrap gap-3">
                    <a href="add-product.php" class="inline-flex items-center h-10 px-5 rounded-lg text-sm font-semibold text-white no-underline transition-all duration-200" style="background-color: #1B2A4A;" onmouseover="this.style.backgroundColor='#2C4066';" onmouseout="this.style.backgroundColor='#1B2A4A';">Add Product</a>
                    <a href="add-brand.php" class="inline-flex items-center h-10 px-5 rounded-lg text-sm font-semibold no-underline transition-all duration-200" style="color: #1B2A4A; border: 1px solid #1B2A4A;" onmouseover="this.style.backgroundColor='#F0F2F5';" onmouseout="this.style.backgroundColor='transparent';">Add Brand</a>
                    <a href="orders.php?status=pending" class="inline-flex items-center h-10 px-5 rounded-lg text-sm font-semibold no-underline transition-all duration-200" style="color: #1B2A4A; border: 1px solid #1B2A4A;" onmouseover="this.style.backgroundColor='#F0F2F5';" onmouseout="this.style.backgroundColor='transparent';">Pending Orders</a>
                    <a href="customers.php" class="inline-flex items-center h-10 px-5 rounded-lg text-sm font-semibold no-underline transition-all duration-200" style="color: #1B2A4A; border: 1px solid #1B2A4A;" onmouseover="this.style.backgroundColor='#F0F2F5';" onmouseout="this.style.backgroundColor='transparent';">View Customers</a>
                </div>
            </div>

        </div>
    </main>

    <script src="../assets/js/admin.js"></script>

</body>
</html>