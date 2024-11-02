<?php
include 'auth.php';

include "dbConnect.php"; // Include your database connection

// Check if form data is posted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $visit_id = $_POST['visit_id'];
    $visit_date = $_POST['visit_date'];
    $treatment = $_POST['treatment'];
    $treatment_options = isset($_POST['treatment_options']) ? implode(',', $_POST['treatment_options']) : '';
    $medicines = $_POST['medicines'];
    $xray_taken = isset($_POST['xray']) ? 1 : 0;
    $xray_details = isset($_POST['xray_details']) ? $_POST['xray_details'] : '';
    $fees = $_POST['fees'];
    $s_uric_acid = $_POST['s_uric_acid'];
    $calcium = $_POST['calcium'];
    $esr = $_POST['esr'];
    $cholesterol = $_POST['cholesterol'];

    // Update the visit in the database
    $update_query = "UPDATE visits SET visit_date = ?, treatment = ?, treatment_options = ?, medicines = ?, xray_taken = ?, xray_details = ?, fees = ?, s_uric_acid = ?, calcium = ?, esr = ?, cholesterol = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("ssssisssissi", $visit_date, $treatment, $treatment_options, $medicines, $xray_taken, $xray_details, $fees, $s_uric_acid, $calcium, $esr, $cholesterol, $visit_id);

    if ($update_stmt->execute()) {
        header('Location: visitDetails.php?visit_id=' . $visit_id);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }
}
?>
