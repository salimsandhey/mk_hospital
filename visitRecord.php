<?php
include 'auth.php';

include "dbConnect.php"; // Include your database connection

$patient_id = $_GET['id']; // Get the patient ID from the URL
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Visit</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <script src="assets/js/jquery.min.js"></script>
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

        // JavaScript to handle dynamic medicine addition with quantity
        function addMedicine() {
            const medicineName = $('#medicine_search').val();
            const quantity = $('#medicine_quantity').val();

            if (medicineName && quantity > 0) {
                // Create a new list item
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';
                li.textContent = `${medicineName} - Quantity: ${quantity}`;

                // Add delete button to list item
                const deleteBtn = document.createElement('button');
                deleteBtn.textContent = 'Remove';
                deleteBtn.className = 'btn btn-danger btn-sm';
                deleteBtn.onclick = function () {
                    li.remove();
                    updateMedicines();
                };

                li.appendChild(deleteBtn);

                // Add the new list item to the medicines list
                document.getElementById('medicines_list').appendChild(li);

                // Update the hidden input with the new medicines list
                updateMedicines();

                // Reset quantity input for the next entry
                $('#medicine_quantity').val('');
                $('#medicine_search').val('');
            } else {
                alert("Please select a medicine and enter a valid quantity.");
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
    </script>
    <style>
        .medicine-result {
            cursor: pointer;
            padding: 8px;
            border: 1px solid #ccc;
            margin-top: -1px;
            /* Overlap with input */
            z-index: 1000;
            background-color: white;
        }

        .medicine-result:hover {
            background-color: #f0f0f0;
        }
    </style>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container mb-5">
        <h2>Record Patient Visit</h2>
        <form action="saveVisit.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">

            <!-- Visit Date -->
            <div class="mb-3">
                <label for="visit_date" class="form-label">Visit Date</label>
                <input type="date" class="form-control" name="visit_date" id="visit_date" required>
            </div>

            <!-- General Treatment Description (Text Area) -->
            <div class="mb-3">
                <label for="treatment" class="form-label">Treatment Description</label>
                <textarea class="form-control" name="treatment" rows="4" placeholder="Describe the treatment"
                    required></textarea>
            </div>

            <!-- Treatment Options (Checkboxes) -->
            <div class="mb-3">
                <label for="treatment_options" class="form-label">Specific Treatment Options</label><br>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="footbooster" name="treatment_options[]"
                        value="FOOTBOOSTER (FB)">
                    <label class="form-check-label" for="footbooster">FOOTBOOSTER (FB)</label>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="tense" name="treatment_options[]"
                        value="TENSE+">
                    <label class="form-check-label" for="tense">TENSE+</label>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="tmc" name="treatment_options[]" value="TMC">
                    <label class="form-check-label" for="tmc">TMC</label>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="ift" name="treatment_options[]" value="IFT">
                    <label class="form-check-label" for="ift">IFT</label>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="jsb" name="treatment_options[]" value="JSB">
                    <label class="form-check-label" for="jsb">JSB</label>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="laser" name="treatment_options[]"
                        value="LASER THERAPY">
                    <label class="form-check-label" for="laser">LASER THERAPY</label>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="lico" name="treatment_options[]" value="LICO">
                    <label class="form-check-label" for="lico">LICO</label>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="elastic_bandage" name="treatment_options[]"
                        value="ELASTIC BANDAGE">
                    <label class="form-check-label" for="elastic_bandage">ELASTIC BANDAGE</label>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="bandage" name="treatment_options[]"
                        value="BANDAGE">
                    <label class="form-check-label" for="bandage">BANDAGE</label>
                </div>
            </div>


            <!-- Medicines (with search and quantity on one row) -->
            <div class="mb-3">
                <label for="medicines" class="form-label">Medicines Prescribed</label>

                <div class="input-group mb-3">
                    <input type="text" id="medicine_search" class="form-control" placeholder="Search Medicine"
                        oninput="searchMedicines()">
                    <input type="number" id="medicine_quantity" class="form-control" placeholder="Quantity" min="1">
                    <button class="btn btn-secondary" type="button" onclick="addMedicine()">Add</button>
                </div>

                <!-- Div to show search results -->
                <div id="medicine_results"></div>

                <!-- List of selected medicines with quantity -->
                <ul id="medicines_list" class="list-group mt-3"></ul>

                <!-- Hidden input to store the selected medicines -->
                <input type="hidden" id="medicines_input" name="medicines">
            </div>

            <!-- X-ray Checkbox and Details -->
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="xray_checkbox" name="xray"
                    onclick="toggleXrayDetails()">
                <label class="form-check-label" for="xray_checkbox">X-ray Taken</label>
            </div>

            <div id="xray_details_container" style="display: none;">
                <div class="mb-3">
                    <label for="xray_details" class="form-label">X-ray Details</label>
                    <textarea class="form-control" name="xray_details" rows="3"
                        placeholder="Describe the X-ray taken"></textarea>
                </div>
                <div class="mb-3">
                    <label for="xray_file" class="form-label">Upload X-ray</label>
                    <input type="file" class="form-control" name="xray_file[]" id="xray_file" accept="image/*" multiple>
                    <small class="form-text text-muted">You can upload multiple X-ray images.</small>
                </div>
            </div>

            <!-- New Test Fields Block -->
            <div class="mb-3">
                <input type="checkbox" class="form-check-input" id="test_results_checkbox"
                    onclick="toggleTestResults()">
                <label for="test_results_checkbox" class="form-check-label">Test Results</label>
            </div>
            <div id="test_results_container" style="display: none;">
                <label class="form-label">Test Results</label>
                <div class="mb-3 input-group">
                    <input type="text" class="form-control mx-2" name="s_uric_acid" placeholder="S URIC ACID">
                    <input type="text" class="form-control" name="calcium" placeholder="CALCIUM">
                </div>

                <div class="mb-3 input-group">
                    <input type="text" class="form-control mx-2" name="esr" placeholder="E.S.R">
                    <input type="text" class="form-control" name="cholesterol" placeholder="CHOLESTEROL">
                </div>

            </div>

            <!-- Fees -->
            <div class="mb-3">
                <label for="fees" class="form-label">Consultation Fees</label>
                <input type="number" class="form-control" name="fees" placeholder="Enter fees amount" required>
            </div>

            <!-- Submit Button -->
            <button type="submit" id="saveVisitBtn" class="btn btn-primary">Save Visit</button>
        </form>

    </div>

    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->
    <script>
        // Automatically set today's date in the date input
        const today = new Date().toISOString().split('T')[0]; // Get the current date in YYYY-MM-DD format
        document.getElementById('visit_date').value = today;  // Set the date input value
        function toggleXrayDetails() {
            const xrayDetails = document.getElementById('xray_details_container');
            const xrayUpload = document.getElementById('xray_upload_container');
            if (document.getElementById('xray_checkbox').checked) {
                xrayDetails.style.display = 'block';
                xrayUpload.style.display = 'block';
            } else {
                xrayDetails.style.display = 'none';
                xrayUpload.style.display = 'none';
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
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>