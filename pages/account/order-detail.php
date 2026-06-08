<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../login.php'); exit; }
require_once '../../config/db.php';

$user_id = $_SESSION['user_id'];
$order_id = (int)($_GET['id'] ?? 0);

if (!$order_id) { header('Location: orders.php'); exit; }

$stmt = mysqli_prepare($conn, "SELECT * FROM orders WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $order_id, $user_id);
mysqli_stmt_execute($stmt);
$order = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$order) { header('Location: orders.php'); exit; }

$items_result = mysqli_query($conn, "SELECT oi.*, p.slug FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = $order_id");
$items = mysqli_fetch_all($items_result, MYSQLI_ASSOC);

// Sidebar counts
$order_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM orders WHERE user_id = $user_id"))['cnt'];
$wishlist_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM wishlists WHERE user_id = $user_id"))['cnt'];
$cart_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM cart WHERE user_id = $user_id"))['cnt'];

$page_title = "Order #" . $order['order_number'] . " – ChronoNest";
$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);

$status_classes = match($order['order_status']) {
    'pending' => 'bg-[#E3F2FD] text-[#1565C0]',
    'confirmed' => 'bg-[#FFF3E0] text-[#E65100]',
    'processing' => 'bg-[#FFF3E0] text-[#E65100]',
    'shipped' => 'bg-[#E3F2FD] text-[#1565C0]',
    'delivered' => 'bg-[#E8F5E9] text-[#2E7D32]',
    default => 'bg-[#F7F8FA] text-[#5A5F6D]'
};
$pay_classes = match($order['payment_status']) {
    'paid' => 'bg-[#E8F5E9] text-[#2E7D32]',
    'failed' => 'bg-[#FDEAEA] text-[#D64545]',
    default => 'bg-[#FFF3E0] text-[#E65100]'
};

$timeline = [
    ['status' => 'pending', 'label' => 'Order Placed', 'date' => $order['ordered_at'], 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
    ['status' => 'confirmed', 'label' => 'Confirmed', 'date' => $order['confirmed_at'], 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
    ['status' => 'processing', 'label' => 'Processing', 'date' => null, 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'],
    ['status' => 'shipped', 'label' => 'Shipped', 'date' => $order['shipped_at'], 'icon' => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 12a2 2 0 002 2h8a2 2 0 002-2L19 8'],
    ['status' => 'delivered', 'label' => 'Delivered', 'date' => $order['delivered_at'], 'icon' => 'M5 13l4 4L19 7'],
];

$status_order = ['pending' => 0, 'confirmed' => 1, 'processing' => 2, 'shipped' => 3, 'delivered' => 4];
$current_step = $status_order[$order['order_status']] ?? 0;

$od_length_map = ['XS' => 145, 'S' => 160, 'M' => 175, 'L' => 190, 'XL' => 205];

require_once '../../includes/header.php';
?>

<!-- PAGE HEADER -->
<section class="bg-[#1B2A4A] py-10">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="font-['Inter'] font-semibold text-[11px] uppercase tracking-[1.5px] text-[#C9A84C]">Order Details</span>
                <h1 class="font-['Playfair_Display'] font-bold text-[28px] md:text-[32px] text-white mt-1">#<?= htmlspecialchars($order['order_number']) ?></h1>
                <p class="font-['Inter'] text-[13px] text-[#B0B8C9] mt-1">Placed on <?= date('F d, Y · h:i A', strtotime($order['ordered_at'])) ?></p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-block px-3 py-1.5 rounded-lg font-['Inter'] font-semibold text-[12px] <?= $status_classes ?>"><?= ucfirst($order['order_status']) ?></span>
                <span class="inline-block px-3 py-1.5 rounded-lg font-['Inter'] font-semibold text-[12px] <?= $pay_classes ?>"><?= ucfirst($order['payment_status']) ?></span>
            </div>
        </div>
    </div>
</section>

<div class="bg-white border-b border-[#E0E2E7]">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-3">
        <nav class="flex items-center gap-2 font-['Inter'] text-[13px] text-[#5A5F6D]">
            <a href="../../index.php" class="hover:text-[#C9A84C] transition-colors">Home</a>
            <svg class="w-3 h-3 text-[#8A8F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="index.php" class="hover:text-[#C9A84C] transition-colors">Account</a>
            <svg class="w-3 h-3 text-[#8A8F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="orders.php" class="hover:text-[#C9A84C] transition-colors">Orders</a>
            <svg class="w-3 h-3 text-[#8A8F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-[#1A1A2E] font-medium">#<?= htmlspecialchars($order['order_number']) ?></span>
        </nav>
    </div>
</div>

<!-- ALERTS -->
<div class="container mx-auto px-4 sm:px-6 lg:px-8 mt-4">
    <?php if ($success): ?>
    <div class="flex items-center gap-3 bg-[#E8F5E9] border border-[#2E7D32]/20 text-[#2E7D32] rounded-lg px-4 py-3 mb-4 font-['Inter'] text-[14px]"><svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
    <div class="flex items-center gap-3 bg-[#FDEAEA] border border-[#D64545]/20 text-[#D64545] rounded-lg px-4 py-3 mb-4 font-['Inter'] text-[14px]"><svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
</div>

<section class="py-8 bg-[#F7F8FA]">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-8">

            <!-- SIDEBAR -->
            <aside class="w-full lg:w-[240px] flex-shrink-0">
                <div class="bg-white rounded-xl border border-[#E0E2E7] shadow-[0_2px_12px_rgba(0,0,0,0.08)] overflow-hidden sticky top-[100px]">
                    <nav class="flex flex-col">
                        <a href="index.php" class="flex items-center gap-3 px-5 py-3.5 border-l-[3px] border-transparent hover:bg-[#F7F8FA] hover:border-[#E0E2E7] font-['Inter'] font-medium text-[14px] text-[#5A5F6D] hover:text-[#1A1A2E] transition-all duration-150">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            My Profile
                        </a>
                        <a href="orders.php" class="flex items-center gap-3 px-5 py-3.5 bg-[#FFF8E7] border-l-[3px] border-[#C9A84C] font-['Inter'] font-semibold text-[14px] text-[#C9A84C]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            My Orders
                            <?php if ($order_count): ?><span class="ml-auto bg-[#FFF8E7] border border-[#C9A84C]/30 rounded-full px-2 py-0.5 font-['Inter'] font-semibold text-[11px] text-[#C9A84C]"><?= $order_count ?></span><?php endif; ?>
                        </a>
                        <a href="wishlist.php" class="flex items-center gap-3 px-5 py-3.5 border-l-[3px] border-transparent hover:bg-[#F7F8FA] hover:border-[#E0E2E7] font-['Inter'] font-medium text-[14px] text-[#5A5F6D] hover:text-[#1A1A2E] transition-all duration-150">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            Wishlist
                            <?php if ($wishlist_count): ?><span class="ml-auto bg-[#F7F8FA] border border-[#E0E2E7] rounded-full px-2 py-0.5 font-['Inter'] font-semibold text-[11px] text-[#5A5F6D]"><?= $wishlist_count ?></span><?php endif; ?>
                        </a>
                        <a href="../cart.php" class="flex items-center gap-3 px-5 py-3.5 border-l-[3px] border-transparent hover:bg-[#F7F8FA] hover:border-[#E0E2E7] font-['Inter'] font-medium text-[14px] text-[#5A5F6D] hover:text-[#1A1A2E] transition-all duration-150">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                            Cart
                            <?php if ($cart_count): ?><span class="ml-auto bg-[#F7F8FA] border border-[#E0E2E7] rounded-full px-2 py-0.5 font-['Inter'] font-semibold text-[11px] text-[#5A5F6D]"><?= $cart_count ?></span><?php endif; ?>
                        </a>
                        <div class="border-t border-[#F0F1F3]"></div>
                        <a href="../logout.php" class="flex items-center gap-3 px-5 py-3.5 border-l-[3px] border-transparent hover:bg-[#FDEAEA] font-['Inter'] font-medium text-[14px] text-[#D64545] transition-all duration-150">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Logout
                        </a>
                    </nav>
                </div>
            </aside>

            <!-- MAIN CONTENT -->
            <div class="flex-1">

                <!-- ORDER TIMELINE -->
                <div class="bg-white rounded-xl border border-[#E0E2E7] shadow-[0_2px_12px_rgba(0,0,0,0.08)] p-6 mb-6">
                    <h3 class="font-['Playfair_Display'] font-semibold text-[18px] text-[#1A1A2E] mb-6">Order Progress</h3>
                    <div class="flex items-start justify-between relative">
                        <div class="absolute top-5 left-0 right-0 h-0.5 bg-[#E0E2E7]"></div>
                        <div class="absolute top-5 left-0 h-0.5 bg-[#C9A84C] transition-all duration-500" style="width: <?= ($current_step / 4) * 100 ?>%"></div>
                        <?php foreach ($timeline as $i => $step):
                            $is_done = $i <= $current_step;
                            $is_current = $i === $current_step;
                        ?>
                        <div class="relative flex flex-col items-center text-center z-10 flex-1">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center border-2 transition-all duration-300 <?= $is_done ? 'bg-[#C9A84C] border-[#C9A84C]' : 'bg-white border-[#E0E2E7]' ?> <?= $is_current ? 'ring-4 ring-[#C9A84C]/20' : '' ?>">
                                <svg class="w-4 h-4 <?= $is_done ? 'text-white' : 'text-[#8A8F99]' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $step['icon'] ?>"/></svg>
                            </div>
                            <div class="mt-2 font-['Inter'] font-semibold text-[11px] sm:text-[12px] <?= $is_done ? 'text-[#1A1A2E]' : 'text-[#8A8F99]' ?>"><?= $step['label'] ?></div>
                            <?php if ($step['date']): ?>
                            <div class="font-['Inter'] text-[10px] text-[#8A8F99] mt-0.5 hidden sm:block"><?= date('M d, h:i A', strtotime($step['date'])) ?></div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

                    <!-- ORDER ITEMS -->
                    <div class="xl:col-span-2">
                        <div class="bg-white rounded-xl border border-[#E0E2E7] shadow-[0_2px_12px_rgba(0,0,0,0.08)] overflow-hidden">
                            <div class="px-6 py-4 bg-[#F7F8FA] border-b border-[#E0E2E7]">
                                <h3 class="font-['Playfair_Display'] font-semibold text-[18px] text-[#1A1A2E]">Items Ordered (<?= count($items) ?>)</h3>
                            </div>
                            <div class="divide-y divide-[#F0F1F3]">
                                <?php foreach ($items as $item): ?>
                                <div class="flex gap-4 p-5">
                                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-lg bg-[#F7F8FA] overflow-hidden flex-shrink-0 border border-[#E0E2E7]">
                                        <?php if ($item['product_image']): ?>
                                        <img src="../../assets/uploads/products/<?= htmlspecialchars($item['product_image']) ?>" alt="" class="w-full h-full object-cover">
                                        <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center"><svg class="w-8 h-8 text-[#E0E2E7]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="1"/></svg></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-3">
                                            <div>
                                                <?php if ($item['slug']): ?>
                                                <a href="../product-detail.php?slug=<?= urlencode($item['slug']) ?>" class="font-['Inter'] font-semibold text-[14px] text-[#1A1A2E] hover:text-[#C9A84C] transition-colors line-clamp-2"><?= htmlspecialchars($item['product_name']) ?></a>
                                                <?php else: ?>
                                                <span class="font-['Inter'] font-semibold text-[14px] text-[#1A1A2E] line-clamp-2"><?= htmlspecialchars($item['product_name']) ?></span>
                                                <?php endif; ?>
                                                <div class="font-['Inter'] text-[12px] text-[#8A8F99] mt-0.5">Model: <?= htmlspecialchars($item['product_model_number']) ?></div>
                                                <?php if ($item['selected_strap_size']):
                                                    $od_len = $od_length_map[$item['selected_strap_size']] ?? null;
                                                ?>
                                                <div class="inline-flex items-center gap-1.5 font-['Inter'] text-[12px] text-[#5A5F6D] mt-0.5">
                                                    <svg class="w-3 h-3 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                                                    Strap Size: <?= htmlspecialchars($item['selected_strap_size']) ?><?= $od_len ? " ({$od_len} mm)" : '' ?>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-right flex-shrink-0">
                                                <div class="font-['Inter'] font-bold text-[15px] text-[#1A1A2E]">NPR <?= number_format($item['total'], 0) ?></div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-4 mt-2 font-['Inter'] text-[12px] text-[#5A5F6D]">
                                            <span>Qty: <?= $item['quantity'] ?></span>
                                            <span>@ NPR <?= number_format($item['price'], 0) ?></span>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- ORDER SIDEBAR INFO -->
                    <div class="space-y-5">

                        <!-- PRICE SUMMARY -->
                        <div class="bg-white rounded-xl border border-[#E0E2E7] shadow-[0_2px_12px_rgba(0,0,0,0.08)] p-5">
                            <h3 class="font-['Inter'] font-semibold text-[15px] text-[#1A1A2E] mb-4">Price Summary</h3>
                            <div class="space-y-2.5">
                                <div class="flex justify-between font-['Inter'] text-[14px]"><span class="text-[#5A5F6D]">Subtotal</span><span class="text-[#1A1A2E] font-semibold">NPR <?= number_format($order['total_amount'], 0) ?></span></div>
                                <div class="flex justify-between font-['Inter'] text-[14px]"><span class="text-[#5A5F6D]">Shipping</span><span class="text-[#1A1A2E] font-semibold">NPR <?= number_format($order['shipping_charge'], 0) ?></span></div>
                                <div class="w-full h-px bg-[#E0E2E7]"></div>
                                <div class="flex justify-between"><span class="font-['Inter'] font-semibold text-[15px] text-[#1A1A2E]">Grand Total</span><span class="font-['Inter'] font-bold text-[22px] text-[#C9A84C]">NPR <?= number_format($order['grand_total'], 0) ?></span></div>
                            </div>
                        </div>

                        <!-- PAYMENT INFO -->
                        <div class="bg-white rounded-xl border border-[#E0E2E7] shadow-[0_2px_12px_rgba(0,0,0,0.08)] p-5">
                            <h3 class="font-['Inter'] font-semibold text-[15px] text-[#1A1A2E] mb-4">Payment Details</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between font-['Inter'] text-[13px]">
                                    <span class="text-[#5A5F6D]">Method</span>
                                    <span class="flex items-center gap-1.5 text-[#1A1A2E] font-medium">
                                        <?php if ($order['payment_method'] === 'eSewa'): ?><div class="w-3 h-3 rounded-full bg-[#60BB46]"></div><?php else: ?><svg class="w-3.5 h-3.5 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg><?php endif; ?>
                                        <?= $order['payment_method'] ?>
                                    </span>
                                </div>
                                <div class="flex justify-between font-['Inter'] text-[13px]"><span class="text-[#5A5F6D]">Status</span><span class="inline-block px-2 py-0.5 rounded font-semibold text-[11px] <?= $pay_classes ?>"><?= ucfirst($order['payment_status']) ?></span></div>
                                <?php if ($order['transaction_id']): ?>
                                <div class="flex justify-between font-['Inter'] text-[13px]"><span class="text-[#5A5F6D]">Transaction ID</span><span class="text-[#1A1A2E] font-mono text-[12px] bg-[#F7F8FA] px-2 py-1 rounded"><?= htmlspecialchars($order['transaction_id']) ?></span></div>
                                <?php endif; ?>
                            </div>
                            <?php if ($order['payment_method'] === 'eSewa' && $order['payment_status'] === 'failed'): ?>
                            <div class="mt-4 p-3 bg-[#FFF3E0] border border-[#E65100]/20 rounded-lg">
                                <div class="flex items-start gap-2 mb-3">
                                    <svg class="w-4 h-4 text-[#E65100] mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    <div>
                                        <div class="font-['Inter'] font-semibold text-[13px] text-[#E65100]">Payment Failed</div>
                                        <p class="font-['Inter'] text-[12px] text-[#5A5F6D] mt-0.5">Your eSewa payment did not complete. Stock has been restored. Please place a new order.</p>
                                    </div>
                                </div>
                                <a href="../shop.php" class="flex items-center justify-center gap-2 h-[36px] w-full bg-[#E65100] hover:bg-[#d44a00] text-white font-['Inter'] font-semibold text-[13px] rounded-lg transition-all duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    Shop & Order Again
                                </a>
                            </div>
                            <?php endif; ?>
                            <?php if ($order['payment_method'] === 'eSewa' && $order['payment_status'] === 'paid'): ?>
                            <div class="mt-4 p-3 bg-[#E8F5E9] border border-[#2E7D32]/20 rounded-lg">
                                <div class="flex items-center gap-2"><svg class="w-4 h-4 text-[#2E7D32]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span class="font-['Inter'] font-semibold text-[13px] text-[#2E7D32]">Payment verified via eSewa</span></div>
                            </div>
                            <?php endif; ?>
                            <?php if ($order['payment_method'] === 'COD' && $order['payment_status'] === 'pending'): ?>
                            <div class="mt-4 p-3 bg-[#FFF8E7] border border-[#C9A84C]/20 rounded-lg">
                                <div class="flex items-center gap-2"><svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg><span class="font-['Inter'] font-semibold text-[13px] text-[#C9A84C]">Pay NPR <?= number_format($order['grand_total'], 0) ?> upon delivery</span></div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- SHIPPING INFO -->
                        <div class="bg-white rounded-xl border border-[#E0E2E7] shadow-[0_2px_12px_rgba(0,0,0,0.08)] p-5">
                            <h3 class="font-['Inter'] font-semibold text-[15px] text-[#1A1A2E] mb-4">Shipping Address</h3>
                            <div class="space-y-2">
                                <div class="font-['Inter'] font-semibold text-[14px] text-[#1A1A2E]"><?= htmlspecialchars($order['customer_name']) ?></div>
                                <div class="font-['Inter'] text-[13px] text-[#5A5F6D] leading-[1.5]"><?= nl2br(htmlspecialchars($order['shipping_address'])) ?></div>
                                <div class="flex items-center gap-2 font-['Inter'] text-[13px] text-[#5A5F6D]"><svg class="w-3.5 h-3.5 text-[#8A8F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg><?= htmlspecialchars($order['customer_phone']) ?></div>
                                <div class="flex items-center gap-2 font-['Inter'] text-[13px] text-[#5A5F6D]"><svg class="w-3.5 h-3.5 text-[#8A8F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg><?= htmlspecialchars($order['customer_email']) ?></div>
                            </div>
                        </div>

                        <?php if ($order['admin_notes']): ?>
                        <div class="bg-[#FFF8E7] rounded-xl border border-[#C9A84C]/20 p-5">
                            <h3 class="font-['Inter'] font-semibold text-[14px] text-[#C9A84C] mb-2 flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>Note from Admin</h3>
                            <p class="font-['Inter'] text-[13px] text-[#5A5F6D] leading-[1.6]"><?= nl2br(htmlspecialchars($order['admin_notes'])) ?></p>
                        </div>
                        <?php endif; ?>

                        <div class="flex flex-col gap-2">
                            <a href="orders.php" class="flex items-center justify-center gap-2 h-[40px] bg-white border border-[#E0E2E7] hover:border-[#1B2A4A] rounded-lg font-['Inter'] font-semibold text-[14px] text-[#5A5F6D] hover:text-[#1A1A2E] transition-all duration-150"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>Back to Orders</a>
                            <a href="../shop.php" class="flex items-center justify-center gap-2 h-[40px] bg-[#1B2A4A] hover:bg-[#2C4066] rounded-lg font-['Inter'] font-semibold text-[14px] text-white transition-all duration-200">Continue Shopping</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<?php require_once '../../includes/footer.php'; ?>