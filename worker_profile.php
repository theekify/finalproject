<?php

require 'db.php';

// Check if user_id is set in the URL
if (!isset($_GET['user_id']) || empty($_GET['user_id'])) {
    echo "User ID is required.";
    exit();
}

$user_id = $_GET['user_id'];

// Debugging: Output the user_id
echo "Debug: user_id = " . htmlspecialchars($user_id) . "<br>";

// Fetch worker details
$stmt = $conn->prepare("SELECT * FROM worker WHERE User_ID = ?");
$stmt->execute([$user_id]);
$worker = $stmt->fetch(PDO::FETCH_ASSOC);

// Debugging: Output the worker details
echo "Debug: worker details = ";
var_dump($worker);
echo "<br>";

if (!$worker) {
    echo "Worker not found.";
    exit();
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $passport_number = $_POST['passport_number'];
    $visa_number = $_POST['visa_number'];

    // Handle health report upload
    if ($_FILES['health_report']['error'] === UPLOAD_ERR_OK) {
        $health_report = file_get_contents($_FILES['health_report']['tmp_name']);
    } else {
        $health_report = null;
    }

    // Update worker profile
    $stmt = $conn->prepare("UPDATE worker SET Passport_Number = ?, Visa_Number = ?, Health_Report = ? WHERE User_ID = ?");
    $stmt->execute([$passport_number, $visa_number, $health_report, $user_id]);

    echo "Profile updated successfully! Wait for staff approval.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Worker Profile</title>
    <link rel="stylesheet" href="worker_profile.css">
</head>
<body>
    <div class="container">
        <?php
        // Check if profile is incomplete
        if (empty($worker['Passport_Number']) || empty($worker['Visa_Number']) || empty($worker['Health_Report'])) {
            echo '<div class="banner">Please complete your profile to proceed.</div>';
        }
        ?>
        <h1>Worker Profile</h1>
        <form class="profile-form" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="passport_number">Passport Number:</label>
                <input type="text" id="passport_number" name="passport_number" value="<?php echo htmlspecialchars($worker['Passport_Number']); ?>" required>
            </div>
            <div class="form-group">
                <label for="visa_number">Visa Number:</label>
                <input type="text" id="visa_number" name="visa_number" value="<?php echo htmlspecialchars($worker['Visa_Number']); ?>" required>
            </div>
            <div class="form-group">
                <label for="health_report">Health Report (PDF):</label>
                <input type="file" id="health_report" name="health_report" accept="application/pdf" required>
            </div>
            <button type="submit">Save Changes</button>
        </form>
        <a href="worker_dashboard.php?user_id=<?php echo htmlspecialchars($user_id); ?>" class="back-link">Back to Dashboard</a>
    </div>
</body>
</html>