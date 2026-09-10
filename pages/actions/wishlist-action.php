<?php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'Please log in to manage your wishlist.';
    header('Location: ../login.php'); exit;
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';
$product_id = (int)($_POST['product_id'] ?? 0);
$redirect = $_POST['redirect'] ?? '';

if ($action === 'add' && $product_id) {
    $check = $conn->prepare("SELECT id FROM wishlists WHERE user_id = ? AND product_id = ?");
    $check->bind_param("ii", $user_id, $product_id);
    $check->execute();
    if ($check->get_result()->num_rows === 0) {
        $ins = $conn->prepare("INSERT INTO wishlists (user_id, product_id) VALUES (?, ?)");
        $ins->bind_param("ii", $user_id, $product_id);
        $ins->execute();
        $_SESSION['success'] = 'Added to wishlist!';
    } else {
        $_SESSION['success'] = 'Already in your wishlist.';
    }
}

if ($action === 'remove' && $product_id) {
    $del = $conn->prepare("DELETE FROM wishlists WHERE user_id = ? AND product_id = ?");
    $del->bind_param("ii", $user_id, $product_id);
    $del->execute();
    $_SESSION['success'] = 'Removed from wishlist.';
}

if ($action === 'move_to_cart' && $product_id) {
    $p = $conn->prepare("SELECT id, slug, stock_quantity, strap_adjustable, strap_size_options FROM products WHERE id = ? AND is_active = 1");
    $p->bind_param("i", $product_id);
    $p->execute();
    $product = $p->get_result()->fetch_assoc();

    if (!$product || $product['stock_quantity'] <= 0) {
        $_SESSION['error'] = 'Product is unavailable or out of stock.';
    } elseif ($product['strap_adjustable'] && $product['strap_size_options']) {
        // Adjustable strap with size options — needs selection on product page
        $_SESSION['error'] = 'Please select a strap size before adding to cart.';
        header('Location: ../product-detail.php?slug=' . urlencode($product['slug']));
        exit;
    } else {
        // Fixed strap or no size options — add directly with null strap size
        $check = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ? AND selected_strap_size IS NULL");
        $check->bind_param("ii", $user_id, $product_id);
        $check->execute();
        $existing = $check->get_result()->fetch_assoc();

        if ($existing) {
            $new_qty = $existing['quantity'] + 1;
            if ($new_qty <= $product['stock_quantity']) {
                $upd = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
                $upd->bind_param("ii", $new_qty, $existing['id']);
                $upd->execute();
            }
        } else {
            $strap_null = null;
            $ins = $conn->prepare("INSERT INTO cart (user_id, product_id, selected_strap_size, quantity) VALUES (?, ?, ?, 1)");
            $ins->bind_param("iis", $user_id, $product_id, $strap_null);
            $ins->execute();
        }

        // Remove from wishlist
        $del = $conn->prepare("DELETE FROM wishlists WHERE user_id = ? AND product_id = ?");
        $del->bind_param("ii", $user_id, $product_id);
        $del->execute();

        $_SESSION['success'] = 'Moved to cart!';
    }
}

if ($redirect) {
    header('Location: ../' . ltrim($redirect, '../'));
} else {
    header('Location: ../account/wishlist.php');
}
exit;
?>