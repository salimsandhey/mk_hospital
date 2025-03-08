<?php
include 'auth.php';
include "dbConnect.php"; // Include the database connection

// Date range filter
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Get patient demographics
$ageGroupQuery = "SELECT 
                    CASE 
                        WHEN age < 18 THEN 'Under 18'
                        WHEN age BETWEEN 18 AND 30 THEN '18-30'
                        WHEN age BETWEEN 31 AND 45 THEN '31-45'
                        WHEN age BETWEEN 46 AND 60 THEN '46-60'
                        ELSE 'Over 60'
                    END AS age_group,
                    COUNT(*) as count
                  FROM patient
                  GROUP BY age_group
                  ORDER BY FIELD(age_group, 'Under 18', '18-30', '31-45', '46-60', 'Over 60')";
$ageGroupResult = mysqli_query($conn, $ageGroupQuery);
$ageGroups = [];
while ($row = mysqli_fetch_assoc($ageGroupResult)) {
    $ageGroups[] = $row;
}

// Get gender distribution
$genderQuery = "SELECT gender, COUNT(*) as count FROM patient GROUP BY gender";
$genderResult = mysqli_query($conn, $genderQuery);
$genders = [];
while ($row = mysqli_fetch_assoc($genderResult)) {
    $genders[] = $row;
}

// Get new patients over time
$newPatientsQuery = "SELECT DATE_FORMAT(registration_date, '%Y-%m') as month, COUNT(*) as count 
                    FROM patient 
                    WHERE registration_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH) 
                    GROUP BY month 
                    ORDER BY month";
$newPatientsResult = mysqli_query($conn, $newPatientsQuery);
$newPatients = [];
while ($row = mysqli_fetch_assoc($newPatientsResult)) {
    $newPatients[] = $row;
}

// Get patients with most visits
$frequentPatientsQuery = "SELECT p.id, p.name, p.contact as phone, COUNT(v.id) as visit_count 
                         FROM patient p
                         JOIN visits v ON p.id = v.patient_id
                         WHERE v.visit_date BETWEEN '$startDate' AND '$endDate'
                         GROUP BY p.id
                         ORDER BY visit_count DESC
                         LIMIT 10";
$frequentPatientsResult = mysqli_query($conn, $frequentPatientsQuery);

// Get patients with highest spending
$highSpendingPatientsQuery = "SELECT p.id, p.name, p.contact as phone, SUM(v.fees) as total_spent 
                             FROM patient p
                             JOIN visits v ON p.id = v.patient_id
                             WHERE v.visit_date BETWEEN '$startDate' AND '$endDate'
                             GROUP BY p.id
                             ORDER BY total_spent DESC
                             LIMIT 10";
$highSpendingPatientsResult = mysqli_query($conn, $highSpendingPatientsQuery);

// Get patients who haven't visited in a long time
$inactivePatientsQuery = "SELECT p.id, p.name, p.contact as phone, MAX(v.visit_date) as last_visit,
                         DATEDIFF(CURDATE(), MAX(v.visit_date)) as days_since_last_visit
                         FROM patient p
                         JOIN visits v ON p.id = v.patient_id
                         GROUP BY p.id
                         HAVING days_since_last_visit > 180
                         ORDER BY days_since_last_visit DESC
                         LIMIT 20";
$inactivePatientsResult = mysqli_query($conn, $inactivePatientsQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Report</title>
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
            <h2>Patient Report</h2>
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
        
        <!-- Demographics Charts -->
        <div class="row">
            <div class="col-md-6">
                <div class="report-card">
                    <h5>Patient Age Distribution</h5>
                    <div class="chart-container">
                        <canvas id="ageChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="report-card">
                    <h5>Gender Distribution</h5>
                    <div class="chart-container">
                        <canvas id="genderChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- New Patients Over Time -->
        <div class="row">
            <div class="col-12">
                <div class="report-card">
                    <h5>New Patient Registrations (Last 12 Months)</h5>
                    <div class="chart-container">
                        <canvas id="newPatientsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Frequent Patients -->
        <div class="row">
            <div class="col-md-6">
                <div class="report-card">
                    <h5>Most Frequent Patients</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Patient Name</th>
                                    <th>Phone</th>
                                    <th>Visit Count</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($patient = mysqli_fetch_assoc($frequentPatientsResult)) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($patient['name']); ?></td>
                                        <td><?php echo htmlspecialchars($patient['phone']); ?></td>
                                        <td><?php echo $patient['visit_count']; ?></td>
                                        <td>
                                            <a href="patientDetails.php?id=<?php echo $patient['id']; ?>" class="btn btn-sm custom-btn">View</a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- High Spending Patients -->
            <div class="col-md-6">
                <div class="report-card">
                    <h5>Highest Spending Patients</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Patient Name</th>
                                    <th>Phone</th>
                                    <th>Total Spent</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($patient = mysqli_fetch_assoc($highSpendingPatientsResult)) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($patient['name']); ?></td>
                                        <td><?php echo htmlspecialchars($patient['phone']); ?></td>
                                        <td>₹<?php echo number_format($patient['total_spent']); ?></td>
                                        <td>
                                            <a href="patientDetails.php?id=<?php echo $patient['id']; ?>" class="btn btn-sm custom-btn">View</a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Inactive Patients -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="report-card">
                    <h5>Inactive Patients (No Visit in Last 6 Months)</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Patient Name</th>
                                    <th>Phone</th>
                                    <th>Last Visit</th>
                                    <th>Days Since Last Visit</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($patient = mysqli_fetch_assoc($inactivePatientsResult)) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($patient['name']); ?></td>
                                        <td><?php echo htmlspecialchars($patient['phone']); ?></td>
                                        <td><?php echo date('d M Y', strtotime($patient['last_visit'])); ?></td>
                                        <td><?php echo $patient['days_since_last_visit']; ?></td>
                                        <td>
                                            <a href="patientDetails.php?id=<?php echo $patient['id']; ?>" class="btn btn-sm custom-btn">View</a>
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

    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        // Age Distribution Chart
        const ageCtx = document.getElementById('ageChart').getContext('2d');
        const ageChart = new Chart(ageCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_column($ageGroups, 'age_group')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($ageGroups, 'count')); ?>,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)'
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
        
        // Gender Distribution Chart
        const genderCtx = document.getElementById('genderChart').getContext('2d');
        const genderChart = new Chart(genderCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_column($genders, 'gender')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($genders, 'count')); ?>,
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(255, 206, 86, 0.7)'
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
        
        // New Patients Chart
        const newPatientsCtx = document.getElementById('newPatientsChart').getContext('2d');
        const newPatientsChart = new Chart(newPatientsCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($newPatients, 'month')); ?>,
                datasets: [{
                    label: 'New Patients',
                    data: <?php echo json_encode(array_column($newPatients, 'count')); ?>,
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