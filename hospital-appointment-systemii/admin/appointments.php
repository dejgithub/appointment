<?php
include_once 'auth_check.php';
include_once '../config/database.php';
include_once '../classes/Appointment.php';

$database = new Database();
$db = $database->getConnection();

// Handle status updates
if ($_POST && isset($_POST['action']) && isset($_POST['appointment_id'])) {
    $appointment = new Appointment($db);
    $appointment->id = $_POST['appointment_id'];
    $appointment->status = $_POST['new_status'];
    
    if ($appointment->updateStatus()) {
        $success_message = "Appointment status updated successfully!";
    } else {
        $error_message = "Failed to update appointment status.";
    }
}

// Get filter parameters
$filter = $_GET['filter'] ?? 'all';
$search = $_GET['search'] ?? '';

// Build query based on filters
$where_conditions = [];
$params = [];

if ($filter == 'today') {
    $where_conditions[] = "DATE(a.appointment_date) = CURDATE()";
} elseif ($filter == 'upcoming') {
    $where_conditions[] = "a.appointment_date >= CURDATE()";
} elseif ($filter == 'past') {
    $where_conditions[] = "a.appointment_date < CURDATE()";
} elseif ($filter != 'all') {
    $where_conditions[] = "a.status = :status";
    $params[':status'] = $filter;
}

if (!empty($search)) {
    $where_conditions[] = "(p.name LIKE :search OR d.name LIKE :search OR p.phone LIKE :search)";
    $params[':search'] = "%$search%";
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

$query = "SELECT a.*, p.name as patient_name, p.phone as patient_phone, p.email as patient_email,
                 d.name as doctor_name, d.specialization
          FROM appointments a
          LEFT JOIN patients p ON a.patient_id = p.id
          LEFT JOIN doctors d ON a.doctor_id = d.id
          $where_clause
          ORDER BY a.appointment_date DESC, a.appointment_time DESC";

$stmt = $db->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments Management - HealthCare Plus</title>
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
        .filter-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
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
                        <a class="nav-link active" href="appointments.php">
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
                        <h2><i class="fas fa-calendar-alt me-2 text-primary"></i>Appointments Management</h2>
                        <div>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAppointmentModal">
                                <i class="fas fa-plus me-2"></i>Add Appointment
                            </button>
                        </div>
                    </div>

                    <!-- Alerts -->
                    <?php if (isset($success_message)): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Filters -->
                    <div class="filter-card p-4 mb-4">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Filter by Status</label>
                                <select name="filter" class="form-select">
                                    <option value="all" <?php echo $filter == 'all' ? 'selected' : ''; ?>>All Appointments</option>
                                    <option value="today" <?php echo $filter == 'today' ? 'selected' : ''; ?>>Today</option>
                                    <option value="upcoming" <?php echo $filter == 'upcoming' ? 'selected' : ''; ?>>Upcoming</option>
                                    <option value="scheduled" <?php echo $filter == 'scheduled' ? 'selected' : ''; ?>>Scheduled</option>
                                    <option value="confirmed" <?php echo $filter == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                    <option value="completed" <?php echo $filter == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="cancelled" <?php echo $filter == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Search</label>
                                <input type="text" name="search" class="form-control" placeholder="Search by patient name, doctor, or phone..." value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-2"></i>Filter
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Appointments Table -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>ID</th>
                                            <th>Patient</th>
                                            <th>Doctor</th>
                                            <th>Date & Time</th>
                                            <th>Reason</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($appointments)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">No appointments found</p>
                                            </td>
                                        </tr>
                                        <?php else: ?>
                                        <?php foreach($appointments as $apt): ?>
                                        <tr>
                                            <td>#<?php echo $apt['id']; ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($apt['patient_name']); ?></strong><br>
                                                <small class="text-muted">
                                                    <i class="fas fa-phone me-1"></i><?php echo htmlspecialchars($apt['patient_phone']); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($apt['doctor_name']); ?></strong><br>
                                                <small class="text-muted"><?php echo htmlspecialchars($apt['specialization']); ?></small>
                                            </td>
                                            <td>
                                                <strong><?php echo date('M j, Y', strtotime($apt['appointment_date'])); ?></strong><br>
                                                <small class="text-muted"><?php echo date('g:i A', strtotime($apt['appointment_time'])); ?></small>
                                            </td>
                                            <td>
                                                <span class="d-inline-block text-truncate" style="max-width: 200px;" 
                                                      title="<?php echo htmlspecialchars($apt['reason']); ?>">
                                                    <?php echo htmlspecialchars($apt['reason']); ?>
                                                </span>
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
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-primary" title="View Details" 
                                                            onclick="viewAppointment(<?php echo $apt['id']; ?>)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <?php if ($apt['status'] == 'scheduled'): ?>
                                                    <button class="btn btn-outline-success" title="Confirm" 
                                                            onclick="updateStatus(<?php echo $apt['id']; ?>, 'confirmed')">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <?php endif; ?>
                                                    <?php if ($apt['status'] != 'cancelled' && $apt['status'] != 'completed'): ?>
                                                    <button class="btn btn-outline-danger" title="Cancel" 
                                                            onclick="updateStatus(<?php echo $apt['id']; ?>, 'cancelled')">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                    <?php endif; ?>
                                                    <button class="btn btn-outline-danger" title="Delete" 
                                                            onclick="deleteAppointment(<?php echo $apt['id']; ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Update Form (Hidden) -->
    <form id="statusUpdateForm" method="POST" style="display: none;">
        <input type="hidden" name="action" value="update_status">
        <input type="hidden" name="appointment_id" id="statusAppointmentId">
        <input type="hidden" name="new_status" id="newStatus">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateStatus(appointmentId, newStatus) {
            if (confirm('Are you sure you want to update this appointment status to ' + newStatus + '?')) {
                fetch('appointment_actions.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=update_status&appointment_id=${appointmentId}&new_status=${newStatus}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', data.message);
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showAlert('danger', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('danger', 'An error occurred while updating the appointment');
                });
            }
        }

        function viewAppointment(appointmentId) {
            fetch('appointment_actions.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=get_appointment&appointment_id=${appointmentId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const apt = data.appointment;
                    const modalContent = `
                        <div class="modal fade" id="viewAppointmentModal" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title"><i class="fas fa-eye me-2"></i>Appointment Details #${apt.id}</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6 class="text-primary"><i class="fas fa-user me-2"></i>Patient Information</h6>
                                                <table class="table table-sm">
                                                    <tr><td><strong>Name:</strong></td><td>${apt.patient_name}</td></tr>
                                                    <tr><td><strong>Email:</strong></td><td>${apt.patient_email}</td></tr>
                                                    <tr><td><strong>Phone:</strong></td><td>${apt.patient_phone}</td></tr>
                                                    <tr><td><strong>Gender:</strong></td><td>${apt.gender || 'Not specified'}</td></tr>
                                                    <tr><td><strong>Blood Group:</strong></td><td>${apt.blood_group || 'Not specified'}</td></tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="text-primary"><i class="fas fa-stethoscope me-2"></i>Appointment Information</h6>
                                                <table class="table table-sm">
                                                    <tr><td><strong>Doctor:</strong></td><td>${apt.doctor_name}</td></tr>
                                                    <tr><td><strong>Specialization:</strong></td><td>${apt.specialization}</td></tr>
                                                    <tr><td><strong>Date:</strong></td><td>${new Date(apt.appointment_date).toLocaleDateString()}</td></tr>
                                                    <tr><td><strong>Time:</strong></td><td>${apt.appointment_time}</td></tr>
                                                    <tr><td><strong>Fee:</strong></td><td>$${apt.consultation_fee}</td></tr>
                                                    <tr><td><strong>Status:</strong></td><td><span class="badge bg-primary">${apt.status}</span></td></tr>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <h6 class="text-primary"><i class="fas fa-clipboard me-2"></i>Reason for Visit</h6>
                                                <p class="border p-3 rounded">${apt.reason}</p>
                                            </div>
                                        </div>
                                        ${apt.medical_history ? `
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <h6 class="text-primary"><i class="fas fa-file-medical me-2"></i>Medical History</h6>
                                                <p class="border p-3 rounded">${apt.medical_history}</p>
                                            </div>
                                        </div>
                                        ` : ''}
                                        ${apt.address ? `
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <h6 class="text-primary"><i class="fas fa-map-marker-alt me-2"></i>Address</h6>
                                                <p class="border p-3 rounded">${apt.address}</p>
                                            </div>
                                        </div>
                                        ` : ''}
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        ${apt.status === 'scheduled' ? `<button type="button" class="btn btn-success" onclick="updateStatus(${apt.id}, 'confirmed')">Confirm</button>` : ''}
                                        ${apt.status !== 'cancelled' && apt.status !== 'completed' ? `<button type="button" class="btn btn-danger" onclick="updateStatus(${apt.id}, 'cancelled')">Cancel</button>` : ''}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    // Remove existing modal if any
                    const existingModal = document.getElementById('viewAppointmentModal');
                    if (existingModal) {
                        existingModal.remove();
                    }
                    
                    // Add modal to body
                    document.body.insertAdjacentHTML('beforeend', modalContent);
                    
                    // Show modal
                    const modal = new bootstrap.Modal(document.getElementById('viewAppointmentModal'));
                    modal.show();
                } else {
                    showAlert('danger', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred while fetching appointment details');
            });
        }

        function deleteAppointment(appointmentId) {
            if (confirm('Are you sure you want to delete this appointment? This action cannot be undone.')) {
                fetch('appointment_actions.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=delete_appointment&appointment_id=${appointmentId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', data.message);
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showAlert('danger', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('danger', 'An error occurred while deleting the appointment');
                });
            }
        }

        function showAlert(type, message) {
            const alertContainer = document.querySelector('.p-4');
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            alertContainer.insertAdjacentHTML('afterbegin', alertHtml);
        }
    </script>
</body>
</html>
