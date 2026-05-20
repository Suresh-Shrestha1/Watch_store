<?php
session_start();

// Block direct access — only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit();
}

require_once '../../config/db.php';

// Get form data safely
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// --- VALIDATION ---
if (empty($email) || empty($password)) {
    $_SESSION['login_error'] = 'Please fill in all fields.';
    $_SESSION['login_email'] = $email;
    header('Location: ../index.php');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['login_error'] = 'Please enter a valid email address.';
    $_SESSION['login_email'] = $email;
    header('Location: ../index.php');
    exit();
}

// prepare() creates safe query — ? is placeholder for email value
$stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ? AND role = 'admin' LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $admin = $result->fetch_assoc();

    if (password_verify($password, $admin['password'])) {

        // Prevent session hijacking
        session_regenerate_id(true);

        // Store admin data in session
        $_SESSION['admin_id']    = $admin['id'];
        $_SESSION['admin_name']  = $admin['name'];
        $_SESSION['admin_email'] = $admin['email'];
        $_SESSION['admin_role']  = $admin['role'];

        // Update last login time
        $updateStmt = $conn->prepare("UPDATE users SET last_login_at = NOW() WHERE id = ?");
        $updateStmt->bind_param("i", $admin['id']);
        $updateStmt->execute();
        $updateStmt->close();

        $stmt->close();
        $conn->close();

        header('Location: ../dashboard.php');
        exit();

    } else {

        // Wrong password
        $_SESSION['login_error'] = 'Invalid email or password.';
        $_SESSION['login_email'] = $email;
    }

} else {
    // No admin found with that email
    $_SESSION['login_error'] = 'Invalid email or password.';
    $_SESSION['login_email'] = $email;
}

$stmt->close();
$conn->close();

header('Location: ../index.php');
exit();
?>