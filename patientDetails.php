<?php
include 'auth.php';

include 'dbConnect.php';
$patient_id = $_GET['id'];

// Fetch patient details
$query = "SELECT * FROM patient WHERE id = '$patient_id'";
$result = mysqli_query($conn, $query);

$allergic_meds_query = "SELECT * FROM allergic_medicines WHERE patient_id = '$patient_id'";
$allergic_meds_result = mysqli_query($conn, $allergic_meds_query);

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
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
    <div class="container-fix">
        <div class="card mb-4 profile-box">
            <div class="profile-head">
                <h5 class="card-title">Patient Profile</h5>
                <h6 class="card-subtitle primary-color">Patient ID: <?php echo $patient_id ?></h6>
            </div>
            <div class="card-body">
                <h5 class="card-title">Personal Information</h5>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($name) ?></p>
                <p><strong>Age:</strong> <?php echo htmlspecialchars($age) ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($address) ?></p>
                <p><strong>Contact:</strong> <?php echo htmlspecialchars($contact) ?></p>
                <p><strong>Diseases:</strong> <?php echo htmlspecialchars($disease) ?></p>
                <!-- Allergic Medicines Section -->
                <div class="meds-title"><strong>Allergic Medicines:</strong></div>
                <div class="allergic-meds">
                    <?php
                    if (mysqli_num_rows($allergic_meds_result) > 0) {
                        while ($med = mysqli_fetch_assoc($allergic_meds_result)) {
                            echo '<div class="allergic-item">' . htmlspecialchars($med['medicine_name']) . '</div>';
                        }
                    } else {
                        // echo '<div class="allergic-item">None</div>';
                    }
                    ?>
                </div>

                <hr>
                <div class="d-flex justify-content-between">
                    <div>
                        <button type="button" class="btn custom-btn" data-bs-toggle="modal"
                            data-bs-target="#allergicMedicineModal">
                            Add Allergic Medicine
                        </button>
                    </div>
                    <div class="d-flex justify-content-end align-items-center">
                        <a href="editPatient.php?id=<?php echo $patient_id ?>" class="edit-btn me-2">
                            <i class="fa-solid fa-pen-to-square"></i>&nbsp;Edit
                        </a>
                        <!-- Trigger for Delete Modal -->
                        <button type="button" class="delete-btn" data-bs-toggle="modal" data-bs-target="#deleteModal"
                            data-id="<?php echo $patient_id ?>" data-name="<?php echo htmlspecialchars($name) ?>">
                            <i class="fa-solid fa-trash"></i>
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-3 profile-box">
            <div class="card profile-box">
                <div class="profile-head d-flex justify-content-between align-items-center">
                    <h4>Previous Visits</h4>
                    <!-- New Visit Button in the Header -->
                    <a href="visitRecord.php?id=<?php echo $patient_id ?>" class="visit-btn">
                        <i class="fas fa-plus"></i>
                        Visit</a>
                </div>
                <div class="card-body">
                    <?php if (mysqli_num_rows($visits_result) > 0) { ?>
                        <div class="table-responsive">
                            <table class="table table-hover ">
                                <thead>
                                    <tr>
                                        <th>Visit Date</th>
                                        <th>Treatment</th>
                                        <th>Medicines</th>
                                        <th>Specific Treatments</th>
                                        <th>X-ray</th>
                                        <th>X-ray Description</th>
                                        <th>Fees</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($visit = mysqli_fetch_assoc($visits_result)) {
                                        $medicines_string = $visit['medicines'];
                                        $medicines_list = explode(",", $medicines_string);
                                        
                                        echo "<tr class='clickable-row' data-href='visitDetails.php?visit_id=" . $visit['id'] . "'>";
                                        echo "<td>" . date("d M Y", strtotime($visit['visit_date'])) . "</td>";
                                        echo "<td>" . htmlspecialchars($visit['treatment']) . "</td>";
    
                                        // Display Medicines
                                        echo "<td class='medicines-list'>";
                                        if (empty(trim($medicines_string))) {
                                            // If there are no medicines, display "None"
                                            echo "None";
                                        } else {
                                            // Split the medicines string by commas
                                            $medicines_list = explode(",", $medicines_string);
    
                                            foreach ($medicines_list as $medicine_entry) {
                                                // Extract only the medicine name (before "- Quantity")
                                                $medicine_name = explode(" - Quantity", $medicine_entry)[0];
    
                                                // Display each medicine name in a separate styled box
                                                echo "<span class='badge primary-background med-item'>" . htmlspecialchars(trim($medicine_name)) . "</span>";
                                            }
                                        }
                                        echo "</td>";
                                        // Display specific treatments
                                        $treatmentOptions = explode(",", $visit['treatment_options']);
                                        echo "<td>";
                                        foreach ($treatmentOptions as $option) {
                                            echo '<span class="badge primary-background">' . htmlspecialchars(trim($option)) . '</span> ';
                                        }
                                        echo "</td>";
                                        echo "<td>" . ($visit['xray_taken'] ? "Yes" : "No") . "</td>"; // Display Yes/No for X-ray
                                        echo "<td>" . htmlspecialchars($visit['xray_details']) . "</td>";
                                        echo "<td>₹" . htmlspecialchars($visit['fees']) . "</td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
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
                    Are you sure you want to delete the profile of <strong id="patientName"></strong>? This action
                    cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a id="confirmDelete" href="#" class="btn btn-danger">Delete</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Allergic Medicine Modal -->
    <div class="modal fade" id="allergicMedicineModal" tabindex="-1" aria-labelledby="allergicMedicineModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="allergicMedicineModalLabel">Manage Allergic Medicines</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="allergicMedicineForm" action="saveAllergicMedicine.php" method="post">
                        <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
                        <div class="mb-3">
                            <label for="allergic_medicines" class="form-label">Allergic Medicines</label>
                            <textarea class="form-control" id="allergic_medicines" name="allergic_medicines"
                                rows="4"></textarea>
                        </div>
                        <button type="submit" class="btn custom-btn">Save Allergic Medicines</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->
    <script>
        // Make table rows clickable
        document.querySelectorAll('.clickable-row').forEach(function (row) {
            row.addEventListener('click', function () {
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

        // Fetch previous allergic medicines and populate the textarea
        document.getElementById('allergicMedicineModal').addEventListener('show.bs.modal', function (event) {
            var patientId = "<?php echo $patient_id; ?>"; // Use PHP to get patient ID
            var textarea = document.getElementById('allergic_medicines');

            // Fetch allergic medicines via AJAX
            fetch('fetchAllergicMedicines.php?patient_id=' + patientId)
                .then(response => response.json())
                .then(data => {
                    // Populate textarea with previous allergic medicines
                    textarea.value = data.medicines.join('\n'); // Join medicines with new lines
                });
        });
    </script>
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>