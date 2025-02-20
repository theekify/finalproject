<?php

require 'db.php';

// Fetch admin details
$stmt = $conn->prepare("SELECT * FROM admin WHERE User_ID = ?");
$stmt->execute([1]); // Assuming User_ID 1 for demonstration
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
    <p>Email: admin@example.com</p> <!-- Hardcoded email for demonstration -->
    <p>Role: Admin</p> <!-- Hardcoded role for demonstration -->

    <h2>Admin Functions</h2>
    <ul>
        <li><a href="admin_view_activity_logs.php">View User Activity Logs</a></li>
        <li><a href="admin_send_notifications.php">Send Notifications</a></li>
        <li><a href="admin_generate_reports.php">Generate Reports</a></li>
    </ul>

    <a href="logout.php">Logout</a>
</body>
</html>
