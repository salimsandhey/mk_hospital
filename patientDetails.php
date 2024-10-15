<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php'); // Redirect to login page
    exit;
}

include 'dbConnect.php';
$patient_id = $_GET['id'];

// Fetch patient details
$query = "SELECT * FROM patient WHERE id = '$patient_id'";
$result = mysqli_query($conn, $query);

// Fetch visit details
$visits_sql = "SELECT * FROM visits WHERE patient_id = $patient_id ORDER BY visit_date DESC";
$visits_result = mysqli_query($conn, $visits_sql);

if ($row = mysqli_fetch_assoc($result)) {
    // Store patient data in variables
    $name = $row['name'];
    $age = $row['age'];
    $address = $row['address'];
    $disease = $row['disease'];
    $contact = $row['contact'];
} else {
    echo "No patient found with ID " . htmlspecialchars($patient_id);
    exit; // Stop execution if no patient found
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Profile</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <style>
        .bg-custom {
            background-color: #007BFF;
            color: white;
        }

        .table th {
            background-color: #e9ecef;
        }

        /* Make table rows clickable */
        .clickable-row {
            cursor: pointer;
        }

        .clickable-row:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <div class="card mb-4">
            <div class="card-header bg-custom">
                <h5 class="card-title">Patient Profile</h5>
                <h6 class="card-subtitle text-muted">Patient ID: <?php echo $patient_id ?></h6>
            </div>
            <div class="card-body">
                <h5 class="card-title">Personal Information</h5>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($name) ?></p>
                <p><strong>Age:</strong> <?php echo htmlspecialchars($age) ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($address) ?></p>
                <p><strong>Contact:</strong> <?php echo htmlspecialchars($contact) ?></p>
                <p><strong>Diseases:</strong> <?php echo htmlspecialchars($disease) ?></p>
                <div class="d-flex justify-content-end">
                    <a href="editPatient.php?id=<?php echo $patient_id ?>" class="btn btn-primary me-2">Edit Profile</a>
                    <!-- Trigger for Delete Modal -->
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" 
                        data-id="<?php echo $patient_id ?>" data-name="<?php echo htmlspecialchars($name) ?>">
                        Delete Profile
                    </button>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Visits</h4>
                    <!-- New Visit Button in the Header -->
                    <a href="visitRecord.php?id=<?php echo $patient_id ?>" class="btn btn-primary">New Visit</a>
                </div>
                <div class="card-body">
                    <?php if (mysqli_num_rows($visits_result) > 0) { ?>
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Visit Date</th>
                                    <th>Treatment</th>
                                    <!-- <th>Specific Treatments</th> -->
                                    <!-- <th>Medicines</th> -->
                                    <th>X-ray</th>
                                    <!-- <th>X-ray Description</th> -->
                                    <th>Fees</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($visit = mysqli_fetch_assoc($visits_result)) {
                                    echo "<tr class='clickable-row' data-href='visitDetails.php?visit_id=" . $visit['id'] . "'>";
                                    echo "<td>" . date("d M Y", strtotime($visit['visit_date'])) . "</td>";
                                    echo "<td>" . htmlspecialchars($visit['treatment']) . "</td>";

                                    // Display specific treatments
                                    // $treatmentOptions = explode(",", $visit['treatment_options']);
                                    // echo "<td>";
                                    // foreach ($treatmentOptions as $option) {
                                    //     echo '<span class="badge bg-primary">' . htmlspecialchars(trim($option)) . '</span> ';
                                    // }
                                    // echo "</td>";

                                    // Display Medicines
                                    // echo "<td>" . (!empty($visit['medicines']) ? htmlspecialchars($visit['medicines']) : "None") . "</td>";
                                    echo "<td>" . ($visit['xray_taken'] ? "Yes" : "No") . "</td>"; // Display Yes/No for X-ray
                                    // echo "<td>" . htmlspecialchars($visit['xray_details']) . "</td>";
                                    echo "<td>₹" . htmlspecialchars($visit['fees']) . "</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    <?php } else { ?>
                        <p>No visits recorded for this patient yet.</p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete the profile of <strong id="patientName"></strong>? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a id="confirmDelete" href="#" class="btn btn-danger">Delete</a>
                </div>
            </div>
        </div>
    </div>

    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->
    <script>
        // Make table rows clickable
        document.querySelectorAll('.clickable-row').forEach(function(row) {
            row.addEventListener('click', function() {
                window.location.href = row.getAttribute('data-href');
            });
        });

        // Add event listener for the delete button to set patient name and id in the modal
        var deleteModal = document.getElementById('deleteModal');
        deleteModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var patientId = button.getAttribute('data-id');
            var patientName = button.getAttribute('data-name');
            var deleteLink = "deletePatient.php?id=" + patientId;

            var patientNameElement = document.getElementById('patientName');
            patientNameElement.textContent = patientName;

            var confirmDeleteButton = document.getElementById('confirmDelete');
            confirmDeleteButton.setAttribute('href', deleteLink);
        });
    </script>
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>
