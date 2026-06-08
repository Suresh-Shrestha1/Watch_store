<?php
// ============================================================
//  test_esewa.php  –  Test all eSewa settings before paying
//  Visit: http://localhost/watch_store/pages/actions/esewa/test_esewa.php
//  DELETE THIS FILE in production!
// ============================================================
require_once 'esewa_config.php';

echo "<h2>eSewa Configuration Test</h2>";
echo "<style>
    body { font-family: monospace; padding: 20px; }
    .ok  { color: green; font-weight: bold; }
    .err { color: red;   font-weight: bold; }
    .box { background: #f5f5f5; padding: 12px; border-radius: 6px;
           margin: 10px 0; border-left: 4px solid #ccc; }
    .ok-box  { border-left-color: green; }
    .err-box { border-left-color: red;   }
    table { border-collapse: collapse; width: 100%; }
    td, th { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background: #f0f0f0; }
</style>";

// ── Test 1: Constants defined ─────────────────────────────────
echo "<div class='box'><h3>Test 1: Constants</h3>";
$constants = [
    'ESEWA_MERCHANT_ID',
    'ESEWA_PRODUCT_CODE',
    'ESEWA_SECRET_KEY',
    'ESEWA_PAYMENT_URL',
    'ESEWA_VERIFY_URL',
    'SUCCESS_URL',
    'FAILURE_URL',
];

foreach ($constants as $c) {
    if (defined($c)) {
        echo "<span class='ok'>✅ $c</span> = <code>" . constant($c) . "</code><br>";
    } else {
        echo "<span class='err'>❌ $c NOT DEFINED</span><br>";
    }
}
echo "</div>";

// ── Test 2: DB connection ─────────────────────────────────────
echo "<div class='box'><h3>Test 2: Database Connection</h3>";
if (isset($conn) && $conn instanceof mysqli) {
    echo "<span class='ok'>✅ \$conn exists and is mysqli</span><br>";

    // Check orders table
    $result = $conn->query("SHOW TABLES LIKE 'orders'");
    if ($result && $result->num_rows > 0) {
        echo "<span class='ok'>✅ 'orders' table exists</span><br>";
    } else {
        echo "<span class='err'>❌ 'orders' table NOT FOUND</span><br>";
    }

    // Show pending eSewa orders
    $pending = $conn->query(
        "SELECT id, order_number, grand_total, payment_status, ordered_at
         FROM orders
         WHERE payment_method = 'eSewa'
         ORDER BY id DESC LIMIT 5"
    );
    if ($pending && $pending->num_rows > 0) {
        echo "<br><strong>Recent eSewa orders:</strong><br>";
        echo "<table><tr>
                <th>ID</th>
                <th>Order Number</th>
                <th>Grand Total</th>
                <th>Status</th>
                <th>Date</th>
              </tr>";
        while ($row = $pending->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['order_number']}</td>
                    <td>NPR {$row['grand_total']}</td>
                    <td>{$row['payment_status']}</td>
                    <td>{$row['ordered_at']}</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<span class='err'>⚠️ No eSewa orders found in DB</span><br>";
    }
} else {
    echo "<span class='err'>❌ \$conn NOT available</span><br>";
}
echo "</div>";

// ── Test 3: Signature generation ─────────────────────────────
echo "<div class='box'><h3>Test 3: Signature Function</h3>";
if (function_exists('generate_esewa_signature')) {
    echo "<span class='ok'>✅ generate_esewa_signature() exists</span><br>";

    // Test with known values from eSewa docs
    $test_amount = "1000.00";
    $test_uuid   = "TEST-UUID-123";
    $test_sig    = generate_esewa_signature($test_amount, $test_uuid);

    $expected_msg = "total_amount={$test_amount},transaction_uuid={$test_uuid},product_code=" . ESEWA_PRODUCT_CODE;
    $expected_sig = base64_encode(hash_hmac('sha256', $expected_msg, ESEWA_SECRET_KEY, true));

    echo "Test amount: <code>$test_amount</code><br>";
    echo "Test UUID: <code>$test_uuid</code><br>";
    echo "Message signed: <code>$expected_msg</code><br>";
    echo "Generated sig: <code>$test_sig</code><br>";
    echo "Expected sig:  <code>$expected_sig</code><br>";

    if ($test_sig === $expected_sig) {
        echo "<span class='ok'>✅ Signature matches!</span><br>";
    } else {
        echo "<span class='err'>❌ Signature MISMATCH</span><br>";
    }
} else {
    echo "<span class='err'>❌ generate_esewa_signature() NOT FOUND</span><br>";
}
echo "</div>";

// ── Test 4: cURL to eSewa verify endpoint ─────────────────────
echo "<div class='box'><h3>Test 4: eSewa API Connectivity</h3>";
$test_url = ESEWA_VERIFY_URL
          . "?product_code=" . urlencode(ESEWA_PRODUCT_CODE)
          . "&transaction_uuid=TEST-123"
          . "&total_amount=100.00";

echo "Testing URL: <code>$test_url</code><br><br>";

$ch = curl_init($test_url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 10,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_HTTPHEADER     => ['Accept: application/json'],
]);
$response  = curl_exec($ch);
$err       = curl_error($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($err) {
    echo "<span class='err'>❌ cURL Error: $err</span><br>";
    echo "<strong>This means your server CANNOT reach eSewa!</strong><br>";
    echo "Possible fixes:<br>";
    echo "- Check internet connectivity on your server<br>";
    echo "- Check firewall settings<br>";
    echo "- Make sure cURL is enabled in PHP<br>";
} else {
    echo "<span class='ok'>✅ Connected to eSewa API</span><br>";
    echo "HTTP Status: <code>$http_code</code><br>";
    echo "Response: <code>" . htmlspecialchars($response) . "</code><br>";
}
echo "</div>";

// ── Test 5: PHP version & extensions ─────────────────────────
echo "<div class='box'><h3>Test 5: PHP Environment</h3>";
echo "PHP Version: <code>" . PHP_VERSION . "</code><br>";

$exts = ['curl', 'json', 'openssl', 'hash', 'mysqli'];
foreach ($exts as $ext) {
    if (extension_loaded($ext)) {
        echo "<span class='ok'>✅ $ext extension loaded</span><br>";
    } else {
        echo "<span class='err'>❌ $ext extension MISSING</span><br>";
    }
}
echo "</div>";

// ── Test 6: URLs reachable from browser ───────────────────────
echo "<div class='box'><h3>Test 6: Your URLs</h3>";
echo "Success URL: <code>" . SUCCESS_URL . "</code><br>";
echo "Failure URL: <code>" . FAILURE_URL . "</code><br>";
echo "<br>";
echo "<strong>⚠️ Important:</strong> eSewa (even test) needs to reach your success/failure URLs.<br>";
echo "If you're on <code>localhost</code>, eSewa's servers CANNOT call back to localhost.<br>";
echo "Solutions:<br>";
echo "1. Use <a href='https://ngrok.com' target='_blank'>ngrok</a> to expose localhost<br>";
echo "2. Deploy to a real server with a public URL<br>";
echo "3. Use eSewa's test in their sandbox environment<br>";
echo "</div>";

// ── Test 7: Simulate a payment success response ───────────────
echo "<div class='box'><h3>Test 7: Simulate eSewa Success Response</h3>";

// Get the latest pending eSewa order
$latest_stmt = $conn->prepare(
    "SELECT id, order_number, grand_total FROM orders
     WHERE payment_method = 'eSewa' AND payment_status = 'pending'
     ORDER BY id DESC LIMIT 1"
);
$latest_stmt->execute();
$latest_order = $latest_stmt->get_result()->fetch_assoc();
$latest_stmt->close();

if ($latest_order) {
    $sim_uuid       = $latest_order['order_number'] . '-' . time();
    $sim_amount     = number_format((float)$latest_order['grand_total'], 2, '.', '');

    // Build what eSewa would send back
    $signed_message = "transaction_code=SIM001,status=COMPLETE"
                    . ",total_amount={$sim_amount}"
                    . ",transaction_uuid={$sim_uuid}"
                    . ",product_code=" . ESEWA_PRODUCT_CODE
                    . ",signed_field_names=transaction_code,status,total_amount,"
                    . "transaction_uuid,product_code,signed_field_names";

    $sim_sig = base64_encode(hash_hmac('sha256', $signed_message, ESEWA_SECRET_KEY, true));

    // But actually – eSewa only signs specific fields
    // Real message for signature:
    $real_signed_fields = "transaction_code,status,total_amount,transaction_uuid,product_code,signed_field_names";
    $real_parts = [];
    $real_data  = [
        'transaction_code'  => 'SIM001',
        'status'            => 'COMPLETE',
        'total_amount'      => $sim_amount,
        'transaction_uuid'  => $sim_uuid,
        'product_code'      => ESEWA_PRODUCT_CODE,
        'signed_field_names'=> $real_signed_fields,
    ];

    foreach (explode(',', $real_signed_fields) as $f) {
        $f = trim($f);
        if ($f !== 'signature' && isset($real_data[$f])) {
            $real_parts[] = "$f={$real_data[$f]}";
        }
    }
    $real_message = implode(',', $real_parts);
    $real_sig     = base64_encode(hash_hmac('sha256', $real_message, ESEWA_SECRET_KEY, true));

    $real_data['signature'] = $real_sig;

    $sim_payload = base64_encode(json_encode($real_data));
    $sim_url     = SUCCESS_URL . "?data=" . urlencode($sim_payload);

    echo "Found pending order: <strong>{$latest_order['order_number']}</strong> "
       . "(NPR {$latest_order['grand_total']})<br><br>";
    echo "Simulated UUID: <code>$sim_uuid</code><br>";
    echo "Simulated amount: <code>$sim_amount</code><br>";
    echo "Message signed: <code>" . htmlspecialchars($real_message) . "</code><br>";
    echo "Simulated signature: <code>$real_sig</code><br><br>";

    echo "<strong>⚠️ NOTE:</strong> This simulation skips the eSewa API verification step.<br>";
    echo "The payment_success.php will fail at Step 7 (API verify) unless you're testing with a real eSewa payment.<br><br>";

    echo "<a href='" . htmlspecialchars($sim_url) . "' style='
            display:inline-block;padding:10px 20px;
            background:#60BB46;color:#fff;
            text-decoration:none;border-radius:6px;font-weight:bold;'>
        🧪 Simulate Success (skips eSewa API verify)
    </a><br><br>";

    // Also provide the URL for manual testing
    echo "<details><summary>View simulation URL</summary>
          <textarea style='width:100%;height:80px;font-size:11px;'>"
       . htmlspecialchars($sim_url) . "</textarea></details>";
} else {
    echo "<span class='err'>⚠️ No pending eSewa orders to simulate with.</span><br>";
    echo "Create an order with eSewa payment method first.";
}
echo "</div>";