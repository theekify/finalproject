<?php
require 'db.php';

// Fetch complaints from workers
$stmt = $conn->prepare("SELECT * FROM complaints WHERE Status = 'Pending'");
$stmt->execute();
$complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle complaint resolution
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $complaint_id = $_POST['complaint_id'];
    $status = $_POST['status']; // 'Resolved' or 'Escalated'

    // Update complaint status
    $stmt = $conn->prepare("UPDATE complaints SET Status = ? WHERE Complaint_ID = ?");
    $stmt->execute([$status, $complaint_id]);

    echo "Complaint status updated to $status.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitor Complaints</title>
    <link rel="stylesheet" href="staff_comp.css">
</head>
<body>
    <div class="container">
        <h1>Monitor Complaints</h1>
        
       

        <table class="complaints-table">
            <thead>
                <tr>
                    <th>Complaint ID</th>
                    <th>Worker ID</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($complaints as $complaint): ?>
                <tr>
                    <td><?php echo htmlspecialchars($complaint['Complaint_ID']); ?></td>
                    <td><?php echo htmlspecialchars($complaint['Worker_ID']); ?></td>
                    <td><?php echo htmlspecialchars($complaint['Description']); ?></td>
                    <td class="status-<?php echo strtolower($complaint['Status']); ?>">
                        <?php echo htmlspecialchars($complaint['Status']); ?>
                    </td>
                    <td>
                        <form method="POST" class="action-form">
                            <input type="hidden" name="complaint_id" value="<?php echo htmlspecialchars($complaint['Complaint_ID']); ?>">
                            <select name="status">
                                <option value="Resolved">Resolve</option>
                                <option value="Escalated">Escalate</option>
                            </select>
                            <button type="submit">Update</button>
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