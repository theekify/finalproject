<?php
session_start();

// Check if the user is logged in and is a worker
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Worker') {
    header('Location: worker_login.php');
    exit();
}

require 'db.php';

// Fetch worker details
$stmt = $conn->prepare("SELECT * FROM worker WHERE User_ID = ?");
$stmt->execute([$_SESSION['user_id']]);
$worker = $stmt->fetch(PDO::FETCH_ASSOC);

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
    $stmt->execute([$passport_number, $visa_number, $health_report, $_SESSION['user_id']]);

    echo "Profile updated successfully! Wait for staff approval.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Worker Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .profile-form {
            max-width: 400px;
            margin: 0 auto;
        }
        .profile-form input, .profile-form input[type="file"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .profile-form button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .profile-form button:hover {
            background-color: #0056b3;
        }
        .banner {
            background-color: #ffcc00;
            padding: 10px;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php
    // Check if profile is incomplete
    if (empty($worker['Passport_Number']) || empty($worker['Visa_Number']) || empty($worker['Health_Report'])) {
        echo '<div class="banner">Please complete your profile to proceed.</div>';
    }
    ?>
    <h1>Worker Profile</h1>
    <div class="profile-form">
        <form method="POST" enctype="multipart/form-data">
            Passport Number: <input type="text" name="passport_number" value="<?php echo $worker['Passport_Number']; ?>" required><br>
            Visa Number: <input type="text" name="visa_number" value="<?php echo $worker['Visa_Number']; ?>" required><br>
            Health Report (PDF): <input type="file" name="health_report" accept="application/pdf" required><br>
            <button type="submit">Save Changes</button>
        </form>
    </div>
    <a href="worker_dashboard.php">Back to Dashboard</a>
</body>
</html>