<?php
session_start();

// Check if the user is logged in and is a worker
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Worker') {
    header('Location: worker_login.php');
    exit();
}

require 'db.php';

// Handle complaint submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = $_POST['description'];

    // Insert complaint into the database
    $stmt = $conn->prepare("INSERT INTO complaints (Worker_ID, Description, Status) VALUES (?, ?, 'Pending')");
    $stmt->execute([$_SESSION['user_id'], $description]);

    echo "Complaint submitted successfully! Wait for staff resolution.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Complaint</title>
    <link rel="stylesheet" type="text/css" href="worker_complaint.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <h1>Submit Complaint</h1>
    <form method="POST">
        Description: <textarea name="description" rows="5" required></textarea><br>
        <button type="submit">Submit</button>
    </form>
</body>
</html>