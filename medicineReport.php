<?php
include 'auth.php';
include "dbConnect.php"; // Include the database connection

// Require super admin access for this page
requireSuperAdmin();

// Date range filter
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Get total medicines in the database
$totalMedicinesQuery = "SELECT COUNT(*) as total_medicines FROM medicines";
$totalMedicinesResult = mysqli_query($conn, $totalMedicinesQuery);
$totalMedicines = mysqli_fetch_assoc($totalMedicinesResult)['total_medicines'];

// Get most prescribed medicines in the selected period
$topMedicinesQuery = "SELECT 
                        SUBSTRING_INDEX(SUBSTRING_INDEX(m.value, ' - ', 1), ', ', 1) as medicine_name,
                        COUNT(*) as prescription_count
                      FROM visits v
                      JOIN (
                          SELECT 
                              id,
                              TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(medicines, ', ', numbers.n), ', ', -1)) as value
                          FROM 
                              visits
                          CROSS JOIN (
                              SELECT 1 as n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL 
                              SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL 
                              SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10
                          ) numbers
                          WHERE 
                              CHAR_LENGTH(medicines) - CHAR_LENGTH(REPLACE(medicines, ',', '')) >= numbers.n - 1
                      ) m ON v.id = m.id
                      WHERE v.visit_date BETWEEN '$startDate' AND '$endDate'
                      GROUP BY medicine_name
                      ORDER BY prescription_count DESC
                      LIMIT 10";
$topMedicinesResult = mysqli_query($conn, $topMedicinesQuery);
$topMedicines = [];
while ($row = mysqli_fetch_assoc($topMedicinesResult)) {
    $topMedicines[] = $row;
}

// Get medicine timing distribution
$timingQuery = "SELECT 
                  SUBSTRING_INDEX(SUBSTRING_INDEX(m.value, 'Timing: ', -1), ',', 1) as timing,
                  COUNT(*) as count
                FROM visits v
                JOIN (
                    SELECT 
                        id,
                        TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(medicines, ', ', numbers.n), ', ', -1)) as value
                    FROM 
                        visits
                    CROSS JOIN (
                        SELECT 1 as n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL 
                        SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL 
                        SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10
                    ) numbers
                    WHERE 
                        CHAR_LENGTH(medicines) - CHAR_LENGTH(REPLACE(medicines, ',', '')) >= numbers.n - 1
                ) m ON v.id = m.id
                WHERE v.visit_date BETWEEN '$startDate' AND '$endDate'
                  AND m.value LIKE '%Timing:%'
                GROUP BY timing
                ORDER BY count DESC";
$timingResult = mysqli_query($conn, $timingQuery);
$timings = [];
while ($row = mysqli_fetch_assoc($timingResult)) {
    $timings[] = $row;
}

// Get medicine prescription trends over time
$trendQuery = "SELECT 
                DATE_FORMAT(v.visit_date, '%Y-%m') as month,
                COUNT(DISTINCT v.id) as total_prescriptions,
                COUNT(m.value) as total_medicines_prescribed
              FROM visits v
              LEFT JOIN (
                  SELECT 
                      id,
                      TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(medicines, ', ', numbers.n), ', ', -1)) as value
                  FROM 
                      visits
                  CROSS JOIN (
                      SELECT 1 as n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL 
                      SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL 
                      SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10
                  ) numbers
                  WHERE 
                      CHAR_LENGTH(medicines) - CHAR_LENGTH(REPLACE(medicines, ',', '')) >= numbers.n - 1
              ) m ON v.id = m.id
              WHERE v.visit_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
              GROUP BY month
              ORDER BY month";
$trendResult = mysqli_query($conn, $trendQuery);
$trends = [];
while ($row = mysqli_fetch_assoc($trendResult)) {
    $trends[] = $row;
}

// Get recently added medicines
$recentMedicinesQuery = "SELECT * FROM medicines ORDER BY id DESC LIMIT 10";
$recentMedicinesResult = mysqli_query($conn, $recentMedicinesQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicine Report</title>
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
            <h2>Medicine Report</h2>
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
        
        <!-- Key Medicine Metrics -->
        <div class="row">
            <div class="col-md-4">
                <div class="report-card">
                    <div class="metric-value"><?php echo number_format($totalMedicines); ?></div>
                    <div class="metric-label">Total Medicines</div>
                    <div class="small">In the database</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="report-card">
                    <div class="metric-value"><?php echo count($topMedicines) > 0 ? htmlspecialchars($topMedicines[0]['medicine_name']) : 'N/A'; ?></div>
                    <div class="metric-label">Most Prescribed Medicine</div>
                    <div class="small"><?php echo count($topMedicines) > 0 ? $topMedicines[0]['prescription_count'] . ' prescriptions' : ''; ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="report-card">
                    <div class="metric-value"><?php echo count($timings) > 0 ? htmlspecialchars($timings[0]['timing']) : 'N/A'; ?></div>
                    <div class="metric-label">Most Common Timing</div>
                    <div class="small"><?php echo count($timings) > 0 ? $timings[0]['count'] . ' prescriptions' : ''; ?></div>
                </div>
            </div>
        </div>
        
        <!-- Medicine Charts -->
        <div class="row">
            <div class="col-md-6">
                <div class="report-card">
                    <h5>Top 10 Prescribed Medicines</h5>
                    <div class="chart-container">
                        <canvas id="topMedicinesChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="report-card">
                    <h5>Medicine Timing Distribution</h5>
                    <div class="chart-container">
                        <canvas id="timingChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Medicine Prescription Trends -->
        <div class="row">
            <div class="col-12">
                <div class="report-card">
                    <h5>Medicine Prescription Trends (Last 12 Months)</h5>
                    <div class="chart-container">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recently Added Medicines -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="report-card">
                    <h5>Recently Added Medicines</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Medicine Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($medicine = mysqli_fetch_assoc($recentMedicinesResult)) { ?>
                                    <tr>
                                        <td><?php echo $medicine['id']; ?></td>
                                        <td><?php echo htmlspecialchars($medicine['name']); ?></td>
                                        <td>
                                            <a href="medicineDashboard.php" class="btn btn-sm custom-btn">View All</a>
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
        // Top Medicines Chart
        const topMedicinesCtx = document.getElementById('topMedicinesChart').getContext('2d');
        const topMedicinesChart = new Chart(topMedicinesCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($topMedicines, 'medicine_name')); ?>,
                datasets: [{
                    label: 'Prescription Count',
                    data: <?php echo json_encode(array_column($topMedicines, 'prescription_count')); ?>,
                    backgroundColor: 'rgba(52, 60, 146, 0.7)',
                    borderColor: 'rgba(52, 60, 146, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                scales: {
                    x: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // Timing Distribution Chart
        const timingCtx = document.getElementById('timingChart').getContext('2d');
        const timingChart = new Chart(timingCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_column($timings, 'timing')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($timings, 'count')); ?>,
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
        
        // Prescription Trends Chart
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        const trendChart = new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_map(function($trend) {
                    return date('M Y', strtotime($trend['month'] . '-01'));
                }, $trends)); ?>,
                datasets: [{
                    label: 'Total Prescriptions',
                    data: <?php echo json_encode(array_column($trends, 'total_prescriptions')); ?>,
                    backgroundColor: 'rgba(52, 60, 146, 0.2)',
                    borderColor: 'rgba(52, 60, 146, 1)',
                    borderWidth: 2,
                    tension: 0.1
                }, {
                    label: 'Total Medicines Prescribed',
                    data: <?php echo json_encode(array_column($trends, 'total_medicines_prescribed')); ?>,
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