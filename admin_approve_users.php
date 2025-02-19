<?php


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
        // Move user to their respective table based on role
        $stmt = $conn->prepare("SELECT * FROM user WHERE User_ID = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $role = $user['User_Role'];
            $status = 'Approved';

            // Update user status
            $stmt = $conn->prepare("UPDATE user SET User_Status = ? WHERE User_ID = ?");
            $stmt->execute([$status, $user_id]);

            // Insert into respective table
            if ($role === 'Worker') {
                $stmt = $conn->prepare("INSERT INTO worker (User_ID, Passport_Number, Visa_Number, Health_Report, Training_Status, Insurance_Status) VALUES (?, '', '', 'Pending', 'In Progress', 'Inactive')");
                $stmt->execute([$user_id]);
            } elseif ($role === 'Admin') {
                $stmt = $conn->prepare("INSERT INTO admin (User_ID, Admin_Name, Admin_Email, Admin_Phone, Admin_Status) VALUES (?, ?, ?, ?, 'Active')");
                $stmt->execute([$user_id, $user['User_Name'], $user['User_Email'], $user['User_Phone']]);
            } elseif ($role === 'Staff') {
                $stmt = $conn->prepare("INSERT INTO staff (User_ID, Staff_Name, Staff_Email, Staff_Phone, Staff_Address, Staff_Status) VALUES (?, ?, ?, ?, ?, 'Approved')");
                $stmt->execute([$user_id, $user['User_Name'], $user['User_Email'], $user['User_Phone'], $user['User_Address']]);
            } elseif ($role === 'Agency') {
                $stmt = $conn->prepare("INSERT INTO agency (User_ID, Agency_Name, Agency_Address, License_Number, Approval_Status) VALUES (?, ?, ?, ?, 'Approved')");
                $stmt->execute([$user_id, $user['User_Name'], $user['User_Address'], 'LICENSE123']); // Replace with actual license number
            }

            echo "User approved and moved to $role table.";
        }
    } elseif ($action === 'reject') {
        // Delete user from the user table
        $stmt = $conn->prepare("DELETE FROM user WHERE User_ID = ?");
        $stmt->execute([$user_id]);
        echo "User rejected and deleted.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Approve/Reject Users</title>
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