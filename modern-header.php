<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session only if it's not already started
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - Flipoo Medical' : 'Flipoo Medical Management'; ?></title>
    
    <!-- Favicon -->
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="assets/css/modern-theme.css">
    
    <!-- Page-specific CSS -->
    <?php if (isset($pageStyles)): ?>
        <?php foreach ($pageStyles as $style): ?>
            <link rel="stylesheet" href="<?php echo $style; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Toast Container for Notifications -->
    <div class="toast-container"></div>
    
    <!-- Main Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top shadow">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <i class="fas fa-heartbeat text-primary me-2"></i>
                <span class="font-semibold">Flipoo Medical</span>
            </a>
            
            <!-- Mobile Toggle Button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" 
                    aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>
            
            <!-- Navigation Links -->
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav ms-auto">
                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">
                            <i class="fas fa-home me-1"></i> Dashboard
                        </a>
                    </li>
                    
                    <!-- Patients -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo in_array(basename($_SERVER['PHP_SELF']), ['patients.php', 'patientDetails.php', 'newRecord.php']) ? 'active' : ''; ?>" href="#" id="patientsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-injured me-1"></i> Patients
                        </a>
                        <ul class="dropdown-menu animate-fade-in" aria-labelledby="patientsDropdown">
                            <li><a class="dropdown-item" href="patients.php">All Patients</a></li>
                            <li><a class="dropdown-item" href="todayPatients.php">Today's Patients</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addPatientModal">Add New Patient</a></li>
                        </ul>
                    </li>
                    
                    <!-- Visits -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo in_array(basename($_SERVER['PHP_SELF']), ['visitRecord.php', 'visitDetails.php', 'editVisitDetails.php']) ? 'active' : ''; ?>" href="#" id="visitsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-calendar-check me-1"></i> Visits
                        </a>
                        <ul class="dropdown-menu animate-fade-in" aria-labelledby="visitsDropdown">
                            <li><a class="dropdown-item" href="todayVisits.php">Today's Visits</a></li>
                            <li><a class="dropdown-item" href="visitRecord.php">Visit Records</a></li>
                        </ul>
                    </li>
                    
                    <!-- Medicines -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'medicineDashboard.php' ? 'active' : ''; ?>" href="medicineDashboard.php">
                            <i class="fas fa-pills me-1"></i> Medicines
                        </a>
                    </li>
                    
                    <!-- Reports -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo in_array(basename($_SERVER['PHP_SELF']), ['misDashboard.php', 'financialReport.php', 'patientReport.php', 'medicineReport.php']) ? 'active' : ''; ?>" href="#" id="reportsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-chart-bar me-1"></i> Reports
                        </a>
                        <ul class="dropdown-menu animate-fade-in" aria-labelledby="reportsDropdown">
                            <li><a class="dropdown-item" href="misDashboard.php">MIS Dashboard</a></li>
                            <li><a class="dropdown-item" href="financialReport.php">Financial Reports</a></li>
                            <li><a class="dropdown-item" href="patientReport.php">Patient Reports</a></li>
                            <li><a class="dropdown-item" href="medicineReport.php">Medicine Reports</a></li>
                        </ul>
                    </li>
                </ul>
                
                <!-- Right Side Actions -->
                <div class="d-flex align-items-center ms-lg-3">
                    <!-- Add Patient Button -->
                    <button class="btn btn-primary btn-sm me-2 d-none d-lg-block" data-bs-toggle="modal" data-bs-target="#addPatientModal">
                        <i class="fas fa-user-plus me-1"></i> New Patient
                    </button>
                    
                    <!-- Dark Mode Toggle -->
                    <div class="form-check form-switch me-3">
                        <input class="form-check-input" type="checkbox" id="darkModeToggle">
                        <label class="form-check-label" for="darkModeToggle">
                            <i class="fas fa-moon"></i>
                        </label>
                    </div>
                    
                    <!-- User Profile Dropdown -->
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="avatar rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                <?php 
                                    $username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
                                    echo strtoupper(substr($username, 0, 1));
                                ?>
                            </div>
                            <span class="d-none d-lg-inline"><?php echo $username; ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end animate-fade-in" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item" href="profile.php">
                                    <i class="fas fa-user me-2"></i> Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#featuresModal">
                                    <i class="fas fa-star me-2"></i> New Features
                                    <span class="badge bg-danger ms-2">New</span>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Page Content Container -->
    <div class="content-container">
        <!-- Page content will be inserted here -->

<?php include 'addPatient.php'; // Include the Add Patient Modal ?>

<!-- New Features Modal -->
<div class="modal fade" id="featuresModal" tabindex="-1" aria-labelledby="featuresModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="featuresModalLabel">
                    <i class="fas fa-star me-2"></i> New Features
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3"><strong>Last Update:</strong> <?php echo date('F j, Y'); ?></p>
                
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title d-flex align-items-center">
                            <i class="fas fa-paint-brush text-primary me-2"></i> Modern UI Redesign
                        </h6>
                        <p class="card-text text-muted">Completely redesigned user interface with modern aesthetics, animations, and improved usability.</p>
                    </div>
                </div>
                
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title d-flex align-items-center">
                            <i class="fas fa-moon text-primary me-2"></i> Dark Mode
                        </h6>
                        <p class="card-text text-muted">New dark mode option for comfortable viewing in low-light environments.</p>
                    </div>
                </div>
                
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title d-flex align-items-center">
                            <i class="fas fa-bell text-primary me-2"></i> Toast Notifications
                        </h6>
                        <p class="card-text text-muted">Elegant toast notifications for system messages and alerts.</p>
                    </div>
                </div>
                
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title d-flex align-items-center">
                            <i class="fas fa-mobile-alt text-primary me-2"></i> Improved Mobile Experience
                        </h6>
                        <p class="card-text text-muted">Better responsiveness and usability on mobile devices.</p>
                    </div>
                </div>
                
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title d-flex align-items-center">
                            <i class="fas fa-chart-line text-primary me-2"></i> Enhanced Reports
                        </h6>
                        <p class="card-text text-muted">Improved reporting with interactive charts and export options.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
</body>
</html> 