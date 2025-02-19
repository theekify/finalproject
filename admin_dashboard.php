<?php
require 'db.php';

// Fetch admin details
$stmt = $conn->prepare("SELECT * FROM admin WHERE User_ID = ?");
$stmt->execute([1]); // Assuming a default user ID for demonstration
$admin = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        :root {
            --primary: #6b1950;
            --primary-light: #fdf4f9;
            --primary-dark: #4a1237;
            --text-dark: #2d1422;
            --text-light: #666666;
            --white: #ffffff;
            --border: #e5e7eb;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --shadow: 0 4px 20px rgba(107, 25, 80, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            min-height: 100vh;
            background-color: var(--primary-light);
            color: var(--text-dark);
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background-color: var(--primary);
            color: var(--white);
            padding: 2rem;
        }

        .main-content {
            flex-grow: 1;
            padding: 2rem;
        }

        h1, h2 {
            color: var(--primary);
            margin-bottom: 1rem;
        }

        h1 {
            font-size: 2rem;
        }

        h2 {
            font-size: 1.5rem;
            margin-top: 2rem;
        }

        p {
            margin-bottom: 0.5rem;
        }

        .admin-info {
            margin-bottom: 2rem;
        }

        .admin-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .action-card {
            background-color: var(--white);
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .action-card:hover {
            transform: translateY(-5px);
        }

        .action-card h3 {
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .btn {
            display: inline-block;
            background-color: var(--primary);
            color: var(--white);
            padding: 0.5rem 1rem;
            border-radius: 5px;
            text-decoration: none;
            transition: var(--transition);
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn:hover {
            background-color: var(--primary-dark);
        }

        .btn-secondary {
            background-color: var(--text-light);
        }

        .btn-secondary:hover {
            background-color: var(--text-dark);
        }

        .sidebar-buttons {
            margin-top: 2rem;
        }

        .sidebar-buttons .btn {
            display: block;
            width: 100%;
            margin-bottom: 1rem;
            text-align: center;
        }

        @media (max-width: 768px) {
            .dashboard-container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
            }

            .main-content {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <h2>Admin Dashboard</h2>
            <div class="admin-info">
                <p>Welcome, <?php echo $admin['Admin_Name']; ?>!</p>
                <p>Email: admin@example.com</p> <!-- Placeholder email -->
                <p>Role: Admin</p> <!-- Placeholder role -->
            </div>
            <div class="sidebar-buttons">
                <a href="admin_profile.php" class="btn">View Profile</a>
                <a href="logout.php" class="btn btn-secondary">Logout</a>
            </div>
        </div>
        <div class="main-content">
            <h1>Admin Functions</h1>
            <div class="admin-actions">
                <div class="action-card">
                    <h3>User Management</h3>
                    <p>Approve, reject, and assign roles to users.</p>
                    <a href="admin_approve_users.php" class="btn">Manage Users</a>
                </div>
                <div class="action-card">
                    <h3>Assign Roles</h3>
                    <p>Assign roles to users.</p>
                    <a href="admin_assign_roles.php " class="btn">Manage Users</a>
                </div>
                <div class="action-card">
                    <h3>Activity Logs</h3>
                    <p>View and analyze user activity logs.</p>
                    <a href="admin_view_activity_logs.php" class="btn">View Logs</a>
                </div>
                <div class="action-card">
                    <h3>Notifications</h3>
                    <p>Send notifications to users and staff.</p>
                    <a href="admin_send_notifications.php" class="btn">Send Notifications</a>
                </div>
                <div class="action-card">
                    <h3>Reports</h3>
                    <p>Generate and view system reports.</p>
                    <a href="admin_generate_reports.php" class="btn">Generate Reports</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
