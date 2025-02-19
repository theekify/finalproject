<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch agency from database
    $stmt = $conn->prepare("SELECT user.* FROM user 
                            JOIN agency ON user.User_ID = agency.User_ID 
                            WHERE user.User_Email = ?");
    $stmt->execute([$email]);
    $agency = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($agency && password_verify($password, $agency['User_Password'])) {
        if ($agency['User_Status'] === 'Approved') {
            // Set session variables
            $_SESSION['user_id'] = $agency['User_ID'];
            $_SESSION['user_role'] = $agency['User_Role'];
            $_SESSION['user_email'] = $agency['User_Email'];

            // Redirect to agency dashboard
            header('Location: agency_dashboard.php');
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
    <title>Agency Login</title>
</head>
<body>
    <h1>Agency Login</h1>
    <form method="POST">
        Email: <input type="email" name="email" required><br>
        Password: <input type="password" name="password" required><br>
        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register here</a>.</p>
</body>
</html>