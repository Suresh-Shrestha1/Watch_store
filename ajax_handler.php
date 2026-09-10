<?php
session_start();
require_once 'config/db.php';
// Build absolute login URL so redirect works from any subfolder depth
$script    = $_SERVER['SCRIPT_NAME'];                          // /watch_store/ajax_handler.php
$root      = substr($script, 0, strrpos($script, '/ajax_handler.php')); // /watch_store
$login_url = $root . '/pages/login.php';                      // /watch_store/pages/login.php

header('Content-Type: application/json');

$action = isset($_POST['ajax_action']) ? $_POST['ajax_action'] : '';

if (empty($action)) {
    echo json_encode(['success' => false, 'message' => 'No action specified']);
    exit;
}

// ── ADD TO CART ───────────────────────────────────────────────────────────────
if ($action === 'add_to_cart') {
    if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'redirect' => $login_url]);
        exit;
    }

    $user_id    = (int)$_SESSION['user_id'];
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity   = isset($_POST['quantity'])   ? (int)$_POST['quantity']   : 1;
    $strap_size = isset($_POST['selected_strap_size']) ? trim($_POST['selected_strap_size']) : '';

    if (!$product_id || $quantity < 1) {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit;
    }

    // Check product exists and has stock
    $product = $conn->query(
        "SELECT id, stock_quantity, strap_adjustable, strap_size_options FROM products WHERE id = $product_id AND is_active = 1"
    )->fetch_assoc();

    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit;
    }

    if ($product['stock_quantity'] < $quantity) {
        echo json_encode(['success' => false, 'message' => 'Insufficient stock']);
        exit;
    }

    // STRAP LOGIC: adjustable strap with size options requires a valid selection
    if ($product['strap_adjustable'] && $product['strap_size_options']) {
        $valid_sizes = array_map('trim', explode(',', $product['strap_size_options']));
        if (!$strap_size || !in_array($strap_size, $valid_sizes)) {
            echo json_encode([
                'success' => false,
                'requires_strap_size' => true,
                'strap_sizes' => $valid_sizes,
                'message' => 'Please select a strap size.'
            ]);
            exit;
        }
    } else {
        $strap_size = null;
    }

    // Check if already in cart (same product + same strap size)
    if ($strap_size === null) {
        $existing = $conn->query(
            "SELECT id, quantity FROM cart WHERE user_id = $user_id AND product_id = $product_id AND selected_strap_size IS NULL"
        )->fetch_assoc();
    } else {
        $strap_size_esc = $conn->real_escape_string($strap_size);
        $existing = $conn->query(
            "SELECT id, quantity FROM cart WHERE user_id = $user_id AND product_id = $product_id AND selected_strap_size = '$strap_size_esc'"
        )->fetch_assoc();
    }

    if ($existing) {
        $new_qty = $existing['quantity'] + $quantity;
        $conn->query(
            "UPDATE cart SET quantity = $new_qty, updated_at = NOW() WHERE id = {$existing['id']}"
        );
    } else {
        $strap_value = $strap_size === null ? 'NULL' : "'" . $conn->real_escape_string($strap_size) . "'";
        $conn->query(
            "INSERT INTO cart (user_id, product_id, selected_strap_size, quantity, added_at, updated_at)
             VALUES ($user_id, $product_id, $strap_value, $quantity, NOW(), NOW())"
        );
    }

    // Get updated cart count
    $cart_count = $conn->query(
        "SELECT SUM(quantity) as cnt FROM cart WHERE user_id = $user_id"
    )->fetch_assoc()['cnt'] ?? 0;

    echo json_encode(['success' => true, 'cart_count' => (int)$cart_count]);
    exit;
}

// ── TOGGLE WISHLIST ───────────────────────────────────────────────────────────
if ($action === 'toggle_wishlist') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'redirect' => $login_url]);
        exit;
    }

    $user_id    = (int)$_SESSION['user_id'];
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

    if (!$product_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid product']);
        exit;
    }

    // Check if already in wishlist
    $check = $conn->query(
        "SELECT id FROM wishlists WHERE user_id = $user_id AND product_id = $product_id"
    );

    if ($check->num_rows > 0) {
        // Remove from wishlist
        $conn->query(
            "DELETE FROM wishlists WHERE user_id = $user_id AND product_id = $product_id"
        );
        echo json_encode(['success' => true, 'action' => 'removed']);
    } else {
        // Add to wishlist
        $conn->query(
            "INSERT INTO wishlists (user_id, product_id, added_at)
             VALUES ($user_id, $product_id, NOW())"
        );
        echo json_encode(['success' => true, 'action' => 'added']);
    }
    exit;
}

// ── UNKNOWN ACTION ────────────────────────────────────────────────────────────
echo json_encode(['success' => false, 'message' => 'Unknown action']);
exit;