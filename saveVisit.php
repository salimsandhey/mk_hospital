<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php'); // Redirect to login page
    exit;
}

include 'dbConnect.php'; // Include your database connection

// Get data from the form
$patient_id = $_POST['patient_id'];
$visit_date = $_POST['visit_date'];
$treatment = $_POST['treatment'];
$medicines = $_POST['medicines'];
$fees = $_POST['fees'];
$xray_taken = isset($_POST['xray']) ? 1 : 0; // Check if X-ray checkbox was selected
$xray_details = $xray_taken ? $_POST['xray_details'] : NULL; // Only store details if X-ray is taken

// Get selected treatment options (checkboxes)
$treatment_options = isset($_POST['treatment_options']) ? implode(",", $_POST['treatment_options']) : '';

// Optional test results
$s_uric_acid = $_POST['s_uric_acid'] ?? NULL;
$calcium = $_POST['calcium'] ?? NULL;
$esr = $_POST['esr'] ?? NULL;
$cholesterol = $_POST['cholesterol'] ?? NULL;

// Handle X-ray file uploads
$xray_file_paths = []; // Array to store file paths
if ($xray_taken && isset($_FILES['xray_file']) && $_FILES['xray_file']['error'][0] === UPLOAD_ERR_OK) {
    $upload_dir = 'uploads/xrays/'; // Directory to store the files

    // Create the directory if it doesn't exist
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    foreach ($_FILES['xray_file']['tmp_name'] as $key => $tmp_name) {
        if ($_FILES['xray_file']['error'][$key] === UPLOAD_ERR_OK) {
            // Create a unique file name to prevent overwriting
            $file_name = uniqid('xray_', true) . '.' . pathinfo($_FILES['xray_file']['name'][$key], PATHINFO_EXTENSION);
            $file_path = $upload_dir . $file_name;

            // Move the uploaded file to the desired location
            if (move_uploaded_file($tmp_name, $file_path)) {
                $xray_file_paths[] = $file_path; // Add the file path to the array
            } else {
                echo "Error uploading file: $file_name";
                exit;
            }
        }
    }
}

// Convert array of file paths to a comma-separated string for the visits table (if needed)
$xray_file_path_str = implode(',', $xray_file_paths);

// Prepare the SQL query with the new test result fields for the visits table
$query = "INSERT INTO visits (patient_id, visit_date, treatment, medicines, fees, xray_taken, xray_details, treatment_options, xray_file, s_uric_acid, calcium, esr, cholesterol) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

// Prepare and execute the statement for visits table
$stmt = $conn->prepare($query);
$stmt->bind_param("isssdisssssss", $patient_id, $visit_date, $treatment, $medicines, $fees, $xray_taken, $xray_details, $treatment_options, $xray_file_path_str, $s_uric_acid, $calcium, $esr, $cholesterol);

if ($stmt->execute()) {
    // Get the last inserted visit ID
    $last_visit_id = $conn->insert_id;

    // Now save the x-ray image paths in the xray_images table
    foreach ($xray_file_paths as $image_path) {
        $xray_insert_query = "INSERT INTO xray_images (visit_id, patient_id, image_path, description) VALUES (?, ?, ?, ?)";
        $xray_stmt = $conn->prepare($xray_insert_query);
        
        // You can set the description to a default value or use the xray_details input
        $description = $xray_details ?? 'No description provided';

        $xray_stmt->bind_param("iiss", $last_visit_id, $patient_id, $image_path, $description);
        
        // Execute the insert for each x-ray file path
        if (!$xray_stmt->execute()) {
            echo "Error saving X-ray image path: " . $xray_stmt->error;
            exit;
        }
        
        $xray_stmt->close(); // Close the statement after each insert
    }

    // Redirect to the patient's profile page or show success message
    header("Location: patientDetails.php?id=$patient_id");
} else {
    echo "Error: " . $stmt->error;
}

// Close statement and connection
$stmt->close();
$conn->close();
?>
