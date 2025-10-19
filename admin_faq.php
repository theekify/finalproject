<?php
require 'db.php';

// Fetch feedback from agencies
$stmt = $conn->prepare("SELECT f.Feedback_ID, f.Feedback, f.Created_At, a.Agency_Name FROM feedback f JOIN agency a ON f.Agency_ID = a.Agency_ID");
$stmt->execute();
$feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agency Feedback</title>
    <style>
        :root {
            --primary: #6b1950;
            --primary-light: #f8f9fc;
            --text-primary: #1a1a1a;
            --text-secondary: #666666;
            --border: #e5e7eb;
            --background: #f1f5f9;
            --white: #ffffff;
            --shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            --radius: 8px;
        }

        body {
            background-color: var(--background);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            padding: 2rem;
        }

        h1 {
            color: var(--primary);
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .feedback-table {
            width: 100%;
            border-collapse: collapse;
            background-color: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .feedback-table th, .feedback-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        .feedback-table th {
            background-color: var(--primary-light);
            color: var(--primary);
            font-weight: 600;
        }

        .feedback-table tr:hover {
            background-color: var(--primary-light);
        }

        .feedback-content {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .back-link {
            display: inline-block;
            margin-top: 1.5rem;
            padding: 0.75rem 1.5rem;
            background-color: var(--primary);
            color: var(--white);
            text-decoration: none;
            border-radius: var(--radius);
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s;
            text-align: center;
        }

        .back-link:hover {
            background-color: var(--primary);
            opacity: 0.9;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .feedback-table {
                font-size: 14px;
            }

            .feedback-table th, .feedback-table td {
                padding: 0.75rem;
            }

            .feedback-content {
                max-width: 150px;
            }
        }  
    </style>
</head>
<body>
    <div class="container">    
        <h1>Agency Feedback</h1>
        <table class="feedback-table">
            <thead>
                <tr>
                    <th>Feedback ID</th>
                    <th>Agency Name</th>
                    <th>Feedback</th>
                    <th>Submitted At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($feedbacks as $feedback): ?>
                <tr>
                    <td><?php echo htmlspecialchars($feedback['Feedback_ID']); ?></td>
                    <td><?php echo htmlspecialchars($feedback['Agency_Name']); ?></td>
                    <td class="feedback-content"><?php echo htmlspecialchars($feedback['Feedback']); ?></td>
                    <td><?php echo htmlspecialchars($feedback['Created_At']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="admin_dashboard.php" class="back-link">Back to Dashboard</a>
    </div>
</body>
</html>