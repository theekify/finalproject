<?php


require 'db.php';

// Fetch all users
$stmt = $conn->prepare("SELECT * FROM user");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle role assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $role = $_POST['role'];

    // Update user role
    $stmt = $conn->prepare("UPDATE user SET User_Role = ? WHERE User_ID = ?");
    $stmt->execute([$role, $user_id]);
    echo "Role assigned successfully.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Assign Roles</title>
</head>
<body>
    <h1>Assign Roles</h1>
    <table border="1">
        <tr>
            <th>User ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Current Role</th>
            <th>Assign Role</th>
        </tr>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?php echo $user['User_ID']; ?></td>
            <td><?php echo $user['User_Name']; ?></td>
            <td><?php echo $user['User_Email']; ?></td>
            <td><?php echo $user['User_Role']; ?></td>
            <td>
                <form method="POST">
                    <input type="hidden" name="user_id" value="<?php echo $user['User_ID']; ?>">
                    <select name="role">
                        <option value="Admin">Admin</option>
                        <option value="Staff">Staff</option>
                        <option value="Agency">Agency</option>
                        <option value="Worker">Worker</option>
                    </select>
                    <button type="submit">Assign</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>