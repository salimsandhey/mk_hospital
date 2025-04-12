<?php
include 'auth.php';
include 'dbConnect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get medicine data from form
    $medicine_id = $_POST['medicine_id'] ?? 0;
    $medicine_name = trim($_POST['medicine_name'] ?? '');

    // Validate input
    if (empty($medicine_id) || !is_numeric($medicine_id)) {
        $_SESSION['message'] = "Invalid medicine ID.";
        $_SESSION['message_type'] = "danger";
        header('Location: medicineDashboard.php');
        exit;
    }

    if (empty($medicine_name)) {
        $_SESSION['message'] = "Medicine name cannot be empty.";
        $_SESSION['message_type'] = "danger";
        header('Location: medicineDashboard.php');
        exit;
    }

    // Check if medicine exists
    $checkQuery = "SELECT id FROM medicines WHERE id = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("i", $medicine_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows === 0) {
        $_SESSION['message'] = "Medicine not found.";
        $_SESSION['message_type'] = "danger";
        header('Location: medicineDashboard.php');
        exit;
    }

    // Update medicine
    $updateQuery = "UPDATE medicines SET name = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("si", $medicine_name, $medicine_id);
    
    if ($updateStmt->execute()) {
        $_SESSION['message'] = "Medicine updated successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error updating medicine: " . $conn->error;
        $_SESSION['message_type'] = "danger";
    }

    $updateStmt->close();
} else {
    $_SESSION['message'] = "Invalid request method.";
    $_SESSION['message_type'] = "danger";
}

// Redirect back to the medicine dashboard
header('Location: medicineDashboard.php');
exit;
?> 