<?php
include "dbConnect.php"; // Include your database connection

if (isset($_POST['search'])) {
    $search = $_POST['search'];

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, name FROM medicines WHERE name LIKE ?");
    $searchTerm = "%" . $search . "%"; // Adding wildcards for LIKE clause
    $stmt->bind_param("s", $searchTerm); // Binding the parameter
    $stmt->execute();
    
    // Fetch the results
    $result = $stmt->get_result();
    
    // Output the results as clickable items
    if ($result->num_rows > 0) {
        while ($medicine = $result->fetch_assoc()) {
            echo "<div class='medicine-result' onclick=\"selectMedicine('{$medicine['id']}', '{$medicine['name']}')\">{$medicine['name']}</div>";
        }
    } else {
        echo "<div class='medicine-result'>No medicines found</div>";
    }
    
    // Close the statement
    $stmt->close();
}

// Close the connection if not needed anymore
// $conn->close();
?>
