<?php

require 'db.php';

// Fetch agency details
$stmt = $conn->prepare("SELECT * FROM agency WHERE User_ID = ?");
$stmt->execute([$_GET['user_id']]);
$agency = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $license_number = $_POST['license_number'];

    // Update agency profile
    $stmt = $conn->prepare("UPDATE agency SET License_Number = ? WHERE User_ID = ?");
    $stmt->execute([$license_number, $_GET['user_id']]);

    echo "Profile updated successfully! Wait for staff approval.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Agency Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .profile-form {
            max-width: 400px;
            margin: 0 auto;
        }
        .profile-form input {
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
    if (empty($agency['License_Number'])) {
        echo '<div class="banner">Please complete your profile to proceed.</div>';
    }
    ?>
    <h1>Agency Profile</h1>
    <div class="profile-form">
        <form method="POST">
            License Number: <input type="text" name="license_number" value="<?php echo $agency['License_Number']; ?>" required><br>
            <button type="submit">Save Changes</button>
        </form>
    </div>
    <a href="agency_dashboard.php">Back to Dashboard</a>
</body>
</html>
