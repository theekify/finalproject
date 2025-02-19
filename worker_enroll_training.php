<?php
session_start();

// Check if the user is logged in and is a worker
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Worker') {
    header('Location: worker_login.php');
    exit();
}

require 'db.php';

// Fetch available training programs
$stmt = $conn->prepare("SELECT * FROM training WHERE Training_Status = 'Open'");
$stmt->execute();
$trainings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle training enrollment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $training_id = $_POST['training_id'];

    // Enroll worker in training program
    $stmt = $conn->prepare("UPDATE worker SET Training_Status = 'In Progress' WHERE Worker_ID = ?");
    $stmt->execute([$_SESSION['user_id']]);

    echo "Enrolled in training program successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Enroll in Training Programs</title>
</head>
<body>
    <h1>Enroll in Training Programs</h1>
    <table border="1">
        <tr>
            <th>Training ID</th>
            <th>Training Name</th>
            <th>Training Description</th>
            <th>Training Status</th>
            <th>Action</th>
        </tr>
        <?php foreach ($trainings as $training): ?>
        <tr>
            <td><?php echo $training['Training_ID']; ?></td>
            <td><?php echo $training['Training_Name']; ?></td>
            <td><?php echo $training['Training_Description']; ?></td>
            <td><?php echo $training['Training_Status']; ?></td>
            <td>
                <form method="POST">
                    <input type="hidden" name="training_id" value="<?php echo $training['Training_ID']; ?>">
                    <button type="submit">Enroll</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>