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
    $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
    mysqli_stmt_bind_param($check, "s", $email);
    mysqli_stmt_execute($check);
    if (mysqli_stmt_get_result($check)->num_rows > 0) {
        $_SESSION['error'] = 'Email is already registered.';
        header('Location: ../register.php'); exit;
    }

    // Check duplicate phone
    $check2 = mysqli_prepare($conn, "SELECT id FROM users WHERE phone = ?");
    mysqli_stmt_bind_param($check2, "s", $phone);
    mysqli_stmt_execute($check2);
    if (mysqli_stmt_get_result($check2)->num_rows > 0) {
        $_SESSION['error'] = 'Phone number is already registered.';
        header('Location: ../register.php'); exit;
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($conn, "INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, 'customer')");
    mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $phone, $hashed);

    if (mysqli_stmt_execute($stmt)) {
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

    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ? AND role = 'customer'");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$user || !password_verify($password, $user['password'])) {
        $_SESSION['error'] = 'Invalid email or password.';
        header('Location: ../login.php'); exit;
    }

    // Update last login
    $update = mysqli_prepare($conn, "UPDATE users SET last_login_at = NOW() WHERE id = ?");
    mysqli_stmt_bind_param($update, "i", $user['id']);
    mysqli_stmt_execute($update);

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