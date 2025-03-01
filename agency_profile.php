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

$success_message = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $license_number = $_POST['license_number'];
    $agency_name = $_POST['agency_name'];
    $agency_address = $_POST['agency_address'];
    $agency_phone = $_POST['agency_phone'];

    // Update agency profile
    $stmt = $conn->prepare("UPDATE agency SET License_Number = ?, Agency_Name = ?, Agency_Address = ?, Agency_Phone = ? WHERE User_ID = ?");
    $stmt->execute([$license_number, $agency_name, $agency_address, $agency_phone, $user_id]);

    $success_message = "Profile updated successfully! Wait for staff approval.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agency Profile</title>
    <style>
        :root {
            --primary: #6b1950;
            --primary-light: #f8f9fc;
            --text-primary: #333333;
            --text-secondary: #666666;
            --background: #f5f5f5;
            --white: #ffffff;
            --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --radius: 8px;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--background);
            color: var(--text-primary);
            line-height: 1.5;
            margin: 0;
            padding: 2rem;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: var(--white);
            padding: 2rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        h1 {
            color: var(--primary);
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .profile-form {
            display: flex;
            flex-direction: column;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-secondary);
        }

        input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #e2e8f0;
            border-radius: var(--radius);
        }

        button {
            background-color: var(--primary);
            color: var(--white);
            padding: 0.75rem;
            border: none;
            border-radius: var(--radius);
            cursor: pointer;
            transition: background-color 0.2s;
        }

        button:hover {
            background-color: #5a1642;
        }

        .success-message {
            background-color: #10b981;
            color: var(--white);
            padding: 1rem;
            border-radius: var(--radius);
            margin-bottom: 1rem;
        }

        .banner {
            background-color: #fcd34d;
            color: #92400e;
            padding: 1rem;
            border-radius: var(--radius);
            margin-bottom: 1rem;
        }

        .back-link {
            display: inline-block;
            margin-top: 1rem;
            color: var(--primary);
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Agency Profile</h1>
        <?php
        // Check if profile is incomplete
        if (empty($agency['License_Number']) || empty($agency['Agency_Name']) || empty($agency['Agency_Address']) || empty($agency['Agency_Phone'])) {
            echo '<div class="banner">Please complete your profile to proceed.</div>';
        }
        ?>
        <?php if ($success_message): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <form method="POST" class="profile-form">
            <div class="form-group">
                <label for="license_number">License Number:</label>
                <input type="text" id="license_number" name="license_number" value="<?php echo htmlspecialchars($agency['License_Number']); ?>" required>
            </div>
            <div class="form-group">
                <label for="agency_name">Agency Name:</label>
                <input type="text" id="agency_name" name="agency_name" value="<?php echo htmlspecialchars($agency['Agency_Name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="agency_address">Agency Address:</label>
                <input type="text" id="agency_address" name="agency_address" value="<?php echo htmlspecialchars($agency['Agency_Address']); ?>" required>
            </div>
            <div class="form-group">
                <label for="agency_phone">Agency Phone:</label>
                <input type="text" id="agency_phone" name="agency_phone" value="<?php echo htmlspecialchars($agency['Agency_Phone']); ?>" required>
            </div>
            <button type="submit">Save Changes</button>
        </form>
        <a href="agency_dashboard.php" class="back-link">Back to Dashboard</a>
    </div>
</body>
</html>
