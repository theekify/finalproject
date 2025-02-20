<?php
require 'db.php';

// Fetch agency details
$stmt = $conn->prepare("SELECT * FROM agency WHERE User_ID = ?");
$stmt->execute([1]); // Assuming a static user ID for demonstration purposes
$agency = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agency Dashboard</title>
    <link rel="stylesheet" href="agency_dashboard.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <h1>Agency Dashboard</h1>
                
            </div>
        </div>
    </header>

    <div class="container">
        <main>
            <div class="dashboard-card">
                <h2>Job Management</h2>
                <ul>
                    <li><a href="agency_post_job.php">Post Job Offer</a></li>
                    <li><a href="agency_track_applications.php">Track Job Applications</a></li>
                </ul>
            </div>
            <div class="dashboard-card">
                <h2>Candidate Interaction</h2>
                <ul>
                    <li><a href="agency_schedule_interview.php">Schedule Interviews</a></li>
                    <li><a href="agency_submit_feedback.php">Submit Feedback</a></li>
                </ul>
            </div>
            <div class="dashboard-card">
                <h2>Agency Profile</h2>
                <ul>
                    <li><a href="agency_profile.php">View/Edit Profile</a></li>
                    <li><a href="agency_performance.php">Performance Metrics</a></li>
                </ul>
            </div>
            <div class="dashboard-card">
                <h2>Support</h2>
                <ul>
                    <li><a href="agency_support.php">Contact Support</a></li>
                    <li><a href="agency_faq.php">FAQ</a></li>
                </ul>
            </div>
        </main>
        <a href="logout.php" class="logout-link">Logout</a>
    </div>
</body>
</html>