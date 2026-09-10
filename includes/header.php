<?php
require_once __DIR__ . '/../config/db.php';

$is_logged_in = isset($_SESSION['user_id']);

$user_name  = '';
$user_email = '';
if ($is_logged_in) {
    $user_name  = $_SESSION['user_name'] ?? 'User';
    $user_email = $_SESSION['user_email'] ?? '';
}

$cart_count     = 0;
$wishlist_count = 0;

if ($is_logged_in) {
    $user_id = intval($_SESSION['user_id']);

    $cart_stmt = $conn->prepare("SELECT COALESCE(SUM(quantity), 0) as total FROM cart WHERE user_id = ?");
    $cart_stmt->bind_param("i", $user_id);
    $cart_stmt->execute();
    $cart_count = intval($cart_stmt->get_result()->fetch_assoc()['total']);
    $cart_stmt->close();

    $wish_stmt = $conn->prepare("SELECT COUNT(*) as total FROM wishlists WHERE user_id = ?");
    $wish_stmt->bind_param("i", $user_id);
    $wish_stmt->execute();
    $wishlist_count = intval($wish_stmt->get_result()->fetch_assoc()['total']);
    $wish_stmt->close();
}

$current_page = basename($_SERVER['PHP_SELF']);

$base_path = '';
if (strpos($_SERVER['PHP_SELF'], '/pages/') !== false) {
    $base_path = '../';
}
if (strpos($_SERVER['PHP_SELF'], '/pages/account/') !== false) {
    $base_path = '../../';
}

$search_value = $_GET['q'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' — ChronoNest' : 'ChronoNest — Luxury Watches'; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700;800&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
    .font-playfair {
        font-family: 'Playfair Display', serif;
    }

    .font-inter {
        font-family: 'Inter', sans-serif;
    }

    .search-input::placeholder,
    .mobile-search-input::placeholder {
        color: rgba(255, 255, 255, 0.4);
    }
    </style>
</head>

<body class="font-inter bg-white text-gray-900">

    <!-- TOP ANNOUNCEMENT BAR -->
    <div class="fixed top-0 left-0 right-0 h-9 z-[10001] w-full bg-[#0F1B33] justify-center px-4 flex items-center">
        <p class="text-white text-[11px] font-bold tracking-wider uppercase">
           Shop with Confidence - Quality You Can Trust
        </p>
    </div>

    <!-- MAIN HEADER -->
    <header class="fixed top-9 left-0 right-0 z-[10001] w-full bg-[#1B2A4A] border-b border-white/5">
        <div
            class="w-full max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-10 xl:px-16 flex items-center justify-between h-14 lg:h-[58px]">

            <!-- Mobile Hamburger -->
            <button onclick="toggleMobileMenu()"
                class="lg:hidden flex-shrink-0 bg-transparent border-0 cursor-pointer text-white p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <!-- Logo -->
            <a href="<?php echo $base_path; ?>index.php" class="no-underline flex-shrink-0">
                <h1
                    class="font-playfair text-white text-lg sm:text-xl lg:text-2xl font-bold tracking-tight leading-none m-0">
                    CHRONONEST
                </h1>
            </a>

            <!-- Desktop Navigation -->
            <nav class="hidden lg:flex items-center gap-5 xl:gap-8">

                <a href="<?php echo $base_path; ?>index.php" class="no-underline transition-colors duration-200 text-xs font-semibold tracking-widest uppercase
                          <?php echo ($current_page === 'index.php' && strpos($_SERVER['PHP_SELF'], '/pages/') === false)
                              ? 'text-[#C9A84C]' : 'text-white hover:text-[#C9A84C]'; ?>">
                    HOME
                </a>

                <div class="relative group">
                    <a href="<?php echo $base_path; ?>pages/shop.php"
                        class="no-underline flex items-center gap-1 transition-colors duration-200 text-xs font-semibold tracking-widest uppercase
                              <?php echo ($current_page === 'shop.php') ? 'text-[#C9A84C]' : 'text-white hover:text-[#C9A84C]'; ?>">
                        SHOP
                        <svg class="w-2 h-2" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </a>

                    <div
                        class="hidden group-hover:block absolute top-full left-1/2 -translate-x-1/2 z-50 pt-3 min-w-[180px]">
                        <div class="rounded-lg overflow-hidden bg-[#1B2A4A] shadow-xl border border-[#C9A84C]/20">
                            <a href="<?php echo $base_path; ?>pages/shop.php?gender=Men"
                                class="block no-underline transition-colors duration-200 text-white text-xs px-4 py-2.5 hover:bg-[#2C4066] hover:text-[#C9A84C]">
                                Men's Watches
                            </a>
                            <a href="<?php echo $base_path; ?>pages/shop.php?gender=Women"
                                class="block no-underline transition-colors duration-200 text-white text-xs px-4 py-2.5 hover:bg-[#2C4066] hover:text-[#C9A84C]">
                                Women's Watches
                            </a>
                            <a href="<?php echo $base_path; ?>pages/shop.php?gender=Unisex"
                                class="block no-underline transition-colors duration-200 text-white text-xs px-4 py-2.5 hover:bg-[#2C4066] hover:text-[#C9A84C]">
                                Unisex Watches
                            </a>
                            <a href="<?php echo $base_path; ?>pages/shop.php?gender=Kids"
                                class="block no-underline transition-colors duration-200 text-white text-xs px-4 py-2.5 hover:bg-[#2C4066] hover:text-[#C9A84C]">
                                Kids' Watches
                            </a>
                        </div>
                    </div>
                </div>

                <a href="<?php echo $base_path; ?>pages/expensive.php"
                    class="no-underline transition-colors duration-200 text-xs font-semibold tracking-widest uppercase
                          <?php echo ($current_page === 'expensive.php') ? 'text-[#C9A84C]' : 'text-white hover:text-[#C9A84C]'; ?>">
                    PREMIUM
                </a>

                <a href="<?php echo $base_path; ?>pages/about.php"
                    class="no-underline transition-colors duration-200 text-xs font-semibold tracking-widest uppercase
                          <?php echo ($current_page === 'about.php') ? 'text-[#C9A84C]' : 'text-white hover:text-[#C9A84C]'; ?>">
                    ABOUT
                </a>

                <!-- <a href="<?php echo $base_path; ?>pages/contact.php"
                    class="no-underline transition-colors duration-200 text-xs font-semibold tracking-widest uppercase
                          <?php echo ($current_page === 'contact.php') ? 'text-[#C9A84C]' : 'text-white hover:text-[#C9A84C]'; ?>">
                    CONTACT
                </a> -->

            </nav>

            <!-- Right Side -->
            <div class="flex items-center flex-shrink-0 gap-2.5 sm:gap-3 lg:gap-4">

                <!-- Desktop Search -->
                <form action="<?php echo $base_path; ?>pages/search.php" method="GET" class="hidden md:block">
                    <div class="relative">
                        <input type="text" name="q" value="<?php echo htmlspecialchars($search_value); ?>"
                            placeholder="Search..." class="search-input outline-none transition-all duration-200
                                      h-8 pl-3.5 pr-9 rounded-full
                                      bg-white/[.08] text-white
                                      border border-white/10 text-xs
                                      w-36 lg:w-44 xl:w-52
                                      focus:border-[#C9A84C] focus:bg-white/[.12]">
                        <button type="submit" class="absolute top-1/2 right-1 -translate-y-1/2
                                       flex items-center justify-center
                                       w-6 h-6 bg-[#C9A84C] border-0 rounded-full cursor-pointer">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="2.5"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </div>
                </form>

                <!-- Wishlist -->
                <a href="<?php echo $is_logged_in ? $base_path . 'pages/account/wishlist.php' : $base_path . 'pages/login.php'; ?>"
                    class="relative no-underline text-white hover:text-[#C9A84C] flex-shrink-0 transition-colors duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.636l1.318-1.318a4.5 4.5 0 116.364 6.364L12 20.364l-7.682-7.682a4.5 4.5 0 010-6.364z" />
                    </svg>
                    <span class="absolute -top-1.5 -right-1.5 flex items-center justify-center
                                 min-w-[16px] h-4 px-0.5 rounded-full
                                 bg-[#C9A84C] text-[#1B2A4A] text-[9px] font-bold">
                        <?php echo $wishlist_count; ?>
                    </span>
                </a>

                <!-- Cart -->
                <a href="<?php echo $base_path; ?>pages/cart.php"
                    class="relative no-underline text-white hover:text-[#C9A84C] flex-shrink-0 transition-colors duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l-1 12H6L5 9z" />
                    </svg>
                    <span class="absolute -top-1.5 -right-1.5 flex items-center justify-center
                                 min-w-[16px] h-4 px-0.5 rounded-full
                                 bg-[#C9A84C] text-[#1B2A4A] text-[9px] font-bold">
                        <?php echo $cart_count; ?>
                    </span>
                </a>

                <!-- Divider -->
                <div class="hidden lg:block h-5 w-px bg-white/20"></div>

                <?php if (!$is_logged_in): ?>

                <div class="hidden lg:flex items-center gap-2">
                    <a href="<?php echo $base_path; ?>pages/login.php" class="inline-flex items-center justify-center no-underline transition-all duration-200
                                  h-8 px-4 rounded text-white border border-[#C9A84C]
                                  text-xs font-semibold
                                  hover:bg-[#C9A84C] hover:text-[#1B2A4A]">
                        Login
                    </a>
                    <a href="<?php echo $base_path; ?>pages/register.php" class="inline-flex items-center justify-center no-underline transition-all duration-200
                                  h-8 px-4 rounded bg-[#C9A84C] text-[#1B2A4A]
                                  text-xs font-semibold
                                  hover:bg-[#B8953F]">
                        Register
                    </a>
                </div>

                <?php else: ?>

                <div class="relative hidden lg:block">
                    <button onclick="toggleUserMenu(event)" class="flex items-center justify-center transition-all duration-200
                                       w-8 h-8 rounded-full
                                       bg-[#2C4066] border-2 border-[#C9A84C]/60
                                       cursor-pointer overflow-hidden
                                       hover:border-[#C9A84C]">
                        <svg class="w-5 h-5 text-[#C9A84C]" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                        </svg>
                    </button>

                    <div id="user-menu" class="hidden absolute right-0 top-full z-50 mt-2
                                    min-w-[210px] bg-[#1B2A4A] rounded-lg overflow-hidden
                                    shadow-xl border border-[#C9A84C]/20">

                        <div class="px-4 py-3 border-b border-white/[.08]">
                            <p class="text-[13px] font-semibold text-white mb-0.5">
                                Hello, <?php echo htmlspecialchars($user_name); ?>
                            </p>
                            <p class="text-[11px] text-[#B0B8C9]">
                                <?php echo htmlspecialchars($user_email); ?>
                            </p>
                        </div>

                        <div class="py-1">
                            <a href="<?php echo $base_path; ?>pages/account/index.php" class="flex items-center no-underline transition-colors duration-200
                                          text-white px-4 py-2 gap-2.5 text-xs hover:bg-[#2C4066]">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor"
                                    stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                My Profile
                            </a>
                            <a href="<?php echo $base_path; ?>pages/account/orders.php" class="flex items-center no-underline transition-colors duration-200
                                          text-white px-4 py-2 gap-2.5 text-xs hover:bg-[#2C4066]">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor"
                                    stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                My Orders
                            </a>
                            <a href="<?php echo $base_path; ?>pages/account/wishlist.php" class="flex items-center no-underline transition-colors duration-200
                                          text-white px-4 py-2 gap-2.5 text-xs hover:bg-[#2C4066]">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor"
                                    stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.636l1.318-1.318a4.5 4.5 0 116.364 6.364L12 20.364l-7.682-7.682a4.5 4.5 0 010-6.364z" />
                                </svg>
                                My Wishlist
                            </a>
                        </div>

                        <div class="border-t border-white/[.08]"></div>

                        <a href="<?php echo $base_path; ?>pages/logout.php" class="flex items-center no-underline transition-colors duration-200
                                      text-red-400 px-4 py-2.5 gap-2.5 text-xs font-medium hover:bg-[#2C4066]">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Logout
                        </a>
                    </div>
                </div>

                <?php endif; ?>
            </div>
        </div>

        <!-- Mobile Search -->
        <div class="md:hidden px-4 pb-3">
            <form action="<?php echo $base_path; ?>pages/search.php" method="GET">
                <div class="relative">
                    <input type="text" name="q" value="<?php echo htmlspecialchars($search_value); ?>"
                        placeholder="Search watches..." class="mobile-search-input w-full outline-none
                                  h-9 pl-4 pr-10 rounded-full
                                  bg-white/[.08] text-white
                                  border border-white/10 text-xs">
                    <button type="submit" class="absolute top-1/2 right-1 -translate-y-1/2
                                   flex items-center justify-center
                                   w-7 h-7 bg-[#C9A84C] border-0 rounded-full cursor-pointer">
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </header>

    <!-- Spacer to offset fixed announcement + header so page content is not hidden -->
    <div aria-hidden="true" class="h-[94px]"></div>

    <!-- Mobile Overlay -->
    <div id="mobile-overlay" onclick="closeMobileMenu()" class="fixed inset-0 hidden lg:hidden bg-black/50 z-[99998]">
    </div>

    <!-- Mobile Drawer -->
    <aside id="mobile-menu" class="fixed top-0 left-0 h-screen w-64 bg-[#1B2A4A] flex flex-col transition-transform duration-300 -translate-x-full lg:hidden z-[99999]">

        <div class="flex items-center justify-between h-12 px-4 border-b border-white/10 flex-shrink-0">
            <h2 class="font-playfair text-white text-base font-bold m-0">CHRONONEST</h2>
            <button onclick="closeMobileMenu()" class="bg-transparent border-0 cursor-pointer text-[#B0B8C9] p-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <?php if ($is_logged_in): ?>
        <div class="flex items-center px-4 py-3 gap-2.5 border-b border-white/10 flex-shrink-0">
            <div class="flex items-center justify-center flex-shrink-0
                            w-8 h-8 rounded-full bg-[#2C4066] border-2 border-[#C9A84C]/60">
                <svg class="w-5 h-5 text-[#C9A84C]" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-white m-0">
                    Hello, <?php echo htmlspecialchars($user_name); ?>
                </p>
                <p class="truncate text-[10px] text-[#B0B8C9] m-0">
                    <?php echo htmlspecialchars($user_email); ?>
                </p>
            </div>
        </div>
        <?php endif; ?>

        <nav class="flex-1 overflow-y-auto p-2">

            <a href="<?php echo $base_path; ?>index.php" class="block no-underline transition-colors duration-200
                      px-3 py-2 rounded mb-0.5 text-xs font-medium hover:bg-[#2C4066]
                      <?php echo ($current_page === 'index.php' && strpos($_SERVER['PHP_SELF'], '/pages/') === false)
                          ? 'text-[#C9A84C]' : 'text-white'; ?>">
                Home
            </a>

            <a href="<?php echo $base_path; ?>pages/shop.php" class="block no-underline transition-colors duration-200
                      px-3 py-2 rounded mb-0.5 text-xs font-medium hover:bg-[#2C4066]
                      <?php echo ($current_page === 'shop.php') ? 'text-[#C9A84C]' : 'text-white'; ?>">
                Shop
            </a>

            <a href="<?php echo $base_path; ?>pages/shop.php?gender=Men"
                class="block no-underline px-3 py-1.5 ml-2.5 rounded mb-px text-[11px] text-[#B0B8C9] hover:text-[#C9A84C]">
                Men's
            </a>
            <a href="<?php echo $base_path; ?>pages/shop.php?gender=Women"
                class="block no-underline px-3 py-1.5 ml-2.5 rounded mb-px text-[11px] text-[#B0B8C9] hover:text-[#C9A84C]">
                Women's
            </a>
            <a href="<?php echo $base_path; ?>pages/shop.php?gender=Unisex"
                class="block no-underline px-3 py-1.5 ml-2.5 rounded mb-px text-[11px] text-[#B0B8C9] hover:text-[#C9A84C]">
                Unisex
            </a>
            <a href="<?php echo $base_path; ?>pages/shop.php?gender=Kids"
                class="block no-underline px-3 py-1.5 ml-2.5 rounded mb-px text-[11px] text-[#B0B8C9] hover:text-[#C9A84C]">
                Kids
            </a>

            <a href="<?php echo $base_path; ?>pages/expensive.php" class="block no-underline px-3 py-2 rounded mb-0.5 text-xs font-medium hover:bg-[#2C4066]
                      <?php echo ($current_page === 'expensive.php') ? 'text-[#C9A84C]' : 'text-white'; ?>">
                Premium Watches
            </a>

            <a href="<?php echo $base_path; ?>pages/about.php" class="block no-underline transition-colors duration-200
                      px-3 py-2 rounded mb-0.5 text-xs font-medium hover:bg-[#2C4066]
                      <?php echo ($current_page === 'about.php') ? 'text-[#C9A84C]' : 'text-white'; ?>">
                About
            </a>

            <a href="<?php echo $base_path; ?>pages/contact.php" class="block no-underline transition-colors duration-200
                      px-3 py-2 rounded mb-0.5 text-xs font-medium hover:bg-[#2C4066]
                      <?php echo ($current_page === 'contact.php') ? 'text-[#C9A84C]' : 'text-white'; ?>">
                Contact
            </a>

            <?php if ($is_logged_in): ?>
            <div class="my-2 border-t border-white/10"></div>
            <a href="<?php echo $base_path; ?>pages/account/index.php"
                class="block no-underline px-3 py-2 rounded mb-0.5 text-xs text-white hover:bg-[#2C4066]">
                My Profile
            </a>
            <a href="<?php echo $base_path; ?>pages/account/orders.php"
                class="block no-underline px-3 py-2 rounded mb-0.5 text-xs text-white hover:bg-[#2C4066]">
                My Orders
            </a>
            <a href="<?php echo $base_path; ?>pages/account/wishlist.php"
                class="block no-underline px-3 py-2 rounded mb-0.5 text-xs text-white hover:bg-[#2C4066]">
                My Wishlist
            </a>
            <?php endif; ?>
        </nav>

        <div class="flex-shrink-0 p-2 border-t border-white/10">
            <?php if (!$is_logged_in): ?>
            <a href="<?php echo $base_path; ?>pages/login.php" class="w-full flex items-center justify-center no-underline
                          h-9 rounded mb-2 text-xs font-semibold text-white border border-[#C9A84C]">
                Login
            </a>
            <a href="<?php echo $base_path; ?>pages/register.php" class="w-full flex items-center justify-center no-underline
                          h-9 rounded text-xs font-semibold bg-[#C9A84C] text-[#1B2A4A]">
                Register
            </a>
            <?php else: ?>
            <a href="<?php echo $base_path; ?>pages/logout.php" class="w-full flex items-center justify-center no-underline
                          h-9 rounded text-xs font-semibold bg-red-500 text-white">
                Logout
            </a>
            <?php endif; ?>
        </div>
    </aside>