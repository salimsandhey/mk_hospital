<?php
include 'auth.php';
include "dbConnect.php"; // Include your database connection file

// Set content type to JSON if it's an AJAX request
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the image ID and visit ID from the POST request
    $image_id = isset($_POST['image_id']) ? intval($_POST['image_id']) : 0;
    $visit_id = isset($_POST['visit_id']) ? intval($_POST['visit_id']) : 0;

    $response = ['success' => false, 'message' => ''];

    if ($image_id > 0) {
        // Get the image path to delete the file
        $stmt = $conn->prepare("SELECT image_path FROM xray_images WHERE id = ?");
        $stmt->bind_param("i", $image_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $image_path = $row['image_path'];
            
            // Delete the record from the database
            $delete_stmt = $conn->prepare("DELETE FROM xray_images WHERE id = ?");
            $delete_stmt->bind_param("i", $image_id);
            
            if ($delete_stmt->execute()) {
                // Try to delete the physical file
                if (file_exists($image_path)) {
                    @unlink($image_path);
                }
                
                // Check if there are any remaining images for this visit
                $check_stmt = $conn->prepare("SELECT COUNT(*) as count FROM xray_images WHERE visit_id = ?");
                $check_stmt->bind_param("i", $visit_id);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();
                $remaining_count = $check_result->fetch_assoc()['count'];
                $check_stmt->close();
                
                // If no images remain, update xray_taken in visits table to 0 only if xray_details is empty
                if ($remaining_count == 0) {
                    $details_check = $conn->prepare("SELECT xray_details FROM visits WHERE id = ?");
                    $details_check->bind_param("i", $visit_id);
                    $details_check->execute();
                    $details_result = $details_check->get_result();
                    $xray_details = $details_result->fetch_assoc()['xray_details'];
                    $details_check->close();
                    
                    // Only update xray_taken if xray_details is also empty
                    if (empty($xray_details)) {
                        $update_stmt = $conn->prepare("UPDATE visits SET xray_taken = 0 WHERE id = ?");
                        $update_stmt->bind_param("i", $visit_id);
                        $update_stmt->execute();
                        $update_stmt->close();
                    }
                }
                
                $response['success'] = true;
                $response['message'] = 'Image deleted successfully';
                $response['remaining_count'] = $remaining_count;
                
                if (!$isAjax) {
                    // Redirect to editvisitdetails.php with the visit_id after deletion for regular form submissions
                    header("Location: editvisitdetails.php?visit_id=" . $visit_id);
                    exit; // Ensure no further code is executed
                }
            } else {
                $response['message'] = "Error deleting image: " . $delete_stmt->error;
            }
            
            $delete_stmt->close();
        } else {
            $response['message'] = "Image not found";
        }
        
        $stmt->close();
    } else {
        $response['message'] = "Invalid image ID";
    }
    
    if ($isAjax) {
        // Return JSON response for AJAX requests
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    } else if (!$response['success']) {
        // Display error message for regular form submissions
        echo $response['message'];
    }
}
?>
