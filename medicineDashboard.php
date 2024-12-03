<?php
include 'auth.php';
include "dbConnect.php"; // Include the database connection

// Fetch all medicines initially
$query = "SELECT * FROM medicines";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicine Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Medicine Dashboard</h2>
            <!-- Add Medicine Button -->
            <a href="addMedicineForm.php" class="btn custom-btn">Add Medicine</a>
        </div>

        <div class="mt-3">
            <!-- Search Input -->
            <input type="text" id="search" class="form-control" placeholder="Search for a medicine by name...">
        </div>

        <!-- Medicines Table -->
        <div id="medicinesTable" class="mt-3">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Medicine Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td>
                                    <button class="btn btn-danger" onclick="confirmDelete(<?php echo $row['id']; ?>)">Delete</button>
                                </td>
                            </tr>
                        <?php }
                    } else { ?>
                        <tr>
                            <td colspan="3" class="text-warning text-center">No medicines found.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        // Live search
        $(document).ready(function () {
            $('#search').on('input', function () {
                const searchValue = $(this).val();
                $.ajax({
                    url: 'searchMedicines.php',
                    type: 'GET',
                    data: { search: searchValue },
                    success: function (data) {
                        $('#medicinesTable').html(data);
                    },
                    error: function () {
                        alert('An error occurred while fetching data.');
                    }
                });
            });
        });

        // Confirm delete (placeholder for actual delete functionality)
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this medicine?')) {
                window.location.href = 'deleteMedicine.php?id=' + id;
            }
        }
    </script>
</body>
</html>
