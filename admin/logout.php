<?php
session_start();

// Clear all session variables
session_unset();

// Destroy the session completely
session_destroy();

// Go back to login page
header('Location: index.php');
exit();
?>