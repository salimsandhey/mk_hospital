<?php
include 'auth.php';
include "dbConnect.php"; // Include the database connection

// Require super admin access for this page
requireSuperAdmin();

// Date range filter
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Get total visits
$totalVisitsQuery = "SELECT COUNT(*) as total_visits FROM visits";
$totalVisitsResult = mysqli_query($conn, $totalVisitsQuery);
$totalVisits = mysqli_fetch_assoc($totalVisitsResult)['total_visits'];

// Get visits in date range
$rangeVisitsQuery = "SELECT COUNT(*) as range_visits FROM visits WHERE visit_date BETWEEN '$startDate' AND '$endDate'";
$rangeVisitsResult = mysqli_query($conn, $rangeVisitsQuery);
$rangeVisits = mysqli_fetch_assoc($rangeVisitsResult)['range_visits'];

// Get average visits per day in date range
$daysDiff = (strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24) + 1;
$avgVisitsPerDay = $rangeVisits / $daysDiff;

// Get visits by day of week
$dayOfWeekQuery = "SELECT 
                    DAYNAME(visit_date) as day_name, 
                    COUNT(*) as visit_count
                  FROM visits 
                  WHERE visit_date BETWEEN '$startDate' AND '$endDate'
                  GROUP BY day_name
                  ORDER BY FIELD(day_name, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')";
$dayOfWeekResult = mysqli_query($conn, $dayOfWeekQuery);
$dayOfWeek = [];
while ($row = mysqli_fetch_assoc($dayOfWeekResult)) {
    $dayOfWeek[] = $row;
}

// Get most common treatments
$treatmentsQuery = "SELECT 
                    treatment_option, 
                    COUNT(*) as count
                  FROM (
                      SELECT 
                          id,
                          TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(treatment_options, ',', numbers.n), ',', -1)) as treatment_option
                      FROM 
                          visits
                      CROSS JOIN (
                          SELECT 1 as n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL 
                          SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL 
                          SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10
                      ) numbers
                      WHERE 
                          CHAR_LENGTH(treatment_options) - CHAR_LENGTH(REPLACE(treatment_options, ',', '')) >= numbers.n - 1
                          AND visit_date BETWEEN '$startDate' AND '$endDate'
                  ) t
                  GROUP BY treatment_option
                  ORDER BY count DESC
                  LIMIT 10";
$treatmentsResult = mysqli_query($conn, $treatmentsQuery);
$treatments = [];
while ($row = mysqli_fetch_assoc($treatmentsResult)) {
    $treatments[] = $row;
}

// Get visits with X-rays
$xrayQuery = "SELECT 
                COUNT(*) as xray_count,
                (SELECT COUNT(*) FROM visits WHERE visit_date BETWEEN '$startDate' AND '$endDate') as total_visits
              FROM visits 
              WHERE xray_taken = 1 
                AND visit_date BETWEEN '$startDate' AND '$endDate'";
$xrayResult = mysqli_query($conn, $xrayQuery);
$xrayData = mysqli_fetch_assoc($xrayResult);
$xrayPercentage = ($xrayData['xray_count'] / max(1, $xrayData['total_visits'])) * 100;

// Get visits over time
$visitsOverTimeQuery = "SELECT 
                        DATE_FORMAT(visit_date, '%Y-%m-%d') as date,
                        COUNT(*) as visit_count
                      FROM visits 
                      WHERE visit_date BETWEEN '$startDate' AND '$endDate'
                      GROUP BY date
                      ORDER BY date";
$visitsOverTimeResult = mysqli_query($conn, $visitsOverTimeQuery);
$visitsOverTime = [];
while ($row = mysqli_fetch_assoc($visitsOverTimeResult)) {
    $visitsOverTime[] = $row;
}

// Get recent visits
$recentVisitsQuery = "SELECT 
                        v.id, 
                        v.visit_date, 
                        p.name as patient_name,
                        p.contact as phone,
                        v.treatment,
                        v.fees
                      FROM visits v
                      JOIN patient p ON v.patient_id = p.id
                      WHERE v.visit_date BETWEEN '$startDate' AND '$endDate'
                      ORDER BY v.visit_date DESC
                      LIMIT 10";
$recentVisitsResult = mysqli_query($conn, $recentVisitsQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visit Report</title>
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
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Visit Report</h2>
            <div>
                <a href="misDashboard.php" class="btn custom-btn me-2">Back to Dashboard</a>
                <a href="superadmin_logout.php" class="btn btn-danger">Logout Super Admin</a>
            </div>
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
        
        <!-- Key Visit Metrics -->
        <div class="row">
            <div class="col-md-3">
                <div class="report-card">
                    <div class="metric-value"><?php echo number_format($totalVisits); ?></div>
                    <div class="metric-label">Total Visits</div>
                    <div class="small">All time</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="report-card">
                    <div class="metric-value"><?php echo number_format($rangeVisits); ?></div>
                    <div class="metric-label">Visits in Selected Period</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="report-card">
                    <div class="metric-value"><?php echo number_format($avgVisitsPerDay, 1); ?></div>
                    <div class="metric-label">Average Visits per Day</div>
                    <div class="small">In selected period</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="report-card">
                    <div class="metric-value"><?php echo number_format($xrayPercentage, 1); ?>%</div>
                    <div class="metric-label">Visits with X-rays</div>
                    <div class="small"><?php echo $xrayData['xray_count']; ?> out of <?php echo $xrayData['total_visits']; ?> visits</div>
                </div>
            </div>
        </div>
        
        <!-- Visit Charts -->
        <div class="row">
            <div class="col-md-6">
                <div class="report-card">
                    <h5>Visits by Day of Week</h5>
                    <div class="chart-container">
                        <canvas id="dayOfWeekChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="report-card">
                    <h5>Most Common Treatments</h5>
                    <div class="chart-container">
                        <canvas id="treatmentsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Visits Over Time -->
        <div class="row">
            <div class="col-12">
                <div class="report-card">
                    <h5>Visits Over Time</h5>
                    <div class="chart-container">
                        <canvas id="visitsOverTimeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Visits -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="report-card">
                    <h5>Recent Visits</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Patient Name</th>
                                    <th>Phone</th>
                                    <th>Treatment</th>
                                    <th>Fees</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($visit = mysqli_fetch_assoc($recentVisitsResult)) { ?>
                                    <tr>
                                        <td><?php echo date('d M Y', strtotime($visit['visit_date'])); ?></td>
                                        <td><?php echo htmlspecialchars($visit['patient_name']); ?></td>
                                        <td><?php echo htmlspecialchars($visit['phone']); ?></td>
                                        <td><?php echo substr(htmlspecialchars($visit['treatment']), 0, 50) . (strlen($visit['treatment']) > 50 ? '...' : ''); ?></td>
                                        <td>₹<?php echo number_format($visit['fees']); ?></td>
                                        <td>
                                            <a href="visitDetails.php?visit_id=<?php echo $visit['id']; ?>" class="btn btn-sm custom-btn">View</a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End container -->
    
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        // Day of Week Chart
        const dayOfWeekCtx = document.getElementById('dayOfWeekChart').getContext('2d');
        const dayOfWeekChart = new Chart(dayOfWeekCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($dayOfWeek, 'day_name')); ?>,
                datasets: [{
                    label: 'Number of Visits',
                    data: <?php echo json_encode(array_column($dayOfWeek, 'visit_count')); ?>,
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
        
        // Treatments Chart
        const treatmentsCtx = document.getElementById('treatmentsChart').getContext('2d');
        const treatmentsChart = new Chart(treatmentsCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_column($treatments, 'treatment_option')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($treatments, 'count')); ?>,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(255, 159, 64, 0.7)',
                        'rgba(199, 199, 199, 0.7)',
                        'rgba(83, 102, 255, 0.7)',
                        'rgba(40, 167, 69, 0.7)',
                        'rgba(220, 53, 69, 0.7)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
        
        // Visits Over Time Chart
        const visitsOverTimeCtx = document.getElementById('visitsOverTimeChart').getContext('2d');
        const visitsOverTimeChart = new Chart(visitsOverTimeCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_map(function($visit) {
                    return date('d M', strtotime($visit['date']));
                }, $visitsOverTime)); ?>,
                datasets: [{
                    label: 'Number of Visits',
                    data: <?php echo json_encode(array_column($visitsOverTime, 'visit_count')); ?>,
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
    </script>
</body>
</html>
<?php ob_end_flush(); ?> 