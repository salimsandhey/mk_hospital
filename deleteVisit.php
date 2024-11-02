<?php
include 'auth.php';
include 'dbConnect.php'; // Include your database connection

// Get the visit ID from the URL
$visit_id = isset($_GET['visit_id']) ? $_GET['visit_id'] : null;

// If no visit ID is provided, show an error
if (!$visit_id) {
    echo "Visit ID is missing!";
    exit;
}

// Fetch the patient_id before deleting the visit to use for redirection
$query = "SELECT patient_id FROM visits WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $visit_id);
$stmt->execute();
$stmt->bind_result($patient_id);
$stmt->fetch();
$stmt->close();

// If the patient ID is not found, show an error
if (!$patient_id) {
    echo "Invalid visit ID!";
    exit;
}

// Delete the visit record from the database
$deleteQuery = "DELETE FROM visits WHERE id = ?";
$deleteStmt = $conn->prepare($deleteQuery);
$deleteStmt->bind_param("i", $visit_id);

if ($deleteStmt->execute()) {
    // If delete is successful, redirect to the patient's details page
    header("Location: patientDetails.php?id=" . $patient_id);
    exit;
} else {
    echo "Error deleting visit record!";
}

$deleteStmt->close();
$conn->close();
?>
