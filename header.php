<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session only if it's not already started
}
include 'addPatient.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Record System</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css"> -->
    <style>
        /* Custom styling for the header */
        .navbar-custom {
            background-color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .navbar-custom .navbar-brand {
            color: #343c92;
            font-weight: bold;
        }

        .navbar-custom .nav-link {
            color: #343c92;
        }

        .navbar-custom .nav-link:hover {
            color: #d1e1ff;
        }

        .btn-accent {
            background-color: #f5a623;
            color: #ffffff;
            border: none;
        }

        .btn-accent:hover {
            background-color: #ffdd57;
        }

        .navbar-custom .dropdown-menu {
            background-color: #f8f9fa;
        }

        .navbar-custom .dropdown-item:hover {
            background-color: #4c70ba;
            color: #ffffff;
        }

        /* Responsive padding */
        .container {
            padding-top: 70px;
        }
    </style>
</head>

<body>
    <!-- Header/Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container-fluid">
            <!-- Logo/Brand -->
            <a class="navbar-brand" href="index.php">Flipoo</a>

            <!-- Toggle button for mobile view -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar Links -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <!-- Home -->
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="index.php">Home</a>
                    </li>

                    <!-- Patients -->
                    <li class="nav-item">
                        <a class="nav-link" href="patients.php">Patients</a>
                    </li>

                    <!-- Medicine Dashboard -->
                    <li class="nav-item">
                        <a class="nav-link" href="medicineDashboard.php">Medicine Dashboard</a>
                    </li>

                    <!-- MIS Dashboard -->
                    <li class="nav-item">
                        <a class="nav-link" href="misDashboard.php">MIS Dashboard</a>
                    </li>

                    <!-- Add New Patient Button -->
                    <li class="nav-item">
                        <a class="btn custom-btn ms-2" data-bs-toggle="modal" data-bs-target="#addPatientModal">Add New
                            Patient</a>
                    </li>

                    <!-- Profile Dropdown -->
                    <li class="nav-item dropdown ms-2">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin'; ?>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <!-- Add dropdown items here if needed -->
                            <li class="nav-item">
                                <a class="nav-link" href="profile.php">Profile</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="logout.php">Logout</a>
                            </li>
                            <li class="nav-item">

                                <button type="button" class="btn btn-info position-relative" data-bs-toggle="modal"
                                    data-bs-target="#featuresModal">
                                    New Features
                                    <span
                                        class="position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle">
                                        <span class="visually-hidden">New alerts</span>
                                    </span>
                                </button>

                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Bootstrap Modal for New Features -->
    <div class="modal fade" id="featuresModal" tabindex="-1" aria-labelledby="featuresModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="featuresModalLabel">New Features in This Update</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Last Update Date:</strong> December 4, 2024</p>
                    <ul>
                        <li>
                            <strong>Add Search in Medicine Dashboard.</strong>
                            
                            <br>
                        </li>
                        <li>
                            <strong>Add new Medicine taking time options.</strong>
                            <br>
                        </li>
                    </ul>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content (Add padding top because of fixed navbar) -->
    <div class="container">
        <!-- Your main content goes here -->
    </div>

    <!-- Bootstrap JS -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script> -->
</body>

</html>