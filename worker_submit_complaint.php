<?php
session_start();
require 'db.php';

// Ensure user_id is set
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Fetch worker details using user_id
$stmt = $conn->prepare("SELECT * FROM worker WHERE User_ID = ?");
$stmt->execute([$user_id]);
$worker = $stmt->fetch(PDO::FETCH_ASSOC);
$worker_id = $worker ? $worker['Worker_ID'] : null;

// Handle complaint submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = $_POST['description'];

    // Insert complaint into the database
    $stmt = $conn->prepare("INSERT INTO complaints (Worker_ID, Description, Status) VALUES (?, ?, 'Pending')");
    $stmt->execute([$worker_id, $description]);

    echo "Complaint submitted successfully! Wait for staff resolution.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Complaint</title>
    <link rel="stylesheet" type="text/css" href="worker_complaints.css">
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