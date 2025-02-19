<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch worker from database
    $stmt = $conn->prepare("SELECT user.* FROM user 
                            JOIN worker ON user.User_ID = worker.User_ID 
                            WHERE user.User_Email = ?");
    $stmt->execute([$email]);
    $worker = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($worker && password_verify($password, $worker['User_Password'])) {
        if ($worker['User_Status'] === 'Approved') {
            // Set session variables
            $_SESSION['user_id'] = $worker['User_ID'];
            $_SESSION['user_role'] = $worker['User_Role'];
            $_SESSION['user_email'] = $worker['User_Email'];

            // Redirect to worker dashboard
            header('Location: worker_dashboard.php');
        } else {
            echo "Your account is pending approval.";
        }
    } else {
        echo "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Worker Login</title>
</head>
<body>
    <h1>Worker Login</h1>
    <form method="POST">
        Email: <input type="email" name="email" required><br>
        Password: <input type="password" name="password" required><br>
        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register here</a>.</p>
</body>
</html>