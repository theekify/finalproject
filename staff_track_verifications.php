<?php
require 'db.php';

// Handle search
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
}

// Fetch approved jobs
$stmt = $conn->prepare("SELECT * FROM job WHERE Job_Status = 'Approved' AND (Job_Title LIKE ? OR Job_Description LIKE ? OR Job_Location LIKE ?)");
$search_term = "%" . $search_query . "%";
$stmt->execute([$search_term, $search_term, $search_term]);
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Verifications</title>
    <link rel="stylesheet" href="staff_track.css">
</head>
<body>
    <div class="container">
        <h1>Track Verifications</h1>
        
        <form method="GET" class="search-form">
            <input type="text" name="search" placeholder="Search by title, description, or location" value="<?php echo htmlspecialchars($search_query); ?>">
            <button type="submit">Search</button>
        </form>
        
        <table class="jobs-table">
            <thead>
                <tr>
                    <th>Job ID</th>
                    <th>Job Title</th>
                    <th>Job Description</th>
                    <th>Job Requirements</th>
                    <th>Job Salary</th>
                    <th>Job Location</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($jobs as $job): ?>
                <tr>
                    <td><?php echo htmlspecialchars($job['Job_ID']); ?></td>
                    <td><?php echo htmlspecialchars($job['Job_Title']); ?></td>
                    <td><?php echo htmlspecialchars($job['Job_Description']); ?></td>
                    <td><?php echo htmlspecialchars($job['Job_Requirements']); ?></td>
                    <td><?php echo htmlspecialchars($job['Job_Salary']); ?></td>
                    <td><?php echo htmlspecialchars($job['Job_Location']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
