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
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>

<body>
    <?php include 'header.php'; ?>

    <div class="container-fix today-patients">
        <h5 class="mb-4">Today's Visits</h5>

        <?php if ($result->num_rows > 0): ?>
            <table class="table table-hover mt-3">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Patient Name</th>
                        <th>Visit Date</th>
                        <th>Treatment</th>
                        <th>Medicines</th>
                        <th>Fees</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($visit = $result->fetch_assoc()): ?>
                        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to delete this visit record for
                                        <?php echo htmlspecialchars($visit['patient_name']); ?>?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <a href="deleteVisit.php?visit_id=<?php echo $visit['id']; ?>"
                                            class="btn btn-danger">Delete</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <tr>
                            <td><?php echo htmlspecialchars($visit['id']); ?></td>
                            <td><?php echo htmlspecialchars($visit['patient_name']); ?></td>
                            <td><?php echo htmlspecialchars($visit['visit_date']); ?></td>
                            <td><?php echo htmlspecialchars($visit['treatment']); ?></td>
                            <td><?php echo htmlspecialchars($visit['medicines']); ?></td>
                            <td><?php echo htmlspecialchars($visit['fees']); ?></td>
                            <!-- <td><button class="action-icon"><i class="fa-solid fa-ellipsis-vertical"></i></button></td> -->
                            <td>
                                <div class="dropdown">
                                    <button class="action-icon dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item"
                                                href="editVisitDetails.php?visit_id=<?php echo $visit['id'] ?>">Edit</a></li>
                                        <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                href="#">Delete</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
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