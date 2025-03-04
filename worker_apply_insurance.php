<?php
session_start(); // Start the session

require 'db.php';

// Handle insurance application
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $policy_number = $_POST['policy_number'];
    $provider_name = $_POST['provider_name'];
    $premium = $_POST['premium'];

    // Validate inputs
    if (empty($policy_number) || empty($provider_name) || empty($premium) || !is_numeric($premium) || $premium <= 0) {
        $error_message = "Invalid input. Please ensure all fields are filled correctly.";
    } else {
        // Insert insurance application into the database
        try {
            $stmt = $conn->prepare("INSERT INTO insurance (Worker_ID, Policy_Number, Provider_Name, Premium, Insurance_Status) VALUES (?, ?, ?, ?, 'Pending')");
            $stmt->execute([$_SESSION['user_id'], $policy_number, $provider_name, $premium]);

            $success_message = "Insurance application submitted successfully! Wait for staff approval.";
        } catch (PDOException $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Insurance</title>
    <link rel="stylesheet" href="worker_apply_insu.css">
</head>
<body>
    <div class="container">
        <h1>Apply for Insurance</h1>
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <?php if (isset($success_message)): ?>
            <div class="success-message"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        <form method="POST" class="insurance-form">
            <div class="form-group">
                <label for="policy_number">Policy Number:</label>
                <input type="text" id="policy_number" name="policy_number" required>
            </div>
            <div class="form-group">
                <label for="provider_name">Provider Name:</label>
                <input type="text" id="provider_name" name="provider_name" required>
            </div>
            <div class="form-group">
                <label for="premium">Premium:</label>
                <input type="number" id="premium" name="premium" required>
            </div>
            <button type="submit">Apply</button>
        </form>
        <a href="worker_dashboard.php" class="back-link">Back to Dashboard</a>
    </div>
</body>
</html>