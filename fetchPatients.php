<?php
include 'auth.php';
include 'dbConnect.php';

// Set content type to JSON
header('Content-Type: application/json');

// Get the current page and search query from POST data
$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
$search = isset($_POST['search']) ? $_POST['search'] : '';

// Number of patients per page
$patientsPerPage = 15;
$offset = ($page - 1) * $patientsPerPage;

// Prepare the SQL query based on whether there's a search query or not
if (!empty($search)) {
    // Search patients by ID, name, or contact number
    $searchParam = "%$search%";
    $countSql = "
        SELECT COUNT(*) as total
        FROM patient p
        WHERE p.id LIKE ? OR p.name LIKE ? OR p.contact LIKE ?
    ";
    $countStmt = $conn->prepare($countSql);
    $countStmt->bind_param("sss", $searchParam, $searchParam, $searchParam);
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $totalPatients = $countResult->fetch_assoc()['total'];
    
    $sql = "
        SELECT p.*, MAX(v.visit_date) as last_visit
        FROM patient p
        LEFT JOIN visits v ON p.id = v.patient_id
        WHERE p.id LIKE ? OR p.name LIKE ? OR p.contact LIKE ?
        GROUP BY p.id
        ORDER BY p.id DESC
        LIMIT ? OFFSET ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssii", $searchParam, $searchParam, $searchParam, $patientsPerPage, $offset);
} else {
    // Get all patients with pagination
    $countSql = "SELECT COUNT(*) as total FROM patient";
    $countResult = $conn->query($countSql);
    $totalPatients = $countResult->fetch_assoc()['total'];
    
    $sql = "
        SELECT p.*, MAX(v.visit_date) as last_visit
        FROM patient p
        LEFT JOIN visits v ON p.id = v.patient_id
        GROUP BY p.id
        ORDER BY p.id DESC
        LIMIT ? OFFSET ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $patientsPerPage, $offset);
}

// Execute the query
$stmt->execute();
$result = $stmt->get_result();

// Initialize HTML output
$html = '';

// Check if there are results
if ($result->num_rows > 0) {
    // Fetch and display each row of data
    while ($row = $result->fetch_assoc()) {
        $html .= "<tr class='' onclick=\"window.location.href='patientDetails.php?id=" . htmlspecialchars($row["id"]) . "'\" style='cursor:pointer;'>";
        $html .= "<td class='bold'>" . htmlspecialchars($row["id"]) . "</td>";
        $html .= "<td>" . htmlspecialchars($row["name"]) . "</td>";
        $html .= "<td>" . htmlspecialchars($row["contact"]) . "</td>";
        $html .= "<td class='hide'>" . htmlspecialchars($row["age"]) . "</td>";
        $html .= "<td>" . htmlspecialchars($row["address"]) . "</td>";
        $html .= "<td class='hide'>" . (isset($row["last_visit"]) && $row["last_visit"] ? htmlspecialchars($row["last_visit"]) : 'N/A') . "</td>";
        $html .= "<td><a href='visitRecord.php?id=" . htmlspecialchars($row["id"]) . "' class='btn custom-btn'>New Visit</a></td>";
        $html .= "</tr>";
    }
} else {
    $html = "<tr><td colspan='7' class='text-center'>No patients found</td></tr>";
}

// Calculate if there are more pages
$totalPages = ceil($totalPatients / $patientsPerPage);
$hasMore = ($page < $totalPages);

// Prepare the response
$response = [
    'html' => $html,
    'currentPage' => $page,
    'hasMore' => $hasMore,
    'totalPatients' => $totalPatients,
    'showing' => min($page * $patientsPerPage, $totalPatients)
];

// Return the JSON response
echo json_encode($response);

// Close statement and connection
$stmt->close();
$conn->close();
?>
