<?php
include 'auth.php';
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
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $disease = $_POST['disease'];
    $contact = $_POST['contact'];  // Get the updated phone number

    $update_sql = "UPDATE patient SET name='$name', age='$age', gender='$gender', address='$address', disease='$disease', contact='$contact' WHERE id = $patient_id";
    
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
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Responsive styles for small screens */
        @media (max-width: 768px) {
            .container-fix {
                margin-left: 10px;
                margin-right: 10px;
            }
            
            .card-body .form-group {
                margin-bottom: 15px;
            }
            
            .button-group {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }
            
            .button-group .btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    
    <div class="container-fix mb-4">
        <!-- Edit Profile Form -->
        <div class="card profile-box">
            <div class="profile-head">
                <h5 class="card-title">Edit Patient Profile</h5>
                <h6 class="card-subtitle primary-color">Patient ID: <?php echo $patient_id ?></h6>
            </div>
            <div class="card-body">
                <!-- Form to edit patient details -->
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 for="name" class="form-label">Name</h6>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo $patient['name']; ?>" required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <h6 for="age" class="form-label">Age</h6>
                            <input type="number" class="form-control" id="age" name="age" value="<?php echo $patient['age']; ?>" required>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <h6 for="gender" class="form-label">Gender</h6>
                            <select class="form-select" id="gender" name="gender" required>
                                <option value="male" <?php echo ($patient['gender'] == 'male') ? 'selected' : ''; ?>>Male</option>
                                <option value="female" <?php echo ($patient['gender'] == 'female') ? 'selected' : ''; ?>>Female</option>
                                <option value="other" <?php echo ($patient['gender'] == 'other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <h6 for="contact" class="form-label">Phone Number</h6>
                        <input type="text" class="form-control" id="contact" name="contact" value="<?php echo $patient['contact']; ?>" required>
                    </div>

                    <div class="mb-3">
                        <h6 for="address" class="form-label">Address</h6>
                        <textarea class="form-control" id="address" name="address" rows="2" required><?php echo $patient['address']; ?></textarea>
                    </div>

                    <div class="mb-3">
                        <h6 for="disease" class="form-label">Disease</h6>
                        <textarea class="form-control" id="disease" name="disease" rows="2" required><?php echo $patient['disease']; ?></textarea>
                    </div>

                    <div class="d-flex justify-content-end button-group mt-4">
                        <a href="patientDetails.php?id=<?php echo $patient_id; ?>" class="btn btn-secondary me-2">Cancel</a>
                        <button type="submit" class="btn custom-btn">Update Profile</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>
