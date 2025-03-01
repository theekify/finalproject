<?php
require 'db.php';

// Handle job approval/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $job_id = $_POST['job_id'];
    $action = $_POST['action'];

    if ($action === 'approve') {
        $stmt = $conn->prepare("UPDATE job SET Job_Status = 'Approved' WHERE Job_ID = ?");
    } elseif ($action === 'reject') {
        $stmt = $conn->prepare("UPDATE job SET Job_Status = 'Rejected' WHERE Job_ID = ?");
    }

    $stmt->execute([$job_id]);
    echo "Job status updated successfully!";
}

// Fetch pending jobs
$jobs = $conn->query("SELECT * FROM job WHERE Job_Status = 'Pending'")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Jobs</title>
    <link rel="stylesheet" href="staff_verify.css">
</head>
<body>
    <div class="container">
        <h1>Verify Jobs</h1>
        
        <table class="jobs-table">
            <thead>
                <tr>
                    <th>Job ID</th>
                    <th>Job Title</th>
                    <th>Job Description</th>
                    <th>Job Requirements</th>
                    <th>Job Salary</th>
                    <th>Job Location</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($jobs as $job): ?>
                <tr>
                    <td><?php echo htmlspecialchars($job['Job_ID']); ?></td>
                    <td><?php echo htmlspecialchars($job['Job_Title']); ?></td>
                    <td class="job-description"><?php echo htmlspecialchars($job['Job_Description']); ?></td>
                    <td class="job-requirements"><?php echo htmlspecialchars($job['Job_Requirements']); ?></td>
                    <td><?php echo htmlspecialchars($job['Job_Salary']); ?></td>
                    <td><?php echo htmlspecialchars($job['Job_Location']); ?></td>
                    <td>
                        <form method="POST" class="action-buttons">
                            <input type="hidden" name="job_id" value="<?php echo htmlspecialchars($job['Job_ID']); ?>">
                            <button type="submit" name="action" value="approve" class="btn btn-approve">Approve</button>
                            <button type="submit" name="action" value="reject" class="btn btn-reject">Reject</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="staff_dashboard.php" class="back-link">Back to Dashboard</a>
    </div>
</body>
</html>