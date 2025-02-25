<?php

require 'db.php';

// Fetch staff details
$stmt = $conn->prepare("SELECT * FROM staff WHERE User_ID = ?");
$stmt->execute([1]); // Replace 1 with the actual user ID you want to fetch
$staff = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
    <link rel="stylesheet" href="staff_dash.css">
</head>
<body>
    <header class="header">
        <h1>Staff Dashboard</h1>
    </header>

    <div class="container">
        <div class="dashboard-grid">
            <div class="card">
                <h2>Training Management</h2>
                <div class="card-links">
                    <a href="staff_post_course.php" class="card-link">Post a course</a>
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

            <div class="card">
                <h2>Staff Profile</h2>
                <div class="card-links">
                    <a href="staff_profile.php" class="card-link">View/Edit Profile</a>
                  
                </div>
            </div>
        </div>

        <a href="logout.php" class="logout-button">Logout</a>
    </div>
</body>
</html>