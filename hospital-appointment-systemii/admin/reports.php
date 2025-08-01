<?php
include_once 'auth_check.php';
include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get monthly appointment statistics
$monthly_query = "SELECT 
    DATE_FORMAT(appointment_date, '%Y-%m') as month,
    COUNT(*) as total_appointments,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
    FROM appointments 
    WHERE appointment_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(appointment_date, '%Y-%m')
    ORDER BY month DESC";

$monthly_stmt = $db->prepare($monthly_query);
$monthly_stmt->execute();
$monthly_stats = $monthly_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get doctor performance
$doctor_query = "SELECT 
    d.name,
    d.specialization,
    COUNT(a.id) as total_appointments,
    SUM(CASE WHEN a.status = 'completed' THEN 1 ELSE 0 END) as completed_appointments,
    AVG(d.consultation_fee) as avg_fee
    FROM doctors d
    LEFT JOIN appointments a ON d.id = a.doctor_id
    WHERE d.status = 'active'
    GROUP BY d.id
    ORDER BY total_appointments DESC";

$doctor_stmt = $db->prepare($doctor_query);
$doctor_stmt->execute();
$doctor_stats = $doctor_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - HealthCare Plus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f8fafc; }
        .sidebar {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            min-height: 100vh;
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 15px 20px;
            border-radius: 10px;
            margin: 5px 10px;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar">
                    <div class="p-4">
                        <h4><i class="fas fa-heartbeat me-2"></i>HealthCare Plus</h4>
                        <p class="small mb-0">Admin Panel</p>
                    </div>
                    <nav class="nav flex-column">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link" href="appointments.php">
                            <i class="fas fa-calendar-alt me-2"></i>Appointments
                        </a>
                        <a class="nav-link" href="doctors.php">
                            <i class="fas fa-user-md me-2"></i>Doctors
                        </a>
                        <a class="nav-link" href="patients.php">
                            <i class="fas fa-users me-2"></i>Patients
                        </a>
                        <a class="nav-link" href="departments.php">
                            <i class="fas fa-building me-2"></i>Departments
                        </a>
                        <a class="nav-link active" href="reports.php">
                            <i class="fas fa-chart-bar me-2"></i>Reports
                        </a>
                        <hr class="mx-3 my-3" style="border-color: rgba(255,255,255,0.2);">
                        <a class="nav-link" href="../index.php" target="_blank">
                            <i class="fas fa-external-link-alt me-2"></i>View Website
                        </a>
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="p-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2><i class="fas fa-chart-bar me-2 text-primary"></i>Reports & Analytics</h2>
                        <div>
                            <button class="btn btn-outline-primary">
                                <i class="fas fa-download me-2"></i>Export Report
                            </button>
                        </div>
                    </div>

                    <!-- Monthly Statistics -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0"><i class="fas fa-calendar-alt me-2 text-primary"></i>Monthly Appointment Statistics</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th>Month</th>
                                                    <th>Total Appointments</th>
                                                    <th>Completed</th>
                                                    <th>Cancelled</th>
                                                    <th>Success Rate</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($monthly_stats as $stat): ?>
                                                <tr>
                                                    <td><?php echo date('F Y', strtotime($stat['month'] . '-01')); ?></td>
                                                    <td><?php echo $stat['total_appointments']; ?></td>
                                                    <td>
                                                        <span class="badge bg-success"><?php echo $stat['completed']; ?></span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-danger"><?php echo $stat['cancelled']; ?></span>
                                                    </td>
                                                    <td>
                                                        <?php 
                                                        $success_rate = $stat['total_appointments'] > 0 ? 
                                                            round(($stat['completed'] / $stat['total_appointments']) * 100, 1) : 0;
                                                        ?>
                                                        <div class="progress" style="height: 20px;">
                                                            <div class="progress-bar bg-success" style="width: <?php echo $success_rate; ?>%">
                                                                <?php echo $success_rate; ?>%
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Doctor Performance -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0"><i class="fas fa-user-md me-2 text-primary"></i>Doctor Performance</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th>Doctor</th>
                                                    <th>Specialization</th>
                                                    <th>Total Appointments</th>
                                                    <th>Completed</th>
                                                    <th>Average Fee</th>
                                                    <th>Performance</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($doctor_stats as $doc): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($doc['name']); ?></strong>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($doc['specialization']); ?></td>
                                                    <td>
                                                        <span class="badge bg-primary"><?php echo $doc['total_appointments']; ?></span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-success"><?php echo $doc['completed_appointments']; ?></span>
                                                    </td>
                                                    <td>$<?php echo number_format($doc['avg_fee'], 2); ?></td>
                                                    <td>
                                                        <?php 
                                                        $performance = $doc['total_appointments'] > 0 ? 
                                                            round(($doc['completed_appointments'] / $doc['total_appointments']) * 100, 1) : 0;
                                                        ?>
                                                        <div class="progress" style="height: 20px;">
                                                            <div class="progress-bar bg-<?php echo $performance >= 80 ? 'success' : ($performance >= 60 ? 'warning' : 'danger'); ?>" 
                                                                 style="width: <?php echo $performance; ?>%">
                                                                <?php echo $performance; ?>%
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
