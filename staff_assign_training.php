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
            --primary-light: #f8f9fc;
            --text-primary: #333333;
            --text-secondary: #666666;
            --background: #f5f5f5;
            --white: #ffffff;
            --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --radius: 8px;
            --success: #10b981;
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
            text-align: center;
        }

        .success-message {
            background-color: var(--success);
            color: var(--white);
            padding: 1rem;
            border-radius: var(--radius);
            margin-bottom: 1rem;
            text-align: center;
        }

        .assignment-form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        label {
            font-weight: bold;
            color: var(--text-primary);
        }

        select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #e2e8f0;
            border-radius: var(--radius);
            font-size: 1rem;
        }

        button {
            background-color: var(--primary);
            color: var(--white);
            padding: 0.75rem;
            border: none;
            border-radius: var(--radius);
            cursor: pointer;
            transition: background-color 0.2s;
            font-size: 1rem;
        }

        button:hover {
            background-color: #5a1642;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 1rem;
            color: var(--primary);
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            select, button {
                font-size: 0.875rem;
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

