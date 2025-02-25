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
            --primary-light: #fff5f5;
            --primary-dark: #4a1237;
            --secondary: #2c3e50;
            --text-dark: #2d1422;
            --text-light: #666666;
            --white: #ffffff;
            --border: #e5e7eb;
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
            max-width: 1200px;
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

        .feedback-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .feedback-table th,
        .feedback-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        .feedback-table th {
            background-color: var(--primary);
            color: var(--white);
            font-weight: bold;
            text-transform: uppercase;
        }

        .feedback-table tr:nth-child(even) {
            background-color: var(--primary-light);
        }

        .feedback-table tr:hover {
            background-color: rgba(107, 25, 80, 0.1);
        }

        .feedback-content {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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
                padding: 15px;
            }

            .feedback-table {
                font-size: 14px;
            }

            .feedback-table th,
            .feedback-table td {
                padding: 8px;
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
        <div class="table-responsive">
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
        </div>
        <a href="admin_dashboard.php" class="back-link">Back to Dashboard</a>
    </div>
</body>
</html>