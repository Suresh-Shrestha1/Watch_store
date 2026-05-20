<!-- Top Bar -->
<header class="fixed top-0 right-0 left-0 lg:left-64 z-30 h-20 flex items-center justify-between px-4 lg:px-6 bg-white" style="border-bottom: 1px solid #E0E2E7;">

    <!-- Left -->
    <div class="flex items-center gap-3">
        <!-- Hamburger — mobile only -->
        <button onclick="openSidebar()" class="lg:hidden" style="background: none; border: none; cursor: pointer; color: #1A1A2E;">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <h1 class="font-bold text-2xl" style="font-family: 'Playfair Display', serif; color: #1A1A2E;">
            <?php echo 'Admin Panel'; ?>
        </h1>
    </div>

    <!-- Right -->
    <div class="flex items-center gap-3">
        <div class="hidden sm:block text-right">
            <p class="text-sm font-semibold leading-tight" style="color: #1A1A2E;"><?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?></p>
            <p class="text-xs" style="color: #5A5F6D;">Administrator</p>
        </div>
        <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold text-white" style="background-color: #1B2A4A;">
            <?php echo strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 1)); ?>
        </div>
    </div>

</header>