<?php
session_start();
$page_title = "About Us – ChronoNest";
require_once '../includes/header.php';
?>

<!-- PAGE HEADER -->
<section class="bg-[#1B2A4A] py-14">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="font-['Inter'] font-semibold text-[11px] uppercase tracking-[1.5px] text-[#C9A84C]">Our Story</span>
        <h1 class="font-['Playfair_Display'] font-bold text-[36px] md:text-[44px] text-white mt-2">About ChronoNest</h1>
        <p class="font-['Inter'] text-[15px] text-[#B0B8C9] mt-3 max-w-lg mx-auto">Nepal's dedicated online destination for premium multi-brand watches.</p>
    </div>
</section>

<!-- BREADCRUMB -->
<div class="bg-white border-b border-[#E0E2E7]">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-3">
        <nav class="flex items-center gap-2 font-['Inter'] text-[13px] text-[#5A5F6D]">
            <a href="../index.php" class="hover:text-[#C9A84C] transition-colors">Home</a>
            <svg class="w-3 h-3 text-[#8A8F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-[#1A1A2E] font-medium">About</span>
        </nav>
    </div>
</div>

<!-- MISSION -->
<section class="py-[60px] bg-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div>
                <span class="font-['Inter'] font-semibold text-[11px] uppercase tracking-[1.5px] text-[#C9A84C]">Our Mission</span>
                <h2 class="font-['Playfair_Display'] font-bold text-[28px] md:text-[36px] text-[#1A1A2E] mt-2 mb-5">Making Fine Watches Accessible Across Nepal</h2>
                <p class="font-['Inter'] text-[15px] leading-[1.8] text-[#5A5F6D] mb-5">ChronoNest was founded with a singular vision — to bring the world's finest watches to every corner of Nepal. We recognized that watch enthusiasts in remote districts, hilly regions, and smaller cities had no easy way to access quality timepieces from reputable international and local brands.</p>
                <p class="font-['Inter'] text-[15px] leading-[1.8] text-[#5A5F6D] mb-8">Our platform bridges that gap. Whether you're in Kathmandu, Pokhara, Biratnagar, or a rural village — ChronoNest delivers authentic watches to your doorstep with complete specifications, high-quality images, and transparent pricing.</p>
                <div class="grid grid-cols-3 gap-4">
                    <div class="text-center p-4 bg-[#F7F8FA] rounded-xl border border-[#E0E2E7]">
                        <div class="font-['Playfair_Display'] font-bold text-[28px] text-[#C9A84C]">50+</div>
                        <div class="font-['Inter'] text-[12px] text-[#5A5F6D] mt-1">Brands</div>
                    </div>
                    <div class="text-center p-4 bg-[#F7F8FA] rounded-xl border border-[#E0E2E7]">
                        <div class="font-['Playfair_Display'] font-bold text-[28px] text-[#C9A84C]">500+</div>
                        <div class="font-['Inter'] text-[12px] text-[#5A5F6D] mt-1">Products</div>
                    </div>
                    <div class="text-center p-4 bg-[#F7F8FA] rounded-xl border border-[#E0E2E7]">
                        <div class="font-['Playfair_Display'] font-bold text-[28px] text-[#C9A84C]">77</div>
                        <div class="font-['Inter'] text-[12px] text-[#5A5F6D] mt-1">Districts</div>
                    </div>
                </div>
            </div>
            <div class="relative">
                <div class="rounded-[28px] border border-[#E0E2E7] bg-white p-2 shadow-[0_20px_60px_rgba(15,23,42,0.12)]">
                    <div class="relative overflow-hidden rounded-[24px]">
                        <img src="../assets/images/About_watch.png"
                            alt="Premium ChronoNest watches"
                            class="h-[420px] w-full object-cover"
                            loading="lazy"
                            decoding="async">
                        <div class="absolute inset-0 bg-gradient-to-t from-[#07111f] via-[#07111f]/35 to-transparent"></div>
                        <div class="absolute inset-x-0 bottom-0 p-6 md:p-8">
                            <span class="inline-flex rounded-full border border-white/20 bg-white/10 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.3em] text-[#F6E7B6] backdrop-blur-sm">
                                ChronoNest Signature
                            </span>
                            <h3 class="mt-3 font-['Playfair_Display'] text-[24px] md:text-[28px] font-semibold text-white leading-tight">
                                Wear timeless craftsmanship
                            </h3>
                            <p class="mt-2 max-w-md font-['Inter'] text-[14px] leading-6 text-white/80">
                                Discover watches shaped by precision, elegance, and a presence that feels effortlessly elevated.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CORE VALUES -->
<section class="py-[60px] bg-[#F7F8FA]">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <span class="font-['Inter'] font-semibold text-[11px] uppercase tracking-[1.5px] text-[#C9A84C]">What We Stand For</span>
            <h2 class="font-['Playfair_Display'] font-bold text-[28px] md:text-[32px] text-[#1A1A2E] mt-1">Our Core Values</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="bg-white p-6 rounded-xl border border-[#E0E2E7] shadow-[0_2px_12px_rgba(0,0,0,0.08)] text-center hover:shadow-[0_8px_28px_rgba(0,0,0,0.14)] transition-shadow duration-200">
                <div class="w-12 h-12 rounded-xl bg-[#FFF8E7] border border-[#C9A84C]/20 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <h4 class="font-['Playfair_Display'] font-semibold text-[17px] text-[#1A1A2E] mb-2">Authenticity</h4>
                <p class="font-['Inter'] text-[13px] leading-[1.6] text-[#5A5F6D]">Every watch is 100% genuine — sourced directly from authorized distributors and verified brands.</p>
            </div>
            <div class="bg-white p-6 rounded-xl border border-[#E0E2E7] shadow-[0_2px_12px_rgba(0,0,0,0.08)] text-center hover:shadow-[0_8px_28px_rgba(0,0,0,0.14)] transition-shadow duration-200">
                <div class="w-12 h-12 rounded-xl bg-[#FFF8E7] border border-[#C9A84C]/20 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h4 class="font-['Playfair_Display'] font-semibold text-[17px] text-[#1A1A2E] mb-2">Transparency</h4>
                <p class="font-['Inter'] text-[13px] leading-[1.6] text-[#5A5F6D]">Detailed specs, clear pricing in NPR, and honest stock information — no hidden surprises.</p>
            </div>
            <div class="bg-white p-6 rounded-xl border border-[#E0E2E7] shadow-[0_2px_12px_rgba(0,0,0,0.08)] text-center hover:shadow-[0_8px_28px_rgba(0,0,0,0.14)] transition-shadow duration-200">
                <div class="w-12 h-12 rounded-xl bg-[#FFF8E7] border border-[#C9A84C]/20 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h4 class="font-['Playfair_Display'] font-semibold text-[17px] text-[#1A1A2E] mb-2">Accessibility</h4>
                <p class="font-['Inter'] text-[13px] leading-[1.6] text-[#5A5F6D]">Serving all 77 districts of Nepal — remote or urban, we deliver quality watches to your door.</p>
            </div>
            <div class="bg-white p-6 rounded-xl border border-[#E0E2E7] shadow-[0_2px_12px_rgba(0,0,0,0.08)] text-center hover:shadow-[0_8px_28px_rgba(0,0,0,0.14)] transition-shadow duration-200">
                <div class="w-12 h-12 rounded-xl bg-[#FFF8E7] border border-[#C9A84C]/20 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </div>
                <h4 class="font-['Playfair_Display'] font-semibold text-[17px] text-[#1A1A2E] mb-2">Passion</h4>
                <p class="font-['Inter'] text-[13px] leading-[1.6] text-[#5A5F6D]">We're watch lovers ourselves — curating collections with care, expertise and genuine enthusiasm.</p>
            </div>
        </div>
    </div>
</section>

<!-- WHAT WE OFFER -->
<section class="py-[60px] bg-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <span class="font-['Inter'] font-semibold text-[11px] uppercase tracking-[1.5px] text-[#C9A84C]">Platform Features</span>
            <h2 class="font-['Playfair_Display'] font-bold text-[28px] md:text-[32px] text-[#1A1A2E] mt-1">What ChronoNest Offers</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 max-w-4xl mx-auto">
            <?php
            $features = [
                ['icon' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z', 'title' => 'Advanced Search & Filtering', 'desc' => 'Filter watches by brand, gender, movement type, strap material, dial color, case size, water resistance, and price range.'],
                ['icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z', 'title' => 'High-Quality Product Images', 'desc' => 'Multiple angles, zoom capability, and detailed imagery so you can inspect every aspect of your watch before purchase.'],
                ['icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z', 'title' => 'Wishlist & Cart Management', 'desc' => 'Save watches for later, manage your cart with strap size options, and streamline your purchasing journey.'],
                ['icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'title' => 'Detailed Specifications', 'desc' => 'Case diameter, movement type, water resistance, dial shape, strap info, warranty — everything you need to decide.'],
                ['icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'title' => 'Flexible Payment Options', 'desc' => 'Pay via eSewa digital wallet or choose Cash on Delivery — convenient, secure options for every customer.'],
                ['icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z', 'title' => 'Order Tracking', 'desc' => 'Track your order from placement to delivery — Pending, Confirmed, Processing, Shipped, Delivered.'],
            ];
            foreach ($features as $f):
            ?>
            <div class="flex items-start gap-4 p-5 bg-[#F7F8FA] rounded-xl border border-[#E0E2E7] hover:border-[#C9A84C]/30 transition-colors duration-200">
                <div class="w-10 h-10 rounded-lg bg-[#FFF8E7] border border-[#C9A84C]/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-5 h-5 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $f['icon'] ?>"/></svg>
                </div>
                <div>
                    <h4 class="font-['Inter'] font-semibold text-[15px] text-[#1A1A2E] mb-1"><?= $f['title'] ?></h4>
                    <p class="font-['Inter'] text-[13px] leading-[1.6] text-[#5A5F6D]"><?= $f['desc'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="bg-[#1B2A4A] py-16">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="font-['Playfair_Display'] font-bold text-[28px] md:text-[36px] text-white mb-4">Start Your Watch Journey Today</h2>
        <p class="font-['Inter'] text-[15px] text-[#B0B8C9] mb-8 max-w-lg mx-auto">Join thousands of satisfied customers across Nepal who trust ChronoNest for their timepiece needs.</p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="../pages/shop.php" class="inline-flex items-center gap-2 h-[48px] px-8 bg-[#C9A84C] hover:bg-[#B8953F] text-white font-['Inter'] font-semibold text-[15px] rounded-lg transition-all duration-200 hover:shadow-lg hover:-translate-y-0.5">Browse Watches</a>
            <a href="../pages/contact.php" class="inline-flex items-center gap-2 h-[48px] px-8 bg-transparent hover:bg-white/10 text-white font-['Inter'] font-semibold text-[15px] rounded-lg border border-white/30 transition-all duration-200">Contact Us</a>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>