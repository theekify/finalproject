<?php
session_start();

// Check if the user is logged in and is a worker
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Worker') {
    header('Location: worker_login.php');
    exit();
}

require 'db.php';

// Fetch available jobs
$stmt = $conn->prepare("SELECT * FROM job WHERE Job_Status = 'Approved'");
$stmt->execute();
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle job application
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $job_id = $_POST['job_id'];

    // Insert job application into the database
    $stmt = $conn->prepare("INSERT INTO application (Worker_ID, Job_ID, Application_Status) VALUES (?, ?, 'Pending')");
    $stmt->execute([$_SESSION['user_id'], $job_id]);

    echo "Job application submitted successfully! Wait for approval.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Apply for Jobs</title>
</head>
<body>
    <h1>Apply for Jobs</h1>
    <table border="1">
        <tr>
            <th>Job ID</th>
            <th>Job Title</th>
            <th>Job Description</th>
            <th>Job Requirements</th>
            <th>Job Salary</th>
            <th>Job Location</th>
            <th>Action</th>
        </tr>
        <?php foreach ($jobs as $job): ?>
        <tr>
            <td><?php echo $job['Job_ID']; ?></td>
            <td><?php echo $job['Job_Title']; ?></td>
            <td><?php echo $job['Job_Description']; ?></td>
            <td><?php echo $job['Job_Requirements']; ?></td>
            <td><?php echo $job['Job_Salary']; ?></td>
            <td><?php echo $job['Job_Location']; ?></td>
            <td>
                <form method="POST">
                    <input type="hidden" name="job_id" value="<?php echo $job['Job_ID']; ?>">
                    <button type="submit">Apply</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>