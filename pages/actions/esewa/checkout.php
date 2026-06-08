<?php
// ============================================================
//  checkout.php  –  Show order summary + eSewa payment form
// ============================================================
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once 'esewa_config.php';   // DB + constants + signature fn

// ── 1. Validate order_id from URL ────────────────────────────
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if ($order_id <= 0) {
    die("Invalid order. Please go back and try again.");
}

// ── 2. Load the order (must belong to this user, eSewa, pending) ─
$stmt = $conn->prepare(
    "SELECT id, order_number, customer_name, customer_email,
            customer_phone, shipping_address, payment_method,
            payment_status, total_amount, shipping_charge, grand_total
     FROM orders
     WHERE id = ?
       AND user_id = ?
       AND payment_method = 'eSewa'
       AND payment_status = 'pending'"
);
$stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    die("Order not found, already paid, or does not belong to you.");
}

// ── 3. Load order items ───────────────────────────────────────
$items_stmt = $conn->prepare(
    "SELECT product_name, product_model_number, quantity, price, total
     FROM order_items
     WHERE order_id = ?"
);
$items_stmt->bind_param("i", $order_id);
$items_stmt->execute();
$order_items = $items_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$items_stmt->close();

// ── 4. Build transaction UUID & amounts ──────────────────────
// Format:  <order_number>-<timestamp>
// We strip the timestamp in payment_success.php to recover order_number
$transaction_uuid = $order['order_number'] . '-' . time();

// eSewa amount breakdown rules:
//   amount                  = product cost (subtotal, no shipping)
//   product_delivery_charge = shipping
//   tax_amount              = 0  (we don't add tax separately)
//   product_service_charge  = 0
//   total_amount            = amount + delivery_charge   ← must match exactly
//
// Your DB:  total_amount = subtotal,  grand_total = subtotal + shipping
$amount          = number_format((float)$order['total_amount'],   2, '.', '');
$delivery_charge = number_format((float)$order['shipping_charge'], 2, '.', '');
$total_amount    = number_format((float)$order['grand_total'],    2, '.', '');

// ── 5. Generate signature ─────────────────────────────────────
// Signature is always over total_amount (grand total)
$signature = generate_esewa_signature($total_amount, $transaction_uuid);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay with eSewa – ChronoNest</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif;
               background: #f5f5f5; color: #333; }
        .container { max-width: 600px; margin: 40px auto; padding: 0 16px; }
        .card { background: #fff; border-radius: 12px; padding: 28px;
                box-shadow: 0 2px 12px rgba(0,0,0,.09); margin-bottom: 20px; }
        h1 { font-size: 22px; margin-bottom: 18px; color: #1B2A4A; }
        h2 { font-size: 16px; color: #1B2A4A; margin-bottom: 12px; }
        .info-row { display: flex; justify-content: space-between;
                    padding: 6px 0; border-bottom: 1px solid #f0f0f0;
                    font-size: 14px; }
        .info-row:last-child { border-bottom: none; }
        .label { color: #666; }
        .value { font-weight: 600; }
        .items-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .items-table th { background: #f7f8fa; padding: 8px 10px;
                          text-align: left; border-bottom: 2px solid #e0e0e0; }
        .items-table td { padding: 8px 10px; border-bottom: 1px solid #f0f0f0; }
        .total-section { margin-top: 14px; }
        .grand-total { font-size: 20px; font-weight: 700; color: #1B2A4A; }
        .esewa-btn {
            display: block; width: 100%; padding: 15px;
            background: #60BB46; color: #fff; border: none;
            border-radius: 8px; font-size: 17px; font-weight: 700;
            cursor: pointer; letter-spacing: 0.3px; transition: background .2s;
        }
        .esewa-btn:hover { background: #52a33c; }
        .esewa-logo { display: inline-block; background: #fff;
                      color: #60BB46; padding: 2px 8px; border-radius: 4px;
                      margin-left: 6px; font-weight: 900; font-size: 15px; }
        .back-link { display: inline-block; margin-top: 14px;
                     color: #888; font-size: 13px; text-decoration: none; }
        .back-link:hover { color: #333; }
        /* Debug box – remove in production */
        .debug { background:#f0f0f0; padding:10px; font-size:12px;
                 font-family:monospace; border-radius:6px;
                 margin-bottom:14px; word-break:break-all; }
    </style>
</head>
<body>
<div class="container">

    <!-- ── ORDER SUMMARY CARD ── -->
    <div class="card">
        <h1>Review &amp; Pay</h1>

        <h2>Order Details</h2>
        <div class="info-row">
            <span class="label">Order Number</span>
            <span class="value"><?= htmlspecialchars($order['order_number']) ?></span>
        </div>
        <div class="info-row">
            <span class="label">Name</span>
            <span class="value"><?= htmlspecialchars($order['customer_name']) ?></span>
        </div>
        <div class="info-row">
            <span class="label">Email</span>
            <span class="value"><?= htmlspecialchars($order['customer_email']) ?></span>
        </div>
        <div class="info-row">
            <span class="label">Phone</span>
            <span class="value"><?= htmlspecialchars($order['customer_phone']) ?></span>
        </div>
        <div class="info-row">
            <span class="label">Shipping Address</span>
            <span class="value" style="text-align:right;max-width:60%">
                <?= nl2br(htmlspecialchars($order['shipping_address'])) ?>
            </span>
        </div>
    </div>

    <!-- ── ITEMS CARD ── -->
    <div class="card">
        <h2>Items Ordered</h2>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th style="text-align:right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order_items as $item): ?>
                <tr>
                    <td>
                        <?= htmlspecialchars($item['product_name']) ?>
                        <br>
                        <small style="color:#999">
                            <?= htmlspecialchars($item['product_model_number']) ?>
                        </small>
                    </td>
                    <td><?= (int)$item['quantity'] ?></td>
                    <td style="text-align:right">
                        NPR <?= number_format((float)$item['total'], 0) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total-section">
            <div class="info-row">
                <span class="label">Subtotal</span>
                <span>NPR <?= number_format((float)$order['total_amount'], 0) ?></span>
            </div>
            <div class="info-row">
                <span class="label">Shipping</span>
                <span>NPR <?= number_format((float)$order['shipping_charge'], 0) ?></span>
            </div>
            <div class="info-row" style="padding-top:10px">
                <span class="label grand-total">Grand Total</span>
                <span class="grand-total">
                    NPR <?= number_format((float)$order['grand_total'], 0) ?>
                </span>
            </div>
        </div>
    </div>

    <!-- ── ESEWA PAYMENT FORM CARD ── -->
    <div class="card">
        <h2>Pay with eSewa</h2>
        <p style="font-size:13px;color:#666;margin-bottom:16px">
            Click the button below. You will be taken to eSewa's secure page
            to complete the payment.
        </p>

        <!--
            FIELD EXPLANATION (eSewa v2 API):
            ─────────────────────────────────
            amount                  = product subtotal (no shipping)
            tax_amount              = 0
            product_service_charge  = 0
            product_delivery_charge = shipping cost
            total_amount            = amount + tax + service + delivery
                                      (must equal grand_total exactly)
            transaction_uuid        = your unique ID for this attempt
            product_code            = your eSewa merchant code
            signed_field_names      = comma list of fields included in signature
            signature               = HMAC-SHA256(message, secret_key) | base64
        -->
        <form action="<?= ESEWA_PAYMENT_URL ?>" method="POST">

            <input type="hidden" name="amount"
                   value="<?= $amount ?>">

            <input type="hidden" name="tax_amount"
                   value="0">

            <input type="hidden" name="product_service_charge"
                   value="0">

            <input type="hidden" name="product_delivery_charge"
                   value="<?= $delivery_charge ?>">

            <input type="hidden" name="total_amount"
                   value="<?= $total_amount ?>">

            <input type="hidden" name="transaction_uuid"
                   value="<?= htmlspecialchars($transaction_uuid) ?>">

            <input type="hidden" name="product_code"
                   value="<?= ESEWA_PRODUCT_CODE ?>">

            <!-- Exactly these three fields are signed (in this order) -->
            <input type="hidden" name="signed_field_names"
                   value="total_amount,transaction_uuid,product_code">

            <input type="hidden" name="signature"
                   value="<?= htmlspecialchars($signature) ?>">

            <input type="hidden" name="success_url"
                   value="<?= SUCCESS_URL ?>">

            <input type="hidden" name="failure_url"
                   value="<?= FAILURE_URL ?>">

            <button type="submit" class="esewa-btn">
                Pay NPR <?= number_format((float)$total_amount, 0) ?>
                with <span class="esewa-logo">eSewa</span>
            </button>
        </form>

        <a href="../../cart.php" class="back-link">← Cancel and go back to cart</a>
    </div>

</div>
</body>
</html>