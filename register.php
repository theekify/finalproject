<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash password
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $role = $_POST['role'];

    // Insert into user table
    $stmt = $conn->prepare("INSERT INTO user (User_Name, User_Email, User_Password, User_Phone, User_Address, User_Role) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $email, $password, $phone, $address, $role]);

    echo "Registration successful! Wait for admin approval.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
</head>
<body>
    <h1>Register</h1>
    <form method="POST">
        Name: <input type="text" name="name" required><br>
        Email: <input type="email" name="email" required><br>
        Password: <input type="password" name="password" required><br>
        Phone: <input type="text" name="phone"><br>
        Address: <input type="text" name="address"><br>
        Role: 
        <select name="role" required>
            <option value="Admin">Admin</option>
            <option value="Staff">Staff</option>
            <option value="Agency">Agency</option>
            <option value="Worker">Worker</option>
        </select><br>
        <button type="submit">Register</button>
    </form>
</body>
</html>