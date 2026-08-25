<?php
// ============================================
// Database Connection
// XAMPP defaults: user "root", empty password
// ============================================

$host = "localhost";
$user = "root";
$password = "";
$database = "donation_system";

$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Uncomment the line below temporarily if you want to confirm it works
// echo "Connected successfully to donation_system database.";
?>