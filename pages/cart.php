<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
require_once '../config/db.php';

$user_id = $_SESSION['user_id'];
$page_title = "Shopping Cart – ChronoNest";

$cart_sql = "SELECT c.*, p.name, p.slug, p.price, p.stock_quantity, p.strap_adjustable, p.strap_length_mm, p.strap_size_options, b.name as brand_name,
    (SELECT image_url FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image
    FROM cart c JOIN products p ON c.product_id = p.id JOIN brands b ON p.brand_id = b.id
    WHERE c.user_id = ? AND p.is_active = 1 ORDER BY c.added_at DESC";
$stmt = $conn->prepare($cart_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$subtotal = 0;
foreach ($cart_items as $item) { $subtotal += $item['price'] * $item['quantity']; }
$shipping = $subtotal > 0 ? SHIPPING_CHARGE : 0;
$grand_total = $subtotal + $shipping;

$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
require_once '../includes/header.php';
?>

<!-- HEADER -->
<section class="bg-[#1B2A4A] py-10">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <span class="font-['Inter'] font-semibold text-[11px] uppercase tracking-[1.5px] text-[#C9A84C]">Your Cart</span>
        <h1 class="font-['Playfair_Display'] font-bold text-[32px] text-white mt-1">Shopping Cart</h1>
        <p class="font-['Inter'] text-[14px] text-[#B0B8C9] mt-1"><?= count($cart_items) ?> item<?= count($cart_items) !== 1 ? 's' : '' ?> in your cart</p>
    </div>
</section>

<div class="bg-white border-b border-[#E0E2E7]">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-3">
        <nav class="flex items-center gap-2 font-['Inter'] text-[13px] text-[#5A5F6D]">
            <a href="../index.php" class="hover:text-[#C9A84C] transition-colors">Home</a>
            <svg class="w-3 h-3 text-[#8A8F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-[#1A1A2E] font-medium">Cart</span>
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

<section class="py-8 bg-[#F7F8FA] min-h-[50vh]">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <?php if (empty($cart_items)): ?>
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <svg class="w-20 h-20 text-[#E0E2E7] mb-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
            <h3 class="font-['Playfair_Display'] font-semibold text-[22px] text-[#1A1A2E] mb-2">Your cart is empty</h3>
            <p class="font-['Inter'] text-[14px] text-[#5A5F6D] mb-6">Looks like you haven't added any watches yet.</p>
            <a href="shop.php" class="inline-flex items-center gap-2 h-[44px] px-6 bg-[#C9A84C] hover:bg-[#B8953F] text-white font-['Inter'] font-semibold text-[14px] rounded-lg transition-all duration-200">Continue Shopping</a>
        </div>
        <?php else: ?>
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Cart Items -->
            <div class="flex-1 space-y-4">
                <?php foreach ($cart_items as $item): ?>
                <div class="bg-white rounded-xl border border-[#E0E2E7] shadow-[0_2px_12px_rgba(0,0,0,0.08)] p-4 sm:p-5">
                    <div class="flex gap-4">
                        <a href="product-detail.php?slug=<?= urlencode($item['slug']) ?>" class="w-20 h-20 sm:w-24 sm:h-24 rounded-lg bg-[#F7F8FA] overflow-hidden flex-shrink-0 border border-[#E0E2E7]">
                            <?php if ($item['main_image']): ?>
                            <img src="../assets/uploads/products/<?= htmlspecialchars(basename($item['main_image'])) ?>" alt="" class="w-full h-full object-cover">
                            <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center"><svg class="w-8 h-8 text-[#E0E2E7]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="1"/></svg></div>
                            <?php endif; ?>
                        </a>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="font-['Inter'] text-[11px] text-[#C9A84C] font-semibold"><?= htmlspecialchars($item['brand_name']) ?></div>
                                    <a href="product-detail.php?slug=<?= urlencode($item['slug']) ?>" class="font-['Inter'] font-semibold text-[15px] text-[#1A1A2E] hover:text-[#C9A84C] transition-colors line-clamp-2 block"><?= htmlspecialchars($item['name']) ?></a>

                                    <!-- STRAP DISPLAY IN CART (with length mapping) -->
                                    <?php
                                    $cart_strap_length_map = ['XS' => 145, 'S' => 160, 'M' => 175, 'L' => 190, 'XL' => 205];
                                    ?>
                                    <?php if ($item['selected_strap_size']): ?>
                                        <?php $mapped_length = $cart_strap_length_map[$item['selected_strap_size']] ?? null; ?>
                                        <span class="inline-flex items-center gap-1 font-['Inter'] text-[12px] text-[#1A1A2E] mt-1 bg-[#FFF8E7] border border-[#C9A84C]/20 rounded px-2 py-0.5">
                                            <svg class="w-3 h-3 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                                            Strap: <?= htmlspecialchars($item['selected_strap_size']) ?><?= $mapped_length ? " ({$mapped_length} mm)" : '' ?>
                                        </span>
                                    <?php elseif (!$item['strap_adjustable'] && $item['strap_length_mm']): ?>
                                        <span class="inline-flex items-center gap-1 font-['Inter'] text-[12px] text-[#8A8F99] mt-1">
                                            <svg class="w-3 h-3 text-[#8A8F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                            Fixed Strap (<?= $item['strap_length_mm'] ?> mm)
                                        </span>
                                    <?php elseif ($item['strap_adjustable'] && !$item['selected_strap_size']): ?>
                                        <span class="inline-flex items-center gap-1 font-['Inter'] text-[12px] text-[#8A8F99] mt-1">
                                            <svg class="w-3 h-3 text-[#8A8F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                                            Adjustable Strap
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <form action="actions/cart-action.php" method="POST">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
                                    <button type="submit" class="w-8 h-8 rounded-lg flex items-center justify-center text-[#8A8F99] hover:text-[#D64545] hover:bg-[#FDEAEA] transition-all duration-150" title="Remove">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                            <div class="flex items-center justify-between mt-3">
                                <form action="actions/cart-action.php" method="POST" class="flex items-center gap-1">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
                                    <button type="submit" name="quantity" value="<?= max(1, $item['quantity'] - 1) ?>" class="w-8 h-8 rounded-lg border border-[#E0E2E7] flex items-center justify-center text-[#5A5F6D] hover:border-[#1B2A4A] hover:text-[#1A1A2E] transition-all duration-150 <?= $item['quantity'] <= 1 ? 'opacity-40 pointer-events-none' : '' ?>">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                    </button>
                                    <span class="w-10 h-8 flex items-center justify-center font-['Inter'] font-semibold text-[14px] text-[#1A1A2E]"><?= $item['quantity'] ?></span>
                                    <button type="submit" name="quantity" value="<?= $item['quantity'] + 1 ?>" class="w-8 h-8 rounded-lg border border-[#E0E2E7] flex items-center justify-center text-[#5A5F6D] hover:border-[#1B2A4A] hover:text-[#1A1A2E] transition-all duration-150 <?= $item['quantity'] >= $item['stock_quantity'] ? 'opacity-40 pointer-events-none' : '' ?>">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    </button>
                                </form>
                                <div class="font-['Inter'] font-bold text-[18px] text-[#1A1A2E]">NPR <?= number_format($item['price'] * $item['quantity'], 0) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Order Summary -->
            <div class="w-full lg:w-[380px] flex-shrink-0">
                <div class="bg-white rounded-xl border border-[#E0E2E7] shadow-[0_2px_12px_rgba(0,0,0,0.08)] p-6 sticky top-[100px]">
                    <h3 class="font-['Playfair_Display'] font-semibold text-[20px] text-[#1A1A2E] mb-5">Order Summary</h3>
                    <div class="space-y-3 mb-5">
                        <div class="flex justify-between font-['Inter'] text-[14px]"><span class="text-[#5A5F6D]">Subtotal (<?= count($cart_items) ?> item<?= count($cart_items) > 1 ? 's' : '' ?>)</span><span class="text-[#1A1A2E] font-semibold">NPR <?= number_format($subtotal, 0) ?></span></div>
                        <div class="flex justify-between font-['Inter'] text-[14px]"><span class="text-[#5A5F6D]">Shipping</span><span class="text-[#1A1A2E] font-semibold">NPR <?= number_format($shipping, 0) ?></span></div>
                        <div class="w-full h-px bg-[#E0E2E7]"></div>
                        <div class="flex justify-between font-['Inter']"><span class="text-[#1A1A2E] font-semibold text-[16px]">Grand Total</span><span class="text-[#1A1A2E] font-bold text-[22px]">NPR <?= number_format($grand_total, 0) ?></span></div>
                    </div>
                    <a href="checkout.php" class="flex items-center justify-center gap-2 h-[48px] w-full bg-[#C9A84C] hover:bg-[#B8953F] text-white font-['Inter'] font-semibold text-[15px] rounded-lg transition-all duration-200 hover:shadow-md mb-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Proceed to Checkout
                    </a>
                    <a href="shop.php" class="flex items-center justify-center h-[40px] w-full bg-transparent hover:bg-[#F7F8FA] text-[#1B2A4A] font-['Inter'] font-semibold text-[14px] rounded-lg border border-[#E0E2E7] transition-all duration-200">Continue Shopping</a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>