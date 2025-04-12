<?php
include 'auth.php';
include "dbConnect.php"; // Include the database connection

// Require super admin access for this page
requireSuperAdmin();

// Date range filter
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Get total patients
$patientQuery = "SELECT COUNT(*) as total_patients FROM patient";
$patientResult = mysqli_query($conn, $patientQuery);
$totalPatients = mysqli_fetch_assoc($patientResult)['total_patients'];

// Get new patients in date range
$newPatientsQuery = "SELECT COUNT(*) as new_patients FROM patient WHERE registration_date BETWEEN '$startDate' AND '$endDate'";
$newPatientsResult = mysqli_query($conn, $newPatientsQuery);
$newPatients = mysqli_fetch_assoc($newPatientsResult)['new_patients'];

// Get total visits
$visitsQuery = "SELECT COUNT(*) as total_visits FROM visits";
$visitsResult = mysqli_query($conn, $visitsQuery);
$totalVisits = mysqli_fetch_assoc($visitsResult)['total_visits'];

// Get visits in date range
$rangeVisitsQuery = "SELECT COUNT(*) as range_visits FROM visits WHERE visit_date BETWEEN '$startDate' AND '$endDate'";
$rangeVisitsResult = mysqli_query($conn, $rangeVisitsQuery);
$rangeVisits = mysqli_fetch_assoc($rangeVisitsResult)['range_visits'];

// Get total revenue
$revenueQuery = "SELECT SUM(fees) as total_revenue FROM visits";
$revenueResult = mysqli_query($conn, $revenueQuery);
$totalRevenue = mysqli_fetch_assoc($revenueResult)['total_revenue'];

// Get revenue in date range
$rangeRevenueQuery = "SELECT SUM(fees) as range_revenue FROM visits WHERE visit_date BETWEEN '$startDate' AND '$endDate'";
$rangeRevenueResult = mysqli_query($conn, $rangeRevenueQuery);
$rangeRevenue = mysqli_fetch_assoc($rangeRevenueResult)['range_revenue'];

// Get most common treatments
$treatmentsQuery = "SELECT treatment_options, COUNT(*) as count FROM visits 
                   WHERE visit_date BETWEEN '$startDate' AND '$endDate' 
                   GROUP BY treatment_options 
                   ORDER BY count DESC 
                   LIMIT 5";
$treatmentsResult = mysqli_query($conn, $treatmentsQuery);

// Get most prescribed medicines
$medicinesQuery = "SELECT medicines, COUNT(*) as count FROM visits 
                  WHERE visit_date BETWEEN '$startDate' AND '$endDate' 
                  GROUP BY medicines 
                  ORDER BY count DESC 
                  LIMIT 10";
$medicinesResult = mysqli_query($conn, $medicinesQuery);

// Get monthly visit counts for the chart
$monthlyVisitsQuery = "SELECT DATE_FORMAT(visit_date, '%Y-%m') as month, COUNT(*) as visit_count 
                      FROM visits 
                      WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH) 
                      GROUP BY month 
                      ORDER BY month";
$monthlyVisitsResult = mysqli_query($conn, $monthlyVisitsQuery);
$monthlyVisits = [];
while ($row = mysqli_fetch_assoc($monthlyVisitsResult)) {
    $monthlyVisits[] = $row;
}

// Get monthly revenue for the chart
$monthlyRevenueQuery = "SELECT DATE_FORMAT(visit_date, '%Y-%m') as month, SUM(fees) as revenue 
                       FROM visits 
                       WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH) 
                       GROUP BY month 
                       ORDER BY month";
$monthlyRevenueResult = mysqli_query($conn, $monthlyRevenueQuery);
$monthlyRevenue = [];
while ($row = mysqli_fetch_assoc($monthlyRevenueResult)) {
    $monthlyRevenue[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MIS Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .dashboard-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
            background-color: #fff;
            transition: transform 0.3s ease;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
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
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Management Information System Dashboard</h2>
            <a href="superadmin_logout.php" class="btn btn-danger">Logout Super Admin</a>
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
        
        <!-- Key Metrics -->
        <div class="row">
            <div class="col-md-3">
                <div class="dashboard-card">
                    <div class="metric-value"><?php echo number_format($totalPatients); ?></div>
                    <div class="metric-label">Total Patients</div>
                    <div class="small text-success">+<?php echo $newPatients; ?> new in selected period</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card">
                    <div class="metric-value"><?php echo number_format($totalVisits); ?></div>
                    <div class="metric-label">Total Visits</div>
                    <div class="small text-success"><?php echo $rangeVisits; ?> in selected period</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card">
                    <div class="metric-value">₹<?php echo number_format($totalRevenue); ?></div>
                    <div class="metric-label">Total Revenue</div>
                    <div class="small text-success">₹<?php echo number_format($rangeRevenue); ?> in selected period</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card">
                    <div class="metric-value">₹<?php echo $totalVisits > 0 ? number_format($totalRevenue / $totalVisits, 2) : 0; ?></div>
                    <div class="metric-label">Average Fee per Visit</div>
                </div>
            </div>
        </div>
        
        <!-- Charts -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="dashboard-card">
                    <h5>Monthly Visits (Last 12 Months)</h5>
                    <div class="chart-container">
                        <canvas id="visitsChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="dashboard-card">
                    <h5>Monthly Revenue (Last 12 Months)</h5>
                    <div class="chart-container">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Treatment and Medicine Analysis -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="dashboard-card">
                    <h5>Most Common Treatments</h5>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Treatment</th>
                                <th>Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($treatment = mysqli_fetch_assoc($treatmentsResult)) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($treatment['treatment_options']); ?></td>
                                    <td><?php echo $treatment['count']; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-6">
                <div class="dashboard-card">
                    <h5>Most Prescribed Medicines</h5>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Medicine</th>
                                <th>Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($medicine = mysqli_fetch_assoc($medicinesResult)) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($medicine['medicines']); ?></td>
                                    <td><?php echo $medicine['count']; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Quick Links -->
        <div class="row mt-4 mb-5">
            <div class="col-12">
                <div class="dashboard-card">
                    <h5>Quick Reports</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="patientReport.php" class="btn custom-btn">Patient Report</a>
                        <a href="visitReport.php" class="btn custom-btn">Visit Report</a>
                        <a href="medicineReport.php" class="btn custom-btn">Medicine Report</a>
                        <a href="financialReport.php" class="btn custom-btn">Financial Report</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End of container -->
    
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Visits Chart
        const visitsCtx = document.getElementById('visitsChart').getContext('2d');
        const visitsChart = new Chart(visitsCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($monthlyVisits, 'month')); ?>,
                datasets: [{
                    label: 'Visits',
                    data: [<?php echo implode(', ', array_map(function($item) { return $item['visit_count']; }, $monthlyVisits)); ?>],
                    borderColor: '#343c92',
                    backgroundColor: 'rgba(52, 60, 146, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
        
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: [<?php echo implode(', ', array_map(function($item) { return "'" . date('M Y', strtotime($item['month'] . '-01')) . "'"; }, $monthlyRevenue)); ?>],
                datasets: [{
                    label: 'Revenue',
                    data: [<?php echo implode(', ', array_map(function($item) { return $item['revenue']; }, $monthlyRevenue)); ?>],
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₹' + value;
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
<?php ob_end_flush(); ?> 