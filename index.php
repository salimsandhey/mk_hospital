<?php
// error_reporting(E_ALL);
// ini_set('display_errors',1);

include 'auth.php';
// $client_id = $_SESSION['client_id'];
?>
<?php
include "dbConnect.php"; // Include your database connection
include 'addPatient.php';
// Get today's date
$today = date('Y-m-d');
$yesterday = date("Y-m-d", strtotime("-1 day"));


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

$queryYesterdaysEarnings = "SELECT SUM(fees) AS yesterdays_earnings FROM visits WHERE DATE(visit_date) = '$yesterday'";
$resultYesterdaysEarnings = $conn->query($queryYesterdaysEarnings);
$yesterdaysEarnings = ($resultYesterdaysEarnings->num_rows > 0) ? $resultYesterdaysEarnings->fetch_assoc()['yesterdays_earnings'] : 0.00;

if ($yesterdaysEarnings > 0) {
    $percentageChange = (($todaysEarnings - $yesterdaysEarnings) / $yesterdaysEarnings) * 100;
    $icon = ($percentageChange > 0) ? '<i class="fas fa-arrow-up"></i>' : '<i class="fas fa-arrow-down"></i>';
    $colorClass = ($percentageChange > 0) ? 'text-success' : 'text-danger';
} else {
    $percentageChange = 0;
    $icon = ''; // No icon if there's no previous day data
    $colorClass = 'text-muted';
}
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="index-body">
    <!-- Include the header/navbar here -->
    <?php include "header.php"; ?>

    <div class="welcome container-fix d-flex justify-content-between">
        <div>
            <h3>Welcome, <span class="d-name"><?php 
                if (isset($_SESSION['name']) && $_SESSION['name'] !== '' && $_SESSION['name'] !== 'demo') {
                    echo $_SESSION['name'];
                } elseif (isset($_SESSION['username'])) {
                    echo $_SESSION['username'];
                } else {
                    echo 'User';
                }
            ?></span></h3>
            <p>Have a nice day at work</p>
        </div>
    </div>
    <div class="container-fix dashboard-content">
        <div class="row">
            <div class="col-12 col-md-4">
                <div class="dash-item">
                    <h5><a href="todayPatients.php" class="link">Today's New Patients</a></h5>
                    <h3 class="ms-2 mt-1"><?php echo $newPatientsToday ?></h3>
                    <hr>
                    <p><span class="green"><?php echo $totalPatients ?></span> Total Patients</p>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="dash-item">
                    <h5><a href="todayVisits.php" class="link">Today's New Visits</a></h5>
                    <h3 class="ms-2 mt-1"><?php echo $newVisitsToday ?></h3>
                    <hr>
                    <p><span class="green"><?php echo $totalVisits ?></span> Total Visits </p>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="dash-item">
                    <h5 class="link">Today's Earning
                        <button id="toggleEarningsBtn" class="btn btn-link p-0" onclick="toggleEarnings()">
                            <i class="fa fa-eye" id="toggleIcon"></i>
                        </button>
                    </h5>
                    <h3 class="ms-2 mt-1" id="earningsAmount">***</h3>
                    <hr>
                    <p>
                        <span id="percentageChange" class="<?php echo $colorClass; ?> bold">
                            ***
                        </span> than yesterday
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fix index-patients">
        <div class="d-flex justify-content-between">
            <h5>Patients List</h5>
            <div>
                <a class="show-all" href="patients.php">Show All</a>
                <a class="add-btn ms-2" data-bs-toggle="modal" data-bs-target="#addPatientModal">
                    <i class="fas fa-plus"></i>
                </a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mt-2">
                <thead>
                    <tr>
                        <th scope="col">Id</th>
                        <th scope="col">Name</th>
                        <th scope="col">Mobile</th>
                        <th scope="col">Age</th>
                        <th scope="col">Address</th>
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
                            echo "<td>" . $row["age"] . "</td>";
                            echo "<td>" . $row["address"] . "</td>";
                            echo "<td>" . ($row["last_visit"] ? $row["last_visit"] : 'N/A') . "</td>"; // Last visit date
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
    </div>
    <script>
        let isEarningsVisible = false;

        function toggleEarnings() {
            const earningsAmount = document.getElementById('earningsAmount');
            const toggleIcon = document.getElementById('toggleIcon');
            const percentageChange = document.getElementById('percentageChange');

            if (isEarningsVisible) {
                earningsAmount.innerText = '***'; // Hide earnings
                percentageChange.innerText = '***'; // Hide percentage
                toggleIcon.classList.replace('fa-eye-slash', 'fa-eye');
            } else {
                earningsAmount.innerText = '₹ <?php echo number_format($todaysEarnings, 2); ?>'; // Show earnings
                percentageChange.innerHTML = '<?php echo abs(round($percentageChange, 2)); ?>% <?php echo $icon; ?>'; // Show percentage
                toggleIcon.classList.replace('fa-eye', 'fa-eye-slash');
            }
            isEarningsVisible = !isEarningsVisible;
        }    </script>

    <!-- Bootstrap JS -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>