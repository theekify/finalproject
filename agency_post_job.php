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
    $stmt->execute([$_SESSION['user_id'], $job_title, $job_description, $job_requirements, $job_salary, $job_location]);

    echo "Job posted successfully! Wait for staff verification.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Post Job Offer</title>
</head>
<body>
    <h1>Post Job Offer</h1>
    <form method="POST">
        Job Title: <input type="text" name="job_title" required><br>
        Job Description: <textarea name="job_description" rows="5" required></textarea><br>
        Job Requirements: <textarea name="job_requirements" rows="5" required></textarea><br>
        Job Salary: <input type="number" name="job_salary" required><br>
        Job Location: <input type="text" name="job_location" required><br>
        <button type="submit">Post Job</button>
    </form>
</body>
</html>