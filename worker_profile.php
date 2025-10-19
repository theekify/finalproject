<?php
session_start();
require 'db.php';

// Check if user is logged in and is a worker
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Worker') {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch worker details
$stmt = $conn->prepare("SELECT * FROM worker WHERE User_ID = ?");
$stmt->execute([$user_id]);
$worker = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch user details
$stmt = $conn->prepare("SELECT * FROM user WHERE User_ID = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$worker) {
    echo "Worker profile not found.";
    exit();
}

// Let's check the constraints on the worker table
try {
    $stmt = $conn->prepare("SELECT CONSTRAINT_NAME, CHECK_CLAUSE FROM information_schema.CHECK_CONSTRAINTS WHERE TABLE_NAME = 'worker'");
    $stmt->execute();
    $constraints = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $constraints = [];
}

// Handle profile update
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $passport_number = trim($_POST['passport_number'] ?? '');
    $visa_number = trim($_POST['visa_number'] ?? '');
    
    // Handle health report - use existing value if no file uploaded
    $health_report = $worker['Health_Report'];
    
    if (isset($_FILES['health_report']) && $_FILES['health_report']['error'] === UPLOAD_ERR_OK) {
        // For now, just mark that a file was uploaded
        // In production, you'd want to save the file and store the filename
        $health_report = 'Uploaded';
    }

    try {
        // Use NULL for empty values to avoid constraint violations
        $passport_number = !empty($passport_number) ? $passport_number : null;
        $visa_number = !empty($visa_number) ? $visa_number : null;
        
        // For health report, use specific allowed values
        if ($health_report === 'Uploaded') {
            $health_report = 'Pending'; // Set to Pending after upload for staff review
        } elseif (empty($health_report)) {
            $health_report = 'Pending';
        }

        // Update worker profile
        $stmt = $conn->prepare("UPDATE worker SET Passport_Number = ?, Visa_Number = ?, Health_Report = ? WHERE User_ID = ?");
        $stmt->execute([$passport_number, $visa_number, $health_report, $user_id]);
        
        $message = "Profile updated successfully! Wait for staff approval.";
        
        // Refresh worker data
        $stmt = $conn->prepare("SELECT * FROM worker WHERE User_ID = ?");
        $stmt->execute([$user_id]);
        $worker = $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        // Try alternative approach with more specific values
        try {
            // Use specific allowed values for health report
            $allowed_health_values = ['Pending', 'Approved', 'Rejected', 'Uploaded'];
            $health_report = in_array($health_report, $allowed_health_values) ? $health_report : 'Pending';
            
            $stmt = $conn->prepare("UPDATE worker SET Passport_Number = ?, Visa_Number = ?, Health_Report = ? WHERE User_ID = ?");
            $stmt->execute([$passport_number, $visa_number, $health_report, $user_id]);
            
            $message = "Profile updated successfully! Wait for staff approval.";
            
            // Refresh worker data
            $stmt = $conn->prepare("SELECT * FROM worker WHERE User_ID = ?");
            $stmt->execute([$user_id]);
            $worker = $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e2) {
            $message = "Error updating profile. Please contact support.";
            error_log("Profile update error: " . $e2->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Worker Profile</title>
    <style>
        :root {
            --primary: #6b1950;
            --primary-light: #f8f1f5;
            --text-primary: #1a1a1a;
            --text-secondary: #666;
            --text-light: #999;
            --border: #e0e0e0;
            --background: #fafafa;
            --white: #ffffff;
            --card-shadow: 0 2px 8px rgba(0,0,0,0.04);
            --hover-shadow: 0 4px 20px rgba(0,0,0,0.08);
            --success: #38a169;
            --warning: #d69e2e;
            --error: #e53e3e;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background: var(--background);
            color: var(--text-primary);
            line-height: 1.6;
            padding: 2rem;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: var(--white);
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, var(--primary) 0%, #8b2c65 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .header p {
            opacity: 0.9;
        }

        .profile-content {
            padding: 2rem;
        }

        .banner {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            text-align: center;
            font-weight: 500;
        }

        .banner.success {
            background: var(--success);
            color: white;
        }

        .banner.warning {
            background: var(--warning);
            color: white;
        }

        .banner.error {
            background: var(--error);
            color: white;
        }

        .profile-info {
            background: var(--primary-light);
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid rgba(107, 25, 80, 0.1);
        }

        .info-item:last-child {
            margin-bottom: 0;
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: var(--primary);
        }

        .info-value {
            color: var(--text-primary);
        }

        .profile-form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-group label {
            font-weight: 600;
            color: var(--text-primary);
        }

        .form-group input {
            padding: 0.75rem;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(107, 25, 80, 0.1);
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            justify-content: center;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: #5a1642;
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text-secondary);
        }

        .btn-outline:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-completed {
            background: #d1edff;
            color: #0c5460;
        }

        .form-help {
            font-size: 0.75rem;
            color: var(--text-light);
            margin-top: 0.25rem;
        }

        .required {
            color: var(--error);
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }
            
            .profile-content {
                padding: 1.5rem;
            }
            
            .form-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Worker Profile</h1>
            <p>Manage your profile information</p>
        </div>

        <div class="profile-content">
            <?php if ($message): ?>
                <div class="banner <?php 
                    if (strpos($message, 'successfully') !== false) echo 'success';
                    elseif (strpos($message, 'Error') !== false) echo 'error';
                    else echo 'warning';
                ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <!-- Profile Completion Warning -->
            <?php 
            $profile_complete = true;
            $missing_fields = [];
            
            if (empty($worker['Passport_Number'])) {
                $profile_complete = false;
                $missing_fields[] = 'Passport Number';
            }
            if (empty($worker['Visa_Number'])) {
                $profile_complete = false;
                $missing_fields[] = 'Visa Number';
            }
            if (empty($worker['Health_Report']) || $worker['Health_Report'] === 'Pending') {
                $profile_complete = false;
                $missing_fields[] = 'Health Report';
            }
            
            if (!$profile_complete): 
            ?>
            <div class="banner warning">
                <strong>Complete Your Profile</strong>
                <p>Please provide the following information: <?php echo implode(', ', $missing_fields); ?></p>
            </div>
            <?php endif; ?>

            <!-- Current Profile Information -->
            <div class="profile-info">
                <h3 style="margin-bottom: 1rem; color: var(--primary);">Current Information</h3>
                <div class="info-item">
                    <span class="info-label">Name:</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['User_Name']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['User_Email']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Phone:</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['User_Phone'] ?? 'Not provided'); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Address:</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['User_Address'] ?? 'Not provided'); ?></span>
                </div>
            </div>

            <!-- Update Profile Form -->
            <form class="profile-form" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="passport_number">
                        Passport Number <span class="required">*</span>
                    </label>
                    <input type="text" id="passport_number" name="passport_number" 
                           value="<?php echo htmlspecialchars($worker['Passport_Number'] ?? ''); ?>" 
                           placeholder="Enter your passport number" 
                           required
                           minlength="6"
                           maxlength="20">
                    <div class="form-help">Required. 6-20 characters (letters and numbers only)</div>
                </div>

                <div class="form-group">
                    <label for="visa_number">
                        Visa Number <span class="required">*</span>
                    </label>
                    <input type="text" id="visa_number" name="visa_number" 
                           value="<?php echo htmlspecialchars($worker['Visa_Number'] ?? ''); ?>" 
                           placeholder="Enter your visa number"
                           required
                           minlength="6"
                           maxlength="20">
                    <div class="form-help">Required. 6-20 characters (letters and numbers only)</div>
                </div>

                <div class="form-group">
                    <label for="health_report">
                        Health Report <span class="required">*</span>
                    </label>
                    <input type="file" id="health_report" name="health_report" accept=".pdf,.doc,.docx,image/*" required>
                    <div class="form-help">
                        Required. Upload your health report (PDF, Word, or image files). 
                        Current status: 
                        <span class="status-badge <?php echo ($worker['Health_Report'] && $worker['Health_Report'] !== 'Pending') ? 'status-completed' : 'status-pending'; ?>">
                            <?php echo htmlspecialchars($worker['Health_Report'] ?? 'Not Uploaded'); ?>
                        </span>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Profile
                    </button>
                    <a href="worker_dashboard.php" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>