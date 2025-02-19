<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    header('Location: admin_login.php');
    exit();
}

require 'db.php';

// Fetch pending users
$stmt = $conn->prepare("SELECT * FROM user WHERE User_Status = 'Pending'");
$stmt->execute();
$pending_users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle approval/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $action = $_POST['action']; // 'approve' or 'reject'

    if ($action === 'approve') {
        // Update user status to 'Approved'
        $stmt = $conn->prepare("UPDATE user SET User_Status = 'Approved' WHERE User_ID = ?");
        $stmt->execute([$user_id]);

        echo "User approved successfully!";
    } elseif ($action === 'reject') {
        // Delete user from the user table and their respective table
        $stmt = $conn->prepare("DELETE FROM user WHERE User_ID = ?");
        $stmt->execute([$user_id]);

        // Delete from respective table based on role
        $stmt = $conn->prepare("SELECT User_Role FROM user WHERE User_ID = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $role = $user['User_Role'];
            if ($role === 'Admin') {
                $stmt = $conn->prepare("DELETE FROM admin WHERE User_ID = ?");
            } elseif ($role === 'Staff') {
                $stmt = $conn->prepare("DELETE FROM staff WHERE User_ID = ?");
            } elseif ($role === 'Agency') {
                $stmt = $conn->prepare("DELETE FROM agency WHERE User_ID = ?");
            } elseif ($role === 'Worker') {
                $stmt = $conn->prepare("DELETE FROM worker WHERE User_ID = ?");
            }
            $stmt->execute([$user_id]);
        }

        echo "User rejected and deleted.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Approve/Reject Users</title>
    <link rel="stylesheet" type="text/css" href="admin_approve.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <h1>Pending Users</h1>
    <table border="1">
        <tr>
            <th>User ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Action</th>
        </tr>
        <?php foreach ($pending_users as $user): ?>
        <tr>
            <td><?php echo $user['User_ID']; ?></td>
            <td><?php echo $user['User_Name']; ?></td>
            <td><?php echo $user['User_Email']; ?></td>
            <td><?php echo $user['User_Role']; ?></td>
            <td>
                <form method="POST">
                    <input type="hidden" name="user_id" value="<?php echo $user['User_ID']; ?>">
                    <button type="submit" name="action" value="approve">Approve</button>
                    <button type="submit" name="action" value="reject">Reject</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>