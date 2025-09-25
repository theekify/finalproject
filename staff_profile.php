<?php
require 'db.php';

// Fetch staff details
$stmt = $conn->prepare("SELECT * FROM staff WHERE User_ID = ?");
$stmt->execute([$_GET['user_id']]);
$staff = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    // Update staff profile
    $stmt = $conn->prepare("UPDATE staff SET Staff_Name = ?, Staff_Email = ?, Staff_Phone = ?, Staff_Address = ? WHERE User_ID = ?");
    $stmt->execute([$name, $email, $phone, $address, $_GET['user_id']]);

    echo "Profile updated successfully!";

}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Staff Profile</title>
    <link rel="stylesheet" href="staff_profile.css">
</head>
<body>
    <h1>Staff Profile</h1>
    <div class="profile-form">
        <form method="POST">
            Name: <input type="text" name="name" value="<?php echo $staff['Staff_Name']; ?>" required><br>
            Email: <input type="email" name="email" value="<?php echo $staff['Staff_Email']; ?>" required><br>
            Phone: <input type="text" name="phone" value="<?php echo $staff['Staff_Phone']; ?>"><br>
            Address: <input type="text" name="address" value="<?php echo $staff['Staff_Address']; ?>"><br>
            <button type="submit">Save Changes</button>
        </form>
    </div>
    <a href="staff_dashboard.php">Back to Dashboard</a>
</body>
</html>
