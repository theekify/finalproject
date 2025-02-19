<?php


require 'db.php';

// Fetch workers and training programs
$stmt = $conn->prepare("SELECT * FROM worker");
$stmt->execute();
$workers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT * FROM training");
$stmt->execute();
$trainings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle training assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $worker_id = $_POST['worker_id'];
    $training_id = $_POST['training_id'];

    // Assign worker to training
    $stmt = $conn->prepare("UPDATE worker SET Training_Status = 'In Progress' WHERE Worker_ID = ?");
    $stmt->execute([$worker_id]);

    echo "Worker assigned to training program.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Assign Workers to Training</title>
</head>
<body>
    <h1>Assign Workers to Training Programs</h1>
    <form method="POST">
        Worker: 
        <select name="worker_id" required>
            <?php foreach ($workers as $worker): ?>
            <option value="<?php echo $worker['Worker_ID']; ?>"><?php echo $worker['Worker_ID']; ?> - <?php echo $worker['Passport_Number']; ?></option>
            <?php endforeach; ?>
        </select><br>
        Training Program: 
        <select name="training_id" required>
            <?php foreach ($trainings as $training): ?>
            <option value="<?php echo $training['Training_ID']; ?>"><?php echo $training['Training_Name']; ?></option>
            <?php endforeach; ?>
        </select><br>
        <button type="submit">Assign</button>
    </form>
</body>
</html>