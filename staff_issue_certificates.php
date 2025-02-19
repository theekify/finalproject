<?php


require 'db.php';

// Fetch workers who have completed training
$stmt = $conn->prepare("SELECT * FROM worker WHERE Training_Status = 'Completed' AND Certification_Issued = 'No'");
$stmt->execute();
$workers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle certificate issuance
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $worker_id = $_POST['worker_id'];

    // Update worker's certification status
    $stmt = $conn->prepare("UPDATE worker SET Certification_Issued = 'Yes' WHERE Worker_ID = ?");
    $stmt->execute([$worker_id]);

    echo "Certificate issued to worker.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Issue Certificates</title>
</head>
<body>
    <h1>Issue Certificates</h1>
    <table border="1">
        <tr>
            <th>Worker ID</th>
            <th>Passport Number</th>
            <th>Visa Number</th>
            <th>Training Status</th>
            <th>Certification Issued</th>
            <th>Action</th>
        </tr>
        <?php foreach ($workers as $worker): ?>
        <tr>
            <td><?php echo $worker['Worker_ID']; ?></td>
            <td><?php echo $worker['Passport_Number']; ?></td>
            <td><?php echo $worker['Visa_Number']; ?></td>
            <td><?php echo $worker['Training_Status']; ?></td>
            <td><?php echo $worker['Certification_Issued']; ?></td>
            <td>
                <form method="POST">
                    <input type="hidden" name="worker_id" value="<?php echo $worker['Worker_ID']; ?>">
                    <button type="submit">Issue Certificate</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>