<?php
session_start();

// Clear only the superadmin verification session variables
unset($_SESSION['super_admin_verified']);
unset($_SESSION['super_admin_verify_time']);
unset($_SESSION['super_admin_subdomain']);

// Redirect back to the MIS dashboard, which will now require verification again
header("Location: index.php");
exit();
?> 