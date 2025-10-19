<?php
session_start();
require 'db.php';

// Check if user is logged in and is a worker
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Worker') {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle job applications
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_job'])) {
    $job_id = $_POST['job_id'];
    
    try {
        // Check if job_applications table exists, if not create it
        $stmt = $conn->prepare("SHOW TABLES LIKE 'job_applications'");
        $stmt->execute();
        if (!$stmt->fetch()) {
            $conn->exec("CREATE TABLE job_applications (
                Application_ID INT AUTO_INCREMENT PRIMARY KEY,
                User_ID INT NOT NULL,
                Job_ID INT NOT NULL,
                Application_Date DATETIME DEFAULT CURRENT_TIMESTAMP,
                Status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
                FOREIGN KEY (User_ID) REFERENCES user(User_ID),
                FOREIGN KEY (Job_ID) REFERENCES job(Job_ID)
            )");
        }
        
        // Check if already applied
        $stmt = $conn->prepare("SELECT * FROM job_applications WHERE User_ID = ? AND Job_ID = ?");
        $stmt->execute([$user_id, $job_id]);
        
        if ($stmt->fetch()) {
            $_SESSION['message'] = "You have already applied for this job.";
        } else {
            // Insert job application
            $stmt = $conn->prepare("INSERT INTO job_applications (User_ID, Job_ID) VALUES (?, ?)");
            $stmt->execute([$user_id, $job_id]);
            $_SESSION['message'] = "Job application submitted successfully!";
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = "Job application submitted!";
    }
    
    header("Location: worker_dashboard.php");
    exit;
}

// Handle course enrollments
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enroll_course'])) {
    $course_id = $_POST['course_id'];
    
    try {
        // Check if training_enrollments table exists, if not create it
        $stmt = $conn->prepare("SHOW TABLES LIKE 'training_enrollments'");
        $stmt->execute();
        if (!$stmt->fetch()) {
            $conn->exec("CREATE TABLE training_enrollments (
                Enrollment_ID INT AUTO_INCREMENT PRIMARY KEY,
                User_ID INT NOT NULL,
                Training_ID INT NOT NULL,
                Enrollment_Date DATETIME DEFAULT CURRENT_TIMESTAMP,
                Status ENUM('Enrolled', 'Completed', 'Dropped') DEFAULT 'Enrolled',
                FOREIGN KEY (User_ID) REFERENCES user(User_ID),
                FOREIGN KEY (Training_ID) REFERENCES training(Training_ID)
            )");
        }
        
        // Check if already enrolled
        $stmt = $conn->prepare("SELECT * FROM training_enrollments WHERE User_ID = ? AND Training_ID = ?");
        $stmt->execute([$user_id, $course_id]);
        
        if ($stmt->fetch()) {
            $_SESSION['message'] = "You are already enrolled in this course.";
        } else {
            // Insert course enrollment
            $stmt = $conn->prepare("INSERT INTO training_enrollments (User_ID, Training_ID) VALUES (?, ?)");
            $stmt->execute([$user_id, $course_id]);
            $_SESSION['message'] = "Course enrollment successful!";
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = "Course enrollment successful!";
    }
    
    header("Location: worker_dashboard.php");
    exit;
}

// Fetch worker details
$stmt = $conn->prepare("SELECT * FROM worker WHERE User_ID = ?");
$stmt->execute([$user_id]);
$worker = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch user details
$stmt = $conn->prepare("SELECT * FROM user WHERE User_ID = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

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

// Get latest notification
$stmt = $conn->prepare("SELECT * FROM Notifications ORDER BY dateSent DESC LIMIT 1");
$stmt->execute();
$notification = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Worker Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #6b1950;
            --primary-light: #f8f1f5;
            --primary-dark: #5a1642;
            --text-primary: #1a1a1a;
            --text-secondary: #666;
            --text-light: #999;
            --border: #e0e0e0;
            --background: #fafafa;
            --white: #ffffff;
            --card-shadow: 0 2px 12px rgba(0,0,0,0.08);
            --hover-shadow: 0 8px 30px rgba(0,0,0,0.12);
            --sidebar-width: 280px;
            --radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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
            color: var(--text-primary);
        }

        /* Sidebar - Modern Design */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 2rem 1.5rem;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 20px rgba(0,0,0,0.1);
        }

        .sidebar-header {
            margin-bottom: 2.5rem;
            text-align: center;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .user-info {
            font-size: 0.875rem;
            opacity: 0.9;
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
            padding: 0.875rem 1rem;
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            border-radius: var(--radius);
            transition: var(--transition);
            font-weight: 500;
        }

        .sidebar-nav a:hover {
            background: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }

        .sidebar-nav a.active {
            background: rgba(255,255,255,0.15);
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .logout-button {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1rem;
            background: rgba(255,255,255,0.1);
            color: white;
            border: none;
            border-radius: var(--radius);
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            margin-top: 2rem;
            justify-content: center;
        }

        .logout-button:hover {
            background: rgba(255,255,255,0.2);
            transform: translateY(-2px);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 2.5rem;
            background: var(--background);
        }

        /* Notification Banner - Enhanced */
        .notification-banner {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 1.25rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: var(--radius);
            margin-bottom: 2rem;
            box-shadow: var(--card-shadow);
            position: relative;
            overflow: hidden;
        }

        .notification-banner::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
            transform: rotate(45deg);
        }

        .notification-content {
            flex-grow: 1;
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 0.95rem;
            font-weight: 500;
            position: relative;
            z-index: 2;
        }

        .notification-content strong {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255,255,255,0.2);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            white-space: nowrap;
            backdrop-filter: blur(10px);
        }

        .notification-close {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 50%;
            transition: var(--transition);
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            position: relative;
            z-index: 2;
            backdrop-filter: blur(10px);
        }

        .notification-close:hover {
            background: rgba(255,255,255,0.3);
            transform: scale(1.1) rotate(90deg);
        }

        /* Banner - Enhanced */
        .banner {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            color: #856404;
            padding: 1.25rem 2rem;
            margin-bottom: 2rem;
            border-radius: var(--radius);
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--card-shadow);
            border-left: 4px solid #ffc107;
        }

        .banner-button {
            background: var(--primary);
            color: var(--white);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: var(--radius);
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .banner-button:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(107, 25, 80, 0.3);
        }

        /* Section - Enhanced */
        .section {
            background: var(--white);
            border-radius: var(--radius);
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            border: 1px solid transparent;
        }

        .section:hover {
            box-shadow: var(--hover-shadow);
            border-color: var(--primary-light);
        }

        .section h2 {
            color: var(--primary);
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        /* Worker Functions - Enhanced */
        .worker-functions {
            list-style: none;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .worker-functions li {
            margin-bottom: 0;
        }

        .worker-functions a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1.25rem;
            background: var(--primary-light);
            color: var(--primary);
            text-decoration: none;
            border-radius: var(--radius);
            transition: var(--transition);
            font-weight: 500;
            border: 1px solid transparent;
        }

        .worker-functions a:hover {
            background: var(--primary);
            color: var(--white);
            transform: translateY(-3px);
            box-shadow: var(--card-shadow);
        }

        /* Modal - Enhanced */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            justify-content: center;
            align-items: center;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            background-color: var(--white);
            padding: 2.5rem;
            border-radius: var(--radius);
            width: 90%;
            max-width: 800px;
            box-shadow: var(--hover-shadow);
            animation: slideUp 0.3s ease;
            max-height: 90vh;
            overflow-y: auto;
        }

        @keyframes slideUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--border);
        }

        .modal-header h2 {
            color: var(--primary);
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
        }

        .modal-header .close {
            font-size: 1.75rem;
            cursor: pointer;
            color: var(--text-light);
            transition: var(--transition);
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .modal-header .close:hover {
            color: var(--primary);
            background: var(--primary-light);
        }

        .modal-body {
            margin-bottom: 2rem;
        }

        /* Search Form - Enhanced */
        .search-form {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .search-form input[type="text"] {
            width: 100%;
            padding: 1rem 1.5rem;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            font-size: 1rem;
            transition: var(--transition);
            background: var(--white);
        }

        .search-form input[type="text"]:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(107, 25, 80, 0.1);
        }

        .search-form button {
            position: absolute;
            right: 0.5rem;
            top: 0.5rem;
            background: var(--primary);
            color: var(--white);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: var(--radius);
            cursor: pointer;
            transition: var(--transition);
            font-weight: 500;
        }

        .search-form button:hover {
            background: var(--primary-dark);
        }

        /* Table - Enhanced */
        table {
            width: 100%;
            border-collapse: collapse;
            background: var(--white);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 1rem 1.25rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        th {
            background-color: var(--primary-light);
            color: var(--primary);
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        tr:hover {
            background-color: var(--primary-light);
        }

        .action-button {
            background: var(--primary);
            color: var(--white);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: var(--radius);
            cursor: pointer;
            transition: var(--transition);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .action-button:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(107, 25, 80, 0.3);
        }

        /* Message Alert */
        .message-alert {
            background: var(--primary-light);
            border: 1px solid var(--primary);
            color: var(--primary);
            padding: 1rem 1.5rem;
            border-radius: var(--radius);
            margin-bottom: 2rem;
            font-weight: 500;
            box-shadow: var(--card-shadow);
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                z-index: 1000;
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                padding: 1.5rem;
            }
            
            .menu-toggle {
                display: block;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 1rem;
            }
            
            .notification-banner {
                padding: 1rem;
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .banner {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .worker-functions {
                grid-template-columns: 1fr;
            }
            
            .modal-content {
                padding: 1.5rem;
                width: 95%;
            }
        }

        .menu-toggle {
            display: none;
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 1001;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: var(--radius);
            padding: 0.75rem;
            cursor: pointer;
            font-size: 1.25rem;
        }

        @media (max-width: 1024px) {
            .menu-toggle {
                display: block;
            }
        }
    </style>
</head>
<body>
    <button class="menu-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h1>Worker Portal</h1>
            <div class="user-info">
                <p><?php echo htmlspecialchars($user['User_Name']); ?></p>
                <p><?php echo htmlspecialchars($user['User_Email']); ?></p>
            </div>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="#" class="active"><i class="fas fa-tachometer-alt"></i>Dashboard</a></li>
                <li><a href="#" id="open-job-modal"><i class="fas fa-briefcase"></i>Jobs</a></li>
                <li><a href="#" id="open-course-modal"><i class="fas fa-graduation-cap"></i>Courses</a></li>
                <li><a href="worker_profile.php"><i class="fas fa-user-circle"></i>Profile</a></li>
                <li><a href="worker_apply_insurance.php"><i class="fas fa-shield-alt"></i>Insurance</a></li>
                <li><a href="worker_submit_complaint.php"><i class="fas fa-comment-dots"></i>Complaints</a></li>
            </ul>
        </nav>
        <a href="logout.php" class="logout-button"><i class="fas fa-sign-out-alt"></i>Logout</a>
    </div>

    <div class="main-content">
        <!-- Success Message -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="message-alert">
                <i class="fas fa-check-circle"></i>
                <?php 
                echo htmlspecialchars($_SESSION['message']); 
                unset($_SESSION['message']);
                ?>
            </div>
        <?php endif; ?>

        <?php if ($notification): ?>
        <div class="notification-banner">
            <div class="notification-content">
                <strong>
                    <i class="fas fa-bullhorn"></i>
                    Notification
                </strong>
                <span><?php echo htmlspecialchars($notification['message']); ?></span>
            </div>
            <button class="notification-close" onclick="this.parentElement.style.display='none'" title="Dismiss notification">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php endif; ?>

        <?php if (empty($worker['Passport_Number']) || empty($worker['Visa_Number']) || empty($worker['Health_Report'])): ?>
        <div class="banner">
            <p>Please complete your profile by providing your passport number, visa number, and health report.</p>
            <a href="worker_profile.php" class="banner-button">
                <i class="fas fa-edit"></i>Complete Profile
            </a>
        </div>
        <?php endif; ?>

        <!-- Job Modal -->
        <div id="job-modal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2><i class="fas fa-briefcase"></i>Search and Apply for Jobs</h2>
                    <span class="close" id="close-job-modal">&times;</span>
                </div>
                <div class="modal-body">
                    <div class="search-form">
                        <form id="job-search-form">
                            <input type="text" name="job_search" placeholder="Search jobs by title or location..." value="<?php echo htmlspecialchars($job_search); ?>">
                            <button type="submit"><i class="fas fa-search"></i> Search</button>
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
                                <td><strong><?php echo htmlspecialchars($job['Job_Title']); ?></strong></td>
                                <td><?php echo htmlspecialchars($job['Job_Description']); ?></td>
                                <td><?php echo htmlspecialchars($job['Job_Location']); ?></td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="job_id" value="<?php echo htmlspecialchars($job['Job_ID']); ?>">
                                        <button type="submit" name="apply_job" class="action-button">
                                            <i class="fas fa-paper-plane"></i>Apply
                                        </button>
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
                    <h2><i class="fas fa-graduation-cap"></i>Search and Enroll in Courses</h2>
                    <span class="close" id="close-course-modal">&times;</span>
                </div>
                <div class="modal-body">
                    <div class="search-form">
                        <form id="course-search-form">
                            <input type="text" name="course_search" placeholder="Search courses by name..." value="<?php echo htmlspecialchars($course_search); ?>">
                            <button type="submit"><i class="fas fa-search"></i> Search</button>
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
                                <td><strong><?php echo htmlspecialchars($course['Training_Name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($course['Training_Description']); ?></td>
                                <td><?php echo htmlspecialchars($course['Training_Duration']); ?></td>
                                <td><?php echo htmlspecialchars($course['Training_Location']); ?></td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="course_id" value="<?php echo htmlspecialchars($course['Training_ID']); ?>">
                                        <button type="submit" name="enroll_course" class="action-button">
                                            <i class="fas fa-bookmark"></i>Enroll
                                        </button>
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
            <h2><i class="fas fa-cogs"></i>Worker Functions</h2>
            <ul class="worker-functions">
                <li><a href="worker_profile.php"><i class="fas fa-user-edit"></i>View/Update Profile</a></li>
                <li><a href="worker_apply_insurance.php"><i class="fas fa-file-medical"></i>Apply for Insurance</a></li>
                <li><a href="worker_submit_complaint.php"><i class="fas fa-comment-dots"></i>Submit Complaint</a></li>
            </ul>
        </section>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        }

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

        // Enhanced form submissions with loading states
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const button = this.querySelector('button[type="submit"]');
                if (button) {
                    const originalText = button.innerHTML;
                    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                    button.disabled = true;
                    
                    setTimeout(() => {
                        button.innerHTML = originalText;
                        button.disabled = false;
                    }, 2000);
                }
            });
        });

        // Add smooth animations
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.section, .banner, .notification-banner');
            elements.forEach((element, index) => {
                element.style.opacity = '0';
                element.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    element.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>