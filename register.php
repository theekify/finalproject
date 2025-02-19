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
    $stmt = $conn->prepare("INSERT INTO user (User_Name, User_Email, User_Password, User_Phone, User_Address, User_Role, User_Status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
    $stmt->execute([$name, $email, $password, $phone, $address, $role]);

    // Get the last inserted User_ID
    $user_id = $conn->lastInsertId();

    // Insert into respective table based on role
    if ($role === 'Admin') {
        $stmt = $conn->prepare("INSERT INTO admin (User_ID, Admin_Name, Admin_Email, Admin_Phone, Admin_Status) VALUES (?, ?, ?, ?, 'Active')");
        $stmt->execute([$user_id, $name, $email, $phone]);
    } elseif ($role === 'Staff') {
        $stmt = $conn->prepare("INSERT INTO staff (User_ID, Staff_Name, Staff_Email, Staff_Phone, Staff_Address, Staff_Status) VALUES (?, ?, ?, ?, ?, 'Pending')");
        $stmt->execute([$user_id, $name, $email, $phone, $address]);
    } elseif ($role === 'Agency') {
        $stmt = $conn->prepare("INSERT INTO agency (User_ID, Agency_Name, Agency_Address, License_Number, Approval_Status) VALUES (?, ?, ?, '', 'Pending')");
        $stmt->execute([$user_id, $name, $address]);
    } elseif ($role === 'Worker') {
        $stmt = $conn->prepare("INSERT INTO worker (User_ID, Passport_Number, Visa_Number, Health_Report, Training_Status, Insurance_Status) VALUES (?, '', '', 'Pending', 'In Progress', 'Inactive')");
        $stmt->execute([$user_id]);
    }

    echo "Registration successful! Wait for admin approval.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
    <link rel="stylesheet" href="register.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="registration-container">
        <h1>Register</h1>
        <form method="POST">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            
            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone">
            
            <label for="address">Address:</label>
            <input type="text" id="address" name="address">
            
            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="Admin">Admin</option>
                <option value="Staff">Staff</option>
                <option value="Agency">Agency</option>
                <option value="Worker" selected>Worker</option>
            </select>
            
            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>