<?php
require 'db.php';

// Fetch system activity data
$stmt = $conn->prepare("SELECT * FROM user");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare data for the chart
$roles = array_column($users, 'User_Role');
$roleCounts = array_count_values($roles);
$roleLabels = json_encode(array_keys($roleCounts));
$roleData = json_encode(array_values($roleCounts));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Reports</title>
    <style>
        :root {
            --primary: #6b1950;
            --primary-light: #f8f9fc;
            --text-primary: #333333;
            --text-secondary: #666666;
            --border: #e5e7eb;
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
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        h1 {
            color: var(--primary);
            font-size: 2rem;
            margin-bottom: 1.5rem;
        }

        .table-container {
            background-color: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 2rem;
            margin-top: 2rem;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
        }

        .report-table th,
        .report-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        .report-table th {
            background-color: var(--primary-light);
            color: var(--primary);
            font-weight: 600;
        }

        .report-table tr:last-child td {
            border-bottom: none;
        }

        .status-active {
            color: #10b981;
        }

        .status-inactive {
            color: #ef4444;
        }

        .chart-container {
            background-color: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 1.5rem;
            margin-bottom: 2rem;
            height: 400px;
        }

        .back-link {
            display: inline-block;
            margin-top: 1.5rem;
            padding: 0.75rem 1.5rem;
            background-color: var(--primary);
            color: var(--white);
            text-decoration: none;
            border-radius: var(--radius);
            transition: background-color 0.2s ease;
        }

        .back-link:hover {
            background-color: #5a1642;
        }
    </style>
    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <h1>System Activity Report</h1>

        <div class="chart-container">
            <canvas id="roleChart"></canvas>
        </div>

        <div class="table-container">
            <table class="report-table">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['User_ID']); ?></td>
                        <td><?php echo htmlspecialchars($user['User_Name']); ?></td>
                        <td><?php echo htmlspecialchars($user['User_Email']); ?></td>
                        <td><?php echo htmlspecialchars($user['User_Role']); ?></td>
                        <td class="status-<?php echo strtolower($user['User_Status']); ?>">
                            <?php echo htmlspecialchars($user['User_Status']); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <a href="admin_dashboard.php" class="back-link">Back to Dashboard</a>
    </div>

    <script>
        // Get the data from PHP
        const roleLabels = <?php echo $roleLabels; ?>;
        const roleData = <?php echo $roleData; ?>;

        // Initialize the chart
        const ctx = document.getElementById('roleChart').getContext('2d');
        const roleChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: roleLabels,
                datasets: [{
                    label: 'User Roles',
                    data: roleData,
                    backgroundColor: [
                        'rgba(107, 25, 80, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)',
                    ],
                    borderColor: [
                        'rgba(107, 25, 80, 1)',
                        'rgba(16, 185, 129, 1)',
                        'rgba(245, 158, 11, 1)',
                        'rgba(239, 68, 68, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: 'User Roles Distribution',
                        font: {
                            size: 18,
                            weight: 'bold'
                        },
                        padding: {
                            top: 10,
                            bottom: 30
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>

