<?php
session_start();

require 'db.php';

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch user from database
    $stmt = $conn->prepare("SELECT * FROM user WHERE User_Email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Check if password is correct
        if (password_verify($password, $user['User_Password'])) {
            // Check if user is approved
            if ($user['User_Status'] === 'Approved') {
                // Set session variables
                $_SESSION['user_id'] = $user['User_ID'];
                $_SESSION['user_role'] = $user['User_Role'];
                $_SESSION['user_email'] = $user['User_Email'];
                $_SESSION['user_status'] = $user['User_Status'];
                $_SESSION['user_name'] = $user['User_Name'];

                // Redirect based on role
                switch ($user['User_Role']) {
                    case 'Admin':
                        header('Location: admin_dashboard.php');
                        break;
                    case 'Staff':
                        header('Location: staff_dashboard.php');
                        break;
                    case 'Agency':
                        header('Location: agency_dashboard.php');
                        break;
                    case 'Worker':
                        header('Location: worker_dashboard.php');
                        break;
                    default:
                        header('Location: dashboard.php');
                }
                exit;
            } else {
                // User exists but is not approved
                $error_message = "Your account is pending approval. Please wait for administrator approval.";
            }
        } else {
            // Password is incorrect
            $error_message = "Invalid email or password.";
        }
    } else {
        // User doesn't exist
        $error_message = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SLBFE Job Portal</title>
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
            --warning: #f59e0b;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--background);
            color: var(--text-primary);
            line-height: 1.5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background-color: var(--white);
            padding: 2rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 400px;
        }

        h1 {
            color: var(--primary);
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 0.5rem;
            color: var(--text-secondary);
        }

        input {
            padding: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid #e2e8f0;
            border-radius: var(--radius);
            font-size: 1rem;
        }

        button {
            background-color: var(--primary);
            color: var(--white);
            padding: 0.75rem;
            border: none;
            border-radius: var(--radius);
            cursor: pointer;
            transition: background-color 0.2s;
            font-size: 1rem;
            font-weight: 500;
        }

        button:hover {
            background-color: #5a1642;
        }

        .error-message {
            color: #ef4444;
            margin-bottom: 1rem;
            text-align: center;
            padding: 0.75rem;
            background-color: #fef2f2;
            border-radius: var(--radius);
            border: 1px solid #fecaca;
        }

        .warning-message {
            color: var(--warning);
            margin-bottom: 1rem;
            text-align: center;
            padding: 0.75rem;
            background-color: #fffbeb;
            border-radius: var(--radius);
            border: 1px solid #fef3c7;
        }

        p {
            text-align: center;
            margin-top: 1rem;
        }

        a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
                margin: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Login to SLBFE Job Portal</h1>
        
        <?php if ($error_message): ?>
            <?php if (strpos($error_message, 'pending approval') !== false): ?>
                <div class="warning-message">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php else: ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <form method="POST">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a>.</p>
    </div>
</body>
</html>