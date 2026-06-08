<?php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['user_id'])) { header('Location: ../login.php'); exit; }

$action = $_POST['action'] ?? '';
$user_id = $_SESSION['user_id'];

if ($action === 'place_order') {
    $name = trim($_POST['customer_name'] ?? '');
    $email = trim($_POST['customer_email'] ?? '');
    $phone = trim($_POST['customer_phone'] ?? '');
    $address = trim($_POST['shipping_address'] ?? '');
    $payment = $_POST['payment_method'] ?? 'COD';

    // Validate
    if (!$name || !$email || !$phone || !$address) {
        $_SESSION['error'] = 'All shipping fields are required.';
        header('Location: ../checkout.php'); exit;
    }
    if (!in_array($payment, ['COD', 'eSewa'])) {
        $_SESSION['error'] = 'Invalid payment method.';
        header('Location: ../checkout.php'); exit;
    }
    if (!preg_match('/^9[78]\d{8}$/', $phone)) {
        $_SESSION['error'] = 'Please enter a valid 10-digit phone number.';
        header('Location: ../checkout.php'); exit;
    }

    // Fetch cart items
    $cart_sql = "SELECT c.*, p.name as pname, p.model_number, p.price, p.stock_quantity,
        (SELECT image_url FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image
        FROM cart c JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ? AND p.is_active = 1";
    $stmt = mysqli_prepare($conn, $cart_sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $items = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

    if (empty($items)) {
        $_SESSION['error'] = 'Your cart is empty.';
        header('Location: ../cart.php'); exit;
    }

    // Validate stock
    foreach ($items as $item) {
        if ($item['quantity'] > $item['stock_quantity']) {
            $_SESSION['error'] = "Insufficient stock for \"{$item['pname']}\". Only {$item['stock_quantity']} available.";
            header('Location: ../cart.php'); exit;
        }
    }

    // Calculate totals
    $total_amount = 0;
    foreach ($items as $item) {
        $total_amount += $item['price'] * $item['quantity'];
    }
    $shipping_charge = SHIPPING_CHARGE;
    $grand_total = $total_amount + $shipping_charge;

    // Generate order number
    $order_number = 'CN-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

    // Payment status
    $payment_status = ($payment === 'COD') ? 'pending' : 'pending';

    // Start transaction
    mysqli_begin_transaction($conn);

    try {
        // Insert order
        $order_sql = "INSERT INTO orders (user_id, order_number, customer_name, customer_email, customer_phone, shipping_address, payment_method, payment_status, total_amount, shipping_charge, grand_total, order_status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
        $order_stmt = mysqli_prepare($conn, $order_sql);
        mysqli_stmt_bind_param($order_stmt, "isssssssddd", $user_id, $order_number, $name, $email, $phone, $address, $payment, $payment_status, $total_amount, $shipping_charge, $grand_total);
        mysqli_stmt_execute($order_stmt);
        $order_id = mysqli_insert_id($conn);

        // Insert order items & reduce stock
        foreach ($items as $item) {
            $item_total = $item['price'] * $item['quantity'];
            $oi_sql = "INSERT INTO order_items (order_id, product_id, product_name, product_model_number, product_image, selected_strap_size, quantity, price, total)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $oi_stmt = mysqli_prepare($conn, $oi_sql);
            mysqli_stmt_bind_param($oi_stmt, "iissssidd", $order_id, $item['product_id'], $item['pname'], $item['model_number'], $item['main_image'], $item['selected_strap_size'], $item['quantity'], $item['price'], $item_total);
            mysqli_stmt_execute($oi_stmt);

            // Reduce stock
            $stock_sql = "UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ? AND stock_quantity >= ?";
            $stock_stmt = mysqli_prepare($conn, $stock_sql);
            mysqli_stmt_bind_param($stock_stmt, "iii", $item['quantity'], $item['product_id'], $item['quantity']);
            mysqli_stmt_execute($stock_stmt);

            if (mysqli_affected_rows($conn) === 0) {
                throw new Exception("Stock issue for product: {$item['pname']}");
            }
        }

        // Clear cart
        $clear = mysqli_prepare($conn, "DELETE FROM cart WHERE user_id = ?");
        mysqli_stmt_bind_param($clear, "i", $user_id);
        mysqli_stmt_execute($clear);

        // Update user address if empty
        $addr_check = mysqli_prepare($conn, "SELECT address FROM users WHERE id = ?");
        mysqli_stmt_bind_param($addr_check, "i", $user_id);
        mysqli_stmt_execute($addr_check);
        $user_addr = mysqli_fetch_assoc(mysqli_stmt_get_result($addr_check));
        if (empty($user_addr['address'])) {
            $upd_addr = mysqli_prepare($conn, "UPDATE users SET address = ? WHERE id = ?");
            mysqli_stmt_bind_param($upd_addr, "si", $address, $user_id);
            mysqli_stmt_execute($upd_addr);
        }

        mysqli_commit($conn);

        // Handle eSewa redirect (simplified — in production, redirect to eSewa gateway)
        if ($payment === 'eSewa') {
    header("Location: esewa/checkout.php?order_id=$order_id");
    exit;
} else {
    $_SESSION['success'] = "Order placed successfully! Your order number is #$order_number";
    header("Location: ../account/order-detail.php?id=$order_id");
    exit;
}

    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['error'] = 'Order failed: ' . $e->getMessage();
        header('Location: ../checkout.php');
        exit;
    }
}

header('Location: ../checkout.php');
exit;
?>