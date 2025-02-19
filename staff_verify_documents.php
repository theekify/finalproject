<?php


require 'db.php';

// Fetch documents uploaded by workers
$stmt = $conn->prepare("SELECT document.*, worker.Worker_ID, worker.Passport_Number, worker.Visa_Number 
                        FROM document 
                        JOIN worker ON document.Worker_ID = worker.Worker_ID 
                        WHERE document.Status = 'Pending'");
$stmt->execute();
$documents = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle document verification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $document_id = $_POST['document_id'];
    $status = $_POST['status']; // 'Approved' or 'Rejected'

    // Update document status
    $stmt = $conn->prepare("UPDATE document SET Status = ? WHERE Document_ID = ?");
    $stmt->execute([$status, $document_id]);

    echo "Document status updated to $status.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify Documents</title>
</head>
<body>
    <h1>Verify Documents</h1>
    <table border="1">
        <tr>
            <th>Document ID</th>
            <th>Worker ID</th>
            <th>Passport Number</th>
            <th>Visa Number</th>
            <th>Submission Date</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php foreach ($documents as $document): ?>
        <tr>
            <td><?php echo $document['Document_ID']; ?></td>
            <td><?php echo $document['Worker_ID']; ?></td>
            <td><?php echo $document['Passport_Number']; ?></td>
            <td><?php echo $document['Visa_Number']; ?></td>
            <td><?php echo $document['Submission_Date']; ?></td>
            <td><?php echo $document['Status']; ?></td>
            <td>
                <form method="POST">
                    <input type="hidden" name="document_id" value="<?php echo $document['Document_ID']; ?>">
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