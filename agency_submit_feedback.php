<?php


require 'db.php';

// Handle feedback submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $feedback = $_POST['feedback'];

    // Insert feedback into the database
    $stmt = $conn->prepare("INSERT INTO feedback (Agency_ID, Feedback) VALUES (?, ?)");
    $stmt->execute([$_SESSION['user_id'], $feedback]);

    echo "Feedback submitted successfully.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Feedback</title>
</head>
<body>
    <h1>Submit Feedback</h1>
    <form method="POST">
        Feedback: <textarea name="feedback" rows="5" required></textarea><br>
        <button type="submit">Submit</button>
    </form>
</body>
</html>