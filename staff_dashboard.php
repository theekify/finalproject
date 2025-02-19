<?php

require 'db.php';

// Fetch staff details
$stmt = $conn->prepare("SELECT * FROM staff WHERE User_ID = ?");
$stmt->execute([1]); // Assuming a default user ID for demonstration
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
    <p>Email: example@example.com</p> <!-- Placeholder email -->
    <p>Role: Staff</p> <!-- Placeholder role -->

    <h2>Staff Functions</h2>
    <ul>
        <li><a href="staff_approve_worker.php">Approve Worker Profiles</a></li>
        <li><a href="staff_approve_agency.php">Approve Agency Profiles</a></li>
        <li><a href="staff_assign_training.php">Assign Workers to Training</a></li>
        <li><a href="staff_issue_certificates.php">Issue Certificates</a></li>
        <li><a href="staff_monitor_complaints.php">Monitor Complaints</a></li>
    </ul>

    <a href="logout.php">Logout</a>
</body>
</html>
