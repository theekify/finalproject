<?php
session_start();

// Check if the user is logged in and is a worker
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Worker') {
    header('Location: worker_login.php');
    exit();
}

require 'db.php';

// Fetch worker details
$stmt = $conn->prepare("SELECT * FROM worker WHERE User_ID = ?");
$stmt->execute([$_SESSION['user_id']]);
$worker = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Worker Dashboard</title>
    <style>
        .banner {
            background-color: #ffcc00;
            padding: 10px;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php
    // Check if profile is incomplete
    if (empty($worker['Passport_Number']) || empty($worker['Visa_Number']) || empty($worker['Health_Report'])) {
        echo '<div class="banner">Please complete your profile to proceed.</div>';
    }
    ?>
    <h1>Welcome, <?php echo $worker['Worker_ID']; ?>!</h1>
    <p>Email: <?php echo $_SESSION['user_email']; ?></p>
    <p>Role: <?php echo $_SESSION['user_role']; ?></p>

    <h2>Worker Functions</h2>
    <ul>
        <li><a href="worker_profile.php">View/Update Profile</a></li>
        <li><a href="worker_apply_jobs.php">Apply for Jobs</a></li>
        <li><a href="worker_enroll_training.php">Enroll in Training</a></li>
        <li><a href="worker_apply_insurance.php">Apply for Insurance</a></li>
        <li><a href="worker_submit_complaint.php">Submit Complaint</a></li>
    </ul>

    <a href="logout.php">Logout</a>
</body>
</html>