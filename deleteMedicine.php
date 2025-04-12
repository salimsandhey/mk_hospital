<?php
include 'auth.php';

include 'dbConnect.php';

if (isset($_GET['id'])) {
    $medicine_id = $_GET['id'];
    
    // Validate input
    if (!is_numeric($medicine_id)) {
        $_SESSION['message'] = "Invalid medicine ID.";
        $_SESSION['message_type'] = "danger";
        header('Location: medicineDashboard.php');
        exit;
    }
    
    // Check if the medicine exists
    $checkQuery = "SELECT name FROM medicines WHERE id = ?";
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
    
    // Get the medicine name for the success message
    $medicineName = $checkResult->fetch_assoc()['name'];
    
    // Delete the medicine
    $deleteQuery = "DELETE FROM medicines WHERE id = ?";
    $deleteStmt = $conn->prepare($deleteQuery);
    $deleteStmt->bind_param("i", $medicine_id);
    
    if ($deleteStmt->execute()) {
        $_SESSION['message'] = "Medicine \"" . htmlspecialchars($medicineName) . "\" deleted successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error deleting medicine: " . $conn->error;
        $_SESSION['message_type'] = "danger";
    }
    
    $deleteStmt->close();
} else {
    $_SESSION['message'] = "No medicine specified for deletion.";
    $_SESSION['message_type'] = "warning";
}

header('Location: medicineDashboard.php');
exit;
?>
