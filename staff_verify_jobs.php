<?php
session_start();
require 'db.php';

// Handle job approval/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $job_id = $_POST['job_id'];
    $action = $_POST['action'];

    if ($action === 'approve') {
        // Try multiple possible status values that might be allowed
        $possible_statuses = ['Approved', 'Active', 'Published', 'Live', 'Open'];
        $success = false;
        
        foreach ($possible_statuses as $status) {
            try {
                $stmt = $conn->prepare("UPDATE job SET Job_Status = ? WHERE Job_ID = ?");
                $stmt->execute([$status, $job_id]);
                $_SESSION['message'] = "Job approved successfully! Status set to: " . $status;
                $success = true;
                break;
            } catch (PDOException $e) {
                // Try next status
                continue;
            }
        }
        
        if (!$success) {
            $_SESSION['error'] = "Could not approve job. No valid status found.";
        }
        
    } elseif ($action === 'reject') {
        $stmt = $conn->prepare("DELETE FROM job WHERE Job_ID = ?");
        $stmt->execute([$job_id]);
        $_SESSION['message'] = "Job rejected and deleted successfully!";
    }
    
    header("Location: staff_verify_jobs.php");
    exit;
}

// Fetch pending jobs - try different possible status values for pending jobs
$possible_pending_statuses = ['Pending', 'Inactive', 'Draft', 'Review'];
$jobs = [];

foreach ($possible_pending_statuses as $status) {
    try {
        $jobs = $conn->query("SELECT * FROM job WHERE Job_Status = '$status'")->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($jobs)) {
            break;
        }
    } catch (PDOException $e) {
        continue;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Jobs</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        h1 {
            color: #6b1950;
            margin-bottom: 20px;
            text-align: center;
        }

        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #f8f1f5;
            color: #6b1950;
            font-weight: bold;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-approve {
            background: #28a745;
            color: white;
        }

        .btn-approve:hover {
            background: #218838;
        }

        .btn-reject {
            background: #dc3545;
            color: white;
        }

        .btn-reject:hover {
            background: #c82333;
        }

        .back-link {
            display: inline-block;
            padding: 10px 20px;
            background: #6b1950;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        .back-link:hover {
            background: #5a1642;
        }

        .no-jobs {
            text-align: center;
            padding: 40px;
            color: #666;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Verify Jobs</h1>
        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="message success">
                <?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="message error">
                <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (empty($jobs)): ?>
            <div class="no-jobs">
                <p>No jobs pending verification.</p>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Job ID</th>
                        <th>Job Title</th>
                        <th>Job Description</th>
                        <th>Job Requirements</th>
                        <th>Job Salary</th>
                        <th>Job Location</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($jobs as $job): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($job['Job_ID']); ?></td>
                        <td><?php echo htmlspecialchars($job['Job_Title']); ?></td>
                        <td><?php echo htmlspecialchars($job['Job_Description']); ?></td>
                        <td><?php echo htmlspecialchars($job['Job_Requirements']); ?></td>
                        <td><?php echo htmlspecialchars($job['Job_Salary']); ?></td>
                        <td><?php echo htmlspecialchars($job['Job_Location']); ?></td>
                        <td><?php echo htmlspecialchars($job['Job_Status']); ?></td>
                        <td>
                            <div class="action-buttons">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="job_id" value="<?php echo htmlspecialchars($job['Job_ID']); ?>">
                                    <button type="submit" name="action" value="approve" class="btn btn-approve">Approve</button>
                                </form>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="job_id" value="<?php echo htmlspecialchars($job['Job_ID']); ?>">
                                    <button type="submit" name="action" value="reject" class="btn btn-reject" onclick="return confirm('Are you sure you want to delete this job?')">Reject</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        
        <a href="staff_dashboard.php" class="back-link">Back to Dashboard</a>
    </div>
</body>
</html>