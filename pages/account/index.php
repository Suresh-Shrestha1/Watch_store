<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../login.php'); exit; }
require_once '../../config/db.php';

$user_id = $_SESSION['user_id'];

// Fetch user
$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

// Stats
$order_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM orders WHERE user_id = $user_id"))['cnt'];
$wishlist_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM wishlists WHERE user_id = $user_id"))['cnt'];
$cart_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM cart WHERE user_id = $user_id"))['cnt'];

// Recent orders
$recent_orders_stmt = mysqli_prepare($conn, "SELECT id, order_number, grand_total, order_status, payment_status, payment_method, ordered_at FROM orders WHERE user_id = ? ORDER BY ordered_at DESC LIMIT 3");
mysqli_stmt_bind_param($recent_orders_stmt, "i", $user_id);
mysqli_stmt_execute($recent_orders_stmt);
$recent_orders = mysqli_fetch_all(mysqli_stmt_get_result($recent_orders_stmt), MYSQLI_ASSOC);

$page_title = "My Account – ChronoNest";
$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
require_once '../../includes/header.php';
?>

<!-- PAGE HEADER -->
<section class="bg-[#1B2A4A] py-10">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-[#C9A84C] flex items-center justify-center flex-shrink-0">
                <span class="font-['Inter'] font-bold text-[22px] text-white"><?= strtoupper(substr($user['name'], 0, 1)) ?></span>
            </div>
            <div>
                <span class="font-['Inter'] font-semibold text-[11px] uppercase tracking-[1.5px] text-[#C9A84C]">Welcome back</span>
                <h1 class="font-['Playfair_Display'] font-bold text-[28px] text-white mt-0.5"><?= htmlspecialchars($user['name']) ?></h1>
                <p class="font-['Inter'] text-[13px] text-[#B0B8C9] mt-0.5"><?= htmlspecialchars($user['email']) ?></p>
            </div>
        </div>
    </div>
</section>

<!-- BREADCRUMB -->
<div class="bg-white border-b border-[#E0E2E7]">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-3">
        <nav class="flex items-center gap-2 font-['Inter'] text-[13px] text-[#5A5F6D]">
            <a href="../../index.php" class="hover:text-[#C9A84C] transition-colors">Home</a>
            <svg class="w-3 h-3 text-[#8A8F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-[#1A1A2E] font-medium">My Account</span>
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

            <!-- SIDEBAR NAV -->
            <aside class="w-full lg:w-[240px] flex-shrink-0">
                <div class="bg-white rounded-xl border border-[#E0E2E7] shadow-[0_2px_12px_rgba(0,0,0,0.08)] overflow-hidden sticky top-[100px]">
                    <nav class="flex flex-col">
                        <a href="index.php" class="flex items-center gap-3 px-5 py-3.5 bg-[#FFF8E7] border-l-[3px] border-[#C9A84C] font-['Inter'] font-semibold text-[14px] text-[#C9A84C]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            My Profile
                        </a>
                        <a href="orders.php" class="flex items-center gap-3 px-5 py-3.5 border-l-[3px] border-transparent hover:bg-[#F7F8FA] hover:border-[#E0E2E7] font-['Inter'] font-medium text-[14px] text-[#5A5F6D] hover:text-[#1A1A2E] transition-all duration-150">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            My Orders
                            <?php if ($order_count): ?><span class="ml-auto bg-[#F7F8FA] border border-[#E0E2E7] rounded-full px-2 py-0.5 font-['Inter'] font-semibold text-[11px] text-[#5A5F6D]"><?= $order_count ?></span><?php endif; ?>
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
            <div class="flex-1 space-y-6">

                <!-- STATS -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white rounded-xl border border-[#E0E2E7] shadow-[0_2px_12px_rgba(0,0,0,0.08)] p-5 text-center">
                        <div class="w-10 h-10 rounded-lg bg-[#E3F2FD] flex items-center justify-center mx-auto mb-2"><svg class="w-5 h-5 text-[#1565C0]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></div>
                        <div class="font-['Inter'] font-bold text-[24px] text-[#1A1A2E]"><?= $order_count ?></div>
                        <div class="font-['Inter'] text-[12px] text-[#5A5F6D]">Orders</div>
                    </div>
                    <div class="bg-white rounded-xl border border-[#E0E2E7] shadow-[0_2px_12px_rgba(0,0,0,0.08)] p-5 text-center">
                        <div class="w-10 h-10 rounded-lg bg-[#FDEAEA] flex items-center justify-center mx-auto mb-2"><svg class="w-5 h-5 text-[#D64545]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg></div>
                        <div class="font-['Inter'] font-bold text-[24px] text-[#1A1A2E]"><?= $wishlist_count ?></div>
                        <div class="font-['Inter'] text-[12px] text-[#5A5F6D]">Wishlist</div>
                    </div>
                    <div class="bg-white rounded-xl border border-[#E0E2E7] shadow-[0_2px_12px_rgba(0,0,0,0.08)] p-5 text-center">
                        <div class="w-10 h-10 rounded-lg bg-[#FFF8E7] flex items-center justify-center mx-auto mb-2"><svg class="w-5 h-5 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg></div>
                        <div class="font-['Inter'] font-bold text-[24px] text-[#1A1A2E]"><?= $cart_count ?></div>
                        <div class="font-['Inter'] text-[12px] text-[#5A5F6D]">In Cart</div>
                    </div>
                    <div class="bg-white rounded-xl border border-[#E0E2E7] shadow-[0_2px_12px_rgba(0,0,0,0.08)] p-5 text-center">
                        <div class="w-10 h-10 rounded-lg bg-[#E8F5E9] flex items-center justify-center mx-auto mb-2"><svg class="w-5 h-5 text-[#2E7D32]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                        <div class="font-['Inter'] font-semibold text-[13px] text-[#1A1A2E]"><?= $user['last_login_at'] ? date('M d, Y', strtotime($user['last_login_at'])) : 'N/A' ?></div>
                        <div class="font-['Inter'] text-[12px] text-[#5A5F6D]">Last Login</div>
                    </div>
                </div>

                <!-- PROFILE FORM -->
                <div class="bg-white rounded-xl border border-[#E0E2E7] shadow-[0_2px_12px_rgba(0,0,0,0.08)] overflow-hidden">
                    <div class="px-6 py-4 bg-[#F7F8FA] border-b border-[#E0E2E7]">
                        <h2 class="font-['Playfair_Display'] font-bold text-[20px] text-[#1A1A2E]">Profile Information</h2>
                    </div>
                    <div class="p-6">
                        <form action="../actions/account-action.php" method="POST">
                            <input type="hidden" name="action" value="update_profile">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                                <div>
                                    <label class="block font-['Inter'] font-semibold text-[14px] text-[#1A1A2E] mb-1.5">Full Name <span class="text-[#D64545]">*</span></label>
                                    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required class="w-full h-[44px] px-4 bg-white border border-[#E0E2E7] rounded-lg font-['Inter'] text-[15px] text-[#1A1A2E] focus:outline-none focus:border-2 focus:border-[#C9A84C] focus:shadow-[0_0_0_3px_rgba(201,168,76,0.15)] transition-all duration-150">
                                </div>
                                <div>
                                    <label class="block font-['Inter'] font-semibold text-[14px] text-[#1A1A2E] mb-1.5">Email</label>
                                    <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled class="w-full h-[44px] px-4 bg-[#EDEEF1] border border-[#E0E2E7] rounded-lg font-['Inter'] text-[15px] text-[#8A8F99] cursor-not-allowed">
                                    <p class="font-['Inter'] text-[11px] text-[#8A8F99] mt-1">Email cannot be changed</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                                <div>
                                    <label class="block font-['Inter'] font-semibold text-[14px] text-[#1A1A2E] mb-1.5">Phone Number <span class="text-[#D64545]">*</span></label>
                                    <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" maxlength="10" required class="w-full h-[44px] px-4 bg-white border border-[#E0E2E7] rounded-lg font-['Inter'] text-[15px] text-[#1A1A2E] focus:outline-none focus:border-2 focus:border-[#C9A84C] focus:shadow-[0_0_0_3px_rgba(201,168,76,0.15)] transition-all duration-150">
                                </div>
                                <div>
                                    <label class="block font-['Inter'] font-semibold text-[14px] text-[#1A1A2E] mb-1.5">Member Since</label>
                                    <input type="text" value="<?= date('F d, Y', strtotime($user['created_at'])) ?>" disabled class="w-full h-[44px] px-4 bg-[#EDEEF1] border border-[#E0E2E7] rounded-lg font-['Inter'] text-[15px] text-[#8A8F99] cursor-not-allowed">
                                </div>
                            </div>
                            <div class="mb-5">
                                <label class="block font-['Inter'] font-semibold text-[14px] text-[#1A1A2E] mb-1.5">Address</label>
                                <textarea name="address" rows="3" placeholder="Street, Ward, Municipality, District, Province" class="w-full px-4 py-3 bg-white border border-[#E0E2E7] rounded-lg font-['Inter'] text-[15px] text-[#1A1A2E] placeholder-[#8A8F99] focus:outline-none focus:border-2 focus:border-[#C9A84C] focus:shadow-[0_0_0_3px_rgba(201,168,76,0.15)] transition-all duration-150 resize-none"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                            </div>
                            <button type="submit" class="inline-flex items-center gap-2 h-[44px] px-6 bg-[#C9A84C] hover:bg-[#B8953F] text-white font-['Inter'] font-semibold text-[15px] rounded-lg transition-all duration-200 hover:shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Save Changes
                            </button>
                        </form>
                    </div>
                </div>

                <!-- CHANGE PASSWORD -->
                <div class="bg-white rounded-xl border border-[#E0E2E7] shadow-[0_2px_12px_rgba(0,0,0,0.08)] overflow-hidden">
                    <div class="px-6 py-4 bg-[#F7F8FA] border-b border-[#E0E2E7]">
                        <h2 class="font-['Playfair_Display'] font-bold text-[20px] text-[#1A1A2E]">Change Password</h2>
                    </div>
                    <div class="p-6">
                        <form action="../actions/account-action.php" method="POST">
                            <input type="hidden" name="action" value="change_password">
                            <div class="space-y-4 max-w-md">
                                <div>
                                    <label class="block font-['Inter'] font-semibold text-[14px] text-[#1A1A2E] mb-1.5">Current Password <span class="text-[#D64545]">*</span></label>
                                    <div class="relative">
                                        <input type="password" name="current_password" required placeholder="Enter current password" class="w-full h-[44px] px-4 pr-12 bg-white border border-[#E0E2E7] rounded-lg font-['Inter'] text-[15px] text-[#1A1A2E] placeholder-[#8A8F99] focus:outline-none focus:border-2 focus:border-[#C9A84C] focus:shadow-[0_0_0_3px_rgba(201,168,76,0.15)] transition-all duration-150">
                                        <button type="button" onclick="this.previousElementSibling.type=this.previousElementSibling.type==='password'?'text':'password'" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#8A8F99] hover:text-[#5A5F6D] transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
                                    </div>
                                </div>
                                <div>
                                    <label class="block font-['Inter'] font-semibold text-[14px] text-[#1A1A2E] mb-1.5">New Password <span class="text-[#D64545]">*</span></label>
                                    <div class="relative">
                                        <input type="password" name="new_password" required placeholder="Minimum 8 characters" class="w-full h-[44px] px-4 pr-12 bg-white border border-[#E0E2E7] rounded-lg font-['Inter'] text-[15px] text-[#1A1A2E] placeholder-[#8A8F99] focus:outline-none focus:border-2 focus:border-[#C9A84C] focus:shadow-[0_0_0_3px_rgba(201,168,76,0.15)] transition-all duration-150">
                                        <button type="button" onclick="this.previousElementSibling.type=this.previousElementSibling.type==='password'?'text':'password'" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#8A8F99] hover:text-[#5A5F6D] transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
                                    </div>
                                </div>
                                <div>
                                    <label class="block font-['Inter'] font-semibold text-[14px] text-[#1A1A2E] mb-1.5">Confirm New Password <span class="text-[#D64545]">*</span></label>
                                    <div class="relative">
                                        <input type="password" name="confirm_password" required placeholder="Re-enter new password" class="w-full h-[44px] px-4 pr-12 bg-white border border-[#E0E2E7] rounded-lg font-['Inter'] text-[15px] text-[#1A1A2E] placeholder-[#8A8F99] focus:outline-none focus:border-2 focus:border-[#C9A84C] focus:shadow-[0_0_0_3px_rgba(201,168,76,0.15)] transition-all duration-150">
                                        <button type="button" onclick="this.previousElementSibling.type=this.previousElementSibling.type==='password'?'text':'password'" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#8A8F99] hover:text-[#5A5F6D] transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="inline-flex items-center gap-2 h-[44px] px-6 bg-[#1B2A4A] hover:bg-[#2C4066] text-white font-['Inter'] font-semibold text-[15px] rounded-lg transition-all duration-200 mt-5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Update Password
                            </button>
                        </form>
                    </div>
                </div>

                <!-- RECENT ORDERS -->
                <?php if (!empty($recent_orders)): ?>
                <div class="bg-white rounded-xl border border-[#E0E2E7] shadow-[0_2px_12px_rgba(0,0,0,0.08)] overflow-hidden">
                    <div class="px-6 py-4 bg-[#F7F8FA] border-b border-[#E0E2E7] flex items-center justify-between">
                        <h2 class="font-['Playfair_Display'] font-bold text-[20px] text-[#1A1A2E]">Recent Orders</h2>
                        <a href="orders.php" class="font-['Inter'] font-semibold text-[13px] text-[#C9A84C] hover:underline">View All →</a>
                    </div>
                    <div class="divide-y divide-[#F0F1F3]">
                        <?php foreach ($recent_orders as $order):
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
                        ?>
                        <a href="order-detail.php?id=<?= $order['id'] ?>" class="flex items-center justify-between px-6 py-4 hover:bg-[#F7F8FA] transition-colors duration-150 block">
                            <div>
                                <div class="font-['Inter'] font-semibold text-[14px] text-[#1A1A2E]">#<?= htmlspecialchars($order['order_number']) ?></div>
                                <div class="font-['Inter'] text-[12px] text-[#8A8F99] mt-0.5"><?= date('M d, Y · h:i A', strtotime($order['ordered_at'])) ?></div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="text-right">
                                    <div class="font-['Inter'] font-bold text-[15px] text-[#1A1A2E]">NPR <?= number_format($order['grand_total'], 0) ?></div>
                                    <div class="flex items-center gap-1.5 mt-1 justify-end">
                                        <span class="inline-block px-2 py-0.5 rounded font-['Inter'] font-semibold text-[10px] <?= $status_classes ?>"><?= ucfirst($order['order_status']) ?></span>
                                        <span class="inline-block px-2 py-0.5 rounded font-['Inter'] font-semibold text-[10px] <?= $pay_classes ?>"><?= ucfirst($order['payment_status']) ?></span>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-[#8A8F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</section>

<?php require_once '../../includes/footer.php'; ?>