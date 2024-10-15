<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yearly Financial Report</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> -->
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <style>
        .bg-custom {
            background-color: #007BFF; /* Change to your desired primary color */
            color: white;
        }
    </style>
</head>

<body>
    <?php include "header.php"; ?>

    <div class="container mt-5">
        <h1 class="mb-4">Yearly Financial Report</h1>

        <div class="row">
            <?php
            include "dbConnect.php";

            // Get current year
            $year = date('Y');

            // Array of month names
            $months = [
                '01' => 'January',
                '02' => 'February',
                '03' => 'March',
                '04' => 'April',
                '05' => 'May',
                '06' => 'June',
                '07' => 'July',
                '08' => 'August',
                '09' => 'September',
                '10' => 'October',
                '11' => 'November',
                '12' => 'December',
            ];

            foreach ($months as $month => $monthName) {
                // Calculate monthly earnings for each month
                $monthlyEarningsQuery = "SELECT SUM(fees) AS total FROM visits WHERE DATE(visit_date) >= '$year-$month-01' AND DATE(visit_date) < DATE_ADD('$year-$month-01', INTERVAL 1 MONTH)";
                $monthlyEarningsResult = mysqli_query($conn, $monthlyEarningsQuery);
                $monthlyEarningsRow = mysqli_fetch_assoc($monthlyEarningsResult);
                $monthlyEarnings = $monthlyEarningsRow['total'] ? $monthlyEarningsRow['total'] : 0;

                echo "<div class='col-md-4 mb-4'>";
                echo "<div class='card bg-custom'>";
                echo "<div class='card-body text-center'>";
                echo "<h5 class='card-title'>$monthName</h5>";
                echo "<h6 class='card-subtitle'>Total Earnings</h6>";
                echo "<h3>₹" . number_format($monthlyEarnings, 2) . "</h3>";
                echo "<a href='monthly_financials.php?month=$month&year=$year' class='btn btn-light'>View Details</a>";
                echo "</div>";
                echo "</div>";
                echo "</div>";
            }
            ?>
        </div>
    </div>

    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script> -->
        <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>
