<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../login.php'); exit; }
require_once '../../config/db.php';

$user_id = $_SESSION['user_id'];
$page_title = "My Orders – ChronoNest";

$status_filter = isset($_GET['status']) ? trim($_GET['status']) : '';

$where = "user_id = ?";
$params = [$user_id];
$types = "i";

if ($status_filter && in_array($status_filter, ['pending','confirmed','processing','shipped','delivered'])) {
    $where .= " AND order_status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

$stmt = $conn->prepare("SELECT * FROM orders WHERE $where ORDER BY ordered_at DESC");
$stmt->bind_param($types, ...$params);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Counts
$all_count = $conn->query("SELECT COUNT(*) as cnt FROM orders WHERE user_id = $user_id")->fetch_assoc()['cnt'];
$status_counts = [];
foreach (['pending','confirmed','processing','shipped','delivered'] as $s) {
    $sc = $conn->query("SELECT COUNT(*) as cnt FROM orders WHERE user_id = $user_id AND order_status = '$s'")->fetch_assoc();
    $status_counts[$s] = $sc['cnt'];
}

// Sidebar counts
$order_count = $all_count;
$wishlist_count = $conn->query("SELECT COUNT(*) as cnt FROM wishlists WHERE user_id = $user_id")->fetch_assoc()['cnt'];
$cart_count = $conn->query("SELECT COUNT(*) as cnt FROM cart WHERE user_id = $user_id")->fetch_assoc()['cnt'];

require_once '../../includes/header.php';
?>

<!-- PAGE HEADER -->
<section class="bg-[#1B2A4A] py-10">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <span class="font-['Inter'] font-semibold text-[11px] uppercase tracking-[1.5px] text-[#C9A84C]">Order History</span>
        <h1 class="font-['Playfair_Display'] font-bold text-[32px] text-white mt-1">My Orders</h1>
        <p class="font-['Inter'] text-[14px] text-[#B0B8C9] mt-1"><?= count($orders) ?> order<?= count($orders) !== 1 ? 's' : '' ?><?= $status_filter ? ' (' . ucfirst($status_filter) . ')' : '' ?></p>
    </div>
</section>

<div class="bg-white border-b border-[#E0E2E7]">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-3">
        <nav class="flex items-center gap-2 font-['Inter'] text-[13px] text-[#5A5F6D]">
            <a href="../../index.php" class="hover:text-[#C9A84C] transition-colors">Home</a>
            <svg class="w-3 h-3 text-[#8A8F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="index.php" class="hover:text-[#C9A84C] transition-colors">Account</a>
            <svg class="w-3 h-3 text-[#8A8F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-[#1A1A2E] font-medium">Orders</span>
        </nav>
    </div>
</div>

<section class="py-8 bg-[#F7F8FA] min-h-screen">
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

                <!-- STATUS TABS -->
                <div class="flex items-center gap-2 mb-6 overflow-x-auto pb-2">
                    <a href="orders.php" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full font-['Inter'] font-medium text-[13px] transition-all duration-150 whitespace-nowrap <?= !$status_filter ? 'bg-[#1B2A4A] text-white' : 'bg-white border border-[#E0E2E7] text-[#5A5F6D] hover:border-[#1B2A4A] hover:text-[#1A1A2E]' ?>">All <span class="font-semibold">(<?= $all_count ?>)</span></a>
                    <?php
                    $tab_config = [
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                    ];
                    foreach ($tab_config as $skey => $slabel):
                        if ($status_counts[$skey] == 0) continue;
                    ?>
                    <a href="orders.php?status=<?= $skey ?>" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full font-['Inter'] font-medium text-[13px] transition-all duration-150 whitespace-nowrap <?= $status_filter === $skey ? 'bg-[#1B2A4A] text-white' : 'bg-white border border-[#E0E2E7] text-[#5A5F6D] hover:border-[#1B2A4A] hover:text-[#1A1A2E]' ?>"><?= $slabel ?> <span class="font-semibold">(<?= $status_counts[$skey] ?>)</span></a>
                    <?php endforeach; ?>
                </div>

                <?php if (empty($orders)): ?>
                <div class="flex flex-col items-center justify-center py-20 text-center">
                    <svg class="w-20 h-20 text-[#E0E2E7] mb-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <h3 class="font-['Playfair_Display'] font-semibold text-[22px] text-[#1A1A2E] mb-2">No orders found</h3>
                    <p class="font-['Inter'] text-[14px] text-[#5A5F6D] mb-6"><?= $status_filter ? 'No ' . $status_filter . ' orders.' : "You haven't placed any orders yet." ?></p>
                    <a href="../shop.php" class="inline-flex items-center gap-2 h-[44px] px-6 bg-[#C9A84C] hover:bg-[#B8953F] text-white font-['Inter'] font-semibold text-[14px] rounded-lg transition-all duration-200">Start Shopping</a>
                </div>
                <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($orders as $order):
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
                        $ic = $conn->query("SELECT COUNT(*) as cnt, SUM(quantity) as qty FROM order_items WHERE order_id = {$order['id']}")->fetch_assoc();
                    ?>
                    <a href="order-detail.php?id=<?= $order['id'] ?>" class="block bg-white rounded-xl border border-[#E0E2E7] shadow-[0_2px_12px_rgba(0,0,0,0.08)] hover:shadow-[0_8px_28px_rgba(0,0,0,0.14)] hover:-translate-y-0.5 transition-all duration-200 overflow-hidden">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between p-5 gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-2 flex-wrap">
                                    <span class="font-['Inter'] font-bold text-[15px] text-[#1A1A2E]">#<?= htmlspecialchars($order['order_number']) ?></span>
                                    <span class="inline-block px-2.5 py-1 rounded font-['Inter'] font-semibold text-[11px] <?= $status_classes ?>"><?= ucfirst($order['order_status']) ?></span>
                                    <span class="inline-block px-2.5 py-1 rounded font-['Inter'] font-semibold text-[11px] <?= $pay_classes ?>"><?= ucfirst($order['payment_status']) ?></span>
                                </div>
                                <div class="flex items-center gap-4 font-['Inter'] text-[13px] text-[#5A5F6D] flex-wrap">
                                    <span class="flex items-center gap-1"><svg class="w-3.5 h-3.5 text-[#8A8F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><?= date('M d, Y · h:i A', strtotime($order['ordered_at'])) ?></span>
                                    <span class="flex items-center gap-1"><svg class="w-3.5 h-3.5 text-[#8A8F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg><?= $ic['qty'] ?> item<?= $ic['qty'] > 1 ? 's' : '' ?></span>
                                    <span class="flex items-center gap-1"><svg class="w-3.5 h-3.5 text-[#8A8F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg><?= $order['payment_method'] ?></span>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="text-right">
                                    <div class="font-['Inter'] font-bold text-[20px] text-[#1A1A2E]">NPR <?= number_format($order['grand_total'], 0) ?></div>
                                </div>
                                <svg class="w-5 h-5 text-[#8A8F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</section>

<?php require_once '../../includes/footer.php'; ?>