<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../login.php'); exit; }
require_once '../../config/db.php';

$user_id = $_SESSION['user_id'];
$page_title = "My Wishlist – ChronoNest";

$stmt = $conn->prepare("SELECT w.id as wishlist_id, w.added_at, p.id as product_id, p.name, p.slug, p.price, p.stock_quantity, p.strap_adjustable, p.strap_size_options, p.movement_type, p.gender, b.name as brand_name,
    (SELECT image_url FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image
    FROM wishlists w JOIN products p ON w.product_id = p.id JOIN brands b ON p.brand_id = b.id
    WHERE w.user_id = ? AND p.is_active = 1 ORDER BY w.added_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$wishlist = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Sidebar counts
$order_count = $conn->query("SELECT COUNT(*) as cnt FROM orders WHERE user_id = $user_id")->fetch_assoc()['cnt'];
$wishlist_count = count($wishlist);
$cart_count = $conn->query("SELECT COUNT(*) as cnt FROM cart WHERE user_id = $user_id")->fetch_assoc()['cnt'];

$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
require_once '../../includes/header.php';
?>

<!-- PAGE HEADER -->
<section class="bg-[#1B2A4A] py-10">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <span class="font-['Inter'] font-semibold text-[11px] uppercase tracking-[1.5px] text-[#C9A84C]">Saved Items</span>
        <h1 class="font-['Playfair_Display'] font-bold text-[32px] text-white mt-1">My Wishlist</h1>
        <p class="font-['Inter'] text-[14px] text-[#B0B8C9] mt-1"><?= $wishlist_count ?> item<?= $wishlist_count !== 1 ? 's' : '' ?> saved</p>
    </div>
</section>

<div class="bg-white border-b border-[#E0E2E7]">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-3">
        <nav class="flex items-center gap-2 font-['Inter'] text-[13px] text-[#5A5F6D]">
            <a href="../../index.php" class="hover:text-[#C9A84C] transition-colors">Home</a>
            <svg class="w-3 h-3 text-[#8A8F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="index.php" class="hover:text-[#C9A84C] transition-colors">Account</a>
            <svg class="w-3 h-3 text-[#8A8F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-[#1A1A2E] font-medium">Wishlist</span>
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
                        <a href="orders.php" class="flex items-center gap-3 px-5 py-3.5 border-l-[3px] border-transparent hover:bg-[#F7F8FA] hover:border-[#E0E2E7] font-['Inter'] font-medium text-[14px] text-[#5A5F6D] hover:text-[#1A1A2E] transition-all duration-150">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            My Orders
                            <?php if ($order_count): ?><span class="ml-auto bg-[#F7F8FA] border border-[#E0E2E7] rounded-full px-2 py-0.5 font-['Inter'] font-semibold text-[11px] text-[#5A5F6D]"><?= $order_count ?></span><?php endif; ?>
                        </a>
                        <a href="wishlist.php" class="flex items-center gap-3 px-5 py-3.5 bg-[#FFF8E7] border-l-[3px] border-[#C9A84C] font-['Inter'] font-semibold text-[14px] text-[#C9A84C]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            Wishlist
                            <?php if ($wishlist_count): ?><span class="ml-auto bg-[#FFF8E7] border border-[#C9A84C]/30 rounded-full px-2 py-0.5 font-['Inter'] font-semibold text-[11px] text-[#C9A84C]"><?= $wishlist_count ?></span><?php endif; ?>
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
                <?php if (empty($wishlist)): ?>
                <div class="flex flex-col items-center justify-center py-20 text-center">
                    <svg class="w-20 h-20 text-[#E0E2E7] mb-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    <h3 class="font-['Playfair_Display'] font-semibold text-[22px] text-[#1A1A2E] mb-2">Your wishlist is empty</h3>
                    <p class="font-['Inter'] text-[14px] text-[#5A5F6D] mb-6">Save watches you love to come back to them later.</p>
                    <a href="../shop.php" class="inline-flex items-center gap-2 h-[44px] px-6 bg-[#C9A84C] hover:bg-[#B8953F] text-white font-['Inter'] font-semibold text-[14px] rounded-lg transition-all duration-200">Explore Watches</a>
                </div>
                <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    <?php foreach ($wishlist as $item): ?>
                    <div class="bg-white border border-[#E0E2E7] rounded-xl overflow-hidden shadow-[0_2px_12px_rgba(0,0,0,0.08)] hover:shadow-[0_8px_28px_rgba(0,0,0,0.14)] transition-all duration-200 group relative">
                        <form action="../actions/wishlist-action.php" method="POST" class="absolute top-3 right-3 z-10">
                            <input type="hidden" name="action" value="remove">
                            <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                            <input type="hidden" name="redirect" value="account/wishlist.php">
                            <button type="submit" class="w-8 h-8 rounded-full bg-white/90 backdrop-blur-sm border border-[#E0E2E7] flex items-center justify-center text-[#D64545] hover:bg-[#FDEAEA] transition-all duration-150 shadow-sm" title="Remove from wishlist">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </form>
                        <a href="../product-detail.php?slug=<?= urlencode($item['slug']) ?>" class="block">
                            <div class="relative bg-[#F7F8FA] aspect-square overflow-hidden">
                                <?php if ($item['main_image']): ?>
                                <img src="../../assets/uploads/products/<?= htmlspecialchars(basename($item['main_image'])) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                                <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center"><svg class="w-16 h-16 text-[#E0E2E7]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="1"/><path stroke-linecap="round" stroke-width="1" d="M12 6v6l4 2"/></svg></div>
                                <?php endif; ?>
                                <div class="absolute top-3 left-3">
                                    <?php if ($item['stock_quantity'] == 0): ?>
                                    <span class="inline-block px-2.5 py-1 rounded bg-[#FDEAEA] text-[#D64545] font-['Inter'] font-semibold text-[11px]">Out of Stock</span>
                                    <?php elseif ($item['stock_quantity'] <= 5): ?>
                                    <span class="inline-block px-2.5 py-1 rounded bg-[#FFF3E0] text-[#E65100] font-['Inter'] font-semibold text-[11px]">Low Stock</span>
                                    <?php else: ?>
                                    <span class="inline-block px-2.5 py-1 rounded bg-[#E8F5E9] text-[#2E7D32] font-['Inter'] font-semibold text-[11px]">In Stock</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                        <div class="p-4">
                            <div class="font-['Inter'] text-[12px] text-[#C9A84C] font-semibold mb-1"><?= htmlspecialchars($item['brand_name']) ?></div>
                            <a href="../product-detail.php?slug=<?= urlencode($item['slug']) ?>" class="font-['Inter'] font-semibold text-[14px] text-[#1A1A2E] hover:text-[#C9A84C] leading-[1.4] mb-2 line-clamp-2 block transition-colors"><?= htmlspecialchars($item['name']) ?></a>
                            <div class="flex items-center gap-2 mb-3">
                                <span class="font-['Inter'] text-[11px] text-[#5A5F6D] bg-[#F7F8FA] px-2 py-0.5 rounded"><?= htmlspecialchars($item['gender']) ?></span>
                                <span class="font-['Inter'] text-[11px] text-[#5A5F6D] bg-[#F7F8FA] px-2 py-0.5 rounded"><?= htmlspecialchars($item['movement_type']) ?></span>
                            </div>
                            <div class="font-['Inter'] font-bold text-[18px] text-[#1A1A2E] mb-3">NPR <?= number_format($item['price'], 0) ?></div>
                            <?php if ($item['stock_quantity'] > 0): ?>
                                <?php if ($item['strap_adjustable'] && $item['strap_size_options']): ?>
                                <a href="../product-detail.php?slug=<?= urlencode($item['slug']) ?>" class="flex items-center justify-center gap-2 h-[38px] w-full bg-[#1B2A4A] hover:bg-[#2C4066] text-white font-['Inter'] font-semibold text-[13px] rounded-lg transition-all duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                                    Select Size & Add to Cart
                                </a>
                                <?php else: ?>
                                <form action="../actions/wishlist-action.php" method="POST">
                                    <input type="hidden" name="action" value="move_to_cart">
                                    <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                    <input type="hidden" name="redirect" value="account/wishlist.php">
                                    <button type="submit" class="flex items-center justify-center gap-2 h-[38px] w-full bg-[#C9A84C] hover:bg-[#B8953F] text-white font-['Inter'] font-semibold text-[13px] rounded-lg transition-all duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                                        Move to Cart
                                    </button>
                                </form>
                                <?php endif; ?>
                            <?php else: ?>
                            <button disabled class="flex items-center justify-center h-[38px] w-full bg-[#C5C9D1] text-[#8A8F99] font-['Inter'] font-semibold text-[13px] rounded-lg cursor-not-allowed">Out of Stock</button>
                            <?php endif; ?>
                        </div>
                        <div class="px-4 pb-3">
                            <div class="font-['Inter'] text-[11px] text-[#8A8F99]">Added <?= date('M d, Y', strtotime($item['added_at'])) ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once '../../includes/footer.php'; ?>