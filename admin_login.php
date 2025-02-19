<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch admin from database
    $stmt = $conn->prepare("SELECT user.* FROM user 
                            JOIN admin ON user.User_ID = admin.User_ID 
                            WHERE user.User_Email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['User_Password'])) {
       
            // Redirect to admin dashboard
            header('Location: admin_dashboard.php');
        } else {
            echo "Your account is pending approval.";
        }
    } else {
        echo "Invalid email or password.";
    }

?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link rel="stylesheet" href="admin_login.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <h1>Admin Login</h1>
    <form method="POST">
        Email: <input type="email" name="email" required><br>
        Password: <input type="password" name="password" required><br>
        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register here</a>.</p>
</body>
</html>
