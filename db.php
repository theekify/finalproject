<?php
// Database configuration
$host = 'localhost';
$dbname = 'JobPortal';
$username = 'root'; // Replace with your database username
$password = 'orypubit'; // Replace with your database password

// Create connection
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>