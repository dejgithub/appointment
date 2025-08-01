<?php
include_once 'auth_check.php';
include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get all patients with their appointment count
$query = "SELECT p.*, 
                 COUNT(a.id) as appointment_count,
                 MAX(a.appointment_date) as last_appointment
          FROM patients p
          LEFT JOIN appointments a ON p.id = a.patient_id
          GROUP BY p.id
          ORDER BY p.created_at DESC";

$stmt = $db->prepare($query);
$stmt->execute();
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patients Management - HealthCare Plus</title>
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
                        <a class="nav-link active" href="patients.php">
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
                        <h2><i class="fas fa-users me-2 text-primary"></i>Patients Management</h2>
                        <div>
                            <button class="btn btn-outline-primary me-2">
                                <i class="fas fa-download me-2"></i>Export
                            </button>
                            <button class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Add Patient
                            </button>
                        </div>
                    </div>

                    <!-- Patients Table -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>ID</th>
                                            <th>Patient Info</th>
                                            <th>Contact</th>
                                            <th>Details</th>
                                            <th>Appointments</th>
                                            <th>Last Visit</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($patients as $patient): ?>
                                        <tr>
                                            <td>#<?php echo $patient['id']; ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                                         style="width: 40px; height: 40px;">
                                                        <i class="fas fa-user text-white"></i>
                                                    </div>
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($patient['name']); ?></strong><br>
                                                        <small class="text-muted">
                                                            <?php echo ucfirst($patient['gender']); ?>, 
                                                            <?php echo date('Y') - date('Y', strtotime($patient['date_of_birth'])); ?> years
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <i class="fas fa-envelope me-1 text-muted"></i>
                                                    <small><?php echo htmlspecialchars($patient['email']); ?></small>
                                                </div>
                                                <div>
                                                    <i class="fas fa-phone me-1 text-muted"></i>
                                                    <small><?php echo htmlspecialchars($patient['phone']); ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($patient['blood_group']): ?>
                                                <span class="badge bg-danger mb-1"><?php echo $patient['blood_group']; ?></span><br>
                                                <?php endif; ?>
                                                <small class="text-muted">
                                                    DOB: <?php echo date('M j, Y', strtotime($patient['date_of_birth'])); ?>
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-primary fs-6"><?php echo $patient['appointment_count']; ?></span>
                                            </td>
                                            <td>
                                                <?php if ($patient['last_appointment']): ?>
                                                    <?php echo date('M j, Y', strtotime($patient['last_appointment'])); ?>
                                                <?php else: ?>
                                                    <span class="text-muted">Never</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-primary" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-outline-success" title="Book Appointment">
                                                        <i class="fas fa-calendar-plus"></i>
                                                    </button>
                                                    <button class="btn btn-outline-info" title="Medical History">
                                                        <i class="fas fa-file-medical"></i>
                                                    </button>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
