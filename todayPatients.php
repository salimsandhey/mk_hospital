<?php
include 'dbConnect.php'; // Include your database connection

// Fetch today's date
$today = date('Y-m-d');

// Query to get patients registered today
$queryTodayPatients = "SELECT id, name, age, gender, contact, address, disease, registration_date FROM patient WHERE DATE(registration_date) = '$today'";
$resultTodayPatients = $conn->query($queryTodayPatients);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Today's New Patients</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css"> <!-- Bootstrap CSS -->
</head>
<body>
<?php include 'header.php'; ?>
    <div class="container">
        <h2>New Patients Added Today</h2>
        <?php if ($resultTodayPatients->num_rows > 0): ?>
            <table class="table table-striped table-bordered mt-3">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Contact</th>
                        <th>Address</th>
                        <th>Disease</th>
                        <th>Registration Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($patient = $resultTodayPatients->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($patient['id']); ?></td>
                            <td><?php echo htmlspecialchars($patient['name']); ?></td>
                            <td><?php echo htmlspecialchars($patient['age']); ?></td>
                            <td><?php echo htmlspecialchars($patient['gender']); ?></td>
                            <td><?php echo htmlspecialchars($patient['contact']); ?></td>
                            <td><?php echo htmlspecialchars($patient['address']); ?></td>
                            <td><?php echo htmlspecialchars($patient['disease']); ?></td>
                            <td><?php echo htmlspecialchars($patient['registration_date']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info mt-3">No new patients added today.</div>
        <?php endif; ?>
    </div>
    
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script> <!-- Bootstrap JS -->
</body>
</html>
<?php
$conn->close(); // Close the database connection
?>
