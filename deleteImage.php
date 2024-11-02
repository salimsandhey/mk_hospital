<?php
include 'auth.php';
include "dbConnect.php"; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the image ID and visit ID from the POST request
    $image_id = isset($_POST['image_id']) ? intval($_POST['image_id']) : 0;
    $visit_id = isset($_POST['visit_id']) ? intval($_POST['visit_id']) : 0;

    if ($image_id > 0) {
        // Prepare and execute the delete query
        $stmt = $conn->prepare("DELETE FROM xray_images WHERE id = ?");
        $stmt->bind_param("i", $image_id);

        if ($stmt->execute()) {
            // Redirect to updatevisit.php with the visit_id after deletion
            header("Location: editvisitdetails.php?visit_id=" . $visit_id);
            exit; // Ensure no further code is executed
        } else {
            echo "Error deleting image: " . $stmt->error; // Display error if deletion fails
        }
    } else {
        echo "Invalid image ID.";
    }
}
?>
