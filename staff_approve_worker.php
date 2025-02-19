<?php
session_start();

// Check if the user is logged in and is a staff member
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Staff') {
    header('Location: staff_login.php');
    exit();
}

require 'db.php';

// Fetch workers with pending approval
$stmt = $conn->prepare("SELECT * FROM worker WHERE Health_Report IS NOT NULL AND Approval_Status = 'Pending'");
$stmt->execute();
$workers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle approval
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $worker_id = $_POST['worker_id'];
    $action = $_POST['action']; // 'approve' or 'reject'

    if ($action === 'approve') {
        // Approve worker profile
        $stmt = $conn->prepare("UPDATE worker SET Approval_Status = 'Approved' WHERE Worker_ID = ?");
        $stmt->execute([$worker_id]);
        echo "Worker profile approved!";
    } elseif ($action === 'reject') {
        // Reject worker profile
        $stmt = $conn->prepare("UPDATE worker SET Approval_Status = 'Rejected' WHERE Worker_ID = ?");
        $stmt->execute([$worker_id]);
        echo "Worker profile rejected!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Approve Worker Profiles</title>
</head>
<body>
    <h1>Approve Worker Profiles</h1>
    <table border="1">
        <tr>
            <th>Worker ID</th>
            <th>Passport Number</th>
            <th>Visa Number</th>
            <th>Health Report</th>
            <th>Action</th>
        </tr>
        <?php foreach ($workers as $worker): ?>
        <tr>
            <td><?php echo $worker['Worker_ID']; ?></td>
            <td><?php echo $worker['Passport_Number']; ?></td>
            <td><?php echo $worker['Visa_Number']; ?></td>
            <td><a href="view_health_report.php?id=<?php echo $worker['Worker_ID']; ?>" target="_blank">View Health Report</a></td>
            <td>
                <form method="POST">
                    <input type="hidden" name="worker_id" value="<?php echo $worker['Worker_ID']; ?>">
                    <button type="submit" name="action" value="approve">Approve</button>
                    <button type="submit" name="action" value="reject">Reject</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>