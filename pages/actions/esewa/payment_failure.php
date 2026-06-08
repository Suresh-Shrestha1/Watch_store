<?php
// ============================================================
//  payment_failure.php  –  eSewa redirects here on failure
// ============================================================
session_start();
require_once 'esewa_config.php';   // $conn + constants

$order_info = null;

// ── Try to extract order info if eSewa sent a data payload ───
if (!empty($_GET['data'])) {
    $decoded = base64_decode($_GET['data'], true);

    if ($decoded !== false) {
        $esewa_data = json_decode($decoded, true);

        if (is_array($esewa_data)) {
            $uuid = $esewa_data['transaction_uuid'] ?? '';

            if ($uuid) {
                // Recover order_number by stripping the trailing -<timestamp>
                $order_number = preg_replace('/-\d+$/', '', $uuid);

                if ($order_number) {
                    $stmt = $conn->prepare(
                        "SELECT id, order_number, grand_total
                         FROM orders
                         WHERE order_number = ?
                           AND payment_method = 'eSewa'"
                    );
                    $stmt->bind_param("s", $order_number);
                    $stmt->execute();
                    $order_info = $stmt->get_result()->fetch_assoc();
                    $stmt->close();
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed – ChronoNest</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif;
               background: #fff5f5; min-height: 100vh;
               display: flex; align-items: center; justify-content: center; }
        .card { background: #fff; border-radius: 14px; padding: 40px 32px;
                max-width: 480px; width: 100%; margin: 20px;
                text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,.10); }
        .icon { width: 70px; height: 70px; background: #e53e3e;
                border-radius: 50%; display: flex;
                align-items: center; justify-content: center;
                margin: 0 auto 20px; }
        .icon svg { width: 36px; height: 36px; stroke: #fff;
                    fill: none; stroke-width: 3; }
        h1 { color: #c53030; font-size: 26px; margin-bottom: 8px; }
        .subtitle { color: #555; font-size: 15px;
                    margin-bottom: 28px; line-height: 1.6; }
        .info-box { background: #fff5f5; border: 1px solid #fed7d7;
                    border-radius: 10px; padding: 16px;
                    margin-bottom: 24px; font-size: 14px; color: #742a2a; }
        .btn { display: inline-block; padding: 13px 28px;
               background: #1B2A4A; color: #fff; text-decoration: none;
               border-radius: 8px; font-size: 15px; font-weight: 600;
               margin: 6px; }
        .btn-green   { background: #60BB46; }
        .btn-outline { background: transparent;
                       border: 2px solid #1B2A4A; color: #1B2A4A; }
    </style>
</head>
<body>
<div class="card">
    <div class="icon">
        <svg viewBox="0 0 24 24">
            <line x1="18" y1="6"  x2="6"  y2="18"/>
            <line x1="6"  y1="6"  x2="18" y2="18"/>
        </svg>
    </div>

    <h1>Payment Failed</h1>
    <p class="subtitle">
        Your payment was not completed.<br>
        This could be because you cancelled, entered wrong credentials,
        or there was a temporary issue with eSewa.
    </p>

    <?php if ($order_info): ?>
    <div class="info-box">
        <strong>Order #<?= htmlspecialchars($order_info['order_number']) ?></strong>
        is still saved.<br>
        Amount: NPR <?= number_format((float)$order_info['grand_total'], 0) ?><br><br>
        You can retry the payment — your order will not be lost.
    </div>

    <a href="checkout.php?order_id=<?= (int)$order_info['id'] ?>"
       class="btn btn-green">🔄 Retry Payment with eSewa</a><br>

    <?php else: ?>
    <div class="info-box">
        Your order is saved with <strong>pending</strong> status.
        Find it in My Orders and retry payment there.
    </div>
    <?php endif; ?>

    <a href="../../account/orders.php" class="btn">My Orders</a>
    <a href="../../../index.php" class="btn btn-outline">Go to Homepage</a>
</div>
</body>
</html>