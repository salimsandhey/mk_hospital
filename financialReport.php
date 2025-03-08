<?php
include 'auth.php';
include "dbConnect.php"; // Include the database connection

// Date range filter
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Get total revenue in date range
$revenueQuery = "SELECT SUM(fees) as total_revenue FROM visits WHERE visit_date BETWEEN '$startDate' AND '$endDate'";
$revenueResult = mysqli_query($conn, $revenueQuery);
$totalRevenue = mysqli_fetch_assoc($revenueResult)['total_revenue'] ?: 0;

// Get average revenue per visit
$avgRevenueQuery = "SELECT AVG(fees) as avg_revenue FROM visits WHERE visit_date BETWEEN '$startDate' AND '$endDate'";
$avgRevenueResult = mysqli_query($conn, $avgRevenueQuery);
$avgRevenue = mysqli_fetch_assoc($avgRevenueResult)['avg_revenue'] ?: 0;

// Get daily revenue for the selected period
$dailyRevenueQuery = "SELECT visit_date, SUM(fees) as daily_revenue, COUNT(*) as visit_count 
                     FROM visits 
                     WHERE visit_date BETWEEN '$startDate' AND '$endDate' 
                     GROUP BY visit_date 
                     ORDER BY visit_date";
$dailyRevenueResult = mysqli_query($conn, $dailyRevenueQuery);
$dailyRevenue = [];
while ($row = mysqli_fetch_assoc($dailyRevenueResult)) {
    $dailyRevenue[] = $row;
}

// Get monthly revenue for the last 12 months
$monthlyRevenueQuery = "SELECT DATE_FORMAT(visit_date, '%Y-%m') as month, 
                        SUM(fees) as monthly_revenue, 
                        COUNT(*) as visit_count 
                        FROM visits 
                        WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH) 
                        GROUP BY month 
                        ORDER BY month";
$monthlyRevenueResult = mysqli_query($conn, $monthlyRevenueQuery);
$monthlyRevenue = [];
while ($row = mysqli_fetch_assoc($monthlyRevenueResult)) {
    $monthlyRevenue[] = $row;
}

// Get revenue by treatment type
$treatmentRevenueQuery = "SELECT treatment_options, SUM(fees) as revenue, COUNT(*) as count 
                         FROM visits 
                         WHERE visit_date BETWEEN '$startDate' AND '$endDate' 
                         GROUP BY treatment_options 
                         ORDER BY revenue DESC";
$treatmentRevenueResult = mysqli_query($conn, $treatmentRevenueQuery);

// Calculate year-over-year growth
$currentYearQuery = "SELECT SUM(fees) as current_year_revenue 
                    FROM visits 
                    WHERE visit_date BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 YEAR) AND CURDATE()";
$currentYearResult = mysqli_query($conn, $currentYearQuery);
$currentYearRevenue = mysqli_fetch_assoc($currentYearResult)['current_year_revenue'] ?: 0;

$previousYearQuery = "SELECT SUM(fees) as previous_year_revenue 
                     FROM visits 
                     WHERE visit_date BETWEEN DATE_SUB(CURDATE(), INTERVAL 2 YEAR) AND DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
$previousYearResult = mysqli_query($conn, $previousYearQuery);
$previousYearRevenue = mysqli_fetch_assoc($previousYearResult)['previous_year_revenue'] ?: 1; // Avoid division by zero

$yoyGrowth = (($currentYearRevenue - $previousYearRevenue) / $previousYearRevenue) * 100;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Report</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .report-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
            background-color: #fff;
        }
        
        .metric-value {
            font-size: 2rem;
            font-weight: bold;
            color: #343c92;
        }
        
        .metric-label {
            color: #6c757d;
            font-size: 1rem;
        }
        
        .chart-container {
            height: 300px;
            margin-bottom: 30px;
        }
        
        .date-filter {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .table-responsive {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .growth-positive {
            color: #28a745;
        }
        
        .growth-negative {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Financial Report</h2>
            <a href="misDashboard.php" class="btn custom-btn">Back to Dashboard</a>
        </div>
        
        <!-- Date Range Filter -->
        <div class="date-filter">
            <form method="GET" action="" class="row g-3">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $startDate; ?>">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $endDate; ?>">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn custom-btn">Apply Filter</button>
                </div>
            </form>
        </div>
        
        <!-- Key Financial Metrics -->
        <div class="row">
            <div class="col-md-3">
                <div class="report-card">
                    <div class="metric-value">₹<?php echo number_format($totalRevenue); ?></div>
                    <div class="metric-label">Total Revenue</div>
                    <div class="small">Selected Period</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="report-card">
                    <div class="metric-value">₹<?php echo number_format($avgRevenue, 2); ?></div>
                    <div class="metric-label">Average Revenue per Visit</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="report-card">
                    <div class="metric-value"><?php echo count($dailyRevenue); ?></div>
                    <div class="metric-label">Active Days</div>
                    <div class="small">Days with at least one visit</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="report-card">
                    <div class="metric-value <?php echo $yoyGrowth >= 0 ? 'growth-positive' : 'growth-negative'; ?>">
                        <?php echo number_format($yoyGrowth, 2); ?>%
                    </div>
                    <div class="metric-label">Year-over-Year Growth</div>
                </div>
            </div>
        </div>
        
        <!-- Revenue Charts -->
        <div class="row">
            <div class="col-md-6">
                <div class="report-card">
                    <h5>Daily Revenue (Selected Period)</h5>
                    <div class="chart-container">
                        <canvas id="dailyRevenueChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="report-card">
                    <h5>Monthly Revenue (Last 12 Months)</h5>
                    <div class="chart-container">
                        <canvas id="monthlyRevenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Revenue by Treatment Type -->
        <div class="row">
            <div class="col-12">
                <div class="report-card">
                    <h5>Revenue by Treatment Type</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Treatment Type</th>
                                    <th>Number of Visits</th>
                                    <th>Total Revenue</th>
                                    <th>Average Revenue per Visit</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($treatment = mysqli_fetch_assoc($treatmentRevenueResult)) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($treatment['treatment_options'] ?: 'No Treatment Specified'); ?></td>
                                        <td><?php echo $treatment['count']; ?></td>
                                        <td>₹<?php echo number_format($treatment['revenue']); ?></td>
                                        <td>₹<?php echo number_format($treatment['revenue'] / $treatment['count'], 2); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Revenue Breakdown Table -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="report-card">
                    <h5>Daily Revenue Breakdown</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Number of Visits</th>
                                    <th>Total Revenue</th>
                                    <th>Average Revenue per Visit</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dailyRevenue as $day) { ?>
                                    <tr>
                                        <td><?php echo date('d M Y', strtotime($day['visit_date'])); ?></td>
                                        <td><?php echo $day['visit_count']; ?></td>
                                        <td>₹<?php echo number_format($day['daily_revenue']); ?></td>
                                        <td>₹<?php echo number_format($day['daily_revenue'] / $day['visit_count'], 2); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        // Daily Revenue Chart
        const dailyRevenueCtx = document.getElementById('dailyRevenueChart').getContext('2d');
        const dailyRevenueChart = new Chart(dailyRevenueCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_map(function($day) { 
                    return date('d M', strtotime($day['visit_date'])); 
                }, $dailyRevenue)); ?>,
                datasets: [{
                    label: 'Daily Revenue (₹)',
                    data: <?php echo json_encode(array_column($dailyRevenue, 'daily_revenue')); ?>,
                    backgroundColor: 'rgba(40, 167, 69, 0.2)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 2,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // Monthly Revenue Chart
        const monthlyRevenueCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
        const monthlyRevenueChart = new Chart(monthlyRevenueCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_map(function($month) {
                    return date('M Y', strtotime($month['month'] . '-01'));
                }, $monthlyRevenue)); ?>,
                datasets: [{
                    label: 'Monthly Revenue (₹)',
                    data: <?php echo json_encode(array_column($monthlyRevenue, 'monthly_revenue')); ?>,
                    backgroundColor: 'rgba(52, 60, 146, 0.7)',
                    borderColor: 'rgba(52, 60, 146, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html> 