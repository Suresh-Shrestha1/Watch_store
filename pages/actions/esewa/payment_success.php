<?php
session_start();
require_once 'esewa_config.php';   // $conn + constants + signature fn

// Make sure eSewa actually sent data
if (empty($_GET['data'])) {
    die("No payment data received from eSewa.");
}

// Base64-decode the JSON payload
$raw_data = $_GET['data'];
$decoded  = base64_decode($raw_data, true);   // strict = true

if ($decoded === false || $decoded === '') {
    die("Could not decode eSewa response. Invalid base64 data.");
}

$esewa_data = json_decode($decoded, true);

if (!is_array($esewa_data)) {
    die("Could not parse eSewa response. Invalid JSON payload.");
}

// Extract fields
$transaction_code  = $esewa_data['transaction_code']   ?? '';
$status            = $esewa_data['status']             ?? '';
$total_amount_raw  = $esewa_data['total_amount']       ?? '';
$transaction_uuid  = $esewa_data['transaction_uuid']   ?? '';
$product_code      = $esewa_data['product_code']       ?? '';
$received_sig      = $esewa_data['signature']          ?? '';
$signed_fields_str = $esewa_data['signed_field_names'] ?? '';

// Basic presence check
if (!$transaction_uuid || !$received_sig || !$signed_fields_str) {
    die("Incomplete eSewa response. Required fields are missing.");
}

// Verify signature (prove data wasn't tampered with)
// Rebuild the exact message eSewa signed, using signed_field_names order
$signed_fields = explode(',', $signed_fields_str);
$message_parts = [];

foreach ($signed_fields as $field) {
    $field = trim($field);
    // Skip 'signature' itself – it's not part of the message
    if ($field !== 'signature' && array_key_exists($field, $esewa_data)) {
        $message_parts[] = "{$field}={$esewa_data[$field]}";
    }
}

$message      = implode(',', $message_parts);
$expected_sig = base64_encode(
    hash_hmac('sha256', $message, ESEWA_SECRET_KEY, true)
);

// Constant-time comparison prevents timing attacks
if (!hash_equals($expected_sig, $received_sig)) {
    error_log("eSewa signature mismatch. Expected: $expected_sig | Got: $received_sig | Message: $message");
    die("Signature verification failed. This payment cannot be confirmed.");
}

// Confirm status is COMPLETE
if (strtoupper($status) !== 'COMPLETE') {
    die("Payment not completed. eSewa status: " . htmlspecialchars($status));
}

// Double-verify with eSewa's server (anti-replay)
// Normalize amount format for the API call
$total_amount_for_api = number_format((float)$total_amount_raw, 2, '.', '');

$verify_url = ESEWA_VERIFY_URL
    . "?product_code="     . urlencode(ESEWA_PRODUCT_CODE)
    . "&transaction_uuid=" . urlencode($transaction_uuid)
    . "&total_amount="     . urlencode($total_amount_for_api);

$ch = curl_init($verify_url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 20,
    CURLOPT_SSL_VERIFYPEER => false,   // ← set to TRUE in production
    CURLOPT_HTTPHEADER     => ['Accept: application/json'],
]);
$api_response = curl_exec($ch);
$curl_error   = curl_error($ch);
$http_code    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($curl_error) {
    error_log("eSewa verify cURL error: $curl_error");
    die("Could not reach eSewa verification server. Please contact support.");
}

$api_data = json_decode($api_response, true);

// Log for debugging (remove in production)
error_log("eSewa verify HTTP $http_code | Response: $api_response");

if (!is_array($api_data) || strtoupper($api_data['status'] ?? '') !== 'COMPLETE') {
    $bad_status = htmlspecialchars($api_data['status'] ?? 'unknown');
    die("eSewa payment verification failed. Server status: $bad_status");
}

// Find our order from the transaction_uuid
$order_number = preg_replace('/-\d+$/', '', $transaction_uuid);

if (!$order_number) {
    die("Could not extract order number from transaction UUID.");
}

$stmt = $conn->prepare(
    "SELECT id, order_number, customer_name, grand_total, payment_status
     FROM orders
     WHERE order_number = ?
       AND payment_method = 'eSewa'"
);
$stmt->bind_param("s", $order_number);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    error_log("eSewa success: order not found for order_number=$order_number uuid=$transaction_uuid");
    die("Order not found in our system. Order number: "
        . htmlspecialchars($order_number));
}

// Update DB (idempotent – safe to run twice)
if ($order['payment_status'] !== 'paid') {
    $update = $conn->prepare(
        "UPDATE orders
         SET payment_status = 'paid',
             transaction_id = ?,
             order_status   = 'confirmed',
             confirmed_at   = NOW()
         WHERE order_number = ?
           AND payment_status = 'pending'"   // extra guard
    );
    $update->bind_param("ss", $transaction_code, $order_number);
    $update->execute();

    if ($update->affected_rows === 0) {
        // Could be a duplicate callback – not an error, just log it
        error_log("eSewa success: UPDATE affected 0 rows for $order_number (already paid?)");
    }
    $update->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful – ChronoNest</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-[#f0fbe8] font-['Segoe_UI',Arial,sans-serif]">
    <div class="bg-white rounded-[14px] px-8 py-10 max-w-[500px] w-full mx-5 text-center shadow-[0_4px_20px_rgba(0,0,0,0.10)]">
        <div class="w-[70px] h-[70px] bg-[#60BB46] rounded-full flex items-center justify-center mx-auto mb-5">
            <svg class="w-9 h-9 stroke-white fill-none stroke-[3]" viewBox="0 0 24 24">
                <polyline points="20 6 9 17 4 12" />
            </svg>
        </div>

        <h1 class="text-[#2d7d1a] text-[26px] mb-2 font-bold">Payment Successful!</h1>

        <p class="text-[#555] text-[15px] mb-6">
            Your payment was verified and your order is confirmed.
            <span class="inline-block bg-[#60BB46] text-white px-2.5 py-[3px] rounded-full text-xs font-bold ml-1.5">eSewa</span>
        </p>

        <div class="bg-[#f7f8fa] rounded-[10px] p-[18px] text-left mb-6">
            <div class="flex justify-between py-1.5 text-sm border-b border-[#eee]">
                <span class="text-[#666]">Order Number</span>
                <span class="font-semibold text-[#1B2A4A]"><?= htmlspecialchars($order['order_number']) ?></span>
            </div>

            <div class="flex justify-between py-1.5 text-sm border-b border-[#eee]">
                <span class="text-[#666]">eSewa Transaction ID</span>
                <span class="font-semibold text-[#1B2A4A]"><?= htmlspecialchars($transaction_code) ?></span>
            </div>

            <div class="flex justify-between py-1.5 text-sm border-b border-[#eee]">
                <span class="text-[#666]">Amount Paid</span>
                <span class="font-semibold text-[#1B2A4A]">NPR <?= number_format((float)$order['grand_total'], 0) ?></span>
            </div>

            <div class="flex justify-between py-1.5 text-sm border-b border-[#eee]">
                <span class="text-[#666]">Customer</span>
                <span class="font-semibold text-[#1B2A4A]"><?= htmlspecialchars($order['customer_name']) ?></span>
            </div>

            <div class="flex justify-between py-1.5 text-sm">
                <span class="text-[#666]">Payment Status</span>
                <span class="font-semibold text-[#2d7d1a]">✔ Paid</span>
            </div>
        </div>

        <a href="../../account/orders.php" class="inline-block px-7 py-[13px] bg-[#1B2A4A] hover:bg-[#15213b] text-white no-underline rounded-lg text-[15px] font-semibold m-1 transition-colors duration-200">View My Orders</a>
        <a href="../../../index.php" class="inline-block px-7 py-[13px] bg-transparent hover:bg-[#1B2A4A] border-2 border-[#1B2A4A] text-[#1B2A4A] hover:text-white no-underline rounded-lg text-[15px] font-semibold m-1 transition-colors duration-200">Continue Shopping</a>
    </div>
</body>
</html>
