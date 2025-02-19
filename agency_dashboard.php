<?php
session_start();

// Check if the user is logged in and is an agency
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Agency') {
    header('Location: agency_login.php');
    exit();
}

require 'db.php';

// Fetch agency details
$stmt = $conn->prepare("SELECT * FROM agency WHERE User_ID = ?");
$stmt->execute([$_SESSION['user_id']]);
$agency = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Agency Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .banner {
            background-color: #ffcc00;
            padding: 10px;
            text-align: center;
            margin-bottom: 20px;
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
    <?php
    // Check if profile is incomplete
    if (empty($agency['License_Number'])) {
        echo '<div class="banner">Please complete your profile to proceed.</div>';
    }
    ?>
    <h1>Welcome, <?php echo $agency['Agency_Name']; ?>!</h1>
    <p>Email: <?php echo $_SESSION['user_email']; ?></p>
    <p>Role: <?php echo $_SESSION['user_role']; ?></p>

    <h2>Agency Functions</h2>
    <ul>
        <li><a href="agency_profile.php">View/Update Profile</a></li>
        <li><a href="agency_post_job.php">Post Job Offer</a></li>
        <li><a href="agency_track_applications.php">Track Job Applications</a></li>
        <li><a href="agency_schedule_interview.php">Schedule Interviews</a></li>
        <li><a href="agency_submit_feedback.php">Submit Feedback</a></li>
    </ul>

    <a href="logout.php">Logout</a>
</body>
</html>