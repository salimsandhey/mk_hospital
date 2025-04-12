<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session only if it's not already started
}
include 'addPatient.php';

// Get notification count (placeholder - can be updated with real logic later)
$notificationCount = 3;

// Get username and first letter for avatar
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
$firstLetter = strtoupper(substr($username, 0, 1));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Record System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css"> -->
    <style>
        /* Custom styling for the header */
        :root {
            --primary-color: #2E37A4;
            --secondary-color: #F0F4FF;
            --accent-color: #4f57d1;
            --text-color: #2B2D42;
            --light-text: #A0A3B1;
            --danger-color: #FF5E57;
            --success-color: #00C896;
            --border-radius: 10px;
            --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        .navbar-custom {
            background-color: white;
            box-shadow: var(--box-shadow);
            padding: 0.8rem 1rem;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            transition: var(--transition);
        }

        .navbar-custom .navbar-brand {
            color: var(--primary-color);
            font-weight: 700;
            font-size: 1.5rem;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
        }

        .navbar-brand i {
            margin-right: 10px;
            font-size: 1.75rem;
        }

        .navbar-custom .nav-link {
            color: var(--text-color);
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: var(--transition);
            border-radius: var(--border-radius);
            position: relative;
        }

        .navbar-custom .nav-link.active,
        .navbar-custom .nav-link:hover {
            color: var(--primary-color);
            background-color: var(--secondary-color);
        }

        .nav-link .badge {
            position: absolute;
            top: 0;
            right: 5px;
            font-size: 0.65rem;
            background-color: var(--danger-color);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .custom-btn {
            background-color: var(--primary-color) !important;
            color: white !important;
            border-radius: var(--border-radius);
            padding: 0.5rem 1.25rem;
            transition: var(--transition);
            border: 1px solid var(--primary-color);
            font-weight: 500;
        }

        .custom-btn:hover {
            background-color: var(--accent-color) !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .custom-btn-outline {
            background-color: transparent !important;
            color: var(--primary-color) !important;
            border: 1px solid var(--primary-color);
            border-radius: var(--border-radius);
            padding: 0.5rem 1.25rem;
            transition: var(--transition);
            font-weight: 500;
        }

        .custom-btn-outline:hover {
            background-color: var(--secondary-color) !important;
            transform: translateY(-2px);
        }

        .navbar-custom .dropdown-menu {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-top: 0.75rem;
            padding: 0.5rem;
            min-width: 220px;
        }

        .navbar-custom .dropdown-item {
            padding: 0.75rem 1rem;
            color: var(--text-color);
            font-weight: 500;
            border-radius: calc(var(--border-radius) - 5px);
            transition: var(--transition);
        }

        .navbar-custom .dropdown-item:hover,
        .navbar-custom .dropdown-item:focus {
            background-color: var(--secondary-color);
            color: var(--primary-color);
        }

        .navbar-custom .dropdown-item i {
            margin-right: 10px;
            color: var(--light-text);
            transition: var(--transition);
        }

        .navbar-custom .dropdown-item:hover i {
            color: var(--primary-color);
        }

        .nav-divider {
            height: 1px;
            margin: 0.5rem 0;
            background-color: rgba(0, 0, 0, 0.05);
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-right: 10px;
        }

        .notification-icon {
            font-size: 1.35rem;
            color: var(--text-color);
            position: relative;
            margin-right: 1.5rem;
            transition: var(--transition);
            cursor: pointer;
        }

        .notification-icon:hover {
            color: var(--primary-color);
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -8px;
            background-color: var(--danger-color);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.65rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Modal styles */
        .modal-content {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        .modal-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1rem 1.5rem;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1rem 1.5rem;
        }
        
        .feature-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .feature-list li {
            padding: 12px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: flex-start;
        }
        
        .feature-list li:last-child {
            border-bottom: none;
        }
        
        .feature-icon {
            background-color: var(--secondary-color);
            color: var(--primary-color);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 14px;
            flex-shrink: 0;
        }
        
        .feature-details {
            flex: 1;
        }
        
        .feature-title {
            font-weight: 600;
            margin-bottom: 4px;
        }
        
        .feature-desc {
            color: var(--light-text);
            font-size: 0.875rem;
            margin: 0;
        }

        /* Responsive padding */
        .container {
            padding-top: 90px;
        }

        /* Mobile optimization */
        @media (max-width: 991.98px) {
            .navbar-collapse {
                background-color: white;
                border-radius: var(--border-radius);
                box-shadow: var(--box-shadow);
                padding: 1rem;
                margin-top: 1rem;
            }
            
            .navbar-nav {
                padding: 0.5rem 0;
            }
            
            .navbar-nav .nav-item {
                margin-bottom: 0.5rem;
            }
            
            .navbar-custom .nav-link {
                padding: 0.75rem 1rem;
            }
            
            .user-menu {
                display: flex;
                align-items: center;
                margin-top: 0.5rem;
                padding: 0.75rem 1rem;
                border-radius: var(--border-radius);
                background-color: var(--secondary-color);
            }
            
            .nav-actions {
                display: flex;
                flex-direction: column;
                width: 100%;
            }
            
            .nav-actions .btn {
                margin-top: 0.5rem;
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <!-- Header/Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container-fluid">
            <!-- Logo/Brand -->
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-heartbeat"></i>
                Flipoo
            </a>

            <!-- Toggle button for mobile view -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar Links -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <!-- Home -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" 
                           href="index.php">
                            <i class="fas fa-home me-2"></i>Dashboard
                        </a>
                    </li>

                    <!-- Patients -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'patients.php' ? 'active' : ''; ?>" 
                           href="patients.php">
                            <i class="fas fa-users me-2"></i>Patients
                        </a>
                    </li>

                    <!-- Medicine Dashboard -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'medicineDashboard.php' ? 'active' : ''; ?>" 
                           href="medicineDashboard.php">
                            <i class="fas fa-pills me-2"></i>Medicines
                        </a>
                    </li>

                    <!-- MIS Dashboard -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'misDashboard.php' ? 'active' : ''; ?>" 
                           href="misDashboard.php">
                            <i class="fas fa-chart-bar me-2"></i>Reports
                        </a>
                    </li>
                </ul>
                
                <div class="d-flex align-items-center nav-actions">
                    <!-- Notifications -->
                    <div class="d-none d-lg-block">
                        <div class="notification-icon" data-bs-toggle="modal" data-bs-target="#featuresModal">
                            <i class="fas fa-bell"></i>
                            <?php if ($notificationCount > 0): ?>
                            <span class="notification-badge"><?php echo $notificationCount; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Add New Patient Button -->
                    <a class="btn custom-btn me-3" data-bs-toggle="modal" data-bs-target="#addPatientModal">
                        <i class="fas fa-plus me-2"></i>New Patient
                    </a>

                    <!-- Profile Dropdown -->
                    <div class="dropdown">
                        <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar">
                                <?php echo $firstLetter; ?>
                            </div>
                            <div class="d-none d-lg-block">
                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($username); ?></div>
                                <div class="text-muted small">Doctor</div>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li>
                                <a class="dropdown-item" href="profile.php">
                                    <i class="fas fa-user"></i> My Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#featuresModal">
                                    <i class="fas fa-star"></i> New Features
                                    <?php if ($notificationCount > 0): ?>
                                    <span class="badge bg-danger ms-2"><?php echo $notificationCount; ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-cog"></i> Settings
                                </a>
                            </li>
                            <li><hr class="nav-divider"></li>
                            <li>
                                <a class="dropdown-item" href="logout.php">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Bootstrap Modal for New Features -->
    <div class="modal fade" id="featuresModal" tabindex="-1" aria-labelledby="featuresModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="featuresModalLabel">
                        <i class="fas fa-star text-warning me-2"></i>
                        New Features & Updates
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3"><strong>Last Updated:</strong> <?php echo date('F j, Y'); ?></p>
                    
                    <ul class="feature-list">
                        <li>
                            <div class="feature-icon">
                                <i class="fas fa-paint-brush"></i>
                            </div>
                            <div class="feature-details">
                                <div class="feature-title">Modern Header Design</div>
                                <p class="feature-desc">We've redesigned the application header with better navigation, mobile responsiveness, and visual enhancements.</p>
                            </div>
                        </li>
                        <li>
                            <div class="feature-icon">
                                <i class="fas fa-table"></i>
                            </div>
                            <div class="feature-details">
                                <div class="feature-title">Improved Patients List</div>
                                <p class="feature-desc">The patients page now includes pagination with a "Load More" button and enhanced search functionality.</p>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn custom-btn-outline" data-bs-dismiss="modal">Got it!</button>
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