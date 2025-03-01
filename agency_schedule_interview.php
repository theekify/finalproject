<?php
require 'db.php';

// Fetch job applications for the agency's jobs
$employer_id = 1; // Replace with the actual employer ID
$stmt = $conn->prepare("SELECT application.*, worker.Worker_ID, worker.Passport_Number, job.Job_Title 
                        FROM application 
                        JOIN worker ON application.Worker_ID = worker.Worker_ID 
                        JOIN job ON application.Job_ID = job.Job_ID 
                        WHERE job.Employer_ID = ? AND application.Application_Status = 'Approved'");
$stmt->execute([$employer_id]);
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

$success_message = '';

// Handle interview scheduling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $application_id = $_POST['application_id'];
    $interview_date = $_POST['interview_date'];

    // Update application with interview date
    $stmt = $conn->prepare("UPDATE application SET Interview_Date = ? WHERE Application_ID = ?");
    $stmt->execute([$interview_date, $application_id]);

    $success_message = "Interview scheduled successfully.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Interviews</title>
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

        .interview-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .interview-table th,
        .interview-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        .interview-table th {
            background-color: var(--primary);
            color: var(--white);
            font-weight: bold;
            text-transform: uppercase;
        }

        .interview-table tr:nth-child(even) {
            background-color: var(--primary-light);
        }

        .interview-table tr:hover {
            background-color: rgba(107, 25, 80, 0.1);
        }

        .schedule-form {
            display: flex;
        }

        .schedule-form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .schedule-form input[type="datetime-local"] {
            padding: 8px;
            border: 1px solid var(--border);
            border-radius: 4px;
        }

        .schedule-form button {
            background-color: var(--primary);
            color: var(--white);
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .schedule-form button:hover {
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

            .interview-table {
                font-size: 14px;
            }

            .interview-table th,
            .interview-table td {
                padding: 8px;
            }

            .schedule-form {
                flex-direction: row;
                align-items: center;
                gap: 5px;
            }

            .schedule-form input[type="datetime-local"] {
                flex-grow: 1;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Schedule Interviews</h1>
        
        <?php if ($success_message): ?>
            <div class="success-message">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <table class="interview-table">
            <thead>
                <tr>
                    <th>Application ID</th>
                    <th>Worker ID</th>
                    <th>Passport Number</th>
                    <th>Job Title</th>
                    <th>Schedule Interview</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($applications as $application): ?>
                <tr>
                    <td><?php echo htmlspecialchars($application['Application_ID']); ?></td>
                    <td><?php echo htmlspecialchars($application['Worker_ID']); ?></td>
                    <td><?php echo htmlspecialchars($application['Passport_Number']); ?></td>
                    <td><?php echo htmlspecialchars($application['Job_Title']); ?></td>
                    <td>
                        <form method="POST" class="schedule-form">
                            <input type="hidden" name="application_id" value="<?php echo htmlspecialchars($application['Application_ID']); ?>">
                            <input type="datetime-local" name="interview_date" required>
                            <button type="submit">Schedule</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="agency_dashboard.php" class="back-link">Back to Dashboard</a>
    </div>
</body>
</html>

