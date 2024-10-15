<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php'); // Redirect to login page
    exit;
}
?>
<?php
// Include the database connection file
include 'dbConnect.php';

// Get patient_id from query parameters (passed in URL)
$patient_id = $_GET['id'];  // Assuming you're passing the patient_id in the URL like: editProfile.php?id=1

// Fetch patient details to pre-fill the form
$patient_sql = "SELECT * FROM patient WHERE id = $patient_id";
$patient_result = mysqli_query($conn, $patient_sql);
$patient = mysqli_fetch_assoc($patient_result);

// Update patient information if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $address = $_POST['address'];
    $disease = $_POST['disease'];
    $contact = $_POST['contact'];  // Get the updated phone number

    $update_sql = "UPDATE patient SET name='$name', age='$age', address='$address', disease='$disease', contact='$contact' WHERE id = $patient_id";
    
    if (mysqli_query($conn, $update_sql)) {
        echo "<div class='alert alert-success'>Profile updated successfully.</div>";
        header("Location: patientDetails.php?id=$patient_id");
    } else {
        echo "<div class='alert alert-danger'>Error updating profile: " . mysqli_error($conn) . "</div>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Patient Profile</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
</head>

<body>
    <?php
        include 'header.php';
    ?>
    <div class="container mt-5">
        <!-- Edit Profile Form -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4>Edit Patient Profile</h4>
            </div>
            <div class="card-body">
                <!-- Form to edit patient details -->
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo $patient['name']; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="age" class="form-label">Age</label>
                        <input type="number" class="form-control" id="age" name="age" value="<?php echo $patient['age']; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3" required><?php echo $patient['address']; ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="disease" class="form-label">Disease</label>
                        <textarea class="form-control" id="disease" name="disease" rows="3" required><?php echo $patient['disease']; ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="contact" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="contact" name="contact" value="<?php echo $patient['contact']; ?>" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Profile</button>
                    <a href="patientDetails.php?id=<?php echo $patient_id; ?>" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>
