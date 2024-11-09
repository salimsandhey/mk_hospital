<?php
include 'auth.php'; // Ensure user is authenticated
include 'dbConnect.php'; // Database connection

// Get today's date in 'Y-m-d' format
$today = date('Y-m-d');

// Query to get visits for today
$query = "SELECT v.id, v.visit_date, v.treatment, v.medicines, v.fees, p.name AS patient_name 
          FROM visits v
          JOIN patient p ON v.patient_id = p.id
          WHERE v.visit_date = ?";

// Prepare the statement
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Today's Visits</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container mt-5">
        <h2 class="mb-4">Today's Visits</h2>

        <?php if ($result->num_rows > 0) : ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Patient Name</th>
                        <th>Visit Date</th>
                        <th>Treatment</th>
                        <th>Medicines</th>
                        <th>Fees</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($visit = $result->fetch_assoc()) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($visit['id']); ?></td>
                            <td><?php echo htmlspecialchars($visit['patient_name']); ?></td>
                            <td><?php echo htmlspecialchars($visit['visit_date']); ?></td>
                            <td><?php echo htmlspecialchars($visit['treatment']); ?></td>
                            <td><?php echo htmlspecialchars($visit['medicines']); ?></td>
                            <td><?php echo htmlspecialchars($visit['fees']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p class="alert alert-info">No visits for today.</p>
        <?php endif; ?>

        <?php
        // Free result and close statement
        $result->free();
        $stmt->close();
        ?>
    </div>

    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
