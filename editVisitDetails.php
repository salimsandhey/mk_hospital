<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'auth.php';

include "dbConnect.php"; // Include your database connection

// Get the visit ID from the URL
$visit_id = isset($_GET['visit_id']) ? intval($_GET['visit_id']) : 0;

if (!$visit_id) {
    echo "Invalid visit ID!";
    exit;
}

// Fetch the visit details
$query = "SELECT * FROM visits WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $visit_id);
$stmt->execute();
$result = $stmt->get_result();
$visit = $result->fetch_assoc();

if (!$visit) {
    echo "Visit not found!";
    exit;
}

$patient_id = $visit['patient_id'];

// Fetch X-ray images
$xray_query = "SELECT * FROM xray_images WHERE visit_id = ?";
$xray_stmt = $conn->prepare($xray_query);
$xray_stmt->bind_param("i", $visit_id);
$xray_stmt->execute();
$xray_result = $xray_stmt->get_result();
$xray_images = $xray_result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Visit</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <script src="assets/js/jquery.min.js"></script>
    <link rel="stylesheet" href="style.css">

    <style>
        .medicine-result {
            cursor: pointer;
            padding: 8px;
            border: 1px solid #ccc;
            margin-top: -1px;
            background-color: white;
        }

        .medicine-result:hover {
            background-color: #f0f0f0;
        }

        .xray-thumbnail {
            width: 100px;
            height: auto;
            display: block;
            margin-bottom: 5px;
        }

        .xray-delete-btn {
            display: block;
            margin-top: 5px;
            color: red;
            cursor: pointer;
        }

        .xray-image {
            width: fit-content;
            margin-right: 15px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            background-color: #f9f9f9;
            display: inline-block;
            vertical-align: top;
        }

        .xray-cont {
            display: flex;
            flex-wrap: wrap;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container mb-5">
        <h2>Edit Patient Visit</h2>
        <form id="visitForm" method="POST" action="updateVisit.php" enctype="multipart/form-data">
            <input type="hidden" name="visit_id" value="<?php echo $visit_id; ?>">
            <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">

            <!-- Visit Date -->
            <div class="mb-3">
                <label for="visit_date" class="form-label">Visit Date</label>
                <input type="date" class="form-control" id="visit_date" name="visit_date" value="<?php echo htmlspecialchars($visit['visit_date']); ?>" required>
                <div class="invalid-feedback">Please select a visit date.</div>
            </div>

            <!-- Treatment Description -->
            <div class="mb-3">
                <label for="treatment" class="form-label">Treatment Description</label>
                <textarea class="form-control" id="treatment" name="treatment" rows="4" required><?php echo htmlspecialchars($visit['treatment']); ?></textarea>
                <div class="invalid-feedback">Please enter treatment description.</div>
            </div>

            <!-- Treatment Options -->
            <div class="mb-3">
                <label class="form-label">Specific Treatment Options</label>
                <?php
                $treatment_options = explode(',', $visit['treatment_options']);
                $treatment_list = [
                    "FOOTBOOSTER (FB)",
                    "TENSE+",
                    "TMC",
                    "IFT",
                    "JSB",
                    "LASER THERAPY",
                    "LICO",
                    "ELASTIC BANDAGE",
                    "BANDAGE"
                ];
                
                foreach ($treatment_list as $option) {
                    $option_id = strtolower(str_replace([' ', '(', ')', '+'], ['_', '', '', ''], $option));
                    $checked = in_array($option, $treatment_options) ? "checked" : "";
                    echo "<div class='form-check'>
                        <input class='form-check-input' type='checkbox' id='$option_id' name='treatment_options[]' value='$option' $checked>
                        <label class='form-check-label' for='$option_id'>$option</label>
                    </div>";
                }
                ?>
            </div>

            <!-- X-ray Section -->
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="xray_checkbox" name="xray" <?php echo $visit['xray_taken'] ? 'checked' : ''; ?> onclick="toggleXrayDetails()">
                <label class="form-check-label" for="xray_checkbox">X-ray Taken</label>
            </div>

            <div id="xray_details_container" style="display: <?php echo $visit['xray_taken'] ? 'block' : 'none'; ?>;">
                <!-- X-ray Details -->
                <div class="mb-3">
                    <label for="xray_details" class="form-label">X-ray Details</label>
                    <textarea class="form-control" id="xray_details" name="xray_details" rows="3"><?php echo htmlspecialchars($visit['xray_details']); ?></textarea>
                </div>
                
                <!-- Existing X-ray Images -->
                <div class="mb-3">
                    <label class="form-label">X-ray Images</label>
                    <div class="xray-cont">
                        <?php foreach ($xray_images as $xray) { ?>
                            <div class="xray-image mb-2">
                                <img src="<?php echo htmlspecialchars($xray['image_path']); ?>" class="xray-thumbnail">
                                <button type="button" class="btn btn-danger btn-sm mt-2 delete-xray-btn" data-image-id="<?php echo htmlspecialchars($xray['id']); ?>" data-visit-id="<?php echo $visit_id; ?>">Delete</button>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                
                <!-- Upload New X-ray Images -->
                <div class="mb-3">
                    <label for="xray_file" class="form-label">Add New X-ray Images</label>
                    <input type="file" class="form-control" id="xray_file" name="xray_file">
                    <button type="button" class="btn btn-success mt-2" id="add_image_button">Add Image</button>
                </div>
            </div>

            <!-- Test Results -->
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="test_results_checkbox" <?php echo ($visit['s_uric_acid'] || $visit['calcium'] || $visit['esr'] || $visit['cholesterol']) ? 'checked' : ''; ?> onclick="toggleTestResults()">
                <label class="form-check-label" for="test_results_checkbox">Test Results</label>
            </div>

            <div id="test_results_container" style="display: <?php echo ($visit['s_uric_acid'] || $visit['calcium'] || $visit['esr'] || $visit['cholesterol']) ? 'block' : 'none'; ?>;">
                <div class="mb-3 input-group">
                    <input type="text" class="form-control" name="s_uric_acid" value="<?php echo htmlspecialchars($visit['s_uric_acid']); ?>" placeholder="S URIC ACID">
                    <input type="text" class="form-control" name="calcium" value="<?php echo htmlspecialchars($visit['calcium']); ?>" placeholder="CALCIUM">
                </div>
                <div class="mb-3 input-group">
                    <input type="text" class="form-control" name="esr" value="<?php echo htmlspecialchars($visit['esr']); ?>" placeholder="E.S.R">
                    <input type="text" class="form-control" name="cholesterol" value="<?php echo htmlspecialchars($visit['cholesterol']); ?>" placeholder="CHOLESTEROL">
                </div>
            </div>

            <!-- Fees -->
            <div class="mb-3">
                <label for="fees" class="form-label">Consultation Fees</label>
                <input type="number" class="form-control" id="fees" name="fees" value="<?php echo htmlspecialchars($visit['fees']); ?>" required>
                <div class="invalid-feedback">Please enter consultation fees.</div>
            </div>

            <!-- Medicines Section -->
            <div class="mb-3">
                <label class="form-label">Medicines Prescribed</label>
                
                <!-- Medicine Search and Add -->
                <div class="input-group mb-3">
                    <input type="text" id="medicine_search" class="form-control" placeholder="Search Medicine" autocomplete="off">
                    <input type="number" id="medicine_quantity" class="form-control" placeholder="Quantity" min="1">
                    <select id="medicine_timing" class="form-select">
                        <option value="Morning">Morning</option>
                        <option value="Afternoon">Afternoon</option>
                        <option value="Evening">Evening</option>
                        <option value="Morning-Evening">Morning-Evening</option>
                        <option value="Afternoon-Evening">Afternoon-Evening</option>
                        <option value="Empty Stomach">Empty Stomach</option>
                        <option value="Before Sleeping">Before Sleeping</option>
                        <option value="4'o Clock">4'o Clock</option>
                        <option value="11'o Clock">11'o Clock</option>
                        <option value="SOS">SOS</option>
                        <option value="Morning-Afternoon-Evening">Morning-Afternoon-Evening</option>
                    </select>
                    <button class="btn custom-btn" type="button" id="add_medicine_btn">Add</button>
                </div>
                
                <!-- Medicine Search Results -->
                <div id="medicine_results" class="mb-2"></div>
                
                <!-- Medicine List -->
                <ul id="medicines_list" class="list-group mb-2">
                    <?php
                    $medicines = explode(', ', $visit['medicines']);
                    foreach ($medicines as $medicine) {
                        if (!empty(trim($medicine))) {
                            echo '<li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>' . htmlspecialchars($medicine) . '</span>
                                <button type="button" class="btn btn-danger btn-sm remove-medicine">Remove</button>
                            </li>';
                        }
                    }
                    ?>
                </ul>
                
                <!-- Hidden Medicine Input -->
                <input type="hidden" id="medicines_input" name="medicines" value="<?php echo htmlspecialchars($visit['medicines']); ?>">
            </div>

            <!-- Submit Button -->
            <div class="mb-3">
                <button type="submit" id="saveVisitBtn" class="btn custom-btn">Update Visit</button>
                <button type="button" id="checkMedicinesBtn" class="btn btn-secondary ms-2">Check Medicines</button>
            </div>
            
            <!-- Form Status -->
            <div id="formStatus" class="mt-3"></div>
        </form>
    </div>

    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        // DOM Elements
        const visitForm = document.getElementById('visitForm');
        const medicineSearch = document.getElementById('medicine_search');
        const medicineQuantity = document.getElementById('medicine_quantity');
        const medicineTiming = document.getElementById('medicine_timing');
        const medicinesList = document.getElementById('medicines_list');
        const medicinesInput = document.getElementById('medicines_input');
        const medicineResults = document.getElementById('medicine_results');
        const addMedicineBtn = document.getElementById('add_medicine_btn');
        const saveVisitBtn = document.getElementById('saveVisitBtn');
        const formStatus = document.getElementById('formStatus');
        const checkMedicinesBtn = document.getElementById('checkMedicinesBtn');
        
        // Toggle Functions
        function toggleXrayDetails() {
            const xrayDetails = document.getElementById('xray_details_container');
            xrayDetails.style.display = document.getElementById('xray_checkbox').checked ? 'block' : 'none';
        }
        
        function toggleTestResults() {
            const testResults = document.getElementById('test_results_container');
            testResults.style.display = document.getElementById('test_results_checkbox').checked ? 'block' : 'none';
        }
        
        // Medicine Functions
        function searchMedicines() {
            const query = medicineSearch.value.trim();
            
            if (query.length > 0) {
                // Use fetch instead of jQuery AJAX
                fetch('fetchMedicines.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'search=' + encodeURIComponent(query)
                })
                .then(response => response.text())
                .then(html => {
                    medicineResults.innerHTML = html;
                })
                .catch(error => {
                    console.error('Error fetching medicines:', error);
                    medicineResults.innerHTML = '<div class="alert alert-danger">Error fetching medicines</div>';
                });
            } else {
                medicineResults.innerHTML = '';
            }
        }
        
        function selectMedicine(medicineId, medicineName) {
            medicineSearch.value = medicineName;
            medicineResults.innerHTML = '';
        }
        
        function addMedicine() {
            const medicineName = medicineSearch.value.trim();
            const quantity = medicineQuantity.value.trim();
            const timing = medicineTiming.value;
            
            if (medicineName && quantity > 0 && timing) {
                // Create medicine item
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';
                
                // Create medicine text span
                const span = document.createElement('span');
                span.textContent = `${medicineName} - Quantity: ${quantity} - Timing: ${timing}`;
                li.appendChild(span);
                
                // Create remove button
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'btn btn-danger btn-sm remove-medicine';
                removeBtn.textContent = 'Remove';
                removeBtn.addEventListener('click', function() {
                    li.remove();
                    updateMedicinesInput();
                });
                li.appendChild(removeBtn);
                
                // Add to list and update input
                medicinesList.appendChild(li);
                updateMedicinesInput();
                
                // Reset inputs
                medicineSearch.value = '';
                medicineQuantity.value = '';
            } else {
                alert('Please enter a medicine name, quantity, and select a timing.');
            }
        }
        
        function updateMedicinesInput() {
            const medicines = [];
            const medicineItems = medicinesList.querySelectorAll('li span');
            
            medicineItems.forEach(item => {
                medicines.push(item.textContent);
            });
            
            medicinesInput.value = medicines.join(', ');
            console.log('Updated medicines input:', medicinesInput.value);
        }
        
        // Form Submission
        function submitForm(e) {
            e.preventDefault();
            
            // Update medicines input before submission
            updateMedicinesInput();
            
            // Validate form
            if (visitForm.checkValidity()) {
                // Disable submit button
                saveVisitBtn.disabled = true;
                saveVisitBtn.textContent = 'Saving...';
                
                // Show loading message
                formStatus.innerHTML = '<div class="alert alert-info">Submitting form...</div>';
                
                // Create form data
                const formData = new FormData(visitForm);
                
                // Submit form
                fetch('updateVisit.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (response.redirected) {
                        window.location.href = response.url;
                    } else {
                        return response.text().then(text => {
                            formStatus.innerHTML = text;
                            saveVisitBtn.disabled = false;
                            saveVisitBtn.textContent = 'Update Visit';
                        });
                    }
                })
                .catch(error => {
                    console.error('Error submitting form:', error);
                    formStatus.innerHTML = '<div class="alert alert-danger">An error occurred. Please try again.</div>';
                    saveVisitBtn.disabled = false;
                    saveVisitBtn.textContent = 'Update Visit';
                });
            } else {
                // Show validation errors
                visitForm.classList.add('was-validated');
                formStatus.innerHTML = '<div class="alert alert-warning">Please fill in all required fields.</div>';
            }
        }
        
        // X-ray Image Functions
        function addXrayImage() {
            const fileInput = document.getElementById('xray_file');
            const file = fileInput.files[0];
            
            if (file) {
                const formData = new FormData();
                formData.append('xray_file', file);
                formData.append('visit_id', '<?php echo $visit_id; ?>');
                formData.append('patient_id', '<?php echo $patient_id; ?>');
                
                fetch('uploadImage.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Image upload failed: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error uploading image:', error);
                    alert('Error uploading image. Please try again.');
                });
            } else {
                alert('Please select an image first.');
            }
        }
        
        function deleteXrayImage(imageId, visitId) {
            if (confirm('Are you sure you want to delete this X-ray image?')) {
                const formData = new FormData();
                formData.append('image_id', imageId);
                formData.append('visit_id', visitId);
                
                fetch('deleteImage.php', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Failed to delete the image: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error deleting image:', error);
                    alert('Failed to delete the image. Please try again.');
                });
            }
        }
        
        // Event Listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Medicine search
            medicineSearch.addEventListener('input', searchMedicines);
            
            // Add medicine button
            addMedicineBtn.addEventListener('click', addMedicine);
            
            // Remove medicine buttons (for existing items)
            document.querySelectorAll('.remove-medicine').forEach(button => {
                button.addEventListener('click', function() {
                    this.closest('li').remove();
                    updateMedicinesInput();
                });
            });
            
            // Form submission
            visitForm.addEventListener('submit', submitForm);
            
            // Check medicines button
            checkMedicinesBtn.addEventListener('click', function() {
                updateMedicinesInput();
                alert('Current medicines: ' + medicinesInput.value);
            });
            
            // Add X-ray image
            document.getElementById('add_image_button').addEventListener('click', addXrayImage);
            
            // Delete X-ray image buttons
            document.querySelectorAll('.delete-xray-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const imageId = this.getAttribute('data-image-id');
                    const visitId = this.getAttribute('data-visit-id');
                    deleteXrayImage(imageId, visitId);
                });
            });
        });
        
        // Make selectMedicine function globally available
        window.selectMedicine = selectMedicine;
    </script>
</body>
</html>