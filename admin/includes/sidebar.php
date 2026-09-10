<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Overlay -->
<div id="sidebar-overlay" onclick="closeSidebar()" class="fixed inset-0 z-40 hidden"
    style="background-color: rgba(0,0,0,0.5);"></div>

<!-- Sidebar -->
<aside id="sidebar"
    class="fixed top-0 left-0 z-50 h-screen w-64 flex flex-col transition-transform duration-300 -translate-x-full lg:translate-x-0"
    style="background-color: #1B2A4A;">

    <!-- Brand -->
    <div class="flex items-center justify-between h-20 px-5" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
        <a href="dashboard.php" class="no-underline m-auto">
            <img src="../assets/images/ChronoNest rec.png" alt="ChronoNest Logo" class="w-[150px]">
        </a>
        <button onclick="closeSidebar()" class="lg:hidden"
            style="background: none; border: none; cursor: pointer; color: #B0B8C9;">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Nav -->
    <nav class="flex-1 overflow-y-auto px-3 py-4">
        <a href="dashboard.php"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg mb-1 text-sm font-medium no-underline transition-all duration-200 <?php echo ($current_page !== 'dashboard.php') ? 'hover:opacity-80' : ''; ?>"
            style="<?php echo ($current_page === 'dashboard.php') ? 'background-color: rgba(201,168,76,0.15); color: #C9A84C;' : 'color: #B0B8C9;'; ?>">
            Dashboard
        </a>

        <a href="products.php"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg mb-1 text-sm font-medium no-underline transition-all duration-200 <?php echo ($current_page !== 'products.php') ? 'hover:opacity-80' : ''; ?>"
            style="<?php echo ($current_page === 'products.php') ? 'background-color: rgba(201,168,76,0.15); color: #C9A84C;' : 'color: #B0B8C9;'; ?>">
            Products
        </a>

        <a href="brands.php"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg mb-1 text-sm font-medium no-underline transition-all duration-200 <?php echo ($current_page !== 'brands.php') ? 'hover:opacity-80' : ''; ?>"
            style="<?php echo ($current_page === 'brands.php') ? 'background-color: rgba(201,168,76,0.15); color: #C9A84C;' : 'color: #B0B8C9;'; ?>">
            Brands
        </a>

        <a href="orders.php"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg mb-1 text-sm font-medium no-underline transition-all duration-200 <?php echo ($current_page !== 'orders.php') ? 'hover:opacity-80' : ''; ?>"
            style="<?php echo ($current_page === 'orders.php') ? 'background-color: rgba(201,168,76,0.15); color: #C9A84C;' : 'color: #B0B8C9;'; ?>">
            Orders
        </a>

        <a href="customers.php"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg mb-1 text-sm font-medium no-underline transition-all duration-200 <?php echo ($current_page !== 'customers.php') ? 'hover:opacity-80' : ''; ?>"
            style="<?php echo ($current_page === 'customers.php') ? 'background-color: rgba(201,168,76,0.15); color: #C9A84C;' : 'color: #B0B8C9;'; ?>">
            Customers
        </a>

        <!-- <a href="message.php"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg mb-1 text-sm font-medium no-underline transition-all duration-200 <?php echo ($current_page !== 'message.php') ? 'hover:opacity-80' : ''; ?>"
            style="<?php echo ($current_page === 'message.php') ? 'background-color: rgba(201,168,76,0.15); color: #C9A84C;' : 'color: #B0B8C9;'; ?>">
            Messages
        </a> -->

    </nav>

    <!-- Logout -->
    <div class="px-3 py-4" style="border-top: 1px solid rgba(255,255,255,0.1);">
        <a href="logout.php"
            class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium no-underline transition-all duration-200 hover:opacity-80"
            style="color: #B0B8C9;">
            Logout
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                <polyline points="16 17 21 12 16 7" />
                <line x1="21" y1="12" x2="9" y2="12" />
            </svg>
        </a>
    </div>

</aside>