<?php
include 'auth.php';

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
