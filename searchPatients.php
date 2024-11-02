<?php
include 'auth.php';
include 'dbConnect.php';

$search = $_POST['search'] ?? ''; // Get search input

// Prepare SQL query to search by name or ID, including fetching last visit date
$sql = "
    SELECT p.*, MAX(v.visit_date) as last_visit
    FROM patient p
    LEFT JOIN visits v ON p.id = v.patient_id
    WHERE p.name LIKE '%$search%' OR p.id LIKE '%$search%'
    GROUP BY p.id
    ORDER BY p.id DESC
";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr class='' onclick=\"window.location.href='patientDetails.php?id=" . $row["id"] . "'\" style='cursor:pointer;'>";
        echo "<td>" . $row["id"] . "</td>";
        echo "<td>" . $row["name"] . "</td>";
        echo "<td>" . $row["contact"] . "</td>";
        echo "<td>" . $row["age"] . "</td>";
        echo "<td>" . $row["address"] . "</td>";
        echo "<td>" . ($row["last_visit"] ? $row["last_visit"] : 'No visit') . "</td>";
        echo "<td><a href='visitRecord.php?id=" . $row["id"] . "' class='btn btn-success'>New Visit</a></td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='7'>No patients found</td></tr>";
}
?>
