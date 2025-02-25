<?php
require 'db.php';

// Fetch workers and training programs
$stmt = $conn->prepare("SELECT * FROM worker");
$stmt->execute();
$workers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT * FROM training");
$stmt->execute();
$trainings = $stmt->fetchAll(PDO::FETCH_ASSOC);

$success_message = '';

// Handle training assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $worker_id = $_POST['worker_id'];
    $training_id = $_POST['training_id'];

    // Assign worker to training
    $stmt = $conn->prepare("UPDATE worker SET Training_Status = 'In Progress' WHERE Worker_ID = ?");
    $stmt->execute([$worker_id]);

    $success_message = "Worker assigned to training program successfully.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Workers to Training</title>
    <style>
        :root {
            --primary: #6b1950;
            --primary-light: #fff5f5;
            --primary-dark: #4a1237;
            --secondary: #2c3e50;
            --text-dark: #2d1422;
            --text-light: #666666;
            --white: #ffffff;
            --border: #e5e7eb;
            --success: #10b981;
            --shadow: 0 4px 20px rgba(107, 25, 80, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: var(--primary-light);
            color: var(--text-dark);
            line-height: 1.6;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: var(--white);
            border-radius: 8px;
            box-shadow: var(--shadow);
            padding: 30px;
        }

        h1 {
            color: var(--primary);
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
        }

        .success-message {
            background-color: var(--success);
            color: var(--white);
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
        }

        .assignment-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        label {
            font-weight: bold;
            color: var(--text-dark);
        }

        select {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border);
            border-radius: 4px;
            font-size: 16px;
            background-color: var(--white);
        }

        button {
            background-color: var(--primary);
            color: var(--white);
            border: none;
            padding: 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: var(--primary-dark);
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: var(--primary);
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .back-link:hover {
            color: var(--primary-dark);
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            select {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Assign Workers to Training Programs</h1>
        
        <?php if ($success_message): ?>
            <div class="success-message">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="assignment-form">
            <div class="form-group">
                <label for="worker_id">Worker:</label>
                <select id="worker_id" name="worker_id" required>
                    <?php foreach ($workers as $worker): ?>
                    <option value="<?php echo htmlspecialchars($worker['Worker_ID']); ?>">
                        <?php echo htmlspecialchars($worker['Worker_ID'] . ' - ' . $worker['Passport_Number']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="training_id">Training Program:</label>
                <select id="training_id" name="training_id" required>
                    <?php foreach ($trainings as $training): ?>
                    <option value="<?php echo htmlspecialchars($training['Training_ID']); ?>">
                        <?php echo htmlspecialchars($training['Training_Name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit">Assign</button>
        </form>
        <a href="staff_dashboard.php" class="back-link">Back to Dashboard</a>
    </div>
</body>
</html>