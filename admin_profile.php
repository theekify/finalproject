<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    header('Location: admin_login.php');
    exit();
}

require 'db.php';

// Fetch admin details
$stmt = $conn->prepare("SELECT * FROM admin WHERE User_ID = ?");
$stmt->execute([$_SESSION['user_id']]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Update admin profile
    $stmt = $conn->prepare("UPDATE admin SET Admin_Name = ?, Admin_Email = ?, Admin_Phone = ? WHERE User_ID = ?");
    $stmt->execute([$name, $email, $phone, $_SESSION['user_id']]);

    echo "Profile updated successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .profile-form {
            max-width: 400px;
            margin: 0 auto;
        }
        .profile-form input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .profile-form button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .profile-form button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Admin Profile</h1>
    <div class="profile-form">
        <form method="POST">
            Name: <input type="text" name="name" value="<?php echo $admin['Admin_Name']; ?>" required><br>
            Email: <input type="email" name="email" value="<?php echo $admin['Admin_Email']; ?>" required><br>
            Phone: <input type="text" name="phone" value="<?php echo $admin['Admin_Phone']; ?>"><br>
            <button type="submit">Save Changes</button>
        </form>
    </div>
    <a href="admin_dashboard.php">Back to Dashboard</a>
</body>
</html>