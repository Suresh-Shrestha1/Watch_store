<?php
session_start();
$page_title = "Contact Us – ChronoNest";
require_once '../includes/header.php';
?>

<!-- PAGE HEADER -->
<section class="bg-[#1B2A4A] py-14">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="font-['Inter'] font-semibold text-[11px] uppercase tracking-[1.5px] text-[#C9A84C]">Get In Touch</span>
        <h1 class="font-['Playfair_Display'] font-bold text-[36px] md:text-[44px] text-white mt-2">Contact Us</h1>
        <p class="font-['Inter'] text-[15px] text-[#B0B8C9] mt-3 max-w-lg mx-auto">Have a question about a watch or your order? We're here to help.</p>
    </div>
</section>

<!-- BREADCRUMB -->
<div class="bg-white border-b border-[#E0E2E7]">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-3">
        <nav class="flex items-center gap-2 font-['Inter'] text-[13px] text-[#5A5F6D]">
            <a href="../index.php" class="hover:text-[#C9A84C] transition-colors">Home</a>
            <svg class="w-3 h-3 text-[#8A8F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-[#1A1A2E] font-medium">Contact</span>
        </nav>
    </div>
</div>

<section class="py-[60px] bg-[#F7F8FA]">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- CONTACT INFO -->
            <div class="lg:col-span-1 space-y-5">
                <div class="bg-white rounded-xl border border-[#E0E2E7] shadow-[0_2px_12px_rgba(0,0,0,0.08)] p-6">
                    <h3 class="font-['Playfair_Display'] font-semibold text-[20px] text-[#1A1A2E] mb-5">Our Information</h3>
                    <div class="space-y-5">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-lg bg-[#FFF8E7] border border-[#C9A84C]/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-5 h-5 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <div class="font-['Inter'] font-semibold text-[14px] text-[#1A1A2E] mb-1">Our Address</div>
                                <div class="font-['Inter'] text-[14px] text-[#5A5F6D] leading-[1.6]">New Road, Kathmandu<br>Bagmati Province, Nepal</div>
                            </div>
                        </div>
                        <div class="w-full h-px bg-[#F0F1F3]"></div>
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-lg bg-[#FFF8E7] border border-[#C9A84C]/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-5 h-5 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <div>
                                <div class="font-['Inter'] font-semibold text-[14px] text-[#1A1A2E] mb-1">Phone</div>
                                <a href="tel:+9779800000000" class="font-['Inter'] text-[14px] text-[#5A5F6D] hover:text-[#C9A84C] transition-colors">+977 9800000000</a>
                                <div class="font-['Inter'] text-[12px] text-[#8A8F99] mt-0.5">Sun – Fri, 10am – 6pm</div>
                            </div>
                        </div>
                        <div class="w-full h-px bg-[#F0F1F3]"></div>
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-lg bg-[#FFF8E7] border border-[#C9A84C]/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-5 h-5 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <div class="font-['Inter'] font-semibold text-[14px] text-[#1A1A2E] mb-1">Email</div>
                                <a href="mailto:support@chronoNest.com.np" class="font-['Inter'] text-[14px] text-[#5A5F6D] hover:text-[#C9A84C] transition-colors">support@chronoNest.com.np</a>
                                <div class="font-['Inter'] text-[12px] text-[#8A8F99] mt-0.5">We reply within 24 hours</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Business Hours -->
                <div class="bg-white rounded-xl border border-[#E0E2E7] shadow-[0_2px_12px_rgba(0,0,0,0.08)] p-6">
                    <h3 class="font-['Playfair_Display'] font-semibold text-[18px] text-[#1A1A2E] mb-4">Business Hours</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="font-['Inter'] text-[14px] text-[#5A5F6D]">Sunday – Friday</span>
                            <span class="font-['Inter'] font-semibold text-[14px] text-[#1A1A2E]">10:00 – 18:00</span>
                        </div>
                        <div class="w-full h-px bg-[#F0F1F3]"></div>
                        <div class="flex justify-between items-center">
                            <span class="font-['Inter'] text-[14px] text-[#5A5F6D]">Saturday</span>
                            <span class="font-['Inter'] font-semibold text-[14px] text-[#D64545]">Closed</span>
                        </div>
                        <div class="w-full h-px bg-[#F0F1F3]"></div>
                        <div class="flex justify-between items-center">
                            <span class="font-['Inter'] text-[14px] text-[#5A5F6D]">Public Holidays</span>
                            <span class="font-['Inter'] font-semibold text-[14px] text-[#D64545]">Closed</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="bg-white rounded-xl border border-[#E0E2E7] shadow-[0_2px_12px_rgba(0,0,0,0.08)] p-6">
                    <h3 class="font-['Playfair_Display'] font-semibold text-[18px] text-[#1A1A2E] mb-4">We Accept</h3>
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-2 px-3 py-2 bg-[#F7F8FA] rounded-lg border border-[#E0E2E7]">
                            <div class="w-3 h-3 rounded-full bg-[#60BB46]"></div>
                            <span class="font-['Inter'] font-semibold text-[13px] text-[#1A1A2E]">eSewa</span>
                        </div>
                        <div class="flex items-center gap-2 px-3 py-2 bg-[#F7F8FA] rounded-lg border border-[#E0E2E7]">
                            <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                            <span class="font-['Inter'] font-semibold text-[13px] text-[#1A1A2E]">Cash on Delivery</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CONTACT FORM -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl border border-[#E0E2E7] shadow-[0_2px_12px_rgba(0,0,0,0.08)] p-8">
                    <h2 class="font-['Playfair_Display'] font-bold text-[24px] text-[#1A1A2E] mb-2">Send us a Message</h2>
                    <p class="font-['Inter'] text-[14px] text-[#5A5F6D] mb-7">Fill in the form below and we'll get back to you as soon as possible.</p>

                    <form action="" method="POST" novalidate>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                            <div>
                                <label class="block font-['Inter'] font-semibold text-[14px] text-[#1A1A2E] mb-1.5">Full Name <span class="text-[#D64545]">*</span></label>
                                <input type="text" name="name" placeholder="John Doe" required class="w-full h-[44px] px-4 bg-white border border-[#E0E2E7] rounded-lg font-['Inter'] text-[15px] text-[#1A1A2E] placeholder-[#8A8F99] focus:outline-none focus:border-2 focus:border-[#C9A84C] focus:shadow-[0_0_0_3px_rgba(201,168,76,0.15)] transition-all duration-150">
                            </div>
                            <div>
                                <label class="block font-['Inter'] font-semibold text-[14px] text-[#1A1A2E] mb-1.5">Email Address <span class="text-[#D64545]">*</span></label>
                                <input type="email" name="email" placeholder="you@example.com" required class="w-full h-[44px] px-4 bg-white border border-[#E0E2E7] rounded-lg font-['Inter'] text-[15px] text-[#1A1A2E] placeholder-[#8A8F99] focus:outline-none focus:border-2 focus:border-[#C9A84C] focus:shadow-[0_0_0_3px_rgba(201,168,76,0.15)] transition-all duration-150">
                            </div>
                        </div>

                        <div class="mb-5">
                            <label class="block font-['Inter'] font-semibold text-[14px] text-[#1A1A2E] mb-1.5">Phone Number</label>
                            <input type="tel" name="phone" placeholder="98XXXXXXXX" maxlength="10" class="w-full h-[44px] px-4 bg-white border border-[#E0E2E7] rounded-lg font-['Inter'] text-[15px] text-[#1A1A2E] placeholder-[#8A8F99] focus:outline-none focus:border-2 focus:border-[#C9A84C] focus:shadow-[0_0_0_3px_rgba(201,168,76,0.15)] transition-all duration-150">
                        </div>

                        <div class="mb-5">
                            <label class="block font-['Inter'] font-semibold text-[14px] text-[#1A1A2E] mb-1.5">Subject <span class="text-[#D64545]">*</span></label>
                            <select name="subject" required class="w-full h-[44px] px-4 bg-white border border-[#E0E2E7] rounded-lg font-['Inter'] text-[15px] text-[#1A1A2E] focus:outline-none focus:border-2 focus:border-[#C9A84C] focus:shadow-[0_0_0_3px_rgba(201,168,76,0.15)] transition-all duration-150 appearance-none cursor-pointer">
                                <option value="" class="text-[#8A8F99]">Select a subject</option>
                                <option value="order">Order Inquiry</option>
                                <option value="product">Product Question</option>
                                <option value="delivery">Delivery Support</option>
                                <option value="payment">Payment Issue</option>
                                <option value="warranty">Warranty Claim</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="mb-6">
                            <label class="block font-['Inter'] font-semibold text-[14px] text-[#1A1A2E] mb-1.5">Message <span class="text-[#D64545]">*</span></label>
                            <textarea name="message" rows="6" placeholder="Write your message here..." required class="w-full px-4 py-3 bg-white border border-[#E0E2E7] rounded-lg font-['Inter'] text-[15px] text-[#1A1A2E] placeholder-[#8A8F99] focus:outline-none focus:border-2 focus:border-[#C9A84C] focus:shadow-[0_0_0_3px_rgba(201,168,76,0.15)] transition-all duration-150 resize-none leading-[1.6]"></textarea>
                            <p class="font-['Inter'] text-[12px] text-[#8A8F99] mt-1">Please provide as much detail as possible.</p>
                        </div>

                        <button type="submit" class="inline-flex items-center gap-2 h-[44px] px-8 bg-[#C9A84C] hover:bg-[#B8953F] text-white font-['Inter'] font-semibold text-[15px] rounded-lg transition-all duration-200 hover:shadow-md hover:-translate-y-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            Send Message
                        </button>
                    </form>
                </div>

                <!-- MAP PLACEHOLDER -->
                <div class="mt-6 bg-white rounded-xl border border-[#E0E2E7] shadow-[0_2px_12px_rgba(0,0,0,0.08)] overflow-hidden">
                    <div class="bg-[#F7F8FA] border-b border-[#E0E2E7] px-6 py-4 flex items-center gap-3">
                        <svg class="w-5 h-5 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="font-['Inter'] font-semibold text-[14px] text-[#1A1A2E]">Find Us on Map</span>
                    </div>
                    <div class="h-[260px] bg-gradient-to-br from-[#E3F2FD] to-[#F7F8FA] flex flex-col items-center justify-center gap-3">
                        <svg class="w-12 h-12 text-[#1B2A4A]/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                        <span class="font-['Inter'] text-[14px] text-[#8A8F99]">New Road, Kathmandu, Nepal</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- QUICK HELP -->
<section class="py-[60px] bg-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <h2 class="font-['Playfair_Display'] font-bold text-[28px] text-[#1A1A2E]">Quick Help</h2>
            <p class="font-['Inter'] text-[15px] text-[#5A5F6D] mt-2">Common questions and answers</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-3xl mx-auto">
            <div class="p-5 bg-[#F7F8FA] rounded-xl border border-[#E0E2E7] hover:border-[#C9A84C]/40 transition-colors duration-200">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-[#FFF8E7] flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <div class="font-['Inter'] font-semibold text-[14px] text-[#1A1A2E] mb-1">How long does delivery take?</div>
                        <div class="font-['Inter'] text-[13px] text-[#5A5F6D] leading-[1.5]">Kathmandu: 1–2 days. Other cities: 3–7 days depending on location.</div>
                    </div>
                </div>
            </div>
            <div class="p-5 bg-[#F7F8FA] rounded-xl border border-[#E0E2E7] hover:border-[#C9A84C]/40 transition-colors duration-200">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-[#FFF8E7] flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <div class="font-['Inter'] font-semibold text-[14px] text-[#1A1A2E] mb-1">Are all watches genuine?</div>
                        <div class="font-['Inter'] text-[13px] text-[#5A5F6D] leading-[1.5]">Yes, 100% authentic products directly sourced from authorized distributors.</div>
                    </div>
                </div>
            </div>
            <div class="p-5 bg-[#F7F8FA] rounded-xl border border-[#E0E2E7] hover:border-[#C9A84C]/40 transition-colors duration-200">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-[#FFF8E7] flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <div class="font-['Inter'] font-semibold text-[14px] text-[#1A1A2E] mb-1">What warranty do watches come with?</div>
                        <div class="font-['Inter'] text-[13px] text-[#5A5F6D] leading-[1.5]">Warranty varies by brand and model — typically 2 to 5 years. Shown on each product page.</div>
                    </div>
                </div>
            </div>
            <div class="p-5 bg-[#F7F8FA] rounded-xl border border-[#E0E2E7] hover:border-[#C9A84C]/40 transition-colors duration-200">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-[#FFF8E7] flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <div>
                        <div class="font-['Inter'] font-semibold text-[14px] text-[#1A1A2E] mb-1">What payment methods are accepted?</div>
                        <div class="font-['Inter'] text-[13px] text-[#5A5F6D] leading-[1.5]">We accept eSewa digital wallet and Cash on Delivery (COD) across Nepal.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>