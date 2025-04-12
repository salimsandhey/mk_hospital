<?php
// Start output buffering
ob_start();
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit;
}

// // Function to check if user is super admin
// function isSuperAdmin() {
//     global $conn;
//     if (!isset($_SESSION['username'])) {
//         return false;
//     }
    
//     $username = $_SESSION['username'];
//     $query = "SELECT role FROM client_login WHERE username = ?";
              
//     $stmt = mysqli_prepare($conn, $query);
//     mysqli_stmt_bind_param($stmt, "s", $username);
//     mysqli_stmt_execute($stmt);
//     $result = mysqli_stmt_get_result($stmt);
    
//     if ($row = mysqli_fetch_assoc($result)) {
//         return $row['role'] === 'superadmin';
//     }
//     return false;
// }

// Function to check if super admin is verified
function isSuperAdminVerified() {
    // Check if super admin verification exists and is not expired (30 minutes)
    if (isset($_SESSION['super_admin_verified']) && 
        isset($_SESSION['super_admin_verify_time']) && 
        $_SESSION['super_admin_verified'] === true) {
        
        $verifyTime = $_SESSION['super_admin_verify_time'];
        $currentTime = time();
        $timeElapsed = $currentTime - $verifyTime;
        
        // If less than 30 minutes have passed
        if ($timeElapsed < 1800) {
            return true; // Simplified logic - just check time
        }
        
        // If more than 30 minutes, clear the verification
        unset($_SESSION['super_admin_verified']);
        unset($_SESSION['super_admin_verify_time']);
        unset($_SESSION['super_admin_subdomain']);
    }
    return false;
}

// Function to require super admin access
function requireSuperAdmin() {
    $currentPage = basename($_SERVER['PHP_SELF']);
    
    if (!isSuperAdminVerified()) {
        // Store the current page URL for redirect after verification
        $_SESSION['requested_page'] = $currentPage;
        
        // Complete output buffer and clear it
        ob_end_clean();
        
        // Redirect to verification page
        header('Location: superadmin_verify.php?page=' . urlencode($currentPage));
        exit;
    }
}

// Get the current subdomain
$currentSubdomain = explode('.', $_SERVER['HTTP_HOST'])[0];

// Check if the current subdomain matches the one in the session
// if ($currentSubdomain !== $_SESSION['subdomain']) {
//     // Subdomain doesn't match, log the user out and redirect to login page
//     session_unset();
//     session_destroy();
//     header('Location: login.php');
//     exit;
// }

// Optionally, check username if specific access is needed
if (isset($_SESSION['username']) && $_SESSION['username'] === 'yassensandhey') {
    // Grant specific access based on username
    // Your code for specific access here
}

// Proceed with the page content
// Don't end output buffering here, let the page do it
