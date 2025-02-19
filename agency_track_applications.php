<?php
session_start();

// Check if the user is logged in and is an agency
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Agency') {
    header('Location: agency_login.php');
    exit();
}

require 'db.php';

// Fetch job applications for the agency's jobs
$stmt = $conn->prepare("SELECT application.*, worker.Worker_ID, worker.Passport_Number, job.Job_Title 
                        FROM application 
                        JOIN worker ON application.Worker_ID = worker.Worker_ID 
                        JOIN job ON application.Job_ID = job.Job_ID 
                        WHERE job.Employer_ID = ?");
$stmt->execute([$_SESSION['user_id']]);
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Track Job Applications</title>
</head>
<body>
    <h1>Track Job Applications</h1>
    <table border="1">
        <tr>
            <th>Application ID</th>
            <th>Worker ID</th>
            <th>Passport Number</th>
            <th>Job Title</th>
            <th>Application Status</th>
            <th>Interview Date</th>
        </tr>
        <?php foreach ($applications as $application): ?>
        <tr>
            <td><?php echo $application['Application_ID']; ?></td>
            <td><?php echo $application['Worker_ID']; ?></td>
            <td><?php echo $application['Passport_Number']; ?></td>
            <td><?php echo $application['Job_Title']; ?></td>
            <td><?php echo $application['Application_Status']; ?></td>
            <td><?php echo $application['Interview_Date']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>