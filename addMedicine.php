<?php
include 'auth.php';
include 'dbConnect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and validate medicine name
    $medicine_name = trim($_POST['medicine_name'] ?? '');

    // Validate that the medicine name is not empty
    if (empty($medicine_name)) {
        $_SESSION['message'] = "Please enter a valid medicine name.";
        $_SESSION['message_type'] = "danger";
        header('Location: medicineDashboard.php');
        exit;
    }

    // Check if medicine with the same name already exists
    $checkQuery = "SELECT id FROM medicines WHERE name = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("s", $medicine_name);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        $_SESSION['message'] = "A medicine with this name already exists.";
        $_SESSION['message_type'] = "warning";
        header('Location: medicineDashboard.php');
        exit;
    }

    // Prepare an SQL statement to insert the new medicine
    $insertQuery = "INSERT INTO medicines (name) VALUES (?)";
    $insertStmt = $conn->prepare($insertQuery);
    $insertStmt->bind_param("s", $medicine_name);

    if ($insertStmt->execute()) {
        $_SESSION['message'] = "Medicine \"" . htmlspecialchars($medicine_name) . "\" added successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error adding medicine: " . $conn->error;
        $_SESSION['message_type'] = "danger";
    }

    $insertStmt->close();
} else {
    $_SESSION['message'] = "Invalid request method.";
    $_SESSION['message_type'] = "danger";
}

// Redirect to medicine dashboard
header('Location: medicineDashboard.php');
exit;
?>
