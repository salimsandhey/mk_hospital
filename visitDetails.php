<?php 
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php'); // Redirect to login page
    exit;
}

include 'dbConnect.php'; // Include your database connection

// Get the visit ID from the URL
$visit_id = isset($_GET['visit_id']) ? $_GET['visit_id'] : null;

// If no visit ID is provided, redirect back or show an error
if (!$visit_id) {
    echo "Visit ID is missing!";
    exit;
}

// Fetch the visit details from the database
$query = "SELECT v.patient_id, p.name as patient_name, v.visit_date, v.treatment, v.medicines, v.fees, 
                 v.xray_taken, v.xray_details, v.xray_file, v.treatment_options,
                 v.s_uric_acid, v.calcium, v.esr, v.cholesterol
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
$conn->close();

// Handle multiple X-ray file paths
$xray_files = !empty($visit['xray_file']) ? explode(',', $visit['xray_file']) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visit Details</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
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
    </style>
</head>
<body>
    <!-- Header -->
    <?php include 'header.php'; ?>

    <div class="container mb-4">
        <!-- <h2 class="text-center my-4">Visit Details for <?php echo htmlspecialchars($visit['patient_name']); ?></h2> -->

        <!-- General Info -->
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">General Information</div>
                    <div class="card-body">
                        <p><strong>Visit Date:</strong> <?php echo htmlspecialchars($visit['visit_date']); ?></p>
                        <p><strong>Treatment Description:</strong> <?php echo htmlspecialchars($visit['treatment']); ?></p>
                        <p><strong>Consultation Fees:</strong> $<?php echo htmlspecialchars($visit['fees']); ?></p>
                    </div>
                </div>
            </div>

            <!-- Test Results -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">Test Results</div>
                    <div class="card-body">
                        <ul class="list-group">
                            <li class="list-group-item"><strong>S Uric Acid:</strong> <?php echo htmlspecialchars($visit['s_uric_acid']); ?></li>
                            <li class="list-group-item"><strong>Calcium:</strong> <?php echo htmlspecialchars($visit['calcium']); ?></li>
                            <li class="list-group-item"><strong>E.S.R:</strong> <?php echo htmlspecialchars($visit['esr']); ?></li>
                            <li class="list-group-item"><strong>Cholesterol:</strong> <?php echo htmlspecialchars($visit['cholesterol']); ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Medicines and Treatment -->
        <div class="row">
            <!-- Medicines -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">Medicines Prescribed</div>
                    <div class="card-body">
                        <ul class="list-group">
                            <?php foreach (explode(",", $visit['medicines']) as $medicine): ?>
                                <li class="list-group-item"><?php echo htmlspecialchars(trim($medicine)); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Treatment Options -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">Treatment Options</div>
                    <div class="card-body">
                        <ul class="list-group">
                            <?php foreach (explode(",", $visit['treatment_options']) as $option): ?>
                                <li class="list-group-item"><?php echo htmlspecialchars(trim($option)); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- X-ray Details -->
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header">X-ray Details</div>
                    <div class="card-body">
                        <?php if ($visit['xray_taken']): ?>
                            <p><strong>Description:</strong> <?php echo htmlspecialchars($visit['xray_details']); ?></p>
                            <?php if (!empty($xray_files)): ?>
                                <div class="mb-3">
                                    <label><strong>X-ray Images:</strong></label><br>
                                    <?php foreach ($xray_files as $xray_file): ?>
                                        <?php if (file_exists($xray_file)): ?>
                                            <img src="<?php echo $xray_file; ?>" alt="X-ray Image" class="img-fluid zoomable-img" style="max-width: 200px; margin: 5px;" onclick="openModal('<?php echo $xray_file; ?>')">
                                        <?php else: ?>
                                            <p><strong>X-ray Image:</strong> Not available.</p>
                                        <?php endif; ?>
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
        </div>

        <!-- Back Button -->
        <a href="patientDetails.php?id=<?php echo $visit['patient_id']; ?>" class="btn btn-primary back-button">Back to Patient Details</a>
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
