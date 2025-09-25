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

        .role-select {
            padding: 0.5rem;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            font-size: 0.875rem;
        }

        .btn.assign {
            padding: 0.5rem 1rem;
            background-color: var(--primary);
            color: var(--white);
            border: none;
            border-radius: var(--radius);
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn.assign:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Assign Roles</h1>
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Current Role</th>
                    <th>Assign Role</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['User_ID']; ?></td>
                    <td><?php echo $user['User_Name']; ?></td>
                    <td><?php echo $user['User_Email']; ?></td>
                    <td><?php echo $user['User_Role']; ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="user_id" value="<?php echo $user['User_ID']; ?>">
                            <select name="role" class="role-select">
                                <option value="Admin">Admin</option>
                                <option value="Staff">Staff</option>
                                <option value="Agency">Agency</option>
                                <option value="Worker">Worker</option>
                            </select>
                            <button type="submit" class="btn assign">Assign</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>