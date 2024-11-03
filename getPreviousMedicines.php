<?php
include 'dbConnect.php';

if (isset($_GET['patient_id'])) {
    $patientId = intval($_GET['patient_id']);
    $sql = "SELECT medicines FROM visits WHERE patient_id = $patientId ORDER BY visit_date DESC LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode(['success' => true, 'medicines' => $row['medicines']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No previous medicines found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
