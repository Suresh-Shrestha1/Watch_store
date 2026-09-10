<?php
$footer_base_path = '';
if (strpos($_SERVER['PHP_SELF'], '/pages/') !== false) {
    $footer_base_path = '../';
}
if (strpos($_SERVER['PHP_SELF'], '/pages/account/') !== false) {
    $footer_base_path = '../../';
}

// Compute project root as a full URL — works on localhost AND any live host.
// Uses HTTP_HOST so it never depends on folder depth or server rewrite rules.
$_protocol  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$_host      = $_SERVER['HTTP_HOST'];  // e.g. chrononest.sureshshrestha12.com.np OR localhost

// Find project root by stripping /pages/... or just the filename from PHP_SELF
$_self = $_SERVER['PHP_SELF'];
if (strpos($_self, '/pages/account/') !== false) {
    $_root_path = substr($_self, 0, strpos($_self, '/pages/account/'));
} elseif (strpos($_self, '/pages/') !== false) {
    $_root_path = substr($_self, 0, strpos($_self, '/pages/'));
} else {
    $_root_path = rtrim(dirname($_self), '/');
}

// Full base URL e.g. https://chrononest.sureshshrestha12.com.np
// or http://localhost/watch_store
$_base_url = $_protocol . '://' . $_host . $_root_path;
?>

<footer class="bg-[#1B2A4A] mt-16 lg:mt-20">
    <div class="w-full max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-10 xl:px-16 pt-12 lg:pt-16 pb-10">

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 lg:gap-12">

            <!-- COLUMN 1: Brand -->
            <div>
                <h3 class="font-playfair text-white text-lg lg:text-xl font-bold mb-4 leading-tight">
                    ChronoNest
                </h3>
                <p class="text-[#B0B8C9] text-[13px] leading-relaxed mb-5">
                    Nepal's premier destination for luxury horology, dedicated to heritage, craftsmanship, and the
                    timeless art of watchmaking.
                </p>
                <div class="flex items-center gap-2.5">
                    <a href="#"
                        class="flex items-center justify-center w-9 h-9 rounded-lg bg-[#2C4066] text-white transition-colors duration-200 hover:bg-[#C9A84C] hover:text-[#1B2A4A]">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92s2.92-1.31 2.92-2.92-1.31-2.92-2.92-2.92z" />
                        </svg>
                    </a>
                    <a href="#"
                        class="flex items-center justify-center w-9 h-9 rounded-lg bg-[#2C4066] text-white transition-colors duration-200 hover:bg-[#C9A84C] hover:text-[#1B2A4A]">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zm6.93 6h-2.95c-.32-1.25-.78-2.45-1.38-3.56 1.84.63 3.37 1.91 4.33 3.56zM12 4.04c.83 1.2 1.48 2.53 1.91 3.96h-3.82c.43-1.43 1.08-2.76 1.91-3.96zM4.26 14C4.1 13.36 4 12.69 4 12s.1-1.36.26-2h3.38c-.08.66-.14 1.32-.14 2 0 .68.06 1.34.14 2H4.26zm.82 2h2.95c.32 1.25.78 2.45 1.38 3.56-1.84-.63-3.37-1.9-4.33-3.56zm2.95-8H5.08c.96-1.66 2.49-2.93 4.33-3.56C8.81 5.55 8.35 6.75 8.03 8zM12 19.96c-.83-1.2-1.48-2.53-1.91-3.96h3.82c-.43 1.43-1.08 2.76-1.91 3.96zM14.34 14H9.66c-.09-.66-.16-1.32-.16-2 0-.68.07-1.35.16-2h4.68c.09.65.16 1.32.16 2 0 .68-.07 1.34-.16 2zm.25 5.56c.6-1.11 1.06-2.31 1.38-3.56h2.95c-.96 1.65-2.49 2.93-4.33 3.56zM16.36 14c.08-.66.14-1.32.14-2 0-.68-.06-1.34-.14-2h3.38c.16.64.26 1.31.26 2s-.1 1.36-.26 2h-3.38z" />
                        </svg>
                    </a>
                    <a href="#"
                        class="flex items-center justify-center w-9 h-9 rounded-lg bg-[#2C4066] text-white transition-colors duration-200 hover:bg-[#C9A84C] hover:text-[#1B2A4A]">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- COLUMN 2: Quick Links -->
            <div>
                <h4 class="font-playfair text-[#C9A84C] text-base lg:text-lg font-bold mb-5 leading-tight">
                    Quick Links
                </h4>
                <ul class="list-none p-0 m-0 space-y-3">
                    <li>
                        <a href="<?php echo $footer_base_path; ?>index.php"
                            class="no-underline text-[#B0B8C9] text-sm transition-colors duration-200 hover:text-[#C9A84C]">
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $footer_base_path; ?>pages/shop.php"
                            class="no-underline text-[#B0B8C9] text-sm transition-colors duration-200 hover:text-[#C9A84C]">
                            Shop
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $footer_base_path; ?>pages/expensive.php"
                            class="no-underline text-[#B0B8C9] text-sm transition-colors duration-200 hover:text-[#C9A84C]">
                            Premium Watches
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $footer_base_path; ?>pages/about.php"
                            class="no-underline text-[#B0B8C9] text-sm transition-colors duration-200 hover:text-[#C9A84C]">
                            About
                        </a>
                    </li>
                    <!-- <li>
                        <a href="<?php echo $footer_base_path; ?>pages/contact.php"
                            class="no-underline text-[#B0B8C9] text-sm transition-colors duration-200 hover:text-[#C9A84C]">
                            Contact
                        </a>
                    </li> -->
                </ul>
            </div>

            <!-- COLUMN 3: Customer Service -->
            <div>
                <h4 class="font-playfair text-[#C9A84C] text-base lg:text-lg font-bold mb-5 leading-tight">
                    Customer Service
                </h4>
                <ul class="list-none p-0 m-0 space-y-3">
                    <li>
                        <a href="<?php echo $footer_base_path; ?>pages/account/index.php"
                            class="no-underline text-[#B0B8C9] text-sm transition-colors duration-200 hover:text-[#C9A84C]">
                            My Account
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $footer_base_path; ?>pages/account/orders.php"
                            class="no-underline text-[#B0B8C9] text-sm transition-colors duration-200 hover:text-[#C9A84C]">
                            Order Tracking
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="no-underline text-[#B0B8C9] text-sm transition-colors duration-200 hover:text-[#C9A84C]">
                            Shipping Policy
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="no-underline text-[#B0B8C9] text-sm transition-colors duration-200 hover:text-[#C9A84C]">
                            Terms &amp; Conditions
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="no-underline text-[#B0B8C9] text-sm transition-colors duration-200 hover:text-[#C9A84C]">
                            Privacy Policy
                        </a>
                    </li>
                </ul>
            </div>

            <!-- COLUMN 4: Contact Us -->
            <div>
                <h4 class="font-playfair text-[#C9A84C] text-base lg:text-lg font-bold mb-5 leading-tight">
                    Contact Us
                </h4>
                <ul class="list-none p-0 m-0 space-y-3.5">
                    <li class="flex items-start gap-3">
                        <svg class="w-4 h-4 text-[#C9A84C] flex-shrink-0 mt-0.5" fill="currentColor"
                            viewBox="0 0 24 24">
                            <path
                                d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                        </svg>
                        <span class="text-[#B0B8C9] text-sm leading-snug">
                            Durbar Marg, Kathmandu, Nepal
                        </span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-4 h-4 text-[#C9A84C] flex-shrink-0 mt-0.5" fill="currentColor"
                            viewBox="0 0 24 24">
                            <path
                                d="M20.01 15.38c-1.23 0-2.42-.2-3.53-.56-.35-.12-.74-.03-1.01.24l-1.57 1.97c-2.83-1.35-5.48-3.9-6.89-6.83l1.95-1.66c.27-.28.35-.67.24-1.02-.37-1.11-.56-2.3-.56-3.53 0-.54-.45-.99-.99-.99H4.19C3.65 3 3 3.24 3 3.99 3 13.28 10.73 21 20.01 21c.71 0 .99-.63.99-1.18v-3.45c0-.54-.45-.99-.99-.99z" />
                        </svg>
                        <a href="tel:+9779840000000"
                            class="no-underline text-[#B0B8C9] text-sm transition-colors duration-200 hover:text-[#C9A84C]">
                            +977 9840XXXXXX
                        </a>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-4 h-4 text-[#C9A84C] flex-shrink-0 mt-0.5" fill="currentColor"
                            viewBox="0 0 24 24">
                            <path
                                d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" />
                        </svg>
                        <a href="mailto:chrononest@gmail.com"
                            class="no-underline text-[#B0B8C9] text-sm break-all transition-colors duration-200 hover:text-[#C9A84C]">
                            chrononest@gmail.com
                        </a>
                    </li>
                </ul>
            </div>

        </div>
    </div>

    <!-- COPYRIGHT -->
    <div class="bg-[#0F1B33]">
        <div class="w-full max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-10 xl:px-16 text-center py-4">
            <p class="text-[#B0B8C9] text-xs tracking-wider uppercase">
                &copy; <?php echo date('Y'); ?> ChronoNest. All Rights Reserved.
            </p>
        </div>
    </div>
</footer>

<script>
window.AJAX_URL = '<?php echo $_base_url; ?>/ajax_handler.php';
</script>

<!-- Toast container and animations — available on every page -->
<div id="toastContainer" class="fixed top-24 right-6 z-[9999] flex flex-col gap-2 pointer-events-none"></div>
<style>
@keyframes slideInRight {
    from {
        transform: translateX(120%);
        opacity: 0;
    }

    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOutRight {
    from {
        transform: translateX(0);
        opacity: 1;
    }

    to {
        transform: translateX(120%);
        opacity: 0;
    }
}

.toast-enter {
    animation: slideInRight 0.3s ease-out forwards;
}

.toast-exit {
    animation: slideOutRight 0.3s ease-in forwards;
}
</style>

<script src="<?php echo $_base_url; ?>/assets/js/main.js"></script>

</body>

</html>