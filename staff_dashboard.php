<?php
session_start();

// Check if the user is logged in and is a staff member
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Staff') {
    header('Location: staff_login.php');
    exit();
}

require 'db.php';

// Fetch staff details
$stmt = $conn->prepare("SELECT * FROM staff WHERE User_ID = ?");
$stmt->execute([$_SESSION['user_id']]);
$staff = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Staff Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            color: #333;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            margin: 10px 0;
        }
        a {
            text-decoration: none;
            color: #007BFF;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Welcome, <?php echo $staff['Staff_Name']; ?>!</h1>
    <p>Email: <?php echo $_SESSION['user_email']; ?></p>
    <p>Role: <?php echo $_SESSION['user_role']; ?></p>

    <h2>Staff Functions</h2>
    <ul>
        <li><a href="staff_verify_documents.php">Verify Documents</a></li>
        <li><a href="staff_approve_health_reports.php">Approve/Reject Health Reports</a></li>
        <li><a href="staff_assign_training.php">Assign Workers to Training</a></li>
        <li><a href="staff_issue_certificates.php">Issue Certificates</a></li>
        <li><a href="staff_monitor_complaints.php">Monitor Complaints</a></li>
        <li><a href="staff_profile.php">View/Update Profile</a></li>
    </ul>

    <a href="logout.php">Logout</a>
</body>
</html>