<?php
include 'dbConnect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $medicine_name = trim($_POST['medicine_name']);

    // Validate that the medicine name is not empty
    if (!empty($medicine_name)) {
        // Prepare an SQL statement to insert the new medicine
        $stmt = $conn->prepare("INSERT INTO medicines (name) VALUES (?)");
        $stmt->bind_param('s', $medicine_name);

        if ($stmt->execute()) {
            echo "<script>alert('Medicine added successfully!');</script>";
            header('Location: medicineDashboard.php'); // Redirect to login page

        } else {
            echo "<script>alert('Error: Could not add the medicine.');</script>";
        }
    } else {
        echo "<script>alert('Please enter a valid medicine name.');</script>";
    }
}
?>
