<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once 'esewa_config.php';

// Validate order_id from URL
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if ($order_id <= 0) {
    die("Invalid order. Please go back and try again.");
}

// Load the order (must belong to this user, eSewa, pending)
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

// Load order items
$items_stmt = $conn->prepare(
    "SELECT product_name, product_model_number, quantity, price, total
     FROM order_items
     WHERE order_id = ?"
);
$items_stmt->bind_param("i", $order_id);
$items_stmt->execute();
$order_items = $items_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$items_stmt->close();

// Build transaction UUID & amounts
$transaction_uuid = $order['order_number'] . '-' . time();

$amount          = number_format((float)$order['total_amount'],   2, '.', '');
$delivery_charge = number_format((float)$order['shipping_charge'], 2, '.', '');
$total_amount    = number_format((float)$order['grand_total'],    2, '.', '');

// Generate signature
// Signature is always over total_amount (grand total)
$signature = generate_esewa_signature($total_amount, $transaction_uuid);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay with eSewa – ChronoNest</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-[#f5f5f5] text-[#333] font-['Segoe_UI',Arial,sans-serif]">
    <div class="max-w-[600px] mx-auto mt-10 px-4">
        <!-- ── ORDER SUMMARY CARD ── -->
        <div class="bg-white rounded-xl p-7 shadow-[0_2px_12px_rgba(0,0,0,0.09)] mb-5">
            <h1 class="text-[22px] mb-[18px] text-[#1B2A4A] font-bold">
                Review &amp; Pay
            </h1>
            <h2 class="text-base text-[#1B2A4A] mb-3 font-semibold">
                Order Details
            </h2>
            <div class="flex justify-between py-1.5 border-b border-[#f0f0f0] text-sm">
                <span class="text-[#666]">
                    Order Number
                </span>
                <span class="font-semibold">
                    <?= htmlspecialchars($order['order_number']) ?>
                </span>
            </div>
            <div class="flex justify-between py-1.5 border-b border-[#f0f0f0] text-sm">
                <span class="text-[#666]">
                    Name
                </span>
                <span class="font-semibold">
                    <?= htmlspecialchars($order['customer_name']) ?>
                </span>
            </div>
            <div class="flex justify-between py-1.5 border-b border-[#f0f0f0] text-sm">
                <span class="text-[#666]">
                    Email
                </span>
                <span class="font-semibold">
                    <?= htmlspecialchars($order['customer_email']) ?>
                </span>
            </div>
            <div class="flex justify-between py-1.5 border-b border-[#f0f0f0] text-sm">
                <span class="text-[#666]">
                    Phone
                </span>
                <span class="font-semibold">
                    <?= htmlspecialchars($order['customer_phone']) ?>
                </span>
            </div>
            <div class="flex justify-between py-1.5 text-sm">
                <span class="text-[#666]">
                    Shipping Address
                </span>
                <span class="font-semibold text-right max-w-[60%]">
                    <?= nl2br(htmlspecialchars($order['shipping_address'])) ?>
                </span>
            </div>
        </div>

        <!-- ── ITEMS CARD ── -->
        <div class="bg-white rounded-xl p-7 shadow-[0_2px_12px_rgba(0,0,0,0.09)] mb-5">
            <h2 class="text-base text-[#1B2A4A] mb-3 font-semibold">
                Items Ordered
            </h2>
            <table class="w-full border-collapse text-[13px]">
                <thead>
                    <tr>

                        <th class="bg-[#f7f8fa] px-2.5 py-2 text-left border-b-2 border-[#e0e0e0]">
                            Product
                        </th>

                        <th class="bg-[#f7f8fa] px-2.5 py-2 text-left border-b-2 border-[#e0e0e0]">
                            Qty
                        </th>

                        <th class="bg-[#f7f8fa] px-2.5 py-2 text-right border-b-2 border-[#e0e0e0]">
                            Total
                        </th>

                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_items as $item): ?>
                    <tr>
                        <td class="px-2.5 py-2 border-b border-[#f0f0f0]">
                            <?= htmlspecialchars($item['product_name']) ?>
                            <br>
                            <small class="text-[#999]">
                                <?= htmlspecialchars($item['product_model_number']) ?>
                            </small>
                        </td>
                        <td class="px-2.5 py-2 border-b border-[#f0f0f0]">
                            <?= (int)$item['quantity'] ?>
                        </td>
                        <td class="px-2.5 py-2 border-b border-[#f0f0f0] text-right">
                            NPR <?= number_format((float)$item['total'], 0) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="mt-3.5">
                <div class="flex justify-between py-1.5 border-b border-[#f0f0f0] text-sm">
                    <span class="text-[#666]">
                        Subtotal
                    </span>
                    <span>
                        NPR <?= number_format((float)$order['total_amount'], 0) ?>
                    </span>
                </div>

                <div class="flex justify-between py-1.5 border-b border-[#f0f0f0] text-sm">
                    <span class="text-[#666]">
                        Shipping
                    </span>
                    <span>
                        NPR <?= number_format((float)$order['shipping_charge'], 0) ?>
                    </span>
                </div>

                <div class="flex justify-between pt-2.5">
                    <span class="text-xl font-bold text-[#1B2A4A]">
                        Grand Total
                    </span>
                    <span class="text-xl font-bold text-[#1B2A4A]">
                        NPR <?= number_format((float)$order['grand_total'], 0) ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- ── ESEWA PAYMENT FORM CARD ── -->
        <div class="bg-white rounded-xl p-7 shadow-[0_2px_12px_rgba(0,0,0,0.09)] mb-5">
            <h2 class="text-base text-[#1B2A4A] mb-3 font-semibold">
                Pay with eSewa
            </h2>
            <p class="text-[13px] text-[#666] mb-4">
                Click the button below. You will be taken to eSewa's secure page
                to complete the payment.
            </p>

            <form action="<?= ESEWA_PAYMENT_URL ?>" method="POST">
                <input type="hidden" name="amount" value="<?= $amount ?>">
                <input type="hidden" name="tax_amount" value="0">
                <input type="hidden" name="product_service_charge" value="0">
                <input type="hidden" name="product_delivery_charge" value="<?= $delivery_charge ?>">
                <input type="hidden" name="total_amount" value="<?= $total_amount ?>">
                <input type="hidden" name="transaction_uuid" value="<?= htmlspecialchars($transaction_uuid) ?>">
                <input type="hidden" name="product_code" value="<?= ESEWA_PRODUCT_CODE ?>">
                <!-- Exactly these three fields are signed (in this order) -->
                <input type="hidden" name="signed_field_names" value="total_amount,transaction_uuid,product_code">
                <input type="hidden" name="signature" value="<?= htmlspecialchars($signature) ?>">
                <input type="hidden" name="success_url" value="<?= SUCCESS_URL ?>">
                <input type="hidden" name="failure_url" value="<?= FAILURE_URL ?>">

                <button type="submit"
                    class="block w-full py-[15px] bg-[#60BB46] hover:bg-[#52a33c] text-white border-0 rounded-lg text-[17px] font-bold cursor-pointer tracking-[0.3px] transition-colors duration-200">
                    Pay NPR <?= number_format((float)$total_amount, 0) ?>
                    <span
                        class="inline-block bg-white text-[#60BB46] px-2 py-0.5 rounded ml-1.5 font-black text-[15px]">
                        eSewa
                    </span>
                </button>
            </form>

            <a href="../../cart.php"
                class="inline-block mt-3.5 text-[#888] hover:text-[#333] text-[13px] no-underline transition-colors duration-200">
                ← Cancel and go back to cart
            </a>
        </div>
    </div>
</body>
</html>