<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'auth.php';

include "dbConnect.php"; // Include your database connection

// Get the visit ID from the URL
$visit_id = $_GET['visit_id'];

// Fetch the visit details for the given visit ID
$query = "SELECT * FROM visits WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $visit_id);
$stmt->execute();
$result = $stmt->get_result();
$visit = $result->fetch_assoc();
// echo"<pre>";
// print_r($visit);
// echo $visit['id'];
$xray_query = "SELECT * FROM xray_images WHERE visit_id = ?";
$xray_stmt = $conn->prepare($xray_query);
$xray_stmt->bind_param("i", $visit_id);
$xray_stmt->execute();
$xray_result = $xray_stmt->get_result();
$xray_images = $xray_result->fetch_all(MYSQLI_ASSOC);

if (!$visit) {
    echo "Visit not found!";
    exit;
}

$patient_id = $visit['patient_id']; // Retrieve patient ID for redirection or use later
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
        <form action="updateVisit.php" method="POST" enctype="multipart/form-data" novalidate>
            <input type="hidden" name="visit_id" value="<?php echo $visit_id; ?>">
            <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">

            <!-- Visit Date -->
            <div class="mb-3">
                <label for="visit_date" class="form-label">Visit Date</label>
                <input type="date" class="form-control" name="visit_date" value="<?php echo $visit['visit_date']; ?>"
                    required>
            </div>

            <!-- General Treatment Description (Text Area) -->
            <div class="mb-3">
                <label for="treatment" class="form-label">Treatment Description</label>
                <textarea class="form-control" name="treatment" rows="4"
                    required><?php echo $visit['treatment']; ?></textarea>
            </div>

            <!-- Treatment Options (Checkboxes) -->
            <div class="mb-3">
                <label for="treatment_options" class="form-label">Specific Treatment Options</label><br>
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
                    $checked = in_array($option, $treatment_options) ? "checked" : "";
                    echo "<div class='form-check'>
                        <input class='form-check-input' type='checkbox' id='" . strtolower(str_replace(' ', '_', $option)) . "' name='treatment_options[]' value='$option' $checked>
                        <label class='form-check-label' for='" . strtolower(str_replace(' ', '_', $option)) . "'>$option</label>
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
                <div class="mb-3">
                    <label for="xray_details" class="form-label">X-ray Details</label>
                    <textarea class="form-control" name="xray_details" rows="3"><?php echo $visit['xray_details']; ?></textarea>
                </div>

                <!-- Show previously uploaded X-ray images -->
                <div class="mb-3">
                    <label for="previous_xrays" class="form-label">X-ray Images</label>
                    <div class="xray-cont">
                        <?php foreach ($xray_images as $xray) { ?>
                            <div class="xray-image mb-2">
                                <img src="<?php echo htmlspecialchars($xray['image_path']); ?>" class="xray-thumbnail">
                                <form action="deleteImage.php" method="post" class="delete-xray-form">
                                    <input type="hidden" name="image_id" value="<?php echo htmlspecialchars($xray['id']); ?>">
                                    <input type="hidden" name="visit_id" value="<?php echo htmlspecialchars($visit_id); ?>">
                                    <button type="button" class="btn btn-danger btn-sm mt-2 delete-xray-btn" data-image-id="<?php echo htmlspecialchars($xray['id']); ?>" data-visit-id="<?php echo htmlspecialchars($visit_id); ?>">Delete</button>
                                </form>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <!-- Upload new X-ray images -->
                <div class="mb-3">
                    <label for="xray_file" class="form-label">Add New X-ray Images</label>
                    <input type="file" class="form-control" id="xray_file" name="xray_file">
                    <button type="button" class="btn btn-success mt-2" id="add_image_button">Add Image</button>
                </div>
            </div>

            <!-- Test Fields -->
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="test_results_checkbox" onclick="toggleTestResults()"
                    <?php echo ($visit['s_uric_acid'] || $visit['calcium'] || $visit['esr'] || $visit['cholesterol']) ? 'checked' : ''; ?>>
                <label for="test_results_checkbox" class="form-check-label">Test Results</label>
            </div>

            <div id="test_results_container"
                style="display: <?php echo ($visit['s_uric_acid'] || $visit['calcium'] || $visit['esr'] || $visit['cholesterol']) ? 'block' : 'none'; ?>;">
                <div class="mb-3 input-group">
                    <input type="text" class="form-control" name="s_uric_acid"
                        value="<?php echo $visit['s_uric_acid']; ?>" placeholder="S URIC ACID">
                    <input type="text" class="form-control" name="calcium" value="<?php echo $visit['calcium']; ?>"
                        placeholder="CALCIUM">
                </div>
                <div class="mb-3 input-group">
                    <input type="text" class="form-control" name="esr" value="<?php echo $visit['esr']; ?>"
                        placeholder="E.S.R">
                    <input type="text" class="form-control" name="cholesterol"
                        value="<?php echo $visit['cholesterol']; ?>" placeholder="CHOLESTEROL">
                </div>
            </div>

            <!-- Fees -->
            <div class="mb-3">
                <label for="fees" class="form-label">Consultation Fees</label>
                <input type="number" class="form-control" name="fees" value="<?php echo $visit['fees']; ?>" required>
            </div>

            <!-- Medicines (Search and Add) -->
            <div class="mb-3">
                <label for="medicines" class="form-label">Medicines Prescribed</label>
                <div class="input-group mb-3">
                    <input type="text" id="medicine_search" class="form-control" placeholder="Search Medicine"
                        oninput="searchMedicines()">
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
                    <button class="btn custom-btn" type="button" onclick="addMedicine()">Add</button>
                </div>
                <div id="medicine_results"></div>

                <ul id="medicines_list" class="list-group mt-3">
                    <?php
                    $medicines = explode(', ', $visit['medicines']);
                    foreach ($medicines as $medicine) {
                        echo "<li class='list-group-item d-flex justify-content-between align-items-center'>$medicine
                            <button type='button' class='btn btn-danger btn-sm' onclick='this.parentElement.remove(); updateMedicines();'>Remove</button>
                        </li>";
                    }
                    ?>
                </ul>
                <input type="hidden" id="medicines_input" name="medicines" value="<?php echo $visit['medicines']; ?>">
            </div>

            <!-- Submit Button -->
            <button type="submit" id="saveVisitBtn" class="btn custom-btn">Update Visit</button>
        </form>
    </div>

    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        // Function to fetch medicines based on search input
        function searchMedicines() {
            const query = $('#medicine_search').val();
            if (query.length > 0) {
                $.ajax({
                    url: 'fetchMedicines.php',
                    type: 'POST',
                    data: { search: query },
                    success: function (response) {
                        $('#medicine_results').html(response);
                    }
                });
            } else {
                $('#medicine_results').html('');
            }
        }

        // Function to handle selecting a medicine from search results
        function selectMedicine(medicineId, medicineName) {
            $('#medicine_search').val(medicineName);
            $('#medicines_input').val(medicineId); // Store selected medicine ID
            $('#medicine_results').html(''); // Clear search results
        }

        // JavaScript to toggle the X-ray description text box based on checkbox status
        function toggleXrayDetails() {
            const xrayDetails = document.getElementById('xray_details_container');
            if (document.getElementById('xray_checkbox').checked) {
                xrayDetails.style.display = 'block';
            } else {
                xrayDetails.style.display = 'none';
            }
        }

        // JavaScript to handle dynamic medicine addition with quantity and timing
        function addMedicine() {
            const medicineName = $('#medicine_search').val();
            const quantity = $('#medicine_quantity').val();
            const timing = $('#medicine_timing').val();

            if (medicineName && quantity > 0 && timing) {
                // Create a new list item
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';
                li.textContent = `${medicineName} - Quantity: ${quantity} - Timing: ${timing}`;

                // Add delete button to list item
                const deleteBtn = document.createElement('button');
                deleteBtn.textContent = 'Remove';
                deleteBtn.className = 'btn btn-danger btn-sm';
                deleteBtn.type = 'button';
                deleteBtn.onclick = function () {
                    li.remove();
                    updateMedicines();
                };

                li.appendChild(deleteBtn);

                // Add the new list item to the medicines list
                document.getElementById('medicines_list').appendChild(li);

                // Update the hidden input with the new medicines list
                updateMedicines();

                // Reset inputs for the next entry
                $('#medicine_quantity').val('');
                $('#medicine_search').val('');
            } else {
                alert("Please select a medicine, enter a valid quantity, and select a timing.");
            }
        }

        // Update the hidden input with the selected medicines
        function updateMedicines() {
            const medicines = [];
            const listItems = document.querySelectorAll('#medicines_list li');
            listItems.forEach(item => {
                medicines.push(item.firstChild.textContent);
            });
            document.getElementById('medicines_input').value = medicines.join(', ');
        }

        // Automatically populate medicines in hidden input when the form is submitted
        document.querySelector('form').addEventListener('submit', function () {
            updateMedicines();
        });
    </script>
    <script>
        // Automatically set today's date in the date input
        // window.onload = function () {
        //     const today = new Date().toISOString().split('T')[0];
        //     document.getElementById('visit_date').value = today;
        // };
        function toggleXrayDetails() {
            const xrayDetails = document.getElementById('xray_details_container');
            if (document.getElementById('xray_checkbox').checked) {
                xrayDetails.style.display = 'block';
            } else {
                xrayDetails.style.display = 'none';
            }
        }
        function toggleTestResults() {
            const testResultsContainer = document.getElementById('test_results_container');
            if (document.getElementById('test_results_checkbox').checked) {
                testResultsContainer.style.display = 'block';
            } else {
                testResultsContainer.style.display = 'none';
            }
        }
    </script>
    <script>
        // Add an event listener to the form submission
        document.querySelector('form').addEventListener('submit', function (e) {
            // Check if the form is valid
            if (this.checkValidity()) {
                const saveButton = document.getElementById('saveVisitBtn');
                saveButton.disabled = true; // Disable the button
                saveButton.innerHTML = 'Saving...'; // Optionally change the button text
            } else {
                e.preventDefault(); // Prevent form submission if invalid
            }
        });
    </script>
    <script>
        // Add image via AJAX
        document.getElementById('add_image_button').addEventListener('click', function () {
            let fileInput = document.getElementById('xray_file');
            let file = fileInput.files[0];

            if (file) {
                let formData = new FormData();
                formData.append('xray_file', file);
                formData.append('visit_id', '<?php echo $visit_id; ?>'); // Include visit ID
                formData.append('patient_id', '<?php echo $patient_id; ?>'); // Include patient ID

                // Send AJAX request to upload image
                fetch('uploadImage.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Refresh the page to show the newly added image
                            window.location.reload();
                        } else {
                            alert('Image upload failed!');
                        }
                    })
                    .catch(error => {
                        console.error('Error uploading image:', error);
                    });
            } else {
                alert('Please select an image first.');
            }
        });
    </script>
    <script>
        // Add event listeners for delete buttons
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.delete-xray-btn');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const imageId = this.getAttribute('data-image-id');
                    const visitId = this.getAttribute('data-visit-id');
                    
                    if (confirm('Are you sure you want to delete this X-ray image?')) {
                        // Create form data
                        const formData = new FormData();
                        formData.append('image_id', imageId);
                        formData.append('visit_id', visitId);
                        
                        // Send AJAX request to delete the image
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
                                // Reload the page after successful deletion
                                window.location.reload();
                            } else {
                                alert('Failed to delete the image: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error deleting image:', error);
                            alert('Failed to delete the image. Please try again.');
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>