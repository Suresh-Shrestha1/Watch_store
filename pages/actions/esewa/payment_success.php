<?php
// ============================================================
//  payment_success.php  –  eSewa calls this after payment
// ============================================================
session_start();
require_once 'esewa_config.php';   // $conn + constants + signature fn

// ── STEP 1: Make sure eSewa actually sent data ────────────────
if (empty($_GET['data'])) {
    die("No payment data received from eSewa.");
}

// ── STEP 2: Base64-decode the JSON payload ───────────────────
$raw_data = $_GET['data'];
$decoded  = base64_decode($raw_data, true);   // strict = true

if ($decoded === false || $decoded === '') {
    die("Could not decode eSewa response. Invalid base64 data.");
}

$esewa_data = json_decode($decoded, true);

if (!is_array($esewa_data)) {
    die("Could not parse eSewa response. Invalid JSON payload.");
}

/*
  Expected $esewa_data keys:
    transaction_code   – eSewa's own receipt number (e.g. "0007HBE")
    status             – should be "COMPLETE"
    total_amount       – e.g. "1500.0"  (note: may lack trailing zero)
    transaction_uuid   – what we sent (e.g. "CN-20250604-XXXX-1717488000")
    product_code       – e.g. "EPAYTEST"
    signed_field_names – comma-separated list of signed keys
    signature          – base64 HMAC we must verify
*/

// ── STEP 3: Extract fields ────────────────────────────────────
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

// ── STEP 4: Verify signature (prove data wasn't tampered with) ─
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

// ── STEP 5: Confirm status is COMPLETE ───────────────────────
if (strtoupper($status) !== 'COMPLETE') {
    die("Payment not completed. eSewa status: " . htmlspecialchars($status));
}

// ── STEP 6: Double-verify with eSewa's server (anti-replay) ──
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

// ── STEP 7: Find our order from the transaction_uuid ─────────
// We built uuid as: "<order_number>-<timestamp>"
// Strip "-<digits>" suffix to recover the order_number
// Example: "CN-20250604-A1B2-1717488000" → "CN-20250604-A1B2"
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

// ── STEP 8: Update DB (idempotent – safe to run twice) ───────
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
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif;
               background: #f0fbe8; min-height: 100vh;
               display: flex; align-items: center; justify-content: center; }
        .card { background: #fff; border-radius: 14px; padding: 40px 32px;
                max-width: 500px; width: 100%; margin: 20px;
                text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,.10); }
        .checkmark { width: 70px; height: 70px; background: #60BB46;
                     border-radius: 50%; display: flex;
                     align-items: center; justify-content: center;
                     margin: 0 auto 20px; }
        .checkmark svg { width: 36px; height: 36px; stroke: #fff;
                         fill: none; stroke-width: 3; }
        h1 { color: #2d7d1a; font-size: 26px; margin-bottom: 8px; }
        .subtitle { color: #555; font-size: 15px; margin-bottom: 24px; }
        .detail-box { background: #f7f8fa; border-radius: 10px;
                      padding: 18px; text-align: left; margin-bottom: 24px; }
        .detail-row { display: flex; justify-content: space-between;
                      padding: 6px 0; font-size: 14px;
                      border-bottom: 1px solid #eee; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: #666; }
        .detail-value { font-weight: 600; color: #1B2A4A; }
        .btn { display: inline-block; padding: 13px 28px;
               background: #1B2A4A; color: #fff; text-decoration: none;
               border-radius: 8px; font-size: 15px; font-weight: 600;
               margin: 6px; }
        .btn-outline { background: transparent; border: 2px solid #1B2A4A;
                       color: #1B2A4A; }
        .esewa-badge { display: inline-block; background: #60BB46;
                       color: #fff; padding: 3px 10px; border-radius: 20px;
                       font-size: 12px; font-weight: 700; margin-left: 6px; }
    </style>
</head>
<body>
<div class="card">
    <div class="checkmark">
        <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
    </div>

    <h1>Payment Successful!</h1>
    <p class="subtitle">
        Your payment was verified and your order is confirmed.
        <span class="esewa-badge">eSewa</span>
    </p>

    <div class="detail-box">
        <div class="detail-row">
            <span class="detail-label">Order Number</span>
            <span class="detail-value">
                <?= htmlspecialchars($order['order_number']) ?>
            </span>
        </div>
        <div class="detail-row">
            <span class="detail-label">eSewa Transaction ID</span>
            <span class="detail-value">
                <?= htmlspecialchars($transaction_code) ?>
            </span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Amount Paid</span>
            <span class="detail-value">
                NPR <?= number_format((float)$order['grand_total'], 0) ?>
            </span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Customer</span>
            <span class="detail-value">
                <?= htmlspecialchars($order['customer_name']) ?>
            </span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Payment Status</span>
            <span class="detail-value" style="color:#2d7d1a">✔ Paid</span>
        </div>
    </div>

    <a href="../../account/orders.php" class="btn">View My Orders</a>
    <a href="../../../index.php"        class="btn btn-outline">Continue Shopping</a>
</div>
</body>
</html>