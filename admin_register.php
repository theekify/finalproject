<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash password
    $phone = $_POST['phone'];

    // Check if email already exists
    $stmt = $conn->prepare("SELECT COUNT(*) FROM user WHERE User_Email = ?");
    $stmt->execute([$email]);
    $emailExists = $stmt->fetchColumn();

    if ($emailExists) {
        echo "Email already exists. Please use a different email.";
    } else {
        // Insert into user table
        $stmt = $conn->prepare("INSERT INTO user (User_Name, User_Email, User_Password, User_Phone, User_Role, User_Status) VALUES (?, ?, ?, ?, 'Admin', 'Approved')");
        $stmt->execute([$name, $email, $password, $phone]);

        // Get the last inserted User_ID
        $user_id = $conn->lastInsertId();

        // Insert into admin table
        $stmt = $conn->prepare("INSERT INTO admin (User_ID, Admin_Name, Admin_Email, Admin_Phone, Admin_Status) VALUES (?, ?, ?, ?, 'Active')");
        $stmt->execute([$user_id, $name, $email, $phone]);

        echo "Registration successful! You can now log in.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Registration</title>
    <link rel="stylesheet" href="register.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="registration-container">
        <h1>Admin Register</h1>
        <form method="POST">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            
            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone">
            
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="admin_login.php">Login here</a>.</p>
    </div>
</body>
</html>