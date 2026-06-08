<?php
session_start();

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../orders.php');
    exit();
}

require_once '../../config/db.php';

$action = $_POST['action'] ?? '';


// =============================================
// UPDATE ORDER STATUS
// =============================================
if ($action === 'update_status') {

    $order_id   = intval($_POST['order_id'] ?? 0);
    $new_status = trim($_POST['new_status'] ?? '');

    if ($order_id <= 0) {
        $_SESSION['order_error'] = 'Invalid order.';
        header('Location: ../orders.php');
        exit();
    }

    // Only allow valid statuses
    $valid_statuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered'];
    if (!in_array($new_status, $valid_statuses)) {
        $_SESSION['order_error'] = 'Invalid status.';
        header("Location: ../order-detail.php?id=$order_id");
        exit();
    }

    // Get current order to check current status
    $check = $conn->prepare("SELECT order_status FROM orders WHERE id = ?");
    $check->bind_param("i", $order_id);
    $check->execute();
    $check_result = $check->get_result();

    if ($check_result->num_rows === 0) {
        $_SESSION['order_error'] = 'Order not found.';
        $check->close();
        $conn->close();
        header('Location: ../orders.php');
        exit();
    }

    $current_order = $check_result->fetch_assoc();
    $check->close();

    // Don't allow going backwards in status
    // pending → confirmed → processing → shipped → delivered
    $status_order = [
        'pending'    => 1,
        'confirmed'  => 2,
        'processing' => 3,
        'shipped'    => 4,
        'delivered'  => 5
    ];

    $current_level = $status_order[$current_order['order_status']] ?? 0;
    $new_level     = $status_order[$new_status] ?? 0;

    if ($new_level <= $current_level) {
        $_SESSION['order_error'] = 'Cannot move order backwards. Current status: ' . ucfirst($current_order['order_status']);
        header("Location: ../order-detail.php?id=$order_id");
        exit();
    }

    // Update the status
    $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $order_id);

    if ($stmt->execute()) {

        // Update timestamp based on status
        $timestamp_query = "";

        if ($new_status === 'confirmed') {
            $timestamp_query = "UPDATE orders SET confirmed_at = NOW() WHERE id = ?";
        } elseif ($new_status === 'shipped') {
            $timestamp_query = "UPDATE orders SET shipped_at = NOW() WHERE id = ?";
        } elseif ($new_status === 'delivered') {
            $timestamp_query = "UPDATE orders SET delivered_at = NOW() WHERE id = ?";
        }

        if (!empty($timestamp_query)) {
            $ts_stmt = $conn->prepare($timestamp_query);
            $ts_stmt->bind_param("i", $order_id);
            $ts_stmt->execute();
            $ts_stmt->close();
        }

        // If delivered and payment method is COD mark payment as paid
        if ($new_status === 'delivered') {
            $cod_check = $conn->prepare("SELECT payment_method FROM orders WHERE id = ?");
            $cod_check->bind_param("i", $order_id);
            $cod_check->execute();
            $cod_result = $cod_check->get_result()->fetch_assoc();
            $cod_check->close();

            if ($cod_result['payment_method'] === 'COD') {
                $pay_stmt = $conn->prepare("UPDATE orders SET payment_status = 'paid' WHERE id = ?");
                $pay_stmt->bind_param("i", $order_id);
                $pay_stmt->execute();
                $pay_stmt->close();
            }
        }

        $_SESSION['order_success'] = 'Order status updated to ' . ucfirst($new_status) . '.';
    } else {
        $_SESSION['order_error'] = 'Failed to update order status.';
    }

    $stmt->close();
    $conn->close();
    header("Location: ../order-detail.php?id=$order_id");
    exit();
}


// =============================================
// UPDATE PAYMENT STATUS
// =============================================
if ($action === 'update_payment') {

    $order_id   = intval($_POST['order_id'] ?? 0);
    $new_status = trim($_POST['payment_status'] ?? '');

    if ($order_id <= 0) {
        $_SESSION['order_error'] = 'Invalid order.';
        header('Location: ../orders.php');
        exit();
    }

    // Valid payment statuses
    $valid_pay_statuses = ['pending', 'paid', 'failed'];

    if (!in_array($new_status, $valid_pay_statuses)) {
        $_SESSION['order_error'] = 'Invalid payment status.';
        header("Location: ../order-detail.php?id=$order_id");
        exit();
    }

    // Get order payment method
    $check = $conn->prepare("
        SELECT payment_method
        FROM orders
        WHERE id = ?
    ");

    $check->bind_param("i", $order_id);
    $check->execute();

    $result = $check->get_result();

    if ($result->num_rows === 0) {
        $_SESSION['order_error'] = 'Order not found.';
        $check->close();
        $conn->close();

        header('Location: ../orders.php');
        exit();
    }

    $order = $result->fetch_assoc();
    $check->close();

    // Allow payment status update only for COD orders
    if (strtolower(trim($order['payment_method'])) !== 'cod') {

        $_SESSION['order_error'] =
            'Payment status cannot be updated manually for eSewa orders. It is updated automatically after payment verification.';

        $conn->close();

        header("Location: ../order-detail.php?id=$order_id");
        exit();
    }

    // Update payment status
    $stmt = $conn->prepare("
        UPDATE orders
        SET payment_status = ?
        WHERE id = ?
    ");

    $stmt->bind_param("si", $new_status, $order_id);

    if ($stmt->execute()) {

        $_SESSION['order_success'] =
            'Payment status updated to ' . ucfirst($new_status) . '.';

    } else {

        $_SESSION['order_error'] =
            'Failed to update payment status.';
    }

    $stmt->close();
    $conn->close();

    header("Location: ../order-detail.php?id=$order_id");
    exit();
}

// =============================================
// UPDATE ADMIN NOTES
// =============================================
if ($action === 'update_notes') {

    $order_id = intval($_POST['order_id'] ?? 0);
    $notes    = trim($_POST['admin_notes'] ?? '');

    if ($order_id <= 0) {
        $_SESSION['order_error'] = 'Invalid order.';
        header('Location: ../orders.php');
        exit();
    }

    // Allow empty notes (to clear notes)
    $notes = !empty($notes) ? $notes : null;

    $stmt = $conn->prepare("UPDATE orders SET admin_notes = ? WHERE id = ?");
    $stmt->bind_param("si", $notes, $order_id);

    if ($stmt->execute()) {
        $_SESSION['order_success'] = 'Admin notes updated.';
    } else {
        $_SESSION['order_error'] = 'Failed to update notes.';
    }

    $stmt->close();
    $conn->close();
    header("Location: ../order-detail.php?id=$order_id");
    exit();
}


// =============================================
// DELETE ORDER
// =============================================
if ($action === 'delete') {

    $order_id = intval($_POST['order_id'] ?? 0);

    if ($order_id <= 0) {
        $_SESSION['order_error'] = 'Invalid order.';
        header('Location: ../orders.php');
        exit();
    }

    // Only allow deleting pending orders with no payment
    $check = $conn->prepare("SELECT order_status, payment_status FROM orders WHERE id = ?");
    $check->bind_param("i", $order_id);
    $check->execute();
    $check_result = $check->get_result();

    if ($check_result->num_rows === 0) {
        $_SESSION['order_error'] = 'Order not found.';
        $check->close();
        $conn->close();
        header('Location: ../orders.php');
        exit();
    }

    $order_data = $check_result->fetch_assoc();
    $check->close();

    if ($order_data['order_status'] !== 'pending') {
        $_SESSION['order_error'] = 'Only pending orders can be deleted.';
        $conn->close();
        header('Location: ../orders.php');
        exit();
    }

    if ($order_data['payment_status'] === 'paid') {
        $_SESSION['order_error'] = 'Cannot delete a paid order.';
        $conn->close();
        header('Location: ../orders.php');
        exit();
    }

    // Delete order (order_items will cascade delete)
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);

    if ($stmt->execute()) {
        $_SESSION['order_success'] = 'Order deleted successfully.';
    } else {
        $_SESSION['order_error'] = 'Failed to delete order.';
    }

    $stmt->close();
    $conn->close();
    header('Location: ../orders.php');
    exit();
}


// No matching action
header('Location: ../orders.php');
exit();
?>