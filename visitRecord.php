<?php
include 'auth.php';

include "dbConnect.php"; // Include your database connection

$patient_id = $_GET['id']; // Get the patient ID from the URL

$sql = "SELECT medicines FROM visits WHERE patient_id = $patient_id ORDER BY visit_date DESC LIMIT 1";
$result = mysqli_query($conn, $sql);

$previousMedicines = '';
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $previousMedicines = $row['medicines'];
    print_r($previousMedicines);
}
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
            $('#medicines_input').val(medicineId);
            $('#medicine_results').html('');
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
            const timing = $('#medicine_timing').val();

            if (medicineName && quantity > 0 && timing) {
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';
                li.textContent = `${medicineName} - Quantity: ${quantity} - Timing: ${timing}`;

                const deleteBtn = document.createElement('button');
                deleteBtn.textContent = 'Remove';
                deleteBtn.className = 'btn btn-danger btn-sm';
                deleteBtn.onclick = function () {
                    li.remove();
                    updateMedicines();
                };

                li.appendChild(deleteBtn);
                document.getElementById('medicines_list').appendChild(li);

                updateMedicines();
                $('#medicine_quantity').val('');
                $('#medicine_search').val('');
                $('#medicine_timing').val('Morning'); // Reset timing
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

        function repeatPreviousMedicines() {
            const previousMedicines = <?php echo json_encode($previousMedicines); ?>;
            if (previousMedicines) {
                const medicinesArray = previousMedicines.split(', '); // Assuming medicines are separated by commas
                medicinesArray.forEach(medicine => {
                    const li = document.createElement('li');
                    li.className = 'list-group-item d-flex justify-content-between align-items-center';
                    li.textContent = medicine;

                    const deleteBtn = document.createElement('button');
                    deleteBtn.textContent = 'Remove';
                    deleteBtn.className = 'btn btn-danger btn-sm';
                    deleteBtn.onclick = function () {
                        li.remove();
                        updateMedicines();
                    };

                    li.appendChild(deleteBtn);
                    document.getElementById('medicines_list').appendChild(li);
                });
                updateMedicines();
            } else {
                alert("No previous medicines found for this patient.");
            }
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

        .visit-box {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 10px;
            background-color: #f9f9f9;
        }

        .highlight {
            font-weight: bold;
            color: #2E37A4;
            /* Adjust color to highlight */
        }

        ul {
            padding: 0 !important;
        }
    </style>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container mb-5">
        <div class="d-flex justify-content-between">
            <h2>Record Patient Visit</h2>
            <button type="button" class="btn custom-btn" data-bs-toggle="modal" data-bs-target="#previousVisitsModal">
                View Previous Visits
            </button>
        </div>
        <form action="saveVisit.php" method="POST" enctype="multipart/form-data" id="Form">
            <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">

            <!-- Visit Date -->
            <div class="mb-3">
                <h6 for="visit_date" class="form-label">Visit Date</h6>
                <input type="date" class="form-control" name="visit_date" id="visit_date" required>
            </div>

            <!-- General Treatment Description (Text Area) -->
            <div class="mb-3">
                <h6 for="treatment" class="form-label">Treatment Description</h6>
                <textarea class="form-control" name="treatment" rows="4" placeholder="Describe the treatment"
                    required></textarea>
            </div>

            <!-- Treatment Options (Checkboxes) -->
            <div class=" mb-3">
                <h6 for="treatment_options" class="form-label">Specific Treatment Options</h6><br>
                <div class="row">
                    <div class="col-md-4 col-sm-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="footbooster" name="treatment_options[]"
                                value="FOOTBOOSTER (FB)">
                            <label class="form-check-label" for="footbooster">FOOTBOOSTER (FB)</label>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="tense" name="treatment_options[]"
                                value="TENSE+">
                            <label class="form-check-label" for="tense">TENSE+</label>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="tmc" name="treatment_options[]"
                                value="TMC">
                            <label class="form-check-label" for="tmc">TMC</label>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="ift" name="treatment_options[]"
                                value="IFT">
                            <label class="form-check-label" for="ift">IFT</label>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="jsb" name="treatment_options[]"
                                value="JSB">
                            <label class="form-check-label" for="jsb">JSB</label>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="laser" name="treatment_options[]"
                                value="LASER THERAPY">
                            <label class="form-check-label" for="laser">LASER THERAPY</label>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="lico" name="treatment_options[]"
                                value="LICO">
                            <label class="form-check-label" for="lico">LICO</label>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="elastic_bandage"
                                name="treatment_options[]" value="ELASTIC BANDAGE">
                            <label class="form-check-label" for="elastic_bandage">ELASTIC BANDAGE</label>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="bandage" name="treatment_options[]"
                                value="BANDAGE">
                            <label class="form-check-label" for="bandage">BANDAGE</label>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Medicines (with search and quantity on one row) -->
            <div class="mb-3">
                <div class="d-flex justify-content-between">
                    <h6 for="medicines" class="form-label">Medicines Prescribed</h6>
                    <a href="javascript:void(0);" id="repeatMedicineBtn" class="primary-color"
                        onclick="repeatPreviousMedicines()">Repeat Previous Medicines</a>
                </div>

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
                        <option value="Morning-Afternoon-Evening">Morning-Afternoon-Evening</option>
                    </select>
                    <button class="btn custom-btn" type="button" onclick="addMedicine()">Add</button>
                </div>

                <div id="medicine_results"></div>
                <ul id="medicines_list" class="list-group mt-3"></ul>
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
                <h6 for="fees" class="form-label">Consultation Fees</h6>
                <input type="number" class="form-control" name="fees" placeholder="Enter fees amount" required>
            </div>

            <!-- Submit Button -->
            <button type="submit" id="saveVisitBtn" class="btn custom-btn">Save Visit</button>
        </form>
        <div class="container">
            <!-- Modal -->
            <div class="modal fade" id="previousVisitsModal" tabindex="-1" aria-labelledby="previousVisitsLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="previousVisitsLabel">Previous Visits</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <?php
                            // PHP code to fetch and display previous visits for this patient
                            include 'dbConnect.php';
                            $patient_id = $_GET['id'];
                            $sql = "SELECT visit_date, treatment, medicines FROM visits WHERE patient_id = '$patient_id' ORDER BY visit_date DESC";
                            $result = mysqli_query($conn, $sql);

                            if (mysqli_num_rows($result) > 0) {
                                echo "<ul>";
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<div class='visit-box'>";
                                    echo "<p><span class='highlight'>Date:</span> " . $row['visit_date'] . "</p>";
                                    echo "<p><span class='highlight'>Treatment:</span> " . $row['treatment'] . "</p>";
                                    echo "<p><span class='highlight'>Medicines:</span> " . $row['medicines'] . "</p>";
                                    echo "</div>";
                                }

                                echo "</ul>";
                            } else {
                                echo "No previous visits found.";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
        document.getElementById('Form').addEventListener('submit', function (e) {
            const form = this;
            if (form.checkValidity()) {
                const saveButton = document.getElementById('saveVisitBtn');
                saveButton.disabled = true; // Disable the button
                saveButton.textContent = 'Saving...'; // Change the button text
            } else {
                e.preventDefault(); // Prevent form submission if invalid
            }
        });
    </script>
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>