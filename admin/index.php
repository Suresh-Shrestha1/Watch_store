<?php
session_start();

// If already logged in go to dashboard
if (isset($_SESSION['admin_id']) && $_SESSION['admin_role'] === 'admin') {
    header('Location: dashboard.php');
    exit();
}

// Check for error message passed from login_action.php
$error = '';
if (isset($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}

// Keep email value after error so user doesn't retype
$email = $_SESSION['login_email'] ?? '';
if (isset($_SESSION['login_email'])) {
    unset($_SESSION['login_email']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — ChronoNest</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen flex items-center justify-center px-4 py-10"
    style="background-color: #F7F8FA; font-family: 'Inter', sans-serif;">

    <div class="w-full max-w-md">
        <!-- Brand -->
        <div class="text-center mb-8">
            <img src="../assets/images/ChronoNest sq.png" alt="ChronoNest Logo" class="mx-auto w-[150px]">
            <h1 class="font-bold text-4xl mb-1" style="font-family: 'Playfair Display', serif; color: #1B2A4A;">
                ChronoNest</h1>
            <p class="text-xs font-semibold uppercase tracking-widest" style="color: #C9A84C;">Admin Panel</p>
        </div>

        <!-- Card -->
        <div class="bg-white rounded-xl p-8" style="box-shadow: 0 2px 12px rgba(0,0,0,0.08);">
            <h2 class="font-bold text-2xl text-center mb-1"
                style="font-family: 'Playfair Display', serif; color: #1A1A2E;">Welcome Back</h2>
            <p class="text-sm text-center mb-7" style="color: #5A5F6D;">Sign in to continue to your dashboard</p>

            <!-- Error Message -->
            <?php if (!empty($error)): ?>
            <div class="flex items-center gap-3 rounded-lg px-4 py-3 mb-6"
                style="background-color: #FDEAEA; border: 1px solid rgba(214,69,69,0.25);">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24" style="color: #D64545;">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" y1="7" x2="12" y2="13" />
                    <circle cx="12" cy="17" r="1" />
                </svg>
                <p class="text-sm" style="color: #D64545;"><?php echo htmlspecialchars($error); ?></p>
            </div>
            <?php endif; ?>

            <!-- Form -->
            <form method="POST" action="actions/auth-action.php" novalidate>

                <!-- Email -->
                <div class="mb-5">
                    <label for="email" class="block text-sm font-semibold mb-1.5" style="color: #1A1A2E;">Email Address</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>"
                        placeholder="admin@chrononest.com" required
                        class="w-full h-11 px-4 rounded-lg text-sm outline-none transition-all duration-200
                        border border-[#E0E2E7] text-[#1A1A2E] font-sans
                        focus:border-[#C9A84C] focus:ring-4 focus:ring-[#C9A84C]/15">
                </div>

                <!-- Password -->
                <div class="mb-7">
                    <label for="password" class="block text-sm font-semibold mb-1.5"
                        style="color: #1A1A2E;">Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" placeholder="Enter your password" required
                            class="w-full h-11 px-4 pr-11 rounded-lg text-sm outline-none transition-all duration-200
                            border border-[#E0E2E7] text-[#1A1A2E] font-sans
                            focus:border-[#C9A84C] focus:ring-4 focus:ring-[#C9A84C]/15">

                        <button type="button" onclick="togglePassword()"
                            class="absolute right-3 top-1/2 -translate-y-1/2"
                            style="background: none; border: none; cursor: pointer; color: #8A8F99;">
                            <svg id="icon-eye-open" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                            <svg id="icon-eye-closed" class="w-5 h-5 hidden" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path
                                    d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24" />
                                <line x1="1" y1="1" x2="23" y2="23" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Submit -->
                <button type="submit"
                    class="w-full h-11 rounded-lg text-sm font-semibold text-white transition-all duration-200
                    bg-[#1B2A4A] hover:bg-[#2C4066] cursor-pointer">
                    Sign In to Admin Panel
                </button>

            </form>
        </div>

        <p class="text-center text-xs mt-6" style="color: #8A8F99;">
            &copy; <?php echo date('Y'); ?> ChronoNest. All rights reserved.
        </p>

    </div>

    <script src="../assets/js/admin.js"></script>
</body>

</html>