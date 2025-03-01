<?php
require 'db.php';

$success_message = '';

// Handle feedback submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $agency_id = $_POST['agency_id'];
    $feedback = $_POST['feedback'];

    // Insert feedback into the database
    $stmt = $conn->prepare("INSERT INTO feedback (Agency_ID, Feedback) VALUES (?, ?)");
    $stmt->execute([$agency_id, $feedback]);

    $success_message = "Feedback submitted successfully.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Feedback</title>
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

        .feedback-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
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

        input[type="text"],
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border);
            border-radius: 4px;
            font-size: 16px;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
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

            input[type="text"],
            textarea {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Submit Feedback</h1>
        
        <?php if ($success_message): ?>
            <div class="success-message">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="feedback-form">
            <div class="form-group">
                <label for="agency_id">Agency ID:</label>
                <input type="text" id="agency_id" name="agency_id" required>
            </div>
            <div class="form-group">
                <label for="feedback">Feedback:</label>
                <textarea id="feedback" name="feedback" rows="5" required></textarea>
            </div>
            <button type="submit">Submit</button>
        </form>
        <a href="agency_dashboard.php" class="back-link">Back to Dashboard</a>
    </div>
</body>
</html>
