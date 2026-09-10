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
    $check = $conn->prepare("SELECT id FROM users WHERE phone = ? AND id != ?");
    $check->bind_param("si", $phone, $user_id);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $_SESSION['error'] = 'Phone number is already in use by another account.';
        header('Location: ../account/index.php'); exit;
    }

    $stmt = $conn->prepare("UPDATE users SET name = ?, phone = ?, address = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $phone, $address, $user_id);

    if ($stmt->execute()) {
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

    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if (!password_verify($current, $user['password'])) {
        $_SESSION['error'] = 'Current password is incorrect.';
        header('Location: ../account/index.php'); exit;
    }

    $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
    $upd = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $upd->bind_param("si", $hashed, $user_id);

    if ($upd->execute()) {
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