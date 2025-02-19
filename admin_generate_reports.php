<?php

require 'db.php';

// Fetch system activity data
$stmt = $conn->prepare("SELECT * FROM user");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Generate Reports</title>
    <link rel="stylesheet" href="admin_report.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <h1>System Activity Report</h1>
    <table border="1">
        <tr>
            <th>User ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
        </tr>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?php echo $user['User_ID']; ?></td>
            <td><?php echo $user['User_Name']; ?></td>
            <td><?php echo $user['User_Email']; ?></td>
            <td><?php echo $user['User_Role']; ?></td>
            <td><?php echo $user['User_Status']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
