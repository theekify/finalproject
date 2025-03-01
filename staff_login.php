<?php
session_start();

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch staff from database
    $stmt = $conn->prepare("SELECT user.* FROM user 
                            JOIN staff ON user.User_ID = staff.User_ID 
                            WHERE user.User_Email = ?");
    $stmt->execute([$email]);
    $staff = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($staff && password_verify($password, $staff['User_Password'])) {
        // Set session variables
        $_SESSION['user_id'] = $staff['User_ID'];
        $_SESSION['user_role'] = $staff['User_Role'];
        $_SESSION['user_email'] = $staff['User_Email'];

        // Redirect to staff dashboard
        header('Location: staff_dashboard.php');
    } else {
        echo "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Staff Login</title>
    <link rel="stylesheet" href="staff_login.css">
</head>
<body>
    <h1>Staff Login</h1>
    <form method="POST">
        Email: <input type="email" name="email" required><br>
        Password: <input type="password" name="password" required><br>
        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register here</a>.</p>
</body>
</html>