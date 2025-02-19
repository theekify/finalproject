<?php


require 'db.php';

// Handle insurance application
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $policy_number = $_POST['policy_number'];
    $provider_name = $_POST['provider_name'];
    $premium = $_POST['premium'];

    // Insert insurance application into the database
    $stmt = $conn->prepare("INSERT INTO insurance (Worker_ID, Policy_Number, Provider_Name, Premium, Insurance_Status) VALUES (?, ?, ?, ?, 'Pending')");
    $stmt->execute([$_SESSION['user_id'], $policy_number, $provider_name, $premium]);

    echo "Insurance application submitted successfully! Wait for staff approval.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Apply for Insurance</title>
</head>
<body>
    <h1>Apply for Insurance</h1>
    <form method="POST">
        Policy Number: <input type="text" name="policy_number" required><br>
        Provider Name: <input type="text" name="provider_name" required><br>
        Premium: <input type="number" name="premium" required><br>
        <button type="submit">Apply</button>
    </form>
</body>
</html>