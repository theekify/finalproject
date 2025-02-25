<?php
session_start();
require 'db.php';

// Ensure user_id is set
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch agency details
$stmt = $conn->prepare("SELECT * FROM agency WHERE User_ID = ?");
$stmt->execute([$user_id]);
$agency = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$agency) {
    echo "Agency not found.";
    exit();
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $license_number = $_POST['license_number'];
    $agency_name = $_POST['agency_name'];
    $agency_address = $_POST['agency_address'];
    $agency_phone = $_POST['agency_phone'];

    // Update agency profile
    $stmt = $conn->prepare("UPDATE agency SET License_Number = ?, Agency_Name = ?, Agency_Address = ?, Agency_Phone = ? WHERE User_ID = ?");
    $stmt->execute([$license_number, $agency_name, $agency_address, $agency_phone, $user_id]);

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
    if (empty($agency['License_Number']) || empty($agency['Agency_Name']) || empty($agency['Agency_Address']) || empty($agency['Agency_Phone'])) {
        echo '<div class="banner">Please complete your profile to proceed.</div>';
    }
    ?>
    <h1>Agency Profile</h1>
    <div class="profile-form">
        <form method="POST">
            License Number: <input type="text" name="license_number" value="<?php echo htmlspecialchars($agency['License_Number']); ?>" required><br>
            Agency Name: <input type="text" name="agency_name" value="<?php echo htmlspecialchars($agency['Agency_Name']); ?>" required><br>
            Agency Address: <input type="text" name="agency_address" value="<?php echo htmlspecialchars($agency['Agency_Address']); ?>" required><br>
            Agency Phone: <input type="text" name="agency_phone" value="<?php echo htmlspecialchars($agency['Agency_Phone']); ?>" required><br>
            <button type="submit">Save Changes</button>
        </form>
    </div>
    <a href="agency_dashboard.php">Back to Dashboard</a>
</body>
</html>