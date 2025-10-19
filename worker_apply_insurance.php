<?php
session_start(); // Start the session

require 'db.php';

// Handle insurance application
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $policy_number = $_POST['policy_number'];
    $provider_name = $_POST['provider_name'];
    $premium = $_POST['premium']; // This will be 'yes' or 'no'

    // Validate inputs
    if (empty($policy_number) || empty($provider_name) || empty($premium)) {
        $error_message = "Please fill in all fields.";
    } else {
        // Convert premium to appropriate value for database
        $premium_value = ($premium === 'yes') ? 1 : 0;
        
        // Insert insurance application into the database
        try {
            $stmt = $conn->prepare("INSERT INTO insurance (Worker_ID, Policy_Number, Provider_Name, Premium, Insurance_Status) VALUES (?, ?, ?, ?, 'Pending')");
            $stmt->execute([$_SESSION['user_id'], $policy_number, $provider_name, $premium_value]);

            $success_message = "Insurance application submitted successfully! Wait for staff approval.";
        } catch (PDOException $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Insurance</title>
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
            --radius: 12px;
            --success: #38a169;
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
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            max-width: 500px;
            width: 100%;
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .content {
            padding: 2rem;
        }

        .error-message {
            background: #fed7d7;
            border: 1px solid #feb2b2;
            color: #c53030;
            padding: 1rem;
            border-radius: var(--radius);
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .success-message {
            background: #c6f6d5;
            border: 1px solid #9ae6b4;
            color: #276749;
            padding: 1rem;
            border-radius: var(--radius);
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .insurance-form {
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

        .form-group input,
        .form-group select {
            padding: 0.75rem;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(107, 25, 80, 0.1);
        }

        .premium-options {
            display: flex;
            gap: 1rem;
            margin-top: 0.5rem;
        }

        .premium-option {
            flex: 1;
            text-align: center;
        }

        .premium-option input[type="radio"] {
            display: none;
        }

        .premium-option label {
            display: block;
            padding: 1rem;
            border: 2px solid var(--border);
            border-radius: var(--radius);
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .premium-option input[type="radio"]:checked + label {
            border-color: var(--primary);
            background: var(--primary-light);
            color: var(--primary);
        }

        .premium-option label:hover {
            border-color: var(--primary);
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: var(--radius);
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
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
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(107, 25, 80, 0.3);
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

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }
            
            .content {
                padding: 1.5rem;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .premium-options {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Apply for Insurance</h1>
        </div>

        <div class="content">
            <?php if (isset($error_message)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($success_message)): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="insurance-form">
                <div class="form-group">
                    <label for="policy_number">Policy Number:</label>
                    <input type="text" id="policy_number" name="policy_number" required placeholder="Enter policy number">
                </div>
                
                <div class="form-group">
                    <label for="provider_name">Provider Name:</label>
                    <input type="text" id="provider_name" name="provider_name" required placeholder="Enter insurance provider name">
                </div>
                
                <div class="form-group">
                    <label>Premium Paid:</label>
                    <div class="premium-options">
                        <div class="premium-option">
                            <input type="radio" id="premium_yes" name="premium" value="yes" required>
                            <label for="premium_yes">Yes</label>
                        </div>
                        <div class="premium-option">
                            <input type="radio" id="premium_no" name="premium" value="no" required>
                            <label for="premium_no">No</label>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Apply for Insurance
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