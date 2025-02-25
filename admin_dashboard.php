<?php
require 'db.php';

// Fetch admin details
$stmt = $conn->prepare("SELECT * FROM admin WHERE User_ID = ?");
$stmt->execute([1]); // Assuming User_ID 1 for demonstration
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
            --primary-light: #fff5f5;
            --white: #ffffff;
            --danger: #ef4444;
            --shadow: 0 4px 20px rgba(107, 25, 80, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: var(--primary-light);
            min-height: 100vh;
        }

        .header {
            background-color: var(--primary);
            padding: 2rem 8rem;
            margin-bottom: 4rem;
        }

        .header h1 {
            color: var(--white);
            font-size: 2rem;
            font-weight: bold;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .card {
            background: var(--white);
            border-radius: 8px;
            padding: 2rem;
            box-shadow: var(--shadow);
        }

        .card h2 {
            color: var(--primary);
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .card-links {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .card-link {
            color: var(--primary);
            text-decoration: none;
            font-size: 1rem;
            transition: opacity 0.3s ease;
        }

        .card-link:hover {
            opacity: 0.8;
        }

        .logout-button {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background-color: var(--danger);
            color: var(--white);
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            transition: opacity 0.3s ease;
        }

        .logout-button:hover {
            opacity: 0.9;
        }

        @media (max-width: 1200px) {
            .dashboard-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .header {
                padding: 2rem;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .container {
                padding: 0 1rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <h1>Admin Dashboard</h1>
    </header>

    <div class="container">
        <div class="dashboard-grid">
            <div class="card">
                <h2>User Management</h2>
                <div class="card-links">
                    <a href="admin_approve_users.php" class="card-link">Approve Users</a>
                    <a href="admin_assign_roles.php" class="card-link">Manage Roles</a>
                </div>
            </div>

            <div class="card">
                <h2>System Monitoring</h2>
                <div class="card-links">
                    <a href="admin_view_activity_logs.php" class="card-link">Activity Logs</a>
                    
                </div>
            </div>

            <div class="card">
                <h2>Communications</h2>
                <div class="card-links">
                    <a href="admin_send_notifications.php" class="card-link">Send Notifications</a>
                    <a href="admin_generate_reports.php" class="card-link">Generate Reports</a>
                </div>
            </div>

            <div class="card">
                <h2>Support</h2>
                <div class="card-links">
                    
                    <a href="admin_faq.php" class="card-link">FAQ Management</a>
                </div>
            </div>
        </div>

        <a href="logout.php" class="logout-button">Logout</a>
    </div>
</body>
</html>