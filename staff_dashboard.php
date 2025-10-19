<?php
require 'db.php';

// Fetch staff details
$stmt = $conn->prepare("SELECT * FROM staff WHERE User_ID = ?");
$stmt->execute([1]); // Replace 1 with the actual user ID you want to fetch
$staff = $stmt->fetch(PDO::FETCH_ASSOC);

// Get latest notification
$stmt = $conn->prepare("SELECT * FROM Notifications ORDER BY dateSent DESC LIMIT 1");
$stmt->execute();
$notification = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #6b1950;
            --primary-light: #f8f9fc;
            --text-primary: #333333;
            --text-secondary: #666666;
            --border: #e5e7eb;
            --background: #f5f5f5;
            --white: #ffffff;
            --shadow: 0 1px 2px rgba(0,0,0,0.05);
            --radius: 8px;
            --sidebar-width: 250px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background-color: var(--background);
            min-height: 100vh;
            display: flex;
        }

        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--white);
            color: var(--text-primary);
            padding: 1.5rem;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            overflow-y: auto;
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            margin-bottom: 2rem;
        }

        .sidebar-header h1 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary);
        }

        .sidebar-nav {
            list-style: none;
            flex-grow: 1;
        }

        .sidebar-nav li {
            margin-bottom: 0.5rem;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            color: var(--text-primary);
            text-decoration: none;
            border-radius: var(--radius);
            transition: background-color 0.2s;
        }

        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            background-color: var(--primary-light);
            color: var(--primary);
        }

        .logout-button {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: var(--primary-light);
            color: var(--primary);
            border: none;
            border-radius: var(--radius);
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            margin-top: 1rem;
        }

        .logout-button:hover {
            background: var(--primary);
            color: var(--white);
        }

        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 2rem;
        }

        /* NOTIFICATION BANNER STYLES */
        .notification-banner {
            background: linear-gradient(135deg, #6b1950, #8a2b6b);
            color: white;
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: var(--radius);
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(107, 25, 80, 0.3);
            border-left: 4px solid #ffd700;
        }

        .notification-content {
            flex-grow: 1;
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .notification-content strong {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.2);
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            white-space: nowrap;
        }

        .notification-close {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 50%;
            transition: all 0.2s;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .notification-close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 1.5rem;
            box-shadow: var(--shadow);
        }

        .card h2 {
            color: var(--primary);
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .card-links {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .card-link {
            display: block;
            padding: 0.75rem 0;
            text-decoration: none;
            color: var(--text-primary);
            transition: color 0.2s;
        }

        .card-link:hover {
            color: var(--primary);
        }

        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                height: auto;
                position: static;
                padding: 1rem;
            }

            .main-content {
                margin-left: 0;
                padding: 1rem;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .notification-banner {
                padding: 1rem;
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .notification-content {
                flex-direction: column;
                gap: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <h1>Staff Dashboard</h1>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="staff_dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i>Dashboard</a></li>
                <li><a href="staff_post_course.php"><i class="fas fa-graduation-cap"></i>Post a Course</a></li>
                <li><a href="staff_verify_jobs.php"><i class="fas fa-check-circle"></i>Job Verification</a></li>
                <li><a href="staff_monitor_complaints.php"><i class="fas fa-exclamation-circle"></i>Complaint Management</a></li>
            </ul>
        </nav>
        <a href="logout.php" class="logout-button"><i class="fas fa-sign-out-alt"></i>Logout</a>
    </div>

    <div class="main-content">
        <?php if ($notification): ?>
        <div class="notification-banner">
            <div class="notification-content">
                <strong>
                    <i class="fas fa-bullhorn"></i>
                    Admin Notification
                </strong>
                <span><?php echo htmlspecialchars($notification['message']); ?></span>
            </div>
            <button class="notification-close" onclick="this.parentElement.style.display='none'" title="Dismiss notification">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php endif; ?>

        <div class="dashboard-grid">
            <div class="card">
                <h2>Training Management</h2>
                <div class="card-links">
                    <a href="staff_post_course.php" class="card-link">Post a Course</a>
                    <a href="staff_assign_training.php" class="card-link">Assign Training</a>
                    <a href="staff_issue_certificates.php" class="card-link">Issue Certificates</a>
                </div>
            </div>

            <div class="card">
                <h2>Job Verification</h2>
                <div class="card-links">
                    <a href="staff_verify_jobs.php" class="card-link">Verify Job Postings</a>
                    <a href="staff_track_verifications.php" class="card-link">Track Verifications</a>
                </div>
            </div>

            <div class="card">
                <h2>Complaint Management</h2>
                <div class="card-links">
                    <a href="staff_monitor_complaints.php" class="card-link">Monitor Complaints</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>