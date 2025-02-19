<?php

require 'db.php';

// Fetch admin details
$stmt = $conn->prepare("SELECT * FROM admin WHERE User_ID = ?");
$stmt->execute([1]); // Replace 1 with the actual user ID you want to fetch
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Update admin profile
    $stmt = $conn->prepare("UPDATE admin SET Admin_Name = ?, Admin_Email = ?, Admin_Phone = ? WHERE User_ID = ?");
    $stmt->execute([$name, $email, $phone, 1]); // Replace 1 with the actual user ID you want to update

    echo "Profile updated successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Profile</title>
    <link rel="stylesheet" href="admin_profile.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
</head>
<body>
    <h1>Admin Profile</h1>
    <div class="profile-form">
        <form method="POST">
            Name: <input type="text" name="name" value="<?php echo $admin['Admin_Name']; ?>" required><br>
            Email: <input type="email" name="email" value="<?php echo $admin['Admin_Email']; ?>" required><br>
            Phone: <input type="text" name="phone" value="<?php echo $admin['Admin_Phone']; ?>"><br>
            <button type="submit">Save Changes</button>
        </form>
    </div>
    <a href="admin_dashboard.php">Back to Dashboard</a>
</body>
</html>
