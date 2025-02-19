<?php
session_start();

// Check if the user is logged in and is a worker
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Worker') {
    header('Location: worker_login.php');
    exit();
}

require 'db.php';

// Handle document upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $passport_number = $_POST['passport_number'];
    $visa_number = $_POST['visa_number'];

    // Insert document into the database
    $stmt = $conn->prepare("INSERT INTO document (Worker_ID, Passport_Number, Visa_Number, Status) VALUES (?, ?, ?, 'Pending')");
    $stmt->execute([$_SESSION['user_id'], $passport_number, $visa_number]);

    echo "Documents uploaded successfully! Wait for staff verification.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Documents</title>
</head>
<body>
    <h1>Upload Documents</h1>
    <form method="POST">
        Passport Number: <input type="text" name="passport_number" required><br>
        Visa Number: <input type="text" name="visa_number" required><br>
        <button type="submit">Upload</button>
    </form>
</body>
</html>