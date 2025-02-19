<?php


require 'db.php';

// Fetch all users
$stmt = $conn->prepare("SELECT User_Email FROM user");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle sending notifications
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    // Send email to all users
    foreach ($users as $user) {
        $to = $user['User_Email'];
        $headers = "From: admin@jobportal.com";
        mail($to, $subject, $message, $headers);
    }

    echo "Notifications sent successfully.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Send Notifications</title>
    <link rel="stylesheet" href="admin_noti.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <h1>Send Notifications</h1>
    <form method="POST">
        Subject: <input type="text" name="subject" required><br>
        Message: <textarea name="message" rows="5" required></textarea><br>
        <button type="submit">Send</button>
    </form>
</body>
</html>