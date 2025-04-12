<?php
include 'auth.php';
include "dbConnect.php";

// Get the visit ID from the request
$visit_id = isset($_GET['visit_id']) ? intval($_GET['visit_id']) : 0;

if (!$visit_id) {
    echo "No visit ID provided";
    exit;
}

// Fetch X-ray images
$xray_query = "SELECT * FROM xray_images WHERE visit_id = ?";
$xray_stmt = $conn->prepare($xray_query);
$xray_stmt->bind_param("i", $visit_id);
$xray_stmt->execute();
$xray_result = $xray_stmt->get_result();
$xray_images = $xray_result->fetch_all(MYSQLI_ASSOC);

// Generate HTML for the X-ray images
foreach ($xray_images as $xray) {
    echo '<div class="xray-image mb-2">';
    echo '<img src="' . htmlspecialchars($xray['image_path']) . '" class="xray-thumbnail">';
    echo '<button type="button" class="btn btn-danger btn-sm mt-2 delete-xray-btn" data-image-id="' . htmlspecialchars($xray['id']) . '" data-visit-id="' . $visit_id . '">Delete</button>';
    echo '</div>';
}

if (count($xray_images) === 0) {
    echo '<p>No X-ray images available.</p>';
}
?> 