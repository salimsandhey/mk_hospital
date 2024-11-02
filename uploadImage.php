<?php
include 'auth.php';
include "dbConnect.php"; // Include database connection

$response = ['success' => false]; // Default response

if (isset($_FILES['xray_file']) && isset($_POST['visit_id']) && isset($_POST['patient_id'])) {
    $visit_id = $_POST['visit_id'];
    $patient_id = $_POST['patient_id'];

    // File handling
    $targetDir = "uploads/xrays/";
    
    // Generate a unique name for the image using your logic from savevisit
    $fileType = pathinfo($_FILES["xray_file"]["name"], PATHINFO_EXTENSION);
    $newFileName = $patient_id . "_xray_" . time() . "." . $fileType;
    $targetFilePath = $targetDir . $newFileName;

    // Allow only specific file formats
    $allowTypes = ['jpg', 'png', 'jpeg', 'gif'];
    if (in_array($fileType, $allowTypes)) {
        // Move file to the uploads directory
        if (move_uploaded_file($_FILES["xray_file"]["tmp_name"], $targetFilePath)) {
            // Insert image details into the database with patient_id and visit_id
            $insert = $conn->prepare("INSERT INTO xray_images (visit_id, patient_id, image_path) VALUES (?, ?, ?)");
            $insert->bind_param("iis", $visit_id, $patient_id, $targetFilePath);

            if ($insert->execute()) {
                $response['success'] = true;
                $response['image_path'] = $targetFilePath;
                $response['image_id'] = $insert->insert_id;
            }
            $insert->close();
        }
    }
}

echo json_encode($response);
$conn->close();
?>
