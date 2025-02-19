<?php


require 'db.php';

// Fetch user activity logs (e.g., account creation dates)
$stmt = $conn->prepare("SELECT User_ID, User_Name, User_Email, User_Role, User_Status, User_Address, User_Phone FROM user");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Activity Logs</title>
    <link rel="stylesheet" href="admin_user_activity.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <h1>User Activity Logs</h1>
    <table border="1">
        <tr>
            <th>User ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Address</th>
            <th>Phone</th>
        </tr>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?php echo $user['User_ID']; ?></td>
            <td><?php echo $user['User_Name']; ?></td>
            <td><?php echo $user['User_Email']; ?></td>
            <td><?php echo $user['User_Role']; ?></td>
            <td><?php echo $user['User_Status']; ?></td>
            <td><?php echo $user['User_Address']; ?></td>
            <td><?php echo $user['User_Phone']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>