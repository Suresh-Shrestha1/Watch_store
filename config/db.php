<?php
$host     = "localhost";
$username = "root";
$password = "";
$database = "watch_store";

// new mysqli() creates a connection object
$conn = new mysqli($host, $username, $password, $database);

// Check connection failed
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>