<?php
include_once 'auth_check.php';
include_once '../config/database.php';
include_once '../classes/Appointment.php';
include_once '../classes/Doctor.php';
include_once '../classes/Patient.php';

$database = new Database();
$db = $database->getConnection();

// Get statistics
$appointment = new Appointment($db);
$doctor = new Doctor($db);

// Count appointments by status
$stats_query = "SELECT 
    COUNT(*) as total_appointments,
    SUM(CASE WHEN status = 'scheduled' THEN 1 ELSE 0 END) as scheduled,
    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
    FROM appointments";
$stats_stmt = $db->prepare($stats_query);
$stats_stmt->execute();
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

// Count total doctors and patients
$doctors_count = $db->query("SELECT COUNT(*) FROM doctors WHERE status = 'active'")->fetchColumn();
$patients_count = $db->query("SELECT COUNT(*) FROM patients")->fetchColumn();

// Get recent appointments
$recent_appointments_query = "SELECT a.*, p.name as patient_name, d.name as doctor_name, d.specialization
    FROM appointments a
    LEFT JOIN patients p ON a.patient_id = p.id
    LEFT JOIN doctors d ON a.doctor_id = d.id
    ORDER BY a.created_at DESC
    LIMIT 5";
$recent_stmt = $db->prepare($recent_appointments_query);
$recent_stmt->execute();
$recent_appointments = $recent_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - HealthCare Plus</title>
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
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
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
                        <a class="nav-link active" href="dashboard.php">
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
                        <a class="nav-link" href="reports.php">
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
                        <div>
                            <h2>Dashboard</h2>
                            <p class="text-muted mb-0">Welcome back, <?php echo $_SESSION['admin_username']; ?>!</p>
                        </div>
                        <div class="text-muted">
                            <i class="fas fa-calendar me-1"></i><?php echo date('F j, Y'); ?>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="stat-card">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h3 class="mb-0"><?php echo $stats['total_appointments']; ?></h3>
                                        <p class="text-muted mb-0">Total Appointments</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="stat-card">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                                        <i class="fas fa-user-md"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h3 class="mb-0"><?php echo $doctors_count; ?></h3>
                                        <p class="text-muted mb-0">Active Doctors</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="stat-card">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h3 class="mb-0"><?php echo $patients_count; ?></h3>
                                        <p class="text-muted mb-0">Total Patients</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="stat-card">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h3 class="mb-0"><?php echo $stats['scheduled']; ?></h3>
                                        <p class="text-muted mb-0">Pending Appointments</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Appointment Status Overview -->
                    <div class="row mb-4">
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2 text-primary"></i>Appointment Status Overview</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-3">
                                            <div class="p-3">
                                                <h4 class="text-warning"><?php echo $stats['scheduled']; ?></h4>
                                                <p class="text-muted mb-0">Scheduled</p>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="p-3">
                                                <h4 class="text-success"><?php echo $stats['confirmed']; ?></h4>
                                                <p class="text-muted mb-0">Confirmed</p>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="p-3">
                                                <h4 class="text-secondary"><?php echo $stats['completed']; ?></h4>
                                                <p class="text-muted mb-0">Completed</p>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="p-3">
                                                <h4 class="text-danger"><?php echo $stats['cancelled']; ?></h4>
                                                <p class="text-muted mb-0">Cancelled</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0"><i class="fas fa-calendar-day me-2 text-primary"></i>Today's Summary</h5>
                                </div>
                                <div class="card-body">
                                    <?php
                                    $today_query = "SELECT COUNT(*) as today_appointments FROM appointments WHERE DATE(appointment_date) = CURDATE()";
                                    $today_stmt = $db->prepare($today_query);
                                    $today_stmt->execute();
                                    $today_count = $today_stmt->fetchColumn();
                                    ?>
                                    <div class="text-center">
                                        <h2 class="text-primary"><?php echo $today_count; ?></h2>
                                        <p class="text-muted">Appointments Today</p>
                                        <a href="appointments.php?filter=today" class="btn btn-outline-primary btn-sm">View Details</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Appointments -->
                    <div class="card">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-clock me-2 text-primary"></i>Recent Appointments</h5>
                            <a href="appointments.php" class="btn btn-outline-primary btn-sm">View All</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Patient</th>
                                            <th>Doctor</th>
                                            <th>Date & Time</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($recent_appointments as $apt): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($apt['patient_name']); ?></strong>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($apt['doctor_name']); ?></strong><br>
                                                <small class="text-muted"><?php echo htmlspecialchars($apt['specialization']); ?></small>
                                            </td>
                                            <td>
                                                <?php echo date('M j, Y', strtotime($apt['appointment_date'])); ?><br>
                                                <small class="text-muted"><?php echo date('g:i A', strtotime($apt['appointment_time'])); ?></small>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo $apt['status'] == 'scheduled' ? 'warning' : 
                                                        ($apt['status'] == 'confirmed' ? 'success' : 
                                                        ($apt['status'] == 'completed' ? 'secondary' : 'danger')); 
                                                ?>">
                                                    <?php echo ucfirst($apt['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="appointments.php?id=<?php echo $apt['id']; ?>" class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
