<?php
include 'dbConnect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patient_id = $_POST['patient_id'];
    $allergic_medicines = explode("\n", $_POST['allergic_medicines']); // Split by new line

    // Clear existing allergic medicines for the patient (optional)
    $clearQuery = "DELETE FROM allergic_medicines WHERE patient_id = ?";
    $clearStmt = $conn->prepare($clearQuery);
    $clearStmt->bind_param("i", $patient_id);
    $clearStmt->execute();

    // Insert new allergic medicines
    foreach ($allergic_medicines as $medicine) {
        $medicine = trim($medicine); // Trim whitespace
        if (!empty($medicine)) {
            $insertQuery = "INSERT INTO allergic_medicines (patient_id, medicine_name) VALUES (?, ?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("is", $patient_id, $medicine);
            $insertStmt->execute();
            $insertStmt->close();
        }
    }

    $clearStmt->close();
    $conn->close();

    // Redirect or return success message
    header('Location: patientDetails.php?id=' . $patient_id); // Redirect back to patient profile
    exit;
}
?>
