<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'auth.php';
include "dbConnect.php";

// Log function for debugging
function logMessage($message) {
    error_log("[updateVisit.php] " . $message);
}

// Check if form data is posted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Log the request
    logMessage("POST request received");
    logMessage("POST data: " . print_r($_POST, true));
    
    // Validate required fields
    if (!isset($_POST['visit_id']) || !isset($_POST['patient_id'])) {
        logMessage("Missing required fields: visit_id or patient_id");
        echo "<div class='alert alert-danger'>Missing required fields</div>";
        exit;
    }
    
    // Get form data with validation
    $visit_id = intval($_POST['visit_id']);
    $patient_id = intval($_POST['patient_id']);
    $visit_date = isset($_POST['visit_date']) ? $_POST['visit_date'] : '';
    $treatment = isset($_POST['treatment']) ? $_POST['treatment'] : '';
    $treatment_options = isset($_POST['treatment_options']) ? implode(',', $_POST['treatment_options']) : '';
    $medicines = isset($_POST['medicines']) ? $_POST['medicines'] : '';
    $xray_taken = isset($_POST['xray']) ? 1 : 0;
    $xray_details = isset($_POST['xray_details']) ? $_POST['xray_details'] : '';
    $fees = isset($_POST['fees']) ? $_POST['fees'] : 0;
    $s_uric_acid = isset($_POST['s_uric_acid']) ? $_POST['s_uric_acid'] : '';
    $calcium = isset($_POST['calcium']) ? $_POST['calcium'] : '';
    $esr = isset($_POST['esr']) ? $_POST['esr'] : '';
    $cholesterol = isset($_POST['cholesterol']) ? $_POST['cholesterol'] : '';
    
    // Log the processed data
    logMessage("Processed data: visit_id=$visit_id, patient_id=$patient_id, medicines=$medicines");
    
    // Prepare the update query
    $update_query = "UPDATE visits SET 
                    visit_date = ?, 
                    treatment = ?, 
                    treatment_options = ?, 
                    medicines = ?, 
                    xray_taken = ?, 
                    xray_details = ?, 
                    fees = ?, 
                    s_uric_acid = ?, 
                    calcium = ?, 
                    esr = ?, 
                    cholesterol = ? 
                    WHERE id = ?";
    
    try {
        // Prepare and execute the statement
        $update_stmt = $conn->prepare($update_query);
        
        if (!$update_stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $update_stmt->bind_param(
            "ssssisssissi", 
            $visit_date, 
            $treatment, 
            $treatment_options, 
            $medicines, 
            $xray_taken, 
            $xray_details, 
            $fees, 
            $s_uric_acid, 
            $calcium, 
            $esr, 
            $cholesterol, 
            $visit_id
        );
        
        // Execute the update
        if ($update_stmt->execute()) {
            logMessage("Visit updated successfully: visit_id=$visit_id");
            
            // Redirect to visit details page
            header("Location: visitDetails.php?visit_id=$visit_id");
            exit;
        } else {
            throw new Exception("Execute failed: " . $update_stmt->error);
        }
    } catch (Exception $e) {
        logMessage("Error: " . $e->getMessage());
        echo "<div class='alert alert-danger'>Error updating visit: " . htmlspecialchars($e->getMessage()) . "</div>";
        echo "<a href='editVisitDetails.php?visit_id=$visit_id' class='btn btn-primary mt-3'>Go Back</a>";
    }
} else {
    // Not a POST request
    logMessage("Invalid request method: " . $_SERVER["REQUEST_METHOD"]);
    header("Location: patients.php");
    exit;
}
?>
