<?php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'Please log in to manage your cart.';
    header('Location: ../login.php'); exit;
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';
$redirect = $_POST['redirect'] ?? '../cart.php';
$redirect_url = '../' . ltrim($redirect, '../');

if ($action === 'add') {
    $product_id = (int)($_POST['product_id'] ?? 0);
    $strap_size = trim($_POST['selected_strap_size'] ?? '');

    if (!$product_id) {
        $_SESSION['error'] = 'Invalid product.';
        header("Location: $redirect_url"); exit;
    }

    // Check product
    $p_stmt = $conn->prepare("SELECT stock_quantity, strap_adjustable, strap_size_options FROM products WHERE id = ? AND is_active = 1");
    $p_stmt->bind_param("i", $product_id);
    $p_stmt->execute();
    $product = $p_stmt->get_result()->fetch_assoc();

    if (!$product) {
        $_SESSION['error'] = 'Product not found.';
        header("Location: $redirect_url"); exit;
    }
    if ($product['stock_quantity'] <= 0) {
        $_SESSION['error'] = 'Product is out of stock.';
        header("Location: $redirect_url"); exit;
    }

    // STRAP LOGIC:
    // adjustable=1 + has size_options → customer MUST select a size
    // adjustable=0 → fixed strap → no size needed, set to null
    if ($product['strap_adjustable'] && $product['strap_size_options']) {
        // Adjustable with options — validate selection
        $valid_sizes = array_map('trim', explode(',', $product['strap_size_options']));
        if (!$strap_size || !in_array($strap_size, $valid_sizes)) {
            $_SESSION['error'] = 'Please select a valid strap size.';
            header("Location: $redirect_url"); exit;
        }
    } else {
        // Fixed strap or no options — no selection needed
        $strap_size = null;
    }

    // Check if already in cart (same product + same strap size)
    if ($strap_size === null) {
        $check = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ? AND selected_strap_size IS NULL");
        $check->bind_param("ii", $user_id, $product_id);
    } else {
        $check = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ? AND selected_strap_size = ?");
        $check->bind_param("iis", $user_id, $product_id, $strap_size);
    }
    $check->execute();
    $existing = $check->get_result()->fetch_assoc();

    if ($existing) {
        $new_qty = $existing['quantity'] + 1;
        if ($new_qty > $product['stock_quantity']) {
            $_SESSION['error'] = 'Not enough stock available.';
            header("Location: $redirect_url"); exit;
        }
        $upd = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        $upd->bind_param("ii", $new_qty, $existing['id']);
        $upd->execute();
    } else {
        $ins = $conn->prepare("INSERT INTO cart (user_id, product_id, selected_strap_size, quantity) VALUES (?, ?, ?, 1)");
        $ins->bind_param("iis", $user_id, $product_id, $strap_size);
        $ins->execute();
    }

    $_SESSION['success'] = 'Added to cart successfully!';
    header("Location: $redirect_url");
    exit;
}

if ($action === 'update') {
    $cart_id = (int)($_POST['cart_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 1);
    if ($quantity < 1) $quantity = 1;

    $check = $conn->prepare("SELECT c.*, p.stock_quantity FROM cart c JOIN products p ON c.product_id = p.id WHERE c.id = ? AND c.user_id = ?");
    $check->bind_param("ii", $cart_id, $user_id);
    $check->execute();
    $cart_item = $check->get_result()->fetch_assoc();

    if (!$cart_item) {
        $_SESSION['error'] = 'Cart item not found.';
        header('Location: ../cart.php'); exit;
    }
    if ($quantity > $cart_item['stock_quantity']) {
        $_SESSION['error'] = 'Not enough stock. Only ' . $cart_item['stock_quantity'] . ' available.';
        header('Location: ../cart.php'); exit;
    }

    $upd = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
    $upd->bind_param("iii", $quantity, $cart_id, $user_id);
    $upd->execute();

    $_SESSION['success'] = 'Cart updated.';
    header('Location: ../cart.php');
    exit;
}

if ($action === 'remove') {
    $cart_id = (int)($_POST['cart_id'] ?? 0);
    $del = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $del->bind_param("ii", $cart_id, $user_id);
    $del->execute();
    $_SESSION['success'] = 'Item removed from cart.';
    header('Location: ../cart.php');
    exit;
}

header('Location: ../cart.php');
exit;
?>