<?php
include "dbConnect.php"; // Include the database connection

// Get search term
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Fetch medicines based on search term
$query = "SELECT * FROM medicines";
if ($search) {
    $query .= " WHERE name LIKE '%$search%'";
}
$result = mysqli_query($conn, $query);

// Start generating the full table structure
echo '<table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Medicine Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>';

// Add rows dynamically
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<tr>
                <td>' . htmlspecialchars($row['id']) . '</td>
                <td>' . htmlspecialchars($row['name']) . '</td>
                <td>
                    <button class="btn btn-danger" onclick="confirmDelete(' . $row['id'] . ')">Delete</button>
                </td>
            </tr>';
    }
} else {
    echo '<tr>
            <td colspan="3" class="text-warning text-center">No medicines found.</td>
          </tr>';
}

// Close the table structure
echo '</tbody>
    </table>';
?>
