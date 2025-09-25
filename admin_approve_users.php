<?php
require 'db.php';

// Fetch pending users
$stmt = $conn->prepare("SELECT * FROM user WHERE User_Status = 'Approved'");
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --primary: #6b1950;
            --primary-light: #f8f9fc;
            --text-primary: #1a1a1a;
            --text-secondary: #666666;
            --border: #e5e7eb;
            --background: #f1f5f9;
            --white: #ffffff;
            --shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            --radius: 8px;
        }

        body {
            background-color: var(--background);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            padding: 2rem;
        }

        h1 {
            color: var(--primary);
            margin-bottom: 1.5rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        th {
            background-color: var(--primary-light);
            color: var(--primary);
        }

        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: var(--radius);
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn.approve {
            background-color: #4CAF50;
            color: var(--white);
        }

        .btn.reject {
            background-color: #f44336;
            color: var(--white);
        }

        .btn:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Pending Users</h1>
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pending_users as $user): ?>
                <tr>
                    <td><?php echo $user['User_ID']; ?></td>
                    <td><?php echo $user['User_Name']; ?></td>
                    <td><?php echo $user['User_Email']; ?></td>
                    <td><?php echo $user['User_Role']; ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="user_id" value="<?php echo $user['User_ID']; ?>">
                            <button type="submit" name="action" value="approve" class="btn approve">Approve</button>
                            <button type="submit" name="action" value="reject" class="btn reject">Reject</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>