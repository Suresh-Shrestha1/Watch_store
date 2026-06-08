<?php
session_start();

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../customers.php');
    exit();
}

require_once '../../config/db.php';

$action = $_POST['action'] ?? '';


// =============================================
// DELETE CUSTOMER
// =============================================
if ($action === 'delete') {

    $id = intval($_POST['id'] ?? 0);

    if ($id <= 0) {
        $_SESSION['customer_error'] = 'Invalid customer.';
        header('Location: ../customers.php');
        exit();
    }

    // Don't allow deleting admin users
    $check_admin = $conn->prepare("SELECT role FROM users WHERE id = ?");
    $check_admin->bind_param("i", $id);
    $check_admin->execute();
    $admin_result = $check_admin->get_result();

    if ($admin_result->num_rows > 0) {
        $user = $admin_result->fetch_assoc();
        if ($user['role'] === 'admin') {
            $_SESSION['customer_error'] = 'Cannot delete admin users.';
            $check_admin->close();
            $conn->close();
            header('Location: ../customers.php');
            exit();
        }
    }
    $check_admin->close();

    // Check if customer has orders
    $check_orders = $conn->prepare("SELECT COUNT(*) as total FROM orders WHERE user_id = ?");
    $check_orders->bind_param("i", $id);
    $check_orders->execute();
    $order_row = $check_orders->get_result()->fetch_assoc();
    $check_orders->close();

    if ($order_row['total'] > 0) {
        $_SESSION['customer_error'] = 'Cannot delete customer. They have ' . $order_row['total'] . ' order(s). Orders will lose customer reference.';
        $conn->close();
        header('Location: ../customers.php');
        exit();
    }

    // Delete customer
    // Wishlists and cart items will cascade delete (ON DELETE CASCADE)
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'customer'");
    $stmt->bind_param("i", $id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $_SESSION['customer_success'] = 'Customer deleted successfully.';
    } else {
        $_SESSION['customer_error'] = 'Failed to delete customer.';
    }

    $stmt->close();
    $conn->close();
    header('Location: ../customers.php');
    exit();
}


// No matching action
header('Location: ../customers.php');
exit();
?>