<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php'); // Redirect to login page
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Patients</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .search-bar {
            max-width: 300px;
        }

        .search-bar input {
            padding-left: 30px;
        }

        .search-bar i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #bbb;
        }

        @media (max-width: 576px) {
            .search-bar {
                width: 100%;
                /* Ensure the search bar is 100% width */
            }
        }
    </style>
</head>

<body>
    <?php
    include 'header.php';
    ?>
    <div class="container">
        <div class="mb-3">
            <!-- For larger screens (keeping the previous layout) -->
            <div class=" justify-content-between align-items-center mb-2">
                <h3>Patient List</h3>
                <div class="d-flex">
                    <div class="search-bar position-relative">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchPatient" class="form-control"
                            placeholder="Search by ID, Name, or Phone" onkeyup="searchPatients()">
                    </div>
                    <a class="btn btn-primary ms-2" href="newRecord.php">Add New Patient</a>
                </div>
            </div>
        </div>



        <table class="table table-hover all-patients-table">
            <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Name</th>
                    <th scope="col">Mobile</th>
                    <!-- <th scope="col">Age</th> -->
                    <!-- <th scope="col">Address</th> -->
                    <th scope="col">Last Visit</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody id="patientTableBody">
                <?php
                include "dbConnect.php";
                $sql = "SELECT p.*, MAX(v.visit_date) AS last_visit
                            FROM patient p
                            LEFT JOIN visits v ON p.id = v.patient_id
                            GROUP BY p.id
                            ORDER BY p.id DESC"; // Newest first
                $result = mysqli_query($conn, $sql);

                // Check if there are results
                if (mysqli_num_rows($result) > 0) {
                    // Fetch and display each row of data
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr class='' onclick=\"window.location.href='patientDetails.php?id=" . $row["id"] . "'\" style='cursor:pointer;'>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["name"] . "</td>";
                        echo "<td>" . $row["contact"] . "</td>";
                        // echo "<td>" . $row["age"] . "</td>";
                        // echo "<td>" . $row["address"] . "</td>";
                        echo "<td>" . ($row["last_visit"] ? $row["last_visit"] : 'N/A') . "</td>"; // Last visit date
                        echo "<td><a href='visitRecord.php?id=" . $row["id"] . "' class='btn btn-primary'>New Visit</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>No patients found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->
    <script>
        function searchPatients() {
            const input = document.getElementById('searchPatient').value.toLowerCase();
            const table = document.getElementById('patientTableBody');
            const rows = table.getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                const idCell = rows[i].getElementsByTagName('td')[0];
                const nameCell = rows[i].getElementsByTagName('td')[1];
                const phoneCell = rows[i].getElementsByTagName('td')[2];

                if (nameCell || idCell || phoneCell) {
                    const idText = idCell.textContent || idCell.innerText;
                    const nameText = nameCell.textContent || nameCell.innerText;
                    const phoneText = phoneCell.textContent || phoneCell.innerText;

                    // Check if the input matches the ID, Name, or Phone number
                    if (idText.toLowerCase().indexOf(input) > -1 || nameText.toLowerCase().indexOf(input) > -1 || phoneText.toLowerCase().indexOf(input) > -1) {
                        rows[i].style.display = "";
                    } else {
                        rows[i].style.display = "none";
                    }
                }
            }
        }
    </script>
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>