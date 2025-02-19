<?php
session_start();

// Check if the user is logged in and is a staff member
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Staff') {
    header('Location: staff_login.php');
    exit();
}

require 'db.php';

// Fetch agencies with pending approval
$stmt = $conn->prepare("SELECT * FROM agency WHERE License_Number IS NOT NULL AND Approval_Status = 'Pending'");
$stmt->execute();
$agencies = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle approval
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $agency_id = $_POST['agency_id'];
    $action = $_POST['action']; // 'approve' or 'reject'

    if ($action === 'approve') {
        // Approve agency profile
        $stmt = $conn->prepare("UPDATE agency SET Approval_Status = 'Approved' WHERE Agency_ID = ?");
        $stmt->execute([$agency_id]);
        echo "Agency profile approved!";
    } elseif ($action === 'reject') {
        // Reject agency profile
        $stmt = $conn->prepare("UPDATE agency SET Approval_Status = 'Rejected' WHERE Agency_ID = ?");
        $stmt->execute([$agency_id]);
        echo "Agency profile rejected!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Approve Agency Profiles</title>
</head>
<body>
    <h1>Approve Agency Profiles</h1>
    <table border="1">
        <tr>
            <th>Agency ID</th>
            <th>Agency Name</th>
            <th>License Number</th>
            <th>Action</th>
        </tr>
        <?php foreach ($agencies as $agency): ?>
        <tr>
            <td><?php echo $agency['Agency_ID']; ?></td>
            <td><?php echo $agency['Agency_Name']; ?></td>
            <td><?php echo $agency['License_Number']; ?></td>
            <td>
                <form method="POST">
                    <input type="hidden" name="agency_id" value="<?php echo $agency['Agency_ID']; ?>">
                    <button type="submit" name="action" value="approve">Approve</button>
                    <button type="submit" name="action" value="reject">Reject</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>