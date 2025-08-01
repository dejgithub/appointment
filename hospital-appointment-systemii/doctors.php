<?php
include_once 'config/database.php';
include_once 'classes/Doctor.php';

$database = new Database();
$db = $database->getConnection();

$doctor = new Doctor($db);
$doctors_stmt = $doctor->read();
$doctors = $doctors_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Doctors - HealthCare Plus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8fafc;
        }

        .hero-section {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            padding: 100px 0 50px;
        }

        .doctor-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: none;
            margin-bottom: 30px;
        }

        .doctor-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .doctor-image {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto;
        }

        .navbar-custom {
            background: white !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px 0;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border: none;
            border-radius: 10px;
            padding: 10px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-heartbeat me-2 text-primary"></i>
                <span class="text-primary fw-bold">HealthCare Plus</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="doctors.php">Doctors</a>
                    </li>
                    <li class="nav-item ms-3">
                        <a href="book_appointment.php" class="btn btn-primary">Book Appointment</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="text-center">
                <h1 class="display-4 fw-bold mb-4">Meet Our Expert Doctors</h1>
                <p class="lead">Our team of experienced healthcare professionals dedicated to your well-being</p>
            </div>
        </div>
    </section>

    <!-- Doctors Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <?php foreach($doctors as $doc): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card doctor-card">
                        <div class="card-body text-center p-4">
                            <img src="https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" 
                                 alt="<?php echo htmlspecialchars($doc['name']); ?>" class="doctor-image mb-3">
                            <h5 class="card-title fw-bold"><?php echo htmlspecialchars($doc['name']); ?></h5>
                            <p class="text-primary fw-semibold mb-2"><?php echo htmlspecialchars($doc['specialization']); ?></p>
                            <p class="text-muted small mb-2"><?php echo htmlspecialchars($doc['qualification']); ?></p>
                            <p class="text-muted small mb-3"><?php echo $doc['experience_years']; ?> years experience</p>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-center align-items-center mb-2">
                                    <i class="fas fa-star text-warning me-1"></i>
                                    <span class="fw-semibold">4.8</span>
                                    <span class="text-muted ms-1">(125 reviews)</span>
                                </div>
                                <div class="d-flex justify-content-center align-items-center">
                                    <i class="fas fa-dollar-sign text-success me-1"></i>
                                    <span class="fw-semibold"><?php echo $doc['consultation_fee']; ?></span>
                                    <span class="text-muted ms-1">consultation fee</span>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <span class="badge bg-success">Available Today</span>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <a href="book_appointment.php?doctor_id=<?php echo $doc['id']; ?>" 
                                   class="btn btn-primary">
                                    <i class="fas fa-calendar-plus me-2"></i>Book Appointment
                                </a>
                                <button class="btn btn-outline-primary" data-bs-toggle="modal" 
                                        data-bs-target="#doctorModal<?php echo $doc['id']; ?>">
                                    <i class="fas fa-info-circle me-2"></i>View Details
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Doctor Details Modal -->
                <div class="modal fade" id="doctorModal<?php echo $doc['id']; ?>" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title"><?php echo htmlspecialchars($doc['name']); ?></h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-4 text-center">
                                        <img src="https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" 
                                             alt="<?php echo htmlspecialchars($doc['name']); ?>" 
                                             class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                                        <h5><?php echo htmlspecialchars($doc['name']); ?></h5>
                                        <p class="text-primary"><?php echo htmlspecialchars($doc['specialization']); ?></p>
                                    </div>
                                    <div class="col-md-8">
                                        <h6><i class="fas fa-graduation-cap me-2 text-primary"></i>Qualifications</h6>
                                        <p><?php echo htmlspecialchars($doc['qualification']); ?></p>
                                        
                                        <h6><i class="fas fa-briefcase me-2 text-primary"></i>Experience</h6>
                                        <p><?php echo $doc['experience_years']; ?> years of professional experience</p>
                                        
                                        <h6><i class="fas fa-hospital me-2 text-primary"></i>Department</h6>
                                        <p><?php echo htmlspecialchars($doc['department_name'] ?? 'General'); ?></p>
                                        
                                        <h6><i class="fas fa-dollar-sign me-2 text-primary"></i>Consultation Fee</h6>
                                        <p>$<?php echo $doc['consultation_fee']; ?></p>
                                        
                                        <h6><i class="fas fa-envelope me-2 text-primary"></i>Contact</h6>
                                        <p><?php echo htmlspecialchars($doc['email']); ?><br>
                                           <?php echo htmlspecialchars($doc['phone']); ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <a href="book_appointment.php?doctor_id=<?php echo $doc['id']; ?>" 
                                   class="btn btn-primary">Book Appointment</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
