<?php
session_start();
require_once 'esewa_config.php';   // $conn + constants
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed – ChronoNest</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen flex items-center justify-center bg-[#fff5f5] font-['Segoe_UI',Arial,sans-serif]">
    <div class="bg-white rounded-[14px] px-8 py-10 max-w-[480px] w-full mx-5 text-center shadow-[0_4px_20px_rgba(0,0,0,0.10)]">
        <div class="w-[70px] h-[70px] bg-[#e53e3e] rounded-full flex items-center justify-center mx-auto mb-5">
            <svg class="w-9 h-9 stroke-white fill-none stroke-[3]" viewBox="0 0 24 24">
                <line x1="18" y1="6" x2="6" y2="18" />
                <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
        </div>
        <h1 class="text-[#c53030] text-[26px] mb-2 font-bold">
            Payment Failed
        </h1>
        <p class="text-[#555] text-[15px] mb-7 leading-[1.6]">
            Your payment was not completed.<br>
            This could be because you cancelled, entered wrong credentials,
            or there was a temporary issue with eSewa.
        </p>
        <div class="bg-[#fff5f5] border border-[#fed7d7] rounded-[10px] p-4 mb-6 text-sm text-[#742a2a]">
            Your order is saved with <strong>pending</strong> status.
            Find it in My Orders.
        </div>
        <a href="../../account/orders.php" class="inline-block px-7 py-[13px] bg-[#1B2A4A] hover:bg-[#15213b] text-white no-underline rounded-lg text-[15px] font-semibold m-1 transition-colors duration-200">
            My Orders
        </a>
        <a href="../../../index.php" class="inline-block px-7 py-[13px] bg-transparent hover:bg-[#1B2A4A] border-2 border-[#1B2A4A] text-[#1B2A4A] hover:text-white no-underline rounded-lg text-[15px] font-semibold m-1 transition-colors duration-200">
            Go to Homepage
        </a>
    </div>
</body>
</html>