<?php
include 'dbConnect.php';

$patient_id = $_GET['patient_id'];
$response = [];

// Fetch allergic medicines for the patient
$query = "SELECT medicine_name FROM allergic_medicines WHERE patient_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

$medicines = [];
while ($row = $result->fetch_assoc()) {
    $medicines[] = $row['medicine_name'];
}

$response['medicines'] = $medicines;
$stmt->close();
$conn->close();

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
