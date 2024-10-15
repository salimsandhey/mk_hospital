<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

include 'dbConnect.php';

if (isset($_GET['id'])) {
    $medicine_id = $_GET['id'];

    // SQL to delete the medicine
    $query = "DELETE FROM medicines WHERE id = '$medicine_id'";
    if (mysqli_query($conn, $query)) {
        $_SESSION['message'] = "Medicine deleted successfully!";
    } else {
        $_SESSION['message'] = "Error deleting medicine.";
    }
}

header('Location: medicineDashboard.php');
exit;
?>
