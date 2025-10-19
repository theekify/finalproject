<?php
require 'db.php';

// Fetch admin details
$stmt = $conn->prepare("SELECT * FROM admin WHERE User_ID = ?");
$stmt->execute([1]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['send_notification'])) {
        $message = trim($_POST['message']);
        
        if (!empty($message)) {
            $stmt = $conn->prepare("INSERT INTO Notifications (message) VALUES (?)");
            $stmt->execute([$message]);
            $success = "Notification sent successfully!";
        } else {
            $error = "Please enter a message";
        }
    }
    
    if (isset($_POST['delete_notification'])) {
        $notification_id = $_POST['notification_id'];
        $stmt = $conn->prepare("DELETE FROM Notifications WHERE notificationId = ?");
        $stmt->execute([$notification_id]);
        $success = "Notification deleted successfully!";
    }
}

// Get all notifications
$stmt = $conn->prepare("SELECT * FROM Notifications ORDER BY dateSent DESC");
$stmt->execute();
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get latest notification
$stmt = $conn->prepare("SELECT * FROM Notifications ORDER BY dateSent DESC LIMIT 1");
$stmt->execute();
$current_notification = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Notifications - Admin Dashboard</title>
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

        /* Original Sidebar Styles */
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

        /* Simple Content Styles */
        .notification-container {
            max-width: 800px;
        }

        .page-title {
            color: var(--text-primary);
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 1.5rem;
            border: 1px solid var(--border);
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
            font-weight: 500;
        }

        .form-textarea {
            width: 100%;
            padding: 1rem;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            font-size: 1rem;
            resize: vertical;
            min-height: 120px;
        }

        .form-textarea:focus {
            outline: none;
            border-color: var(--primary);
        }

        .btn {
            padding: 0.75rem 1.5rem;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: var(--radius);
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn:hover {
            background: #5a1540;
        }

        .btn-danger {
            background: #dc2626;
        }

        .btn-danger:hover {
            background: #b91c1c;
        }

        .alert {
            padding: 1rem;
            border-radius: var(--radius);
            margin-bottom: 1rem;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .notification-item {
            padding: 1rem 0;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-message {
            margin-bottom: 0.5rem;
        }

        .notification-meta {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: var(--text-secondary);
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

            .notification-item {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo">Admin Dashboard</div>
        <nav class="nav-menu">
            <a href="admin_dashboard.php" class="nav-item">
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
            <a href="admin_send_notifications.php" class="nav-item active">
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

        <div class="notification-container">
            <h1 class="page-title">Send Notifications</h1>

            <?php if (isset($success)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <form method="POST" action="">
                    <div class="form-group">
                        <label class="form-label" for="message">Notification Message</label>
                        <textarea 
                            class="form-textarea" 
                            id="message" 
                            name="message" 
                            placeholder="Type your notification message here..."
                            required
                            maxlength="255"
                        ></textarea>
                    </div>
                    <button type="submit" name="send_notification" class="btn">
                        <i class="fas fa-paper-plane"></i> Send to All Users
                    </button>
                </form>
            </div>

            <?php if ($current_notification): ?>
                <div class="card">
                    <h3 style="color: var(--primary); margin-bottom: 1rem;">Current Notification</h3>
                    <p style="margin-bottom: 0.5rem;"><?php echo htmlspecialchars($current_notification['message']); ?></p>
                    <small style="color: var(--text-secondary);">
                        Sent: <?php echo date('F j, Y g:i A', strtotime($current_notification['dateSent'])); ?>
                    </small>
                </div>
            <?php endif; ?>

            <div class="card">
                <h3 style="color: var(--primary); margin-bottom: 1rem;">Notification History</h3>
                
                <?php if (count($notifications) > 0): ?>
                    <?php foreach ($notifications as $notification): ?>
                    <div class="notification-item">
                        <div>
                            <div class="notification-message">
                                <?php echo htmlspecialchars($notification['message']); ?>
                            </div>
                            <div class="notification-meta">
                                <?php echo date('M j, Y g:i A', strtotime($notification['dateSent'])); ?> | ID: <?php echo $notification['notificationId']; ?>
                            </div>
                        </div>
                        <form method="POST" action="" onsubmit="return confirm('Are you sure you want to delete this notification?');">
                            <input type="hidden" name="notification_id" value="<?php echo $notification['notificationId']; ?>">
                            <button type="submit" name="delete_notification" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <p>No notifications sent yet</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>