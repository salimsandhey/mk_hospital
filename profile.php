<?php
include 'dbConnect.php';
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];
$query = "SELECT * FROM client_login WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $newPassword = $_POST['password'];

    if (!empty($newPassword)) {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $updateQuery = "UPDATE client_login SET name = ?, password = ? WHERE username = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("sss", $name, $hashedPassword, $username);
    } else {
        $updateQuery = "UPDATE client_login SET name = ? WHERE username = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("ss", $name, $username);
    }

    if ($stmt->execute()) {
        $successMessage = "Profile updated successfully!";
    } else {
        $errorMessage = "Error updating profile.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctor Profile</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include"header.php"?>
<div class="container-fix profile">
    <h2>Edit Profile</h2>
    <?php if (isset($successMessage)) : ?>
        <div class="alert alert-success"><?php echo $successMessage; ?></div>
    <?php elseif (isset($errorMessage)) : ?>
        <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
    <?php endif; ?>
    <form action="" method="POST">
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($doctor['name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">New Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank to keep current password">
        </div>
        <button type="submit" class="btn custom-btn">Save Changes</button>
    </form>
</div>
<script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
