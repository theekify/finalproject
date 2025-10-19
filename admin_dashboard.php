<?php
require 'db.php';

// Fetch admin details
$stmt = $conn->prepare("SELECT * FROM admin WHERE User_ID = ?");
$stmt->execute([1]);
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
            --primary-light: #f8f9fc;
            --text-primary: #1a1a1a;
            --text-secondary: #666666;
            --border: #e5e7eb;
            --background: #f1f5f9;
            --white: #ffffff;
            --shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            --radius: 8px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background-color: var(--background);
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: var(--white);
            border-right: 1px solid var(--border);
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
        }

        .logo {
            color: var(--primary);
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 2rem;
        }

        .nav-menu {
            flex-grow: 1;
        }

        .nav-item {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            color: var(--text-secondary);
            text-decoration: none;
            border-radius: var(--radius);
            margin-bottom: 0.5rem;
            transition: all 0.2s;
        }

        .nav-item:hover, .nav-item.active {
            background: var(--primary-light);
            color: var(--primary);
        }

        .nav-item i {
            margin-right: 0.75rem;
            font-size: 1rem;
        }

        .logout-button {
            padding: 0.75rem;
            background: var(--primary-light);
            color: var(--primary);
            border: none;
            border-radius: var(--radius);
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
            text-decoration: none;
        }

        .logout-button:hover {
            background: var(--primary);
            color: var(--white);
        }

        .main-content {
            flex-grow: 1;
            padding: 2rem;
            overflow-y: auto;
        }

        .top-bar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-bottom: 2rem;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-weight: 600;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 1.5rem;
            border: 1px solid var(--border);
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            margin-bottom: 1rem;
        }

        .card-title {
            color: var(--text-primary);
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .card-subtitle {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .menu-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            text-decoration: none;
            color: var(--text-primary);
            border-radius: var(--radius);
            transition: all 0.2s;
        }

        .menu-item:hover {
            background: var(--primary-light);
            color: var(--primary);
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 500;
            background: var(--primary-light);
            color: var(--primary);
            margin-left: auto;
        }

        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                border-right: none;
                border-bottom: 1px solid var(--border);
            }

            .main-content {
                padding: 1rem;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <!-- Add FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo">Admin Dashboard</div>
        <nav class="nav-menu">
            <a href="#" class="nav-item active">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="admin_approve_users.php" class="nav-item">
                <i class="fas fa-user-check"></i> Approve Users
            </a>
            <a href="admin_assign_roles.php" class="nav-item">
                <i class="fas fa-users-cog"></i> Manage Roles
            </a>
            <a href="admin_view_activity_logs.php" class="nav-item">
                <i class="fas fa-history"></i> Activity Logs
            </a>
            
            <a href="admin_send_notifications.php" class="nav-item">
                <i class="fas fa-bell"></i> Notifications
            </a>
            <a href="admin_generate_reports.php" class="nav-item">
                <i class="fas fa-chart-line"></i> Reports
            </a>
            <a href="admin_faq.php" class="nav-item">
                <i class="fas fa-question-circle"></i> FAQ
            </a>
        </nav>
        <a href="logout.php" class="logout-button">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <div class="user-profile">
                <div class="user-avatar">
                    <!-- User avatar content -->
                </div>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">User Management</div>
                    <div class="card-subtitle">Manage system users and roles</div>
                </div>
                <div class="menu-list">
                    <a href="admin_approve_users.php" class="menu-item">
                        Approve Users
                        <span class="status-badge">New</span>
                    </a>
                    <a href="admin_assign_roles.php" class="menu-item">Manage Roles</a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-title">System Monitoring</div>
                    <div class="card-subtitle">Track system activities and logs</div>
                </div>
                <div class="menu-list">
                    <a href="admin_view_activity_logs.php" class="menu-item">Activity Logs</a>
                    
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-title">Communications</div>
                    <div class="card-subtitle">Manage notifications and reports</div>
                </div>
                <div class="menu-list">
                    <a href="admin_send_notifications.php" class="menu-item">Send Notifications</a>
                    <a href="admin_generate_reports.php" class="menu-item">Generate Reports</a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-title">Support</div>
                    <div class="card-subtitle">Manage help and documentation</div>
                </div>
                <div class="menu-list">
                    <a href="admin_faq.php" class="menu-item">FAQ Management</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>