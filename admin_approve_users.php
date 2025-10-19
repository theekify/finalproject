<?php
require 'db.php';

// Fetch pending users
$stmt = $conn->prepare("SELECT * FROM user WHERE User_Status = 'Pending'");
$stmt->execute();
$pending_users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle approval/rejection - AJAX endpoint
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id']) && isset($_POST['action'])) {
    $user_id = $_POST['user_id'];
    $action = $_POST['action']; // 'approve' or 'reject'
    
    // Set content type to JSON
    header('Content-Type: application/json');
    
    try {
        if ($action === 'approve') {
            // Update user status to 'Approved'
            $stmt = $conn->prepare("UPDATE user SET User_Status = 'Approved' WHERE User_ID = ?");
            $stmt->execute([$user_id]);
            
            // Also update status in respective role table
            $stmt = $conn->prepare("SELECT User_Role FROM user WHERE User_ID = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $role = $user['User_Role'];
                
                if ($role === 'Admin') {
                    $stmt = $conn->prepare("UPDATE admin SET Admin_Status = 'Active' WHERE User_ID = ?");
                } elseif ($role === 'Staff') {
                    $stmt = $conn->prepare("UPDATE staff SET Staff_Status = 'Approved' WHERE User_ID = ?");
                } elseif ($role === 'Agency') {
                    $stmt = $conn->prepare("UPDATE agency SET Approval_Status = 'Approved' WHERE User_ID = ?");
                } elseif ($role === 'Worker') {
                    $stmt = $conn->prepare("UPDATE worker SET Training_Status = 'Completed', Insurance_Status = 'Active' WHERE User_ID = ?");
                }
                
                if (isset($stmt)) {
                    $stmt->execute([$user_id]);
                }
            }
            
            echo json_encode(['success' => true, 'message' => 'User approved successfully!']);
            exit;
            
        } elseif ($action === 'reject') {
            // Get user role before deleting
            $stmt = $conn->prepare("SELECT User_Role FROM user WHERE User_ID = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $role = $user['User_Role'];
                
                // Delete from respective table based on role
                if ($role === 'Admin') {
                    $stmt = $conn->prepare("DELETE FROM admin WHERE User_ID = ?");
                } elseif ($role === 'Staff') {
                    $stmt = $conn->prepare("DELETE FROM staff WHERE User_ID = ?");
                } elseif ($role === 'Agency') {
                    $stmt = $conn->prepare("DELETE FROM agency WHERE User_ID = ?");
                } elseif ($role === 'Worker') {
                    $stmt = $conn->prepare("DELETE FROM worker WHERE User_ID = ?");
                }
                
                if (isset($stmt)) {
                    $stmt->execute([$user_id]);
                }
                
                // Finally delete from user table
                $stmt = $conn->prepare("DELETE FROM user WHERE User_ID = ?");
                $stmt->execute([$user_id]);
            }

            echo json_encode(['success' => true, 'message' => 'User rejected and deleted.']);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid action.']);
            exit;
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Approve/Reject Users</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --primary: #6b1950;
            --primary-light: #f8f9fc;
            --text-primary: #1a1a1a;
            --text-secondary: #666666;
            --border: #e5e7eb;
            --background: #f1f5f9;
            --white: #ffffff;
            --shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            --radius: 8px;
            --success: #4CAF50;
            --error: #f44336;
        }

        body {
            background-color: var(--background);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            padding: 2rem;
        }

        h1 {
            color: var(--primary);
            margin-bottom: 1.5rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        th {
            background-color: var(--primary-light);
            color: var(--primary);
        }

        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: var(--radius);
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s;
            margin: 0 0.25rem;
        }

        .btn.approve {
            background-color: var(--success);
            color: var(--white);
        }

        .btn.reject {
            background-color: var(--error);
            color: var(--white);
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            border-radius: var(--radius);
            color: white;
            font-weight: 500;
            box-shadow: var(--shadow);
            z-index: 1000;
            transform: translateX(120%);
            transition: transform 0.3s ease;
            max-width: 300px;
        }

        .notification.show {
            transform: translateX(0);
        }

        .notification.success {
            background-color: var(--success);
        }

        .notification.error {
            background-color: var(--error);
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--text-secondary);
        }

        .actions {
            display: flex;
            gap: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="notification" id="notification"></div>
    
    <div class="container">
        <h1>Pending Users</h1>
        
        <?php if (empty($pending_users)): ?>
            <div class="empty-state">
                <h2>No pending users</h2>
                <p>All user requests have been processed.</p>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="users-table">
                    <?php foreach ($pending_users as $user): ?>
                    <tr id="user-<?php echo $user['User_ID']; ?>">
                        <td><?php echo htmlspecialchars($user['User_ID']); ?></td>
                        <td><?php echo htmlspecialchars($user['User_Name']); ?></td>
                        <td><?php echo htmlspecialchars($user['User_Email']); ?></td>
                        <td><?php echo htmlspecialchars($user['User_Role']); ?></td>
                        <td>
                            <div class="actions">
                                <button type="button" onclick="approveUser(<?php echo $user['User_ID']; ?>)" class="btn approve">Approve</button>
                                <button type="button" onclick="rejectUser(<?php echo $user['User_ID']; ?>)" class="btn reject">Reject</button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <script>
        function approveUser(userId) {
            processUserAction(userId, 'approve');
        }

        function rejectUser(userId) {
            processUserAction(userId, 'reject');
        }

        function processUserAction(userId, action) {
            const button = event.target;
            const row = document.getElementById(`user-${userId}`);
            const buttons = row.querySelectorAll('.btn');
            
            // Disable buttons during request
            buttons.forEach(btn => {
                btn.disabled = true;
            });
            
            // Create form data
            const formData = new FormData();
            formData.append('user_id', userId);
            formData.append('action', action);
            
            // Send AJAX request
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    // Remove the user row from the table
                    row.remove();
                    
                    // Check if table is empty and show empty state
                    const tableBody = document.getElementById('users-table');
                    if (tableBody && tableBody.children.length === 0) {
                        location.reload();
                    }
                } else {
                    showNotification(data.message || 'An error occurred', 'error');
                    // Re-enable buttons on error
                    buttons.forEach(btn => {
                        btn.disabled = false;
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred. Please try again.', 'error');
                // Re-enable buttons
                buttons.forEach(btn => {
                    btn.disabled = false;
                });
            });
        }
        
        function showNotification(message, type) {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.className = `notification ${type} show`;
            
            setTimeout(() => {
                notification.classList.remove('show');
            }, 4000);
        }
    </script>
</body>
</html>