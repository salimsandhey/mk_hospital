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
    <title>Monthly Financial Report</title>
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
        <h1 class="mb-4">Monthly Financial Report</h1>

        <?php
        include "dbConnect.php";

        // Get month and year from URL parameters
        $month = isset($_GET['month']) ? $_GET['month'] : date('m');
        $year = isset($_GET['year']) ? $_GET['year'] : date('Y');

        echo "<h3>Month: " . date("F", mktime(0, 0, 0, $month, 1)) . " $year</h3>";

        // Fetch daily earnings for the selected month
        $dailyEarningsQuery = "SELECT DATE(visit_date) AS visit_date, SUM(fees) AS dailyEarnings FROM visits WHERE DATE(visit_date) >= '$year-$month-01' AND DATE(visit_date) < DATE_ADD('$year-$month-01', INTERVAL 1 MONTH) GROUP BY DATE(visit_date) ORDER BY DATE(visit_date)";
        $dailyEarningsResult = mysqli_query($conn, $dailyEarningsQuery);

        if (mysqli_num_rows($dailyEarningsResult) > 0) {
            echo "<table class='table table-striped table-bordered'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th>Date</th>";
            echo "<th>Total Earnings</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";

            while ($dailyEarning = mysqli_fetch_assoc($dailyEarningsResult)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars(date("d M Y", strtotime($dailyEarning['visit_date']))) . "</td>";
                echo "<td>₹" . number_format($dailyEarning['dailyEarnings'], 2) . "</td>";
                echo "</tr>";
            }

            echo "</tbody>";
            echo "</table>";
        } else {
            echo "<p>No earnings recorded for this month.</p>";
        }
        ?>
    </div>

    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script> -->
        <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>