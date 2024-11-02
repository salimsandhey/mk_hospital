<?php
include 'auth.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Report</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> -->
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <style>
        .bg-custom {
            background-color: #007BFF;
            /* Change to your desired primary color */
            color: white;
        }
    </style>
</head>

<body>
    <?php
    include 'header.php';
    ?>

    <div class="container mt-5">
        <h1 class="mb-4">Financial Report</h1>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card bg-custom">
                    <div class="card-header">
                        <h5 class="card-title">Daily Earnings</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        include "dbConnect.php";

                        // Fetch daily earnings for today
                        $today = date('Y-m-d');
                        $dailyEarningsQuery = "SELECT SUM(fees) AS dailyEarnings FROM visits WHERE DATE(visit_date) = '$today'";
                        $dailyEarningsResult = mysqli_query($conn, $dailyEarningsQuery);
                        $dailyEarningsRow = mysqli_fetch_assoc($dailyEarningsResult);
                        $dailyEarnings = $dailyEarningsRow['dailyEarnings'] ? $dailyEarningsRow['dailyEarnings'] : 0;
                        ?>
                        <h3 class="text-center">$<?php echo number_format($dailyEarnings, 2); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-custom">
                    <div class="card-header">
                        <h5 class="card-title">Monthly Earnings</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        // Fetch monthly earnings for the current month
                        $month = date('Y-m');
                        $monthlyEarningsQuery = "SELECT SUM(fees) AS monthlyEarnings FROM visits WHERE DATE(visit_date) >= '$month-01' AND DATE(visit_date) < DATE_ADD('$month-01', INTERVAL 1 MONTH)";
                        $monthlyEarningsResult = mysqli_query($conn, $monthlyEarningsQuery);
                        $monthlyEarningsRow = mysqli_fetch_assoc($monthlyEarningsResult);
                        $monthlyEarnings = $monthlyEarningsRow['monthlyEarnings'] ? $monthlyEarningsRow['monthlyEarnings'] : 0;
                        ?>
                        <h3 class="text-center">$<?php echo number_format($monthlyEarnings, 2); ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-custom">
                <h4>Full Earnings Report</h4>
            </div>
            <div class="card-body">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Type</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch all earnings from the visits table
                        $earningsQuery = "SELECT visit_date, treatment, fees FROM visits ORDER BY visit_date DESC";
                        $earningsResult = mysqli_query($conn, $earningsQuery);

                        while ($earning = mysqli_fetch_assoc($earningsResult)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars(date("d M Y", strtotime($earning['visit_date']))) . "</td>";
                            echo "<td>" . htmlspecialchars($earning['treatment']) . "</td>";
                            echo "<td>Consultation</td>"; // Assuming all entries are consultations; adjust as needed
                            echo "<td>$" . number_format($earning['fees'], 2) . "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script> -->
        <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>