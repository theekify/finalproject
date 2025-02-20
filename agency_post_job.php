<?php
require 'db.php';

// Handle job posting
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $job_title = $_POST['job_title'];
    $job_description = $_POST['job_description'];
    $job_requirements = $_POST['job_requirements'];
    $job_salary = $_POST['job_salary'];
    $job_location = $_POST['job_location'];

    // Insert job into the database
    $stmt = $conn->prepare("INSERT INTO job (Employer_ID, Job_Title, Job_Description, Job_Requirements, Job_Salary, Job_Location, Job_Status) 
                            VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
    $stmt->execute([1, $job_title, $job_description, $job_requirements, $job_salary, $job_location]); // Assuming Employer_ID is 1 for now

    echo "Job posted successfully! Wait for staff verification.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Job Offer</title>
    <link rel="stylesheet" href="agency_job.css">
</head>
<body>
    <div class="container">
        <h1>Post Job Offer</h1>
       
        <form method="POST" class="job-form">
            <div class="form-group">
                <label for="job_title">Job Title:</label>
                <input type="text" id="job_title" name="job_title" required>
            </div>
            <div class="form-group">
                <label for="job_description">Job Description:</label>
                <textarea id="job_description" name="job_description" rows="5" required></textarea>
            </div>
            <div class="form-group">
                <label for="job_requirements">Job Requirements:</label>
                <textarea id="job_requirements" name="job_requirements" rows="5" required></textarea>
            </div>
            <div class="form-group">
                <label for="job_salary">Job Salary:</label>
                <input type="number" id="job_salary" name="job_salary" required>
            </div>
            <div class="form-group">
                <label for="job_location">Job Location:</label>
                <input type="text" id="job_location" name="job_location" required>
            </div>
            <button type="submit">Post Job</button>
        </form>
        <a href="agency_dashboard.php" class="back-link">Back to Dashboard</a>
    </div>
</body>
</html>