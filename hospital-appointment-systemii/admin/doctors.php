<?php
include_once 'auth_check.php';
include_once '../config/database.php';
include_once '../classes/Doctor.php';

$database = new Database();
$db = $database->getConnection();

$doctor = new Doctor($db);

// Handle doctor actions
if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_doctor':
                // Add new doctor logic here
                $success_message = "Doctor added successfully!";
                break;
            case 'update_status':
                // Update doctor status logic here
                $success_message = "Doctor status updated successfully!";
                break;
        }
    }
}

$doctors_stmt = $doctor->read();
$doctors = $doctors_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get departments for dropdown
$dept_query = "SELECT * FROM departments ORDER BY name";
$dept_stmt = $db->prepare($dept_query);
$dept_stmt->execute();
$departments = $dept_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctors Management - HealthCare Plus</title>
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
        .doctor-card {
            transition: transform 0.3s ease;
        }
        .doctor-card:hover {
            transform: translateY(-5px);
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
                        <a class="nav-link active" href="doctors.php">
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
                        <h2><i class="fas fa-user-md me-2 text-primary"></i>Doctors Management</h2>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDoctorModal">
                            <i class="fas fa-plus me-2"></i>Add New Doctor
                        </button>
                    </div>

                    <!-- Success Alert -->
                    <?php if (isset($success_message)): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Doctors Grid -->
                    <div class="row">
                        <?php foreach($doctors as $doc): ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card doctor-card">
                                <div class="card-body text-center">
                                    <img src="https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" 
                                         alt="<?php echo htmlspecialchars($doc['name']); ?>" 
                                         class="rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                                    
                                    <h5 class="card-title"><?php echo htmlspecialchars($doc['name']); ?></h5>
                                    <p class="text-primary fw-semibold"><?php echo htmlspecialchars($doc['specialization']); ?></p>
                                    <p class="text-muted small"><?php echo htmlspecialchars($doc['qualification']); ?></p>
                                    
                                    <div class="row text-center mb-3">
                                        <div class="col-6">
                                            <small class="text-muted">Experience</small>
                                            <div class="fw-bold"><?php echo $doc['experience_years']; ?> years</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Fee</small>
                                            <div class="fw-bold">$<?php echo $doc['consultation_fee']; ?></div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <span class="badge bg-<?php echo $doc['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                            <?php echo ucfirst($doc['status']); ?>
                                        </span>
                                    </div>
                                    
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-outline-primary btn-sm" 
                                                onclick="viewDoctor(<?php echo $doc['id']; ?>)">
                                            <i class="fas fa-eye me-1"></i>View Details
                                        </button>
                                        <div class="btn-group">
                                            <button class="btn btn-outline-success btn-sm" 
                                                    onclick="editDoctor(<?php echo $doc['id']; ?>)">
                                                <i class="fas fa-edit me-1"></i>Edit
                                            </button>
                                            <button class="btn btn-outline-<?php echo $doc['status'] == 'active' ? 'warning' : 'success'; ?> btn-sm" 
                                                    onclick="toggleStatus(<?php echo $doc['id']; ?>, '<?php echo $doc['status']; ?>')">
                                                <i class="fas fa-<?php echo $doc['status'] == 'active' ? 'pause' : 'play'; ?> me-1"></i>
                                                <?php echo $doc['status'] == 'active' ? 'Deactivate' : 'Activate'; ?>
                                            </button>
                                            <button class="btn btn-outline-danger btn-sm" 
                                                    onclick="deleteDoctor(<?php echo $doc['id']; ?>)">
                                                <i class="fas fa-trash me-1"></i>Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Doctor Modal -->
    <div class="modal fade" id="addDoctorModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Add New Doctor</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="addDoctorForm">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_doctor">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name *</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Specialization *</label>
                                <input type="text" class="form-control" name="specialization" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone *</label>
                                <input type="tel" class="form-control" name="phone" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Experience (Years) *</label>
                                <input type="number" class="form-control" name="experience_years" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Consultation Fee *</label>
                                <input type="number" step="0.01" class="form-control" name="consultation_fee" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Department</label>
                                <select class="form-select" name="department_id">
                                    <option value="">Select Department</option>
                                    <?php foreach($departments as $dept): ?>
                                    <option value="<?php echo $dept['id']; ?>"><?php echo htmlspecialchars($dept['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Qualification *</label>
                            <input type="text" class="form-control" name="qualification" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Doctor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Doctor Modal -->
    <div class="modal fade" id="editDoctorModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-user-edit me-2"></i>Edit Doctor</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="editDoctorForm">
                    <div class="modal-body">
                        <input type="hidden" name="doctor_id" id="editDoctorId">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name *</label>
                                <input type="text" class="form-control" name="name" id="editName" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Specialization *</label>
                                <input type="text" class="form-control" name="specialization" id="editSpecialization" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email *</label>
                            <input type="email" class="form-control" name="email" id="editEmail" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone *</label>
                            <input type="tel" class="form-control" name="phone" id="editPhone" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Experience (Years) *</label>
                            <input type="number" class="form-control" name="experience_years" id="editExperience" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Consultation Fee *</label>
                            <input type="number" step="0.01" class="form-control" name="consultation_fee" id="editFee" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Department</label>
                            <select class="form-select" name="department_id" id="editDepartment">
                                <option value="">Select Department</option>
                                <?php foreach($departments as $dept): ?>
                                <option value="<?php echo $dept['id']; ?>"><?php echo htmlspecialchars($dept['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="editStatus">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Qualification *</label>
                        <input type="text" class="form-control" name="qualification" id="editQualification" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Update Doctor</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function viewDoctor(doctorId) {
        fetch('doctor_actions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=get_doctor&doctor_id=${doctorId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const doc = data.doctor;
                const modalContent = `
                    <div class="modal fade" id="viewDoctorModal" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title"><i class="fas fa-user-md me-2"></i>Doctor Details</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-4 text-center">
                                            <img src="https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" 
                                                 alt="${doc.name}" class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                                            <h5>${doc.name}</h5>
                                            <p class="text-primary">${doc.specialization}</p>
                                            <span class="badge bg-${doc.status === 'active' ? 'success' : 'secondary'}">${doc.status}</span>
                                        </div>
                                        <div class="col-md-8">
                                            <table class="table">
                                                <tr><td><strong>Email:</strong></td><td>${doc.email}</td></tr>
                                                <tr><td><strong>Phone:</strong></td><td>${doc.phone}</td></tr>
                                                <tr><td><strong>Qualification:</strong></td><td>${doc.qualification}</td></tr>
                                                <tr><td><strong>Experience:</strong></td><td>${doc.experience_years} years</td></tr>
                                                <tr><td><strong>Consultation Fee:</strong></td><td>$${doc.consultation_fee}</td></tr>
                                                <tr><td><strong>Department:</strong></td><td>${doc.department_name || 'Not assigned'}</td></tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" onclick="editDoctor(${doc.id})">Edit Doctor</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                // Remove existing modal if any
                const existingModal = document.getElementById('viewDoctorModal');
                if (existingModal) {
                    existingModal.remove();
                }
                
                // Add modal to body
                document.body.insertAdjacentHTML('beforeend', modalContent);
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('viewDoctorModal'));
                modal.show();
            } else {
                showAlert('danger', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', 'An error occurred while fetching doctor details');
        });
    }

    function editDoctor(doctorId) {
        fetch('doctor_actions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=get_doctor&doctor_id=${doctorId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const doc = data.doctor;
                
                // Close view modal if open
                const viewModal = document.getElementById('viewDoctorModal');
                if (viewModal) {
                    bootstrap.Modal.getInstance(viewModal).hide();
                }
                
                // Populate edit form
                document.getElementById('editDoctorId').value = doc.id;
                document.getElementById('editName').value = doc.name;
                document.getElementById('editSpecialization').value = doc.specialization;
                document.getElementById('editEmail').value = doc.email;
                document.getElementById('editPhone').value = doc.phone;
                document.getElementById('editExperience').value = doc.experience_years;
                document.getElementById('editFee').value = doc.consultation_fee;
                document.getElementById('editQualification').value = doc.qualification;
                document.getElementById('editDepartment').value = doc.department_id || '';
                document.getElementById('editStatus').value = doc.status;
                
                // Show edit modal
                const editModal = new bootstrap.Modal(document.getElementById('editDoctorModal'));
                editModal.show();
            } else {
                showAlert('danger', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', 'An error occurred while fetching doctor details');
        });
    }

    function toggleStatus(doctorId, currentStatus) {
        const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
        if (confirm(`Are you sure you want to ${newStatus === 'active' ? 'activate' : 'deactivate'} this doctor?`)) {
            fetch('doctor_actions.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=update_status&doctor_id=${doctorId}&new_status=${newStatus}`
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
                showAlert('danger', 'An error occurred while updating doctor status');
            });
        }
    }

    function deleteDoctor(doctorId) {
        if (confirm('Are you sure you want to delete this doctor? This action cannot be undone.')) {
            fetch('doctor_actions.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=delete_doctor&doctor_id=${doctorId}`
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
                showAlert('danger', 'An error occurred while deleting the doctor');
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

    // Handle add doctor form submission
    document.getElementById('addDoctorForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('action', 'add_doctor');
        
        fetch('doctor_actions.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                bootstrap.Modal.getInstance(document.getElementById('addDoctorModal')).hide();
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert('danger', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', 'An error occurred while adding the doctor');
        });
    });

    // Handle edit doctor form submission
    document.getElementById('editDoctorForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('action', 'update_doctor');
        
        fetch('doctor_actions.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                bootstrap.Modal.getInstance(document.getElementById('editDoctorModal')).hide();
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert('danger', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', 'An error occurred while updating the doctor');
        });
    });
</script>
</body>
</html>
