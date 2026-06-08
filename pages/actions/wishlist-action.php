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
    $check = mysqli_prepare($conn, "SELECT id FROM wishlists WHERE user_id = ? AND product_id = ?");
    mysqli_stmt_bind_param($check, "ii", $user_id, $product_id);
    mysqli_stmt_execute($check);
    if (mysqli_stmt_get_result($check)->num_rows === 0) {
        $ins = mysqli_prepare($conn, "INSERT INTO wishlists (user_id, product_id) VALUES (?, ?)");
        mysqli_stmt_bind_param($ins, "ii", $user_id, $product_id);
        mysqli_stmt_execute($ins);
        $_SESSION['success'] = 'Added to wishlist!';
    } else {
        $_SESSION['success'] = 'Already in your wishlist.';
    }
}

if ($action === 'remove' && $product_id) {
    $del = mysqli_prepare($conn, "DELETE FROM wishlists WHERE user_id = ? AND product_id = ?");
    mysqli_stmt_bind_param($del, "ii", $user_id, $product_id);
    mysqli_stmt_execute($del);
    $_SESSION['success'] = 'Removed from wishlist.';
}

if ($action === 'move_to_cart' && $product_id) {
    $p = mysqli_prepare($conn, "SELECT id, slug, stock_quantity, strap_adjustable, strap_size_options FROM products WHERE id = ? AND is_active = 1");
    mysqli_stmt_bind_param($p, "i", $product_id);
    mysqli_stmt_execute($p);
    $product = mysqli_fetch_assoc(mysqli_stmt_get_result($p));

    if (!$product || $product['stock_quantity'] <= 0) {
        $_SESSION['error'] = 'Product is unavailable or out of stock.';
    } elseif ($product['strap_adjustable'] && $product['strap_size_options']) {
        // Adjustable strap with size options — needs selection on product page
        $_SESSION['error'] = 'Please select a strap size before adding to cart.';
        header('Location: ../product-detail.php?slug=' . urlencode($product['slug']));
        exit;
    } else {
        // Fixed strap or no size options — add directly with null strap size
        $check = mysqli_prepare($conn, "SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ? AND selected_strap_size IS NULL");
        mysqli_stmt_bind_param($check, "ii", $user_id, $product_id);
        mysqli_stmt_execute($check);
        $existing = mysqli_fetch_assoc(mysqli_stmt_get_result($check));

        if ($existing) {
            $new_qty = $existing['quantity'] + 1;
            if ($new_qty <= $product['stock_quantity']) {
                $upd = mysqli_prepare($conn, "UPDATE cart SET quantity = ? WHERE id = ?");
                mysqli_stmt_bind_param($upd, "ii", $new_qty, $existing['id']);
                mysqli_stmt_execute($upd);
            }
        } else {
            $strap_null = null;
            $ins = mysqli_prepare($conn, "INSERT INTO cart (user_id, product_id, selected_strap_size, quantity) VALUES (?, ?, ?, 1)");
            mysqli_stmt_bind_param($ins, "iis", $user_id, $product_id, $strap_null);
            mysqli_stmt_execute($ins);
        }

        // Remove from wishlist
        $del = mysqli_prepare($conn, "DELETE FROM wishlists WHERE user_id = ? AND product_id = ?");
        mysqli_stmt_bind_param($del, "ii", $user_id, $product_id);
        mysqli_stmt_execute($del);

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