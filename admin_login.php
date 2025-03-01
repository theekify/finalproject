<?php
session_start();

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch admin from database
    $stmt = $conn->prepare("SELECT user.* FROM user 
                            JOIN admin ON user.User_ID = admin.User_ID 
                            WHERE user.User_Email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['User_Password'])) {
        // Set session variables
        $_SESSION['user_id'] = $admin['User_ID'];
        $_SESSION['user_role'] = $admin['User_Role'];
        $_SESSION['user_email'] = $admin['User_Email'];

        // Redirect to admin dashboard
        header('Location: admin_dashboard.php');
    } else {
        $error_message = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
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

        .error-message {
            color: #ef4444;
            margin-bottom: 1rem;
        }

        p {
            text-align: center;
            margin-top: 1rem;
        }

        a {
            color: var(--primary);
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Login</h1>
        <?php if (isset($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form method="POST">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a>.</p>
    </div>
</body>
</html>
