<!-- addPatientModal.php -->
<div class="modal fade" id="addPatientModal" tabindex="-1" aria-labelledby="addPatientModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPatientModalLabel">Add Patient</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="patientForm" action="savePatient.php" method="post">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="age" class="form-label">Age</label>
                        <input type="number" class="form-control" name="age" required min="1">
                    </div>
                    <div class="mb-3">
                        <label for="contact" class="form-label">Contact</label>
                        <input type="text" class="form-control" name="contact" required pattern="\d{10}"
                               placeholder="1234567890">
                        <small class="form-text text-muted">10-digit phone number without spaces.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gender</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="male" value="male" required>
                            <label class="form-check-label" for="male">Male</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="female" value="female" required>
                            <label class="form-check-label" for="female">Female</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control" name="address" required>
                    </div>
                    <div class="mb-3">
                        <label for="disease" class="form-label">Disease</label>
                        <input type="text" class="form-control" name="disease" required>
                    </div>
                    <button type="submit" id="savePatientBtn" class="btn custom-btn">Add Patient</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Form validation and disable submit button on form submission
    document.getElementById('patientForm').addEventListener('submit', function (e) {
        const form = this;
        if (form.checkValidity()) {
            const saveButton = document.getElementById('savePatientBtn');
            saveButton.disabled = true; // Disable the button
            saveButton.textContent = 'Saving...'; // Change the button text
        } else {
            e.preventDefault(); // Prevent form submission if invalid
        }
    });
</script>
