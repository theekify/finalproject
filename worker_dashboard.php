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
    <link rel="stylesheet" type="text/css" href="worker_dash.css">
    
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

    <h2>Search and Apply for Jobs</h2>
    <div class="search-form">
        <form method="GET" action="worker_apply_jobs.php">
            Search: <input type="text" name="search" placeholder="Job Title or Location">
            <button type="submit">Search Jobs</button>
        </form>
    </div>

    <h2>Search and Enroll in Courses</h2>
    <div class="search-form">
        <form method="GET" action="worker_enroll_training.php">
            Search: <input type="text" name="search" placeholder="Training Name">
            <button type="submit">Search Courses</button>
        </form>
    </div>

    <h2>Worker Functions</h2>
    <ul>
        <li><a href="worker_profile.php">View/Update Profile</a></li>
        <li><a href="worker_apply_insurance.php">Apply for Insurance</a></li>
        <li><a href="worker_submit_complaint.php">Submit Complaint</a></li>
    </ul>

    <a href="logout.php">Logout</a>
</body>
</html>