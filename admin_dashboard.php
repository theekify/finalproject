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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            color: #333;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            margin: 10px 0;
        }
        a {
            text-decoration: none;
            color: #007BFF;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Welcome, <?php echo $admin['Admin_Name']; ?>!</h1>
    <p>Email: <?php echo $_SESSION['user_email']; ?></p>
    <p>Role: <?php echo $_SESSION['user_role']; ?></p>

    <h2>Admin Functions</h2>
    <ul>
        <li><a href="admin_approve_users.php">Approve/Reject Users</a></li>
        <li><a href="admin_assign_roles.php">Assign Roles</a></li>
        <li><a href="admin_view_activity_logs.php">View User Activity Logs</a></li>
        <li><a href="admin_send_notifications.php">Send Notifications</a></li>
        <li><a href="admin_generate_reports.php">Generate Reports</a></li>
        <li><a href="admin_profile.php">View/Update Profile</a></li>
    </ul>

    <a href="logout.php">Logout</a>
</body>
</html>