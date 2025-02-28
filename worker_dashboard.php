<?php
require 'db.php';

// Fetch worker details
$user_id = 1; // Assuming a static user ID for demonstration purposes
$stmt = $conn->prepare("SELECT * FROM worker WHERE User_ID = ?");
$stmt->execute([$user_id]);
$worker = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch jobs
$job_search = isset($_GET['job_search']) ? $_GET['job_search'] : '';
$stmt = $conn->prepare("SELECT * FROM job WHERE Job_Title LIKE ? OR Job_Location LIKE ?");
$stmt->execute(['%' . $job_search . '%', '%' . $job_search . '%']);
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch courses
$course_search = isset($_GET['course_search']) ? $_GET['course_search'] : '';
$stmt = $conn->prepare("SELECT * FROM training WHERE Training_Name LIKE ?");
$stmt->execute(['%' . $course_search . '%']);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Worker Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Add this CSS for modals */
        :root {
            --primary: #6b1950;
            --primary-light: #f8f9fc;
            --text-primary: #333333;
            --text-secondary: #666666;
            --border: #e5e7eb;
            --background: #f5f5f5;
            --white: #ffffff;
            --shadow: 0 1px 2px rgba(0,0,0,0.05);
            --radius: 8px;
            --sidebar-width: 250px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background-color: var(--background);
            min-height: 100vh;
            display: flex;
        }

        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--white);
            color: var(--text-primary);
            padding: 1.5rem;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            overflow-y: auto;
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            margin-bottom: 2rem;
        }

        .sidebar-header h1 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary);
        }

        .sidebar-nav {
            list-style: none;
            flex-grow: 1;
        }

        .sidebar-nav li {
            margin-bottom: 0.5rem;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            color: var(--text-primary);
            text-decoration: none;
            border-radius: var(--radius);
            transition: background-color 0.2s;
        }

        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            background-color: var(--primary-light);
            color: var(--primary);
        }

        .logout-button {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: var(--primary-light);
            color: var(--primary);
            border: none;
            border-radius: var(--radius);
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            margin-top: 1rem;
        }

        .logout-button:hover {
            background: var(--primary);
            color: var(--white);
        }

        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 2rem;
        }

        .section {
            background: var(--white);
            border-radius: var(--radius);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow);
        }

        .section h2 {
            color: var(--primary);
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .search-form {
            margin-bottom: 1rem;
        }

        .search-form input[type="text"] {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid var(--border);
            border-radius: var(--radius);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        th {
            background-color: var(--primary-light);
            color: var(--primary);
            font-weight: 600;
        }

        .action-button {
            background-color: var(--primary);
            color: var(--white);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: var(--radius);
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .action-button:hover {
            background-color: var(--primary-light);
            color: var(--primary);
        }

        .worker-functions {
            list-style: none;
        }

        .worker-functions li {
            margin-bottom: 0.5rem;
        }

        .worker-functions a {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: var(--primary-light);
            color: var (--primary);
            text-decoration: none;
            border-radius: var(--radius);
            transition: background-color 0.2s;
        }

        .worker-functions a:hover {
            background-color: var(--primary);
            color: var(--white);
        }

        .banner {
            background-color: var(--primary-light);
            color: var(--primary);
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: var(--radius);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .banner-button {
            background-color: var(--primary);
            color: var(--white);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: var(--radius);
            cursor: pointer;
            transition: background-color 0.2s;
            text-decoration: none;
        }

        .banner-button:hover {
            background-color: var(--primary-light);
            color: var(--primary);
        }

        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                height: auto;
                position: static;
                padding: 1rem;
            }

            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: var(--white);
            padding: 2rem;
            border-radius: var(--radius);
            width: 80%;
            max-width: 600px;
            box-shadow: var(--shadow);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .modal-header h2 {
            color: var(--primary);
            font-size: 1.25rem;
            font-weight: 600;
        }

        .modal-header .close {
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-primary);
        }

        .modal-header .close:hover {
            color: var(--primary);
        }

        .modal-body {
            margin-bottom: 1.5rem;
        }

        .modal-footer {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <h1>Worker Dashboard</h1>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="#" class="active"><i class="fas fa-tachometer-alt"></i>Dashboard</a></li>
                <li><a href="#" id="open-job-modal"><i class="fas fa-briefcase"></i>Jobs</a></li>
                <li><a href="#" id="open-course-modal"><i class="fas fa-graduation-cap"></i>Courses</a></li>
                <li><a href="#"><i class="fas fa-user-circle"></i>Profile</a></li>
                <li><a href="#"><i class="fas fa-shield-alt"></i>Insurance</a></li>
                <li><a href="#"><i class="fas fa-comment-dots"></i>Complaints</a></li>
            </ul>
        </nav>
        <a href="logout.php" class="logout-button"><i class="fas fa-sign-out-alt"></i>Logout</a>
    </div>

    <div class="main-content">
        <?php if (empty($worker['Passport_Number']) || empty($worker['Visa_Number']) || empty($worker['Health_Report'])): ?>
        <div class="banner">
            <p>Please complete your profile by providing your passport number, visa number, and health report.</p>
            <a href="worker_profile.php?user_id=<?php echo htmlspecialchars($user_id); ?>" class="banner-button">Complete Profile</a>
        </div>
        <?php endif; ?>

        <!-- Job Modal -->
        <div id="job-modal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Search and Apply for Jobs</h2>
                    <span class="close" id="close-job-modal">&times;</span>
                </div>
                <div class="modal-body">
                    <div class="search-form">
                        <form id="job-search-form">
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
                            <input type="text" name="job_search" placeholder="Job Title or Location">
                            <button type="submit" class="action-button">Search</button>
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
                        <tbody id="job-results">
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
                </div>
            </div>
        </div>

        <!-- Course Modal -->
        <div id="course-modal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Search and Enroll in Courses</h2>
                    <span class="close" id="close-course-modal">&times;</span>
                </div>
                <div class="modal-body">
                    <div class="search-form">
                        <form id="course-search-form">
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
                            <input type="text" name="course_search" placeholder="Course Name">
                            <button type="submit" class="action-button">Search</button>
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
                        <tbody id="course-results">
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
                </div>
            </div>
        </div>

        <!-- Worker Functions Section -->
        <section class="section">
            <h2>Worker Functions</h2>
            <ul class="worker-functions">
                <li><a href="worker_profile.php?user_id=<?php echo htmlspecialchars($user_id); ?>">View/Update Profile</a></li>
                <li><a href="worker_apply_insurance.php?user_id=<?php echo htmlspecialchars($user_id); ?>">Apply for Insurance</a></li>
                <li><a href="worker_submit_complaint.php?user_id=<?php echo htmlspecialchars($user_id); ?>">Submit Complaint</a></li>
            </ul>
        </section>
    </div>

    <script>
        // JavaScript to handle modals
        const jobModal = document.getElementById("job-modal");
        const courseModal = document.getElementById("course-modal");
        const openJobModal = document.getElementById("open-job-modal");
        const openCourseModal = document.getElementById("open-course-modal");
        const closeJobModal = document.getElementById("close-job-modal");
        const closeCourseModal = document.getElementById("close-course-modal");

        openJobModal.onclick = () => jobModal.style.display = "flex";
        openCourseModal.onclick = () => courseModal.style.display = "flex";
        closeJobModal.onclick = () => jobModal.style.display = "none";
        closeCourseModal.onclick = () => courseModal.style.display = "none";

        window.onclick = (event) => {
            if (event.target === jobModal) jobModal.style.display = "none";
            if (event.target === courseModal) courseModal.style.display = "none";
        };

        // JavaScript to handle form submissions without reloading the page
        document.getElementById('job-search-form').onsubmit = function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            const queryString = new URLSearchParams(formData).toString();
            fetch('worker_dashboard.php?' + queryString)
            .then(response => response.text())
            .then(data => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(data, 'text/html');
                const jobResults = doc.getElementById('job-results').innerHTML;
                document.getElementById('job-results').innerHTML = jobResults;
            });
        };

        document.getElementById('course-search-form').onsubmit = function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            const queryString = new URLSearchParams(formData).toString();
            fetch('worker_dashboard.php?' + queryString)
            .then(response => response.text())
            .then(data => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(data, 'text/html');
                const courseResults = doc.getElementById('course-results').innerHTML;
                document.getElementById('course-results').innerHTML = courseResults;
            });
        };
    </script>
</body>
</html>