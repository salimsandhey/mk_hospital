<?php
// error_reporting(E_ALL);
// ini_set('display_errors',1);

include 'auth.php';
// $client_id = $_SESSION['client_id'];
?>
<?php
include "dbConnect.php"; // Include your database connection

// Get today's date
$today = date('Y-m-d');

// Query to fetch the count of today's new patients
$queryNewPatientsToday = "SELECT COUNT(*) AS new_patients_today FROM patient WHERE DATE(registration_date) = '$today'";
$resultNewPatientsToday = $conn->query($queryNewPatientsToday);
$newPatientsToday = ($resultNewPatientsToday->num_rows > 0) ? $resultNewPatientsToday->fetch_assoc()['new_patients_today'] : 0;

// Query to fetch the total number of patients
$queryTotalPatients = "SELECT COUNT(*) AS total_patients FROM patient";
$resultTotalPatients = $conn->query($queryTotalPatients);
$totalPatients = ($resultTotalPatients->num_rows > 0) ? $resultTotalPatients->fetch_assoc()['total_patients'] : 0;

// Query to fetch the count of today's new visits
$queryNewVisitsToday = "SELECT COUNT(*) AS new_visits_today FROM visits WHERE DATE(visit_date) = '$today'";
$resultNewVisitsToday = $conn->query($queryNewVisitsToday);
$newVisitsToday = ($resultNewVisitsToday->num_rows > 0) ? $resultNewVisitsToday->fetch_assoc()['new_visits_today'] : 0;

// Query to fetch the total number of visits
$queryTotalVisits = "SELECT COUNT(*) AS total_visits FROM visits";
$resultTotalVisits = $conn->query($queryTotalVisits);
$totalVisits = ($resultTotalVisits->num_rows > 0) ? $resultTotalVisits->fetch_assoc()['total_visits'] : 0;

// Query to fetch today's total earnings from visits
$queryTodaysEarnings = "SELECT SUM(fees) AS todays_earnings FROM visits WHERE DATE(visit_date) = '$today'";
$resultTodaysEarnings = $conn->query($queryTodaysEarnings);
$todaysEarnings = ($resultTodaysEarnings->num_rows > 0) ? $resultTodaysEarnings->fetch_assoc()['todays_earnings'] : 0.00;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="index-body">
    <!-- Include the header/navbar here -->
    <?php include "header.php"; ?>

    <div class="welcome container-fix">
        <h3>Welcome, <span class="d-name"><?php echo$_SESSION['name'] ?></span></h3>
        <p>Have a nice day at work</p>
    </div>
    <div class="container-fix dashboard-content">
        <div class="row">
            <div class="col-12 col-md-4">
                <div class="dash-item">
                    <h5>Today's New Patients</h5>
                    <h3 class="ms-2 mt-1"><?php echo $newPatientsToday ?></h3>
                    <hr>
                    <p><span class="green"><?php echo $totalPatients ?></span> Total Patients</p>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="dash-item">
                    <h5>Today's New Visits</h5>
                    <h3 class="ms-2 mt-1"><?php echo $newVisitsToday ?></h3>
                    <hr>
                    <p><span class="green"><?php echo $totalVisits ?></span> Total Visits</p>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="dash-item">
                    <h5>Today's Earning</h5>
                    <h3 class="ms-2 mt-1">₹ <?php echo $todaysEarnings ?></h3>
                    <hr>
                    <p><span class="green">20%</span> than yesterday</p>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fix index-patients">
        <div class="d-flex justify-content-between">
            <h5>Patients List</h5>
            <div>
                <a class="show-all" href="patients.php">Show All</a>
                <a class=" add-btn ms-2" href="newRecord.php">
                        <i class="fas fa-plus"></i>
                    </a>
            </div>
        </div>
        <table class="table table-hover mt-2">
            <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Name</th>
                    <th scope="col">Mobile</th>
                     <th scope="col" class="hide">Age</th> 
                     <th scope="col" class="hide">Address</th> 
                    <th scope="col" class="hide">Last Visit</th>
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
ORDER BY p.id DESC
LIMIT 5"; // Newest first
                $result = mysqli_query($conn, $sql);

                // Check if there are results
                if (mysqli_num_rows($result) > 0) {
                    // Fetch and display each row of data
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr class='' onclick=\"window.location.href='patientDetails.php?id=" . $row["id"] . "'\" style='cursor:pointer;'>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["name"] . "</td>";
                        echo "<td class='primary-color'>" . $row["contact"] . "</td>";
                        echo "<td class='hide'>" . $row["age"] . "</td>";
                        echo "<td class='hide'>" . $row["address"] . "</td>";
                        echo "<td class='hide'>" . ($row["last_visit"] ? $row["last_visit"] : 'N/A') . "</td>"; // Last visit date
                        echo "<td><a href='visitRecord.php?id=" . $row["id"] . "' class='btn custom-btn'>New Visit</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>No patients found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JS -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>