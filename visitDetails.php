<?php
include 'auth.php';

include 'dbConnect.php'; // Include your database connection

// Get the visit ID from the URL
$visit_id = isset($_GET['visit_id']) ? $_GET['visit_id'] : null;

// If no visit ID is provided, redirect back or show an error
if (!$visit_id) {
    echo "Visit ID is missing!";
    exit;
}

// Fetch the visit details from the 'visits' and 'patients' tables
$query = "SELECT v.patient_id, p.name as patient_name, p.age, p.disease, v.visit_date, v.treatment, v.medicines, v.fees, 
v.xray_taken, v.xray_details, v.treatment_options, v.s_uric_acid, v.calcium, v.esr, v.cholesterol
FROM visits v 
JOIN patient p ON v.patient_id = p.id 
WHERE v.id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $visit_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $visit = $result->fetch_assoc();
} else {
    echo "No visit found!";
    exit;
}

$stmt->close();

// Fetch X-ray images from the 'xray_images' table based on the visit ID
$xray_images_query = "SELECT image_path, description FROM xray_images WHERE visit_id = ?";
$xray_stmt = $conn->prepare($xray_images_query);
$xray_stmt->bind_param("i", $visit_id);
$xray_stmt->execute();
$xray_images_result = $xray_stmt->get_result();

$xray_files = [];
while ($row = $xray_images_result->fetch_assoc()) {
    $xray_files[] = $row;
}

$xray_stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visit Details</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .zoomable-img {
            cursor: pointer;
            transition: 0.3s;
        }

        .zoomable-img:hover {
            transform: scale(1.05);
        }

        .card-header {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            text-align: center;
        }

        .back-button {
            margin-top: 20px;
        }
        
        /* Responsive styles for small screens */
        @media (max-width: 768px) {
            .visit-personal {
                height: auto;
                padding: 15px;
                margin-bottom: 15px;
            }
            
            .test-result {
                flex-direction: column;
            }
            
            .test-cell {
                margin-right: 0;
                margin-bottom: 10px;
            }
            
            .card-body.d-flex {
                flex-wrap: wrap;
            }
            
            .treatment-options-container {
                display: flex;
                flex-wrap: wrap;
                gap: 5px;
            }
            
            .treat-cell {
                margin-bottom: 10px;
                margin-right: 5px;
            }
            
            /* Allergic Medicine modal responsiveness */
            .modal-dialog {
                margin: 10px;
            }
            
            .allergic-form .input-group {
                flex-direction: column;
            }
            
            .allergic-form .input-group input,
            .allergic-form .input-group button {
                width: 100%;
                margin-top: 5px;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <?php include 'header.php'; ?>

    <div class="container-fix mb-4">
        <!-- General Info -->
        <div class="visit-personal">
            <div>
                <h5><?php echo htmlspecialchars($visit['patient_name']); ?></h5>
                <p><?php echo htmlspecialchars($visit['age']); ?> Years |
                    <?php echo htmlspecialchars($visit['disease']); ?>
                </p>
                <?php
                // echo "$visit_id";
                //     echo"<pre>";
                //     print_r($visit);
                ?>
            </div>
        </div>

        <div class="container-group">
            <div class="conatiner-fix">
                <div class="visit-card">
                    <div class="visit-card-head">General Information</div>
                    <hr>
                    <div class="card-body">
                        <p><strong>Visit Date:</strong> <?php echo htmlspecialchars($visit['visit_date']); ?></p>
                        <p><strong>Treatment Description:</strong> <?php echo htmlspecialchars($visit['treatment']); ?>
                        </p>
                        <p><strong>Consultation Fees:</strong> ₹<?php echo htmlspecialchars($visit['fees']); ?></p>
                    </div>
                    <div class="d-flex justify-content-end">
                        <a href="editVisitDetails.php?visit_id=<?php echo $visit_id ?>" class="edit-btn me-2">
                            <i class="fa-solid fa-pen-to-square"></i>&nbsp;Edit
                        </a>
                        <!-- Trigger for Delete Modal -->
                        <button type="button" class="delete-btn" data-bs-toggle="modal" data-bs-target="#deleteModal"
                            data-id="<?php echo $visit_id ?>"
                            data-name="<?php echo htmlspecialchars($visit['patient_name']) ?>">
                            <i class="fa-solid fa-trash"></i>
                            Delete
                        </button>
                    </div>
                </div>
            </div>

            <!-- Test Results -->
            <div class="conatiner-fix">
                <div class="visit-card">
                    <div class="visit-card-head">Test Results</div>
                    <hr>
                    <div class="card-body test-result d-flex">
                        <p class="test-cell"><strong>S Uric Acid:</strong>
                            <?php echo htmlspecialchars($visit['s_uric_acid']); ?></p>
                        <p class="test-cell"><strong>Calcium:</strong>
                            <?php echo htmlspecialchars($visit['calcium']); ?></p>
                        <p class="test-cell"><strong>E.S.R:</strong> <?php echo htmlspecialchars($visit['esr']); ?></p>
                        <p class="test-cell"><strong>Cholesterol:</strong>
                            <?php echo htmlspecialchars($visit['cholesterol']); ?></p>
                    </div>
                </div>
            </div>

            <!-- Medicines and Treatment -->
            <div class="conatiner-fix">
                <div class="visit-card">
                    <div class="visit-card-head">Medicines Prescribed</div>
                    <hr>
                    <div class="card-body">
                        <?php foreach (explode(",", $visit['medicines']) as $medicine): ?>
                            <p><?php echo htmlspecialchars(trim($medicine)); ?></p>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Treatment Options -->
            <div class="conatiner-fix">
                <div class="visit-card">
                    <div class="visit-card-head">Treatment Options</div>
                    <hr>
                    <div class="card-body d-flex treatment-options-container">
                        <?php foreach (explode(",", $visit['treatment_options']) as $option): ?>
                            <?php if(trim($option) !== ""): ?>
                                <li class="treat-cell"><?php echo htmlspecialchars(trim($option)); ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- X-ray Details -->
            <div class="conatiner-fix">
                <div class="visit-card">
                    <div class="visit-card-head">X-ray Details</div>
                    <hr>
                    <div class="card-body">
                        <?php if ($visit['xray_taken']): ?>
                            <p><strong>Description:</strong> <?php echo htmlspecialchars($visit['xray_details']); ?></p>

                            <?php if (!empty($xray_files)): ?>
                                <div class="mb-3">
                                    <label><strong>X-ray Images:</strong></label><br>
                                    <?php foreach ($xray_files as $xray_file): ?>
                                        <img src="<?php echo $xray_file['image_path']; ?>" alt="X-ray Image"
                                            class="img-fluid zoomable-img" style="max-width: 200px; margin: 5px;"
                                            onclick="openModal('<?php echo $xray_file['image_path']; ?>')">
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p><strong>X-ray Images:</strong> Not available.</p>
                            <?php endif; ?>
                        <?php else: ?>
                            <p><strong>X-ray:</strong> No X-ray was taken during this visit.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Back Button -->
            <a href="patientDetails.php?id=<?php echo $visit['patient_id']; ?>" class="btn custom-btn back-button">Back
                to Patient Details</a>
        </div>
    </div>

    <!-- Modal for X-ray Image -->
    <div class="modal fade" id="xrayModal" tabindex="-1" aria-labelledby="xrayModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="xrayModalLabel">X-ray Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalXrayImage" src="" alt="X-ray Image" class="img-fluid" style="max-width: 100%;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this visit record for
                    <?php echo htmlspecialchars($visit['patient_name']); ?>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="deleteVisit.php?visit_id=<?php echo $visit_id; ?>" class="btn btn-danger">Delete</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Scripts -->
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        // Open modal and show the large X-ray image
        function openModal(imageSrc) {
            document.getElementById('modalXrayImage').src = imageSrc;
            const modal = new bootstrap.Modal(document.getElementById('xrayModal'));
            modal.show();
        }
    </script>
</body>

</html>