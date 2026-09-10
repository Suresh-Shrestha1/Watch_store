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
    $stmt = $conn->prepare($cart_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

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
    $conn->begin_transaction();

    try {
        // Insert order
        $order_sql = "INSERT INTO orders (user_id, order_number, customer_name, customer_email, customer_phone, shipping_address, payment_method, payment_status, total_amount, shipping_charge, grand_total, order_status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
        $order_stmt = $conn->prepare($order_sql);
        $order_stmt->bind_param("isssssssddd", $user_id, $order_number, $name, $email, $phone, $address, $payment, $payment_status, $total_amount, $shipping_charge, $grand_total);
        $order_stmt->execute();
        $order_id = $conn->insert_id;

        // Insert order items & reduce stock
        foreach ($items as $item) {
            $item_total = $item['price'] * $item['quantity'];
            $oi_sql = "INSERT INTO order_items (order_id, product_id, product_name, product_model_number, product_image, selected_strap_size, quantity, price, total)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $oi_stmt = $conn->prepare($oi_sql);
            $oi_stmt->bind_param("iissssidd", $order_id, $item['product_id'], $item['pname'], $item['model_number'], $item['main_image'], $item['selected_strap_size'], $item['quantity'], $item['price'], $item_total);
            $oi_stmt->execute();

            // Reduce stock
            $stock_sql = "UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ? AND stock_quantity >= ?";
            $stock_stmt = $conn->prepare($stock_sql);
            $stock_stmt->bind_param("iii", $item['quantity'], $item['product_id'], $item['quantity']);
            $stock_stmt->execute();

            if ($conn->affected_rows === 0) {
                throw new Exception("Stock issue for product: {$item['pname']}");
            }
        }

        // Clear cart
        $clear = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $clear->bind_param("i", $user_id);
        $clear->execute();

        // Update user address if empty
        $addr_check = $conn->prepare("SELECT address FROM users WHERE id = ?");
        $addr_check->bind_param("i", $user_id);
        $addr_check->execute();
        $user_addr = $addr_check->get_result()->fetch_assoc();
        if (empty($user_addr['address'])) {
            $upd_addr = $conn->prepare("UPDATE users SET address = ? WHERE id = ?");
            $upd_addr->bind_param("si", $address, $user_id);
            $upd_addr->execute();
        }

        $conn->commit();

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
        $conn->rollback();
        $_SESSION['error'] = 'Order failed: ' . $e->getMessage();
        header('Location: ../checkout.php');
        exit;
    }
}

header('Location: ../checkout.php');
exit;
?>