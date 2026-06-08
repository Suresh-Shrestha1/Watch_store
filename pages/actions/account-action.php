<?php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['user_id'])) { header('Location: ../login.php'); exit; }

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

if ($action === 'update_profile') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if (!$name || !$phone) {
        $_SESSION['error'] = 'Name and phone are required.';
        header('Location: ../account/index.php'); exit;
    }
    if (!preg_match('/^9[78]\d{8}$/', $phone)) {
        $_SESSION['error'] = 'Please enter a valid 10-digit phone number.';
        header('Location: ../account/index.php'); exit;
    }

    // Check phone uniqueness
    $check = mysqli_prepare($conn, "SELECT id FROM users WHERE phone = ? AND id != ?");
    mysqli_stmt_bind_param($check, "si", $phone, $user_id);
    mysqli_stmt_execute($check);
    if (mysqli_stmt_get_result($check)->num_rows > 0) {
        $_SESSION['error'] = 'Phone number is already in use by another account.';
        header('Location: ../account/index.php'); exit;
    }

    $stmt = mysqli_prepare($conn, "UPDATE users SET name = ?, phone = ?, address = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "sssi", $name, $phone, $address, $user_id);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['user_name'] = $name;
        $_SESSION['success'] = 'Profile updated successfully!';
    } else {
        $_SESSION['error'] = 'Failed to update profile.';
    }
    header('Location: ../account/index.php');
    exit;
}

if ($action === 'change_password') {
    $current = $_POST['current_password'] ?? '';
    $new_pass = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (!$current || !$new_pass || !$confirm) {
        $_SESSION['error'] = 'All password fields are required.';
        header('Location: ../account/index.php'); exit;
    }
    if (strlen($new_pass) < 8) {
        $_SESSION['error'] = 'New password must be at least 8 characters.';
        header('Location: ../account/index.php'); exit;
    }
    if ($new_pass !== $confirm) {
        $_SESSION['error'] = 'New passwords do not match.';
        header('Location: ../account/index.php'); exit;
    }

    $stmt = mysqli_prepare($conn, "SELECT password FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!password_verify($current, $user['password'])) {
        $_SESSION['error'] = 'Current password is incorrect.';
        header('Location: ../account/index.php'); exit;
    }

    $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
    $upd = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE id = ?");
    mysqli_stmt_bind_param($upd, "si", $hashed, $user_id);

    if (mysqli_stmt_execute($upd)) {
        $_SESSION['success'] = 'Password changed successfully!';
    } else {
        $_SESSION['error'] = 'Failed to change password.';
    }
    header('Location: ../account/index.php');
    exit;
}

header('Location: ../account/index.php');
exit;
?>