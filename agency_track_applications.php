<?php
require 'db.php';

// Fetch all job applications
$stmt = $conn->prepare("SELECT application.*, worker.Worker_ID, worker.Passport_Number, job.Job_Title 
                        FROM application 
                        JOIN worker ON application.Worker_ID = worker.Worker_ID 
                        JOIN job ON application.Job_ID = job.Job_ID");
$stmt->execute();
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Job Applications</title>
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
            max-width: 1200px;
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
        }

        .applications-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
        }

        .applications-table th,
        .applications-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        .applications-table th {
            background-color: var(--primary-light);
            color: var(--primary);
            font-weight: 600;
        }

        .applications-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .status-pending {
            color: #eab308;
        }

        .status-approved {
            color: #22c55e;
        }

        .status-rejected {
            color: #ef4444;
        }

        .back-link {
            display: inline-block;
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

            .applications-table {
                font-size: 0.875rem;
            }

            .applications-table th,
            .applications-table td {
                padding: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Track Job Applications</h1>
        <div class="table-container">
            <table class="applications-table">
                <thead>
                    <tr>
                        <th>Application ID</th>
                        <th>Worker ID</th>
                        <th>Passport Number</th>
                        <th>Job Title</th>
                        <th>Application Status</th>
                        <th>Interview Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applications as $application): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($application['Application_ID']); ?></td>
                        <td><?php echo htmlspecialchars($application['Worker_ID']); ?></td>
                        <td><?php echo htmlspecialchars($application['Passport_Number']); ?></td>
                        <td><?php echo htmlspecialchars($application['Job_Title']); ?></td>
                        <td class="status-<?php echo strtolower($application['Application_Status']); ?>">
                            <?php echo htmlspecialchars($application['Application_Status']); ?>
                        </td>
                        <td><?php echo htmlspecialchars($application['Interview_Date']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <a href="agency_dashboard.php" class="back-link">Back to Dashboard</a>
    </div>
</body>
</html>
