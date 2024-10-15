<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php'); // Redirect to login page
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa; /* Light gray background */
        }

        .container {
            margin-top: 80px; /* Space for fixed navbar */
        }

        .btn-accent {
            background-color: #343c92;
            color: #ffffff;
            width: 100%;
            margin-bottom: 10px;
        }

        .btn-accent:hover {
            background-color: #5a67d8;
        }
    </style>
</head>

<body>
    <!-- Include the header/navbar here -->
    <?php include "header.php"; ?>
    
    <div class="container">
        <h1 class="mt-5">Dashboard</h1>
        
        <!-- Links to various sections -->
        <div class="row">
            <div class="col-md-4 mb-3">
                <a href="patients.php" class="btn btn-accent">View Patients</a>
            </div>
            <div class="col-md-4 mb-3">
                <a href="newRecord.php" class="btn btn-accent">Add New Patient</a>
            </div>
            <div class="col-md-4 mb-3">
                <a href="yearly_financials.php" class="btn btn-accent">Yearly Financials</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>
