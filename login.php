<?php
session_start();

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch user from database
    $stmt = $conn->prepare("SELECT * FROM user WHERE User_Email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['User_Password'])) {
        // Set session variables
        $_SESSION['user_id'] = $user['User_ID'];
        $_SESSION['user_role'] = $user['User_Role'];
        $_SESSION['user_email'] = $user['User_Email'];
        $_SESSION['user_status'] = $user['User_Status']; // Store user status in session

        // Redirect based on role
        switch ($user['User_Role']) {
            case 'Admin':
                header('Location: admin_dashboard.php');
                break;
            case 'Staff':
                header('Location: staff_dashboard.php');
                break;
            case 'Agency':
                header('Location: agency_dashboard.php');
                break;
            case 'Worker':
                header('Location: worker_dashboard.php');
                break;
        }
    } else {
        echo "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <form method="POST">
        Email: <input type="email" name="email" required><br>
        Password: <input type="password" name="password" required><br>
        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register here</a>.</p>
</body>
</html>