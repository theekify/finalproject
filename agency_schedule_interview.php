<?php

require 'db.php';

// Fetch job applications for the agency's jobs
$stmt = $conn->prepare("SELECT application.*, worker.Worker_ID, worker.Passport_Number, job.Job_Title 
                        FROM application 
                        JOIN worker ON application.Worker_ID = worker.Worker_ID 
                        JOIN job ON application.Job_ID = job.Job_ID 
                        WHERE job.Employer_ID = ? AND application.Application_Status = 'Approved'");
$stmt->execute([$_SESSION['user_id']]);
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle interview scheduling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $application_id = $_POST['application_id'];
    $interview_date = $_POST['interview_date'];

    // Update application with interview date
    $stmt = $conn->prepare("UPDATE application SET Interview_Date = ? WHERE Application_ID = ?");
    $stmt->execute([$interview_date, $application_id]);

    echo "Interview scheduled successfully.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Schedule Interviews</title>
</head>
<body>
    <h1>Schedule Interviews</h1>
    <table border="1">
        <tr>
            <th>Application ID</th>
            <th>Worker ID</th>
            <th>Passport Number</th>
            <th>Job Title</th>
            <th>Schedule Interview</th>
        </tr>
        <?php foreach ($applications as $application): ?>
        <tr>
            <td><?php echo $application['Application_ID']; ?></td>
            <td><?php echo $application['Worker_ID']; ?></td>
            <td><?php echo $application['Passport_Number']; ?></td>
            <td><?php echo $application['Job_Title']; ?></td>
            <td>
                <form method="POST">
                    <input type="hidden" name="application_id" value="<?php echo $application['Application_ID']; ?>">
                    Interview Date: <input type="datetime-local" name="interview_date" required><br>
                    <button type="submit">Schedule</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>