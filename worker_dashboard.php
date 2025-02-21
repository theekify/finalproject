<?php
session_start();
require 'db.php';

// Ensure user_id is set
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = isset($_POST['email']) ? $_POST['email'] : null;
    $password = isset($_POST['password']) ? $_POST['password'] : null;

    if ($email && $password) {
        // Fetch user details
        $stmt = $conn->prepare("SELECT * FROM user WHERE User_Email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['User_Password'])) {
            // Store user_id in session
            $_SESSION['user_id'] = $user['User_ID'];
            // Redirect to worker dashboard with user_id
            header("Location: worker_dashboard.php");
            exit();
        } else {
            echo "Invalid email or password.";
        }
    } else {
        echo "Email and password are required.";
    }
}

// Fetch worker details using user_id
$stmt = $conn->prepare("SELECT * FROM worker WHERE User_ID = ?");
$stmt->execute([$user_id]);
$worker = $stmt->fetch(PDO::FETCH_ASSOC);
$worker_id = $worker ? $worker['Worker_ID'] : null;

// Handle job application
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_job'])) {
    $job_id = $_POST['job_id'];

    if ($worker_id) {
        // Insert job application into the database
        $stmt = $conn->prepare("INSERT INTO application (Worker_ID, Job_ID, Application_Status) VALUES (?, ?, 'Pending')");
        $stmt->execute([$worker_id, $job_id]);

        echo "Job application submitted successfully!";
    } else {
        echo "Worker ID is required to apply for a job.";
    }
}

// Handle course enrollment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enroll_course'])) {
    $course_id = $_POST['course_id'];

    if ($worker_id) {
        // Insert course enrollment into the database
        $stmt = $conn->prepare("INSERT INTO training_enrollment (Worker_ID, Training_ID, Enrollment_Status) VALUES (?, ?, 'Pending')");
        $stmt->execute([$worker_id, $course_id]);

        echo "Course enrollment submitted successfully!";
    } else {
        echo "Worker ID is required to enroll in a course.";
    }
}

// Fetch available jobs and courses
$jobs = $conn->query("SELECT * FROM job WHERE Job_Status = 'Approved'")->fetchAll(PDO::FETCH_ASSOC);
$courses = $conn->query("SELECT * FROM training WHERE Training_Status = 'Open'")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Worker Dashboard</title>
    <link rel="stylesheet" href="worker_dash.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <h1>Worker Dashboard</h1>
                <?php if (empty($worker['Passport_Number']) || empty($worker['Visa_Number']) || empty($worker['Health_Report'])): ?>
        <div class="banner">
            <p>Please complete your profile by providing your passport number, visa number, and health report.</p>
            <a href="worker_profile.php?user_id=<?php echo htmlspecialchars($user_id); ?>" class="banner-button">Complete Profile</a>
        </div>
        <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="container">
        <section class="section">
            <h2>Search and Apply for Jobs</h2>
            <div class="search-form">
                <form method="GET">
                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
                    <input type="text" name="job_search" placeholder="Job Title or Location">
                    <button type="submit">Search</button>
                </form>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Job ID</th>
                        <th>Job Title</th>
                        <th>Job Description</th>
                        <th>Job Location</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($jobs as $job): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($job['Job_ID']); ?></td>
                        <td><?php echo htmlspecialchars($job['Job_Title']); ?></td>
                        <td><?php echo htmlspecialchars($job['Job_Description']); ?></td>
                        <td><?php echo htmlspecialchars($job['Job_Location']); ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="job_id" value="<?php echo htmlspecialchars($job['Job_ID']); ?>">
                                <button type="submit" name="apply_job" class="action-button">Apply</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section class="section">
            <h2>Search and Enroll in Courses</h2>
            <div class="search-form">
                <form method="GET">
                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
                    <input type="text" name="course_search" placeholder="Course Name">
                    <button type="submit">Search</button>
                </form>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Course ID</th>
                        <th>Course Name</th>
                        <th>Course Description</th>
                        <th>Course Duration</th>
                        <th>Course Location</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($courses as $course): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($course['Training_ID']); ?></td>
                        <td><?php echo htmlspecialchars($course['Training_Name']); ?></td>
                        <td><?php echo htmlspecialchars($course['Training_Description']); ?></td>
                        <td><?php echo htmlspecialchars($course['Training_Duration']); ?></td>
                        <td><?php echo htmlspecialchars($course['Training_Location']); ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="course_id" value="<?php echo htmlspecialchars($course['Training_ID']); ?>">
                                <button type="submit" name="enroll_course" class="action-button">Enroll</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section class="section">
            <h2>Worker Functions</h2>
            <ul class="worker-functions">
                <li><a href="worker_profile.php?user_id=<?php echo htmlspecialchars($user_id); ?>">View/Update Profile</a></li>
                <li><a href="worker_apply_insurance.php?user_id=<?php echo htmlspecialchars($user_id); ?>">Apply for Insurance</a></li>
                <li><a href="worker_submit_complaint.php?user_id=<?php echo htmlspecialchars($user_id); ?>">Submit Complaint</a></li>
            </ul>
        </section>
    </main>
</body>
</html>