<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
require_once '../config/db.php';

$user_id = $_SESSION['user_id'];

// Fetch user
$user_stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user = $user_stmt->get_result()->fetch_assoc();

// Fetch cart
$cart_sql = "SELECT c.*, p.name, p.slug, p.price, p.stock_quantity, p.model_number, b.name as brand_name,
    (SELECT image_url FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image
    FROM cart c JOIN products p ON c.product_id = p.id JOIN brands b ON p.brand_id = b.id
    WHERE c.user_id = ? AND p.is_active = 1";
$stmt = $conn->prepare($cart_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if (empty($cart_items)) { header('Location: cart.php'); exit; }

$subtotal = 0;
foreach ($cart_items as $item) { $subtotal += $item['price'] * $item['quantity']; }
$shipping = SHIPPING_CHARGE;
$grand_total = $subtotal + $shipping;

$page_title = "Checkout – ChronoNest";
$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
require_once '../includes/header.php';
?>

<!-- HEADER -->
<section class="bg-[#1B2A4A] py-10">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <span class="font-['Inter'] font-semibold text-[11px] uppercase tracking-[1.5px] text-[#C9A84C]">Final Step</span>
        <h1 class="font-['Playfair_Display'] font-bold text-[32px] text-white mt-1">Checkout</h1>
    </div>
</section>

<div class="bg-white border-b border-[#E0E2E7]">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-3">
        <nav class="flex items-center gap-2 font-['Inter'] text-[13px] text-[#5A5F6D]">
            <a href="../index.php" class="hover:text-[#C9A84C] transition-colors">Home</a>
            <svg class="w-3 h-3 text-[#8A8F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="cart.php" class="hover:text-[#C9A84C] transition-colors">Cart</a>
            <svg class="w-3 h-3 text-[#8A8F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-[#1A1A2E] font-medium">Checkout</span>
        </nav>
    </div>
</div>

<?php if ($error): ?>
<div class="container mx-auto px-4 sm:px-6 lg:px-8 mt-4">
    <div class="flex items-center gap-3 bg-[#FDEAEA] border border-[#D64545]/20 text-[#D64545] rounded-lg px-4 py-3 font-['Inter'] text-[14px]"><svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><?= htmlspecialchars($error) ?></div>
</div>
<?php endif; ?>

<section class="py-8 bg-[#F7F8FA]">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <form action="actions/checkout-action.php" method="POST">
            <input type="hidden" name="action" value="place_order">
            <div class="flex flex-col lg:flex-row gap-8">

                <!-- SHIPPING INFO -->
                <div class="flex-1 space-y-6">
                    <div class="bg-white rounded-xl border border-[#E0E2E7] shadow-[0_2px_12px_rgba(0,0,0,0.08)] p-6">
                        <h3 class="font-['Playfair_Display'] font-semibold text-[20px] text-[#1A1A2E] mb-5 flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-[#1B2A4A] text-white font-['Inter'] font-bold text-[13px] flex items-center justify-center">1</span>
                            Shipping Information
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block font-['Inter'] font-semibold text-[14px] text-[#1A1A2E] mb-1.5">Full Name <span class="text-[#D64545]">*</span></label>
                                <input type="text" name="customer_name" value="<?= htmlspecialchars($user['name']) ?>" required class="w-full h-[44px] px-4 bg-white border border-[#E0E2E7] rounded-lg font-['Inter'] text-[15px] text-[#1A1A2E] focus:outline-none focus:border-2 focus:border-[#C9A84C] focus:shadow-[0_0_0_3px_rgba(201,168,76,0.15)] transition-all duration-150">
                            </div>
                            <div>
                                <label class="block font-['Inter'] font-semibold text-[14px] text-[#1A1A2E] mb-1.5">Phone <span class="text-[#D64545]">*</span></label>
                                <input type="tel" name="customer_phone" value="<?= htmlspecialchars($user['phone']) ?>" maxlength="10" required class="w-full h-[44px] px-4 bg-white border border-[#E0E2E7] rounded-lg font-['Inter'] text-[15px] text-[#1A1A2E] focus:outline-none focus:border-2 focus:border-[#C9A84C] focus:shadow-[0_0_0_3px_rgba(201,168,76,0.15)] transition-all duration-150">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block font-['Inter'] font-semibold text-[14px] text-[#1A1A2E] mb-1.5">Email <span class="text-[#D64545]">*</span></label>
                            <input type="email" name="customer_email" value="<?= htmlspecialchars($user['email']) ?>" required class="w-full h-[44px] px-4 bg-white border border-[#E0E2E7] rounded-lg font-['Inter'] text-[15px] text-[#1A1A2E] focus:outline-none focus:border-2 focus:border-[#C9A84C] focus:shadow-[0_0_0_3px_rgba(201,168,76,0.15)] transition-all duration-150">
                        </div>
                        <div>
                            <label class="block font-['Inter'] font-semibold text-[14px] text-[#1A1A2E] mb-1.5">Shipping Address <span class="text-[#D64545]">*</span></label>
                            <textarea name="shipping_address" rows="3" required placeholder="Street, Ward, Municipality, District, Province" class="w-full px-4 py-3 bg-white border border-[#E0E2E7] rounded-lg font-['Inter'] text-[15px] text-[#1A1A2E] placeholder-[#8A8F99] focus:outline-none focus:border-2 focus:border-[#C9A84C] focus:shadow-[0_0_0_3px_rgba(201,168,76,0.15)] transition-all duration-150 resize-none"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <!-- PAYMENT METHOD -->
                    <div class="bg-white rounded-xl border border-[#E0E2E7] shadow-[0_2px_12px_rgba(0,0,0,0.08)] p-6">
                        <h3 class="font-['Playfair_Display'] font-semibold text-[20px] text-[#1A1A2E] mb-5 flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-[#1B2A4A] text-white font-['Inter'] font-bold text-[13px] flex items-center justify-center">2</span>
                            Payment Method
                        </h3>
                        <div class="space-y-3">
                            <label class="flex items-center gap-4 p-4 bg-[#F7F8FA] border-2 border-[#E0E2E7] rounded-xl cursor-pointer hover:border-[#C9A84C] transition-all duration-150 has-[:checked]:border-[#C9A84C] has-[:checked]:bg-[#FFF8E7]">
                                <input type="radio" name="payment_method" value="COD" checked class="w-5 h-5 text-[#C9A84C] accent-[#C9A84C]">
                                <div class="flex items-center gap-3 flex-1">
                                    <div class="w-10 h-10 rounded-lg bg-white border border-[#E0E2E7] flex items-center justify-center"><svg class="w-5 h-5 text-[#1B2A4A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg></div>
                                    <div>
                                        <div class="font-['Inter'] font-semibold text-[14px] text-[#1A1A2E]">Cash on Delivery</div>
                                        <div class="font-['Inter'] text-[12px] text-[#5A5F6D]">Pay when your order arrives</div>
                                    </div>
                                </div>
                            </label>
                            <label class="flex items-center gap-4 p-4 bg-[#F7F8FA] border-2 border-[#E0E2E7] rounded-xl cursor-pointer hover:border-[#60BB46] transition-all duration-150 has-[:checked]:border-[#60BB46] has-[:checked]:bg-[#f0fbe8]">
                                <input type="radio" name="payment_method" value="eSewa" class="w-5 h-5 text-[#60BB46] accent-[#60BB46]">
                                <div class="flex items-center gap-3 flex-1">
                                    <div class="w-10 h-10 rounded-lg bg-white border border-[#E0E2E7] flex items-center justify-center"><div class="w-5 h-5 rounded-full bg-[#60BB46]"></div></div>
                                    <div>
                                        <div class="font-['Inter'] font-semibold text-[14px] text-[#1A1A2E]">eSewa</div>
                                        <div class="font-['Inter'] text-[12px] text-[#5A5F6D]">Pay via eSewa digital wallet</div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- ORDER SUMMARY -->
                <div class="w-full lg:w-[400px] flex-shrink-0">
                    <div class="bg-white rounded-xl border border-[#E0E2E7] shadow-[0_2px_12px_rgba(0,0,0,0.08)] p-6 sticky top-[100px]">
                        <h3 class="font-['Playfair_Display'] font-semibold text-[20px] text-[#1A1A2E] mb-5 flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-[#1B2A4A] text-white font-['Inter'] font-bold text-[13px] flex items-center justify-center">3</span>
                            Order Summary
                        </h3>

                        <div class="space-y-3 mb-5 max-h-[300px] overflow-y-auto pr-1">
                            <?php foreach ($cart_items as $item): ?>
                            <div class="flex gap-3 pb-3 border-b border-[#F0F1F3] last:border-b-0 last:pb-0">
                                <div class="w-14 h-14 rounded-lg bg-[#F7F8FA] overflow-hidden flex-shrink-0 border border-[#E0E2E7]">
                                    <?php if ($item['main_image']): ?>
                                    <img src="../assets/uploads/products/<?= htmlspecialchars(basename($item['main_image'])) ?>" alt="" class="w-full h-full object-cover">
                                    <?php endif; ?>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-['Inter'] font-medium text-[13px] text-[#1A1A2E] line-clamp-1"><?= htmlspecialchars($item['name']) ?></div>
                                    <div class="font-['Inter'] text-[11px] text-[#8A8F99]">Qty: <?= $item['quantity'] ?><?php
                                        if ($item['selected_strap_size']) {
                                            $co_length_map = ['XS' => 145, 'S' => 160, 'M' => 175, 'L' => 190, 'XL' => 205];
                                            $co_len = $co_length_map[$item['selected_strap_size']] ?? null;
                                            echo ' · Strap: ' . htmlspecialchars($item['selected_strap_size']) . ($co_len ? " ({$co_len} mm)" : '');
                                        }
                                    ?></div>
                                    <div class="font-['Inter'] font-semibold text-[13px] text-[#1A1A2E] mt-0.5">NPR <?= number_format($item['price'] * $item['quantity'], 0) ?></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="space-y-2 pt-4 border-t border-[#E0E2E7]">
                            <div class="flex justify-between font-['Inter'] text-[14px]"><span class="text-[#5A5F6D]">Subtotal</span><span class="text-[#1A1A2E] font-semibold">NPR <?= number_format($subtotal, 0) ?></span></div>
                            <div class="flex justify-between font-['Inter'] text-[14px]"><span class="text-[#5A5F6D]">Shipping</span><span class="text-[#1A1A2E] font-semibold">NPR <?= number_format($shipping, 0) ?></span></div>
                            <div class="w-full h-px bg-[#E0E2E7] my-2"></div>
                            <div class="flex justify-between"><span class="font-['Inter'] font-semibold text-[16px] text-[#1A1A2E]">Grand Total</span><span class="font-['Inter'] font-bold text-[24px] text-[#C9A84C]">NPR <?= number_format($grand_total, 0) ?></span></div>
                        </div>

                        <button type="submit" class="flex items-center justify-center gap-2 h-[48px] w-full bg-[#C9A84C] hover:bg-[#B8953F] text-white font-['Inter'] font-semibold text-[15px] rounded-lg transition-all duration-200 hover:shadow-lg mt-5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Confirm Order
                        </button>
                        <p class="font-['Inter'] text-[11px] text-[#8A8F99] text-center mt-3">By placing this order, you confirm that all details are correct.</p>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>