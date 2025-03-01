<?php
require 'db.php';

// Handle course posting
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_name = $_POST['course_name'];
    $course_description = $_POST['course_description'];
    $course_duration = $_POST['course_duration'];
    $course_location = $_POST['course_location'];

    // Insert course into the database
    $stmt = $conn->prepare("INSERT INTO training (Training_Name, Training_Description, Training_Duration, Training_Location, Training_Status) 
                            VALUES (?, ?, ?, ?, 'Open')");
    $stmt->execute([$course_name, $course_description, $course_duration, $course_location]);

    echo "Course posted successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Course</title>
    <link rel="stylesheet" href="staff_post.css">
</head>
<body>
    <div class="container">
        <h1>Post Course</h1>
        
        <form method="POST" class="course-form">
            <div class="form-group">
                <label for="course_name">Course Name:</label>
                <input type="text" id="course_name" name="course_name" required>
            </div>
            <div class="form-group">
                <label for="course_description">Course Description:</label>
                <textarea id="course_description" name="course_description" rows="5" required></textarea>
            </div>
            <div class="form-group">
                <label for="course_duration">Course Duration:</label>
                <input type="text" id="course_duration" name="course_duration" required>
            </div>
            <div class="form-group">
                <label for="course_location">Course Location:</label>
                <input type="text" id="course_location" name="course_location" required>
            </div>
            <button type="submit">Post Course</button>
        </form>
        <a href="staff_dashboard.php" class="back-link">Back to Dashboard</a>
    </div>
</body>
</html>