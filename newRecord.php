<?php
include 'auth.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Patient</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> -->
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
</head>

<body>
    <?php
    include 'header.php';
    ?>
    <div class="container border py-3 rounded-3 px-4 " style="margin-top: 5rem">
        <h2>Add Patient</h2>
        <form id="patientForm" action="savePatient.php" method="post">
            <div class="row mb-3">
                <div class="col">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" name="name" required>
                </div>
                <div class="col">
                    <label for="age" class="form-label">Age</label>
                    <input type="number" class="form-control" name="age" required min="1">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <label for="contact" class="form-label">Contact</label>
                    <input type="text" class="form-control" name="contact" required pattern="\d{10}"
                        placeholder="1234567890">
                    <small class="form-text text-muted">10-digit phone number without spaces.</small>
                </div>
                <div class="col">
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
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" class="form-control" name="address" required>
            </div>
            <div class="mb-3">
                <label for="disease" class="form-label">Disease</label>
                <input type="text" class="form-control" name="disease" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Patient</button>
        </form>
    </div>

    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script> -->
    <script>
        // Optionally add form validation or other scripts here
        $(document).ready(function () {
            $('#patientForm').on('submit', function (event) {
                // Add your validation or processing logic here if needed
            });
        });
    </script>
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>