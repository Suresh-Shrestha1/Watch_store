<?php
session_start();
require_once '../../config/db.php';

$action = $_POST['action'] ?? '';

if ($action === 'register') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    $_SESSION['old_input'] = ['name' => $name, 'email' => $email, 'phone' => $phone];

    if (!$name || !$email || !$phone || !$password || !$confirm) {
        $_SESSION['error'] = 'All fields are required.';
        header('Location: ../register.php'); exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Please enter a valid email address.';
        header('Location: ../register.php'); exit;
    }
    if (!preg_match('/^9[78]\d{8}$/', $phone)) {
        $_SESSION['error'] = 'Please enter a valid 10-digit Nepal phone number.';
        header('Location: ../register.php'); exit;
    }
    if (strlen($password) < 8) {
        $_SESSION['error'] = 'Password must be at least 8 characters.';
        header('Location: ../register.php'); exit;
    }
    if ($password !== $confirm) {
        $_SESSION['error'] = 'Passwords do not match.';
        header('Location: ../register.php'); exit;
    }

    // Check duplicate email
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $_SESSION['error'] = 'Email is already registered.';
        header('Location: ../register.php'); exit;
    }

    // Check duplicate phone
    $check2 = $conn->prepare("SELECT id FROM users WHERE phone = ?");
    $check2->bind_param("s", $phone);
    $check2->execute();
    if ($check2->get_result()->num_rows > 0) {
        $_SESSION['error'] = 'Phone number is already registered.';
        header('Location: ../register.php'); exit;
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, 'customer')");
    $stmt->bind_param("ssss", $name, $email, $phone, $hashed);

    if ($stmt->execute()) {
        unset($_SESSION['old_input']);
        $_SESSION['success'] = 'Account created successfully! Please log in.';
        header('Location: ../login.php');
    } else {
        $_SESSION['error'] = 'Registration failed. Please try again.';
        header('Location: ../register.php');
    }
    exit;
}

if ($action === 'login') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $_SESSION['old_input'] = ['email' => $email];

    if (!$email || !$password) {
        $_SESSION['error'] = 'Please enter both email and password.';
        header('Location: ../login.php'); exit;
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = 'customer'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if (!$user || !password_verify($password, $user['password'])) {
        $_SESSION['error'] = 'Invalid email or password.';
        header('Location: ../login.php'); exit;
    }

    // Update last login
    $update = $conn->prepare("UPDATE users SET last_login_at = NOW() WHERE id = ?");
    $update->bind_param("i", $user['id']);
    $update->execute();

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    unset($_SESSION['old_input']);

    header('Location: ../../index.php');
    exit;
}

header('Location: ../login.php');
exit;
?>