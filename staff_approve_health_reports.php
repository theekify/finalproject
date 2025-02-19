<?php

require 'db.php';

// Fetch health reports from workers
$stmt = $conn->prepare("SELECT * FROM worker WHERE Health_Report = 'Pending'");
$stmt->execute();
$workers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle health report approval/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $worker_id = $_POST['worker_id'];
    $status = $_POST['status']; // 'Approved' or 'Rejected'

    // Update health report status
    $stmt = $conn->prepare("UPDATE worker SET Health_Report = ? WHERE Worker_ID = ?");
    $stmt->execute([$status, $worker_id]);

    echo "Health report status updated to $status.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Approve/Reject Health Reports</title>
</head>
<body>
    <h1>Health Reports</h1>
    <table border="1">
        <tr>
            <th>Worker ID</th>
            <th>Passport Number</th>
            <th>Visa Number</th>
            <th>Health Report Status</th>
            <th>Action</th>
        </tr>
        <?php foreach ($workers as $worker): ?>
        <tr>
            <td><?php echo $worker['Worker_ID']; ?></td>
            <td><?php echo $worker['Passport_Number']; ?></td>
            <td><?php echo $worker['Visa_Number']; ?></td>
            <td><?php echo $worker['Health_Report']; ?></td>
            <td>
                <form method="POST">
                    <input type="hidden" name="worker_id" value="<?php echo $worker['Worker_ID']; ?>">
                    <select name="status">
                        <option value="Approved">Approve</option>
                        <option value="Rejected">Reject</option>
                    </select>
                    <button type="submit">Update</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>