<?php
require 'db.php';

// Fetch workers who have completed training
$stmt = $conn->prepare("SELECT * FROM worker WHERE Training_Status = 'Completed' AND Certification_Issued = 'No'");
$stmt->execute();
$workers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$success_message = '';

// Handle certificate issuance
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $worker_id = $_POST['worker_id'];

    // Update worker's certification status
    $stmt = $conn->prepare("UPDATE worker SET Certification_Issued = 'Yes' WHERE Worker_ID = ?");
    $stmt->execute([$worker_id]);

    $success_message = "Certificate issued to worker successfully.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issue Certificates</title>
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

        .success-message {
            background-color: var(--success);
            color: var(--white);
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
        }

        .certificate-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .certificate-table th,
        .certificate-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        .certificate-table th {
            background-color: var(--primary);
            color: var(--white);
            font-weight: bold;
            text-transform: uppercase;
        }

        .certificate-table tr:nth-child(even) {
            background-color: var(--primary-light);
        }

        .certificate-table tr:hover {
            background-color: rgba(107, 25, 80, 0.1);
        }

        .issue-form {
            display: inline-block;
        }

        .issue-button {
            background-color: var(--primary);
            color: var(--white);
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .issue-button:hover {
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
                padding: 15px;
            }

            .certificate-table {
                font-size: 14px;
            }

            .certificate-table th,
            .certificate-table td {
                padding: 8px;
            }

            .issue-button {
                padding: 6px 10px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Issue Certificates</h1>
        
        <?php if ($success_message): ?>
            <div class="success-message">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <table class="certificate-table">
            <thead>
                <tr>
                    <th>Worker ID</th>
                    <th>Passport Number</th>
                    <th>Visa Number</th>
                    <th>Training Status</th>
                    <th>Certification Issued</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($workers as $worker): ?>
                <tr>
                    <td><?php echo htmlspecialchars($worker['Worker_ID']); ?></td>
                    <td><?php echo htmlspecialchars($worker['Passport_Number']); ?></td>
                    <td><?php echo htmlspecialchars($worker['Visa_Number']); ?></td>
                    <td><?php echo htmlspecialchars($worker['Training_Status']); ?></td>
                    <td><?php echo htmlspecialchars($worker['Certification_Issued']); ?></td>
                    <td>
                        <form method="POST" class="issue-form">
                            <input type="hidden" name="worker_id" value="<?php echo htmlspecialchars($worker['Worker_ID']); ?>">
                            <button type="submit" class="issue-button">Issue Certificate</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="staff_dashboard.php" class="back-link">Back to Dashboard</a>
    </div>
</body>
</html>