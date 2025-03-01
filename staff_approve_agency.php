<?php
require 'db.php';

// Fetch agencies with pending approval
$stmt = $conn->prepare("SELECT * FROM agency WHERE License_Number IS NOT NULL AND Approval_Status = 'Pending'");
$stmt->execute();
$agencies = $stmt->fetchAll(PDO::FETCH_ASSOC);

$success_message = '';

// Handle approval
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $agency_id = $_POST['agency_id'];
    $action = $_POST['action']; // 'approve' or 'reject'

    if ($action === 'approve') {
        // Approve agency profile
        $stmt = $conn->prepare("UPDATE agency SET Approval_Status = 'Approved' WHERE Agency_ID = ?");
        $stmt->execute([$agency_id]);
        $success_message = "Agency profile approved!";
    } elseif ($action === 'reject') {
        // Reject agency profile
        $stmt = $conn->prepare("UPDATE agency SET Approval_Status = 'Rejected' WHERE Agency_ID = ?");
        $stmt->execute([$agency_id]);
        $success_message = "Agency profile rejected!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve Agency Profiles</title>
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
            --danger: #ef4444;
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

        .success-message {
            background-color: var(--success);
            color: var(--white);
            padding: 1rem;
            border-radius: var(--radius);
            margin-bottom: 1rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
        }

        th, td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        th {
            background-color: var(--primary-light);
            color: var(--primary);
            font-weight: 600;
        }

        tr:nth-child(even) {
            background-color: #f8fafc;
        }

        button {
            background-color: var(--primary);
            color: var(--white);
            padding: 0.5rem 1rem;
            border: none;
            border-radius: var(--radius);
            cursor: pointer;
            transition: background-color 0.2s;
            margin-right: 0.5rem;
        }

        button:hover {
            background-color: #5a1642;
        }

        button[value="reject"] {
            background-color: var(--danger);
        }

        button[value="reject"]:hover {
            background-color: #dc2626;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            table {
                font-size: 0.875rem;
            }

            th, td {
                padding: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Approve Agency Profiles</h1>
        
        <?php if ($success_message): ?>
            <div class="success-message">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Agency ID</th>
                    <th>Agency Name</th>
                    <th>License Number</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($agencies as $agency): ?>
                <tr>
                    <td><?php echo htmlspecialchars($agency['Agency_ID']); ?></td>
                    <td><?php echo htmlspecialchars($agency['Agency_Name']); ?></td>
                    <td><?php echo htmlspecialchars($agency['License_Number']); ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="agency_id" value="<?php echo htmlspecialchars($agency['Agency_ID']); ?>">
                            <button type="submit" name="action" value="approve">Approve</button>
                            <button type="submit" name="action" value="reject">Reject</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
