<?php
require 'db.php';

// Fetch all job applications
$stmt = $conn->prepare("SELECT application.*, worker.Worker_ID, worker.Passport_Number, job.Job_Title 
                        FROM application 
                        JOIN worker ON application.Worker_ID = worker.Worker_ID 
                        JOIN job ON application.Job_ID = job.Job_ID");
$stmt->execute();
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Job Applications</title>
    <link rel="stylesheet" href="agency_track.css">
</head>
<body>
    <div class="container">
        <h1>Track Job Applications</h1>
        <table class="applications-table">
            <thead>
                <tr>
                    <th>Application ID</th>
                    <th>Worker ID</th>
                    <th>Passport Number</th>
                    <th>Job Title</th>
                    <th>Application Status</th>
                    <th>Interview Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($applications as $application): ?>
                <tr>
                    <td><?php echo htmlspecialchars($application['Application_ID']); ?></td>
                    <td><?php echo htmlspecialchars($application['Worker_ID']); ?></td>
                    <td><?php echo htmlspecialchars($application['Passport_Number']); ?></td>
                    <td><?php echo htmlspecialchars($application['Job_Title']); ?></td>
                    <td class="status-<?php echo strtolower($application['Application_Status']); ?>">
                        <?php echo htmlspecialchars($application['Application_Status']); ?>
                    </td>
                    <td><?php echo htmlspecialchars($application['Interview_Date']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="agency_dashboard.php" class="back-link">Back to Dashboard</a>
    </div>
</body>
</html>