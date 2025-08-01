<?php
include_once 'auth_check.php';
include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Handle department actions
if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_department':
                $query = "INSERT INTO departments (name, description) VALUES (:name, :description)";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':name', $_POST['name']);
                $stmt->bindParam(':description', $_POST['description']);
                if ($stmt->execute()) {
                    $success_message = "Department added successfully!";
                }
                break;
        }
    }
}

// Get all departments with doctor count
$query = "SELECT d.*, COUNT(doc.id) as doctor_count, doc_head.name as head_doctor_name
          FROM departments d
          LEFT JOIN doctors doc ON d.id = doc.department_id AND doc.status = 'active'
          LEFT JOIN doctors doc_head ON d.head_doctor_id = doc_head.id
          GROUP BY d.id
          ORDER BY d.name";

$stmt = $db->prepare($query);
$stmt->execute();
$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Departments Management - HealthCare Plus</title>
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
        .dept-card {
            transition: transform 0.3s ease;
        }
        .dept-card:hover {
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
                        <a class="nav-link" href="doctors.php">
                            <i class="fas fa-user-md me-2"></i>Doctors
                        </a>
                        <a class="nav-link" href="patients.php">
                            <i class="fas fa-users me-2"></i>Patients
                        </a>
                        <a class="nav-link active" href="departments.php">
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
                        <h2><i class="fas fa-building me-2 text-primary"></i>Departments Management</h2>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDepartmentModal">
                            <i class="fas fa-plus me-2"></i>Add Department
                        </button>
                    </div>

                    <!-- Success Alert -->
                    <?php if (isset($success_message)): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Departments Grid -->
                    <div class="row">
                        <?php foreach($departments as $dept): ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card dept-card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                             style="width: 50px; height: 50px;">
                                            <i class="fas fa-building text-white"></i>
                                        </div>
                                        <div>
                                            <h5 class="card-title mb-0"><?php echo htmlspecialchars($dept['name']); ?></h5>
                                            <small class="text-muted"><?php echo $dept['doctor_count']; ?> doctors</small>
                                        </div>
                                    </div>
                                    
                                    <p class="text-muted"><?php echo htmlspecialchars($dept['description']); ?></p>
                                    
                                    <?php if ($dept['head_doctor_name']): ?>
                                    <div class="mb-3">
                                        <small class="text-muted">Head of Department:</small><br>
                                        <strong><?php echo htmlspecialchars($dept['head_doctor_name']); ?></strong>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-outline-primary btn-sm" onclick="viewDepartment(<?php echo $dept['id']; ?>)">
                                            <i class="fas fa-eye me-1"></i>View Details
                                        </button>
                                        <div class="btn-group">
                                            <button class="btn btn-outline-success btn-sm" onclick="editDepartment(<?php echo $dept['id']; ?>)">
                                                <i class="fas fa-edit me-1"></i>Edit Department
                                            </button>
                                            <button class="btn btn-outline-danger btn-sm" onclick="deleteDepartment(<?php echo $dept['id']; ?>)">
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

    <!-- Add Department Modal -->
    <div class="modal fade" id="addDepartmentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add New Department</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="addDepartmentForm">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_department">
                        <div class="mb-3">
                            <label class="form-label">Department Name *</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Department</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Department Modal -->
    <div class="modal fade" id="editDepartmentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Department</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="editDepartmentForm">
                    <div class="modal-body">
                        <input type="hidden" name="department_id" id="editDeptId">
                        <div class="mb-3">
                            <label class="form-label">Department Name *</label>
                            <input type="text" class="form-control" name="name" id="editDeptName" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="editDeptDescription" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Update Department</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewDepartment(deptId) {
            fetch('department_actions.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=get_department&department_id=${deptId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const dept = data.department;
                    alert(`Department: ${dept.name}\nDescription: ${dept.description}`);
                } else {
                    showAlert('danger', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred while fetching department details');
            });
        }

        function editDepartment(deptId) {
            fetch('department_actions.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=get_department&department_id=${deptId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const dept = data.department;
                    
                    // Populate edit form
                    document.getElementById('editDeptId').value = dept.id;
                    document.getElementById('editDeptName').value = dept.name;
                    document.getElementById('editDeptDescription').value = dept.description;
                    
                    // Show edit modal
                    const editModal = new bootstrap.Modal(document.getElementById('editDepartmentModal'));
                    editModal.show();
                } else {
                    showAlert('danger', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred while fetching department details');
            });
        }

        function deleteDepartment(deptId) {
            if (confirm('Are you sure you want to delete this department? This action cannot be undone.')) {
                fetch('department_actions.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=delete_department&department_id=${deptId}`
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
                    showAlert('danger', 'An error occurred while deleting the department');
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

        // Handle add department form submission
        document.getElementById('addDepartmentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'add_department');
            
            fetch('department_actions.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    bootstrap.Modal.getInstance(document.getElementById('addDepartmentModal')).hide();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert('danger', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred while adding the department');
            });
        });

        // Handle edit department form submission
        document.getElementById('editDepartmentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'update_department');
            
            fetch('department_actions.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    bootstrap.Modal.getInstance(document.getElementById('editDepartmentModal')).hide();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert('danger', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred while updating the department');
            });
        });
    </script>
</body>
</html>
