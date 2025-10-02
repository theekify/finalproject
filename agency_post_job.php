<?php
require 'db.php';

$success_message = '';

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

    $success_message = "Job posted successfully! Wait for staff verification.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Job Offer</title>
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
            max-width: 600px;
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

        .job-form {
            display: flex;
            flex-direction: column;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-secondary);
        }

        input, textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #e2e8f0;
            border-radius: var(--radius);
        }

        textarea {
            resize: vertical;
        }

        button {
            background-color: var(--primary);
            color: var(--white);
            padding: 0.75rem;
            border: none;
            border-radius: var(--radius);
            cursor: pointer;
            transition: background-color 0.2s;
        }

        button:hover {
            background-color: #5a1642;
        }

        .success-message {
            background-color: #10b981;
            color: var(--white);
            padding: 1rem;
            border-radius: var(--radius);
            margin-bottom: 1rem;
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Post Job Offer</h1>
        <?php if ($success_message): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>
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
