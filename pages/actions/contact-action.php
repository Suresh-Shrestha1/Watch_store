<?php
session_start();
require_once '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /pages/contact.php');
    exit;
}

$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$phone   = trim($_POST['phone'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

$oldQuery = '&name=' . urlencode($name) . '&email=' . urlencode($email) . '&phone=' . urlencode($phone) . '&subject=' . urlencode($subject) . '&message=' . urlencode($message);

// Validate required fields
if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    header('Location: /pages/contact.php?error=required' . $oldQuery);
    exit;
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: /pages/contact.php?error=required' . $oldQuery);
    exit;
}

// Validate message length
if (strlen($message) < 10) {
    header('Location: /pages/contact.php?error=required' . $oldQuery);
    exit;
}

// Get user_id if logged in
$userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

// Insert into messages table
$stmt = $conn->prepare("INSERT INTO messages (user_id, name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssss", $userId, $name, $email, $phone, $subject, $message);

if ($stmt->execute()) {
    $stmt->close();
    header('Location: /pages/contact.php?success=sent');
    exit;
}

$stmt->close();
header('Location: /pages/contact.php?error=required' . $oldQuery);
exit;