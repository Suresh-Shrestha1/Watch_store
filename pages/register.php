<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}
$page_title = "Create Account – ChronoNest";
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
$old = $_SESSION['old_input'] ?? [];
unset($_SESSION['error'], $_SESSION['success'], $_SESSION['old_input']);
require_once '../includes/header.php';
?>

<section class="min-h-[calc(100vh-80px)] bg-[#F7F8FA] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-[480px]">
        <div class="text-center mb-8">
            <a href="../index.php" class="inline-flex items-center gap-2 mb-6">
                <img src="../assets/images/ChronoNest rec.png" alt="ChronoNest" class="h-8 w-auto">
            </a>
            <h1 class="font-['Playfair_Display'] font-bold text-[28px] text-[#1A1A2E]">Create Account</h1>
            <p class="font-['Inter'] text-[15px] text-[#5A5F6D] mt-2">Join ChronoNest and explore the finest watches</p>
        </div>

        <?php if ($error): ?>
        <div class="flex items-center gap-3 bg-[#FDEAEA] border border-[#D64545]/20 text-[#D64545] rounded-lg px-4 py-3 mb-6 font-['Inter'] text-[14px]">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="flex items-center gap-3 bg-[#E8F5E9] border border-[#2E7D32]/20 text-[#2E7D32] rounded-lg px-4 py-3 mb-6 font-['Inter'] text-[14px]">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <?= htmlspecialchars($success) ?>
        </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl border border-[#E0E2E7] shadow-[0_2px_12px_rgba(0,0,0,0.08)] p-8">
            <form action="../pages/actions/auth-action.php" method="POST" novalidate>
                <input type="hidden" name="action" value="register">

                <div class="mb-5">
                    <label for="name" class="block font-['Inter'] font-semibold text-[14px] text-[#1A1A2E] mb-1.5">Full Name</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($old['name'] ?? '') ?>" placeholder="John Doe" required class="w-full h-[44px] px-4 bg-white border border-[#E0E2E7] rounded-lg font-['Inter'] text-[15px] text-[#1A1A2E] placeholder-[#8A8F99] focus:outline-none focus:border-2 focus:border-[#C9A84C] focus:shadow-[0_0_0_3px_rgba(201,168,76,0.15)] transition-all duration-150">
                </div>

                <div class="mb-5">
                    <label for="email" class="block font-['Inter'] font-semibold text-[14px] text-[#1A1A2E] mb-1.5">Email Address</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" placeholder="you@example.com" required class="w-full h-[44px] px-4 bg-white border border-[#E0E2E7] rounded-lg font-['Inter'] text-[15px] text-[#1A1A2E] placeholder-[#8A8F99] focus:outline-none focus:border-2 focus:border-[#C9A84C] focus:shadow-[0_0_0_3px_rgba(201,168,76,0.15)] transition-all duration-150">
                </div>

                <div class="mb-5">
                    <label for="phone" class="block font-['Inter'] font-semibold text-[14px] text-[#1A1A2E] mb-1.5">Phone Number</label>
                    <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($old['phone'] ?? '') ?>" placeholder="98XXXXXXXX" maxlength="10" required class="w-full h-[44px] px-4 bg-white border border-[#E0E2E7] rounded-lg font-['Inter'] text-[15px] text-[#1A1A2E] placeholder-[#8A8F99] focus:outline-none focus:border-2 focus:border-[#C9A84C] focus:shadow-[0_0_0_3px_rgba(201,168,76,0.15)] transition-all duration-150">
                    <p class="font-['Inter'] text-[12px] text-[#8A8F99] mt-1">10-digit Nepal mobile number</p>
                </div>

                <div class="mb-5">
                    <label for="password" class="block font-['Inter'] font-semibold text-[14px] text-[#1A1A2E] mb-1.5">Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" placeholder="Minimum 8 characters" required class="w-full h-[44px] px-4 pr-12 bg-white border border-[#E0E2E7] rounded-lg font-['Inter'] text-[15px] text-[#1A1A2E] placeholder-[#8A8F99] focus:outline-none focus:border-2 focus:border-[#C9A84C] focus:shadow-[0_0_0_3px_rgba(201,168,76,0.15)] transition-all duration-150">
                        <button type="button" onclick="this.previousElementSibling.type = this.previousElementSibling.type === 'password' ? 'text' : 'password'" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#8A8F99] hover:text-[#5A5F6D] transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="confirm_password" class="block font-['Inter'] font-semibold text-[14px] text-[#1A1A2E] mb-1.5">Confirm Password</label>
                    <div class="relative">
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter your password" required class="w-full h-[44px] px-4 pr-12 bg-white border border-[#E0E2E7] rounded-lg font-['Inter'] text-[15px] text-[#1A1A2E] placeholder-[#8A8F99] focus:outline-none focus:border-2 focus:border-[#C9A84C] focus:shadow-[0_0_0_3px_rgba(201,168,76,0.15)] transition-all duration-150">
                        <button type="button" onclick="this.previousElementSibling.type = this.previousElementSibling.type === 'password' ? 'text' : 'password'" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#8A8F99] hover:text-[#5A5F6D] transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full h-[44px] bg-[#C9A84C] hover:bg-[#B8953F] text-white font-['Inter'] font-semibold text-[15px] rounded-lg transition-all duration-200 hover:shadow-md">Create Account</button>
            </form>

            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-[#E0E2E7]"></div></div>
                <div class="relative flex justify-center"><span class="bg-white px-3 font-['Inter'] text-[13px] text-[#8A8F99]">Already have an account?</span></div>
            </div>

            <a href="../pages/login.php" class="flex items-center justify-center h-[44px] bg-transparent hover:bg-[#F7F8FA] text-[#1B2A4A] font-['Inter'] font-semibold text-[15px] rounded-lg border border-[#1B2A4A] transition-all duration-200">Sign In</a>
        </div>

        <p class="text-center font-['Inter'] text-[13px] text-[#8A8F99] mt-6">
            <a href="../index.php" class="text-[#5A5F6D] hover:text-[#C9A84C] transition-colors duration-150">← Back to Home</a>
        </p>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>