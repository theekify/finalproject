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
<html>
<head>
    <title>Monitor Complaints</title>
</head>
<body>
    <h1>Monitor Complaints</h1>
    <table border="1">
        <tr>
            <th>Complaint ID</th>
            <th>Worker ID</th>
            <th>Description</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php foreach ($complaints as $complaint): ?>
        <tr>
            <td><?php echo $complaint['Complaint_ID']; ?></td>
            <td><?php echo $complaint['Worker_ID']; ?></td>
            <td><?php echo $complaint['Description']; ?></td>
            <td><?php echo $complaint['Status']; ?></td>
            <td>
                <form method="POST">
                    <input type="hidden" name="complaint_id" value="<?php echo $complaint['Complaint_ID']; ?>">
                    <select name="status">
                        <option value="Resolved">Resolve</option>
                        <option value="Escalated">Escalate</option>
                    </select>
                    <button type="submit">Update</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>