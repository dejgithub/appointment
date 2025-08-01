<?php
include_once 'config/database.php';
include_once 'classes/Doctor.php';
include_once 'classes/Appointment.php';

$database = new Database();
$db = $database->getConnection();

$doctor = new Doctor($db);
$doctors_stmt = $doctor->read();
$doctors = $doctors_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get recent appointments count for stats
$appointment = new Appointment($db);
$appointments_stmt = $appointment->read();
$total_appointments = $appointments_stmt->rowCount();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthCare Plus - Your Health, Our Priority</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --accent-color: #f59e0b;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --light-bg: #f8fafc;
            --dark-text: #1e293b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: var(--dark-text);
        }

        .gradient-bg {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        }

        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 100px 0;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="white" opacity="0.1"><polygon points="0,0 1000,100 1000,0"/></svg>');
            background-size: cover;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .btn-custom {
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        .btn-primary-custom {
            background: var(--accent-color);
            border: none;
            color: white;
        }

        .btn-primary-custom:hover {
            background: #d97706;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(245, 158, 11, 0.3);
        }

        .btn-outline-custom {
            border: 2px solid white;
            color: white;
            background: transparent;
        }

        .btn-outline-custom:hover {
            background: white;
            color: var(--primary-color);
            transform: translateY(-2px);
        }

        .stats-section {
            background: white;
            padding: 80px 0;
        }

        .stat-card {
            text-align: center;
            padding: 30px;
            border-radius: 15px;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-10px);
        }

        .stat-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .departments-section {
            background: var(--light-bg);
            padding: 80px 0;
        }

        .department-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            height: 100%;
        }

        .department-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .dept-icon {
            width: 80px;
            height: 80px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
            color: white;
        }

        .doctors-section {
            padding: 80px 0;
            background: white;
        }

        .doctor-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: none;
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

        .cta-section {
            background: var(--primary-color);
            color: white;
            padding: 80px 0;
        }

        .quick-card {
            background: white;
            color: var(--dark-text);
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .quick-card:hover {
            transform: translateY(-5px);
        }

        .contact-section {
            background: #1e293b;
            color: white;
            padding: 80px 0;
        }

        .contact-form {
            background: #334155;
            border-radius: 15px;
            padding: 40px;
        }

        .form-control {
            border-radius: 10px;
            border: none;
            padding: 15px;
            margin-bottom: 20px;
        }

        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            border-color: var(--primary-color);
        }

        .footer {
            background: #0f172a;
            color: white;
            padding: 60px 0 30px;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            position: relative;
        }

        .section-subtitle {
            font-size: 1.2rem;
            opacity: 0.8;
            margin-bottom: 50px;
        }

        .navbar-custom {
            background: white !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px 0;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color) !important;
        }

        .nav-link {
            font-weight: 500;
            color: var(--dark-text) !important;
            margin: 0 10px;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: var(--primary-color) !important;
        }

        @media (max-width: 768px) {
            .hero-section {
                padding: 60px 0;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .stat-number {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="#home">
                <i class="fas fa-heartbeat me-2"></i>HealthCare Plus
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#departments">Departments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#doctors">Doctors</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    <li class="nav-item ms-3">
                        <a href="book_appointment.php" class="btn btn-primary-custom btn-custom">Book Appointment</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content">
                    <h1 class="display-4 fw-bold mb-4">Your Health is Our <span style="color: var(--accent-color);">Priority</span></h1>
                    <p class="lead mb-5">Experience world-class healthcare with our team of expert doctors and state-of-the-art facilities. Book your appointment today and take the first step towards a healthier you.</p>
                    <div class="d-flex flex-column flex-sm-row gap-3">
                        <a href="book_appointment.php" class="btn btn-primary-custom btn-custom">
                            <i class="fas fa-calendar-plus me-2"></i>Book Appointment
                        </a>
                        <a href="tel:911" class="btn btn-outline-custom btn-custom">
                            <i class="fas fa-phone me-2"></i>Emergency: 911
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <img src="https://images.unsplash.com/photo-1559757148-5c350d0d3c56?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                         alt="Modern Hospital" class="img-fluid rounded-3 shadow-lg">
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(37, 99, 235, 0.1);">
                            <i class="fas fa-user-md" style="color: var(--primary-color);"></i>
                        </div>
                        <div class="stat-number"><?php echo count($doctors); ?>+</div>
                        <h5>Expert Doctors</h5>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1);">
                            <i class="fas fa-award" style="color: var(--success-color);"></i>
                        </div>
                        <div class="stat-number">15+</div>
                        <h5>Years of Excellence</h5>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(239, 68, 68, 0.1);">
                            <i class="fas fa-heart" style="color: var(--danger-color);"></i>
                        </div>
                        <div class="stat-number"><?php echo $total_appointments; ?>+</div>
                        <h5>Happy Patients</h5>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1);">
                            <i class="fas fa-clock" style="color: var(--accent-color);"></i>
                        </div>
                        <div class="stat-number">24/7</div>
                        <h5>Emergency Care</h5>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Departments Section -->
    <section id="departments" class="departments-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Our Departments</h2>
                <p class="section-subtitle">Comprehensive healthcare services across multiple specializations</p>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card department-card">
                        <div class="dept-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <h4>Cardiology</h4>
                        <p class="text-muted">Heart and cardiovascular diseases treatment with advanced technology</p>
                        <a href="book_appointment.php" class="btn btn-outline-primary">Learn More</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card department-card">
                        <div class="dept-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                            <i class="fas fa-brain"></i>
                        </div>
                        <h4>Neurology</h4>
                        <p class="text-muted">Brain and nervous system disorders with expert neurologists</p>
                        <a href="book_appointment.php" class="btn btn-outline-primary">Learn More</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card department-card">
                        <div class="dept-icon" style="background: linear-gradient(135deg, #2563eb, #1d4ed8);">
                            <i class="fas fa-bone"></i>
                        </div>
                        <h4>Orthopedics</h4>
                        <p class="text-muted">Bone and joint problems with surgical and non-surgical treatments</p>
                        <a href="book_appointment.php" class="btn btn-outline-primary">Learn More</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card department-card">
                        <div class="dept-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                            <i class="fas fa-baby"></i>
                        </div>
                        <h4>Pediatrics</h4>
                        <p class="text-muted">Specialized healthcare for children from infancy to adolescence</p>
                        <a href="book_appointment.php" class="btn btn-outline-primary">Learn More</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card department-card">
                        <div class="dept-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                            <i class="fas fa-microscope"></i>
                        </div>
                        <h4>Dermatology</h4>
                        <p class="text-muted">Skin diseases and cosmetic treatments by certified dermatologists</p>
                        <a href="book_appointment.php" class="btn btn-outline-primary">Learn More</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card department-card">
                        <div class="dept-icon" style="background: linear-gradient(135deg, #6b7280, #4b5563);">
                            <i class="fas fa-stethoscope"></i>
                        </div>
                        <h4>General Medicine</h4>
                        <p class="text-muted">Comprehensive primary healthcare and preventive medicine</p>
                        <a href="book_appointment.php" class="btn btn-outline-primary">Learn More</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Doctors Section -->
    <section id="doctors" class="doctors-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Meet Our Doctors</h2>
                <p class="section-subtitle">Our team of experienced and dedicated healthcare professionals</p>
            </div>
            <div class="row">
                <?php 
                $featured_doctors = array_slice($doctors, 0, 3);
                foreach($featured_doctors as $doc): 
                ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card doctor-card">
                        <div class="card-body text-center p-4">
                            <img src="https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" 
                                 alt="<?php echo htmlspecialchars($doc['name']); ?>" class="doctor-image mb-3">
                            <h5 class="card-title"><?php echo htmlspecialchars($doc['name']); ?></h5>
                            <p class="text-primary fw-semibold"><?php echo htmlspecialchars($doc['specialization']); ?></p>
                            <p class="text-muted small"><?php echo $doc['experience_years']; ?> years experience</p>
                            <p class="text-muted small"><?php echo htmlspecialchars($doc['qualification']); ?></p>
                            <div class="mb-3">
                                <span class="badge bg-success">Available</span>
                            </div>
                            <a href="book_appointment.php?doctor_id=<?php echo $doc['id']; ?>" class="btn btn-primary w-100">Book Appointment</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <a href="doctors.php" class="btn btn-outline-primary btn-lg">View All Doctors</a>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title text-white">Need Immediate Care?</h2>
                <p class="section-subtitle text-white">Don't wait. Book your appointment now or call our emergency hotline</p>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="quick-card">
                        <div class="mb-3">
                            <i class="fas fa-clock fa-3x text-primary"></i>
                        </div>
                        <h4>Quick Booking</h4>
                        <p class="text-muted">Book appointment in under 2 minutes</p>
                        <a href="book_appointment.php" class="btn btn-primary">Book Now</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="quick-card">
                        <div class="mb-3">
                            <i class="fas fa-phone fa-3x text-success"></i>
                        </div>
                        <h4>Emergency Call</h4>
                        <p class="text-muted">24/7 emergency helpline</p>
                        <a href="tel:911" class="btn btn-success">Call 911</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="quick-card">
                        <div class="mb-3">
                            <i class="fas fa-video fa-3x text-info"></i>
                        </div>
                        <h4>Online Consultation</h4>
                        <p class="text-muted">Video call with doctors</p>
                        <a href="#" class="btn btn-info">Start Chat</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mb-5">
                    <h2 class="section-title text-white">Get in Touch</h2>
                    <p class="section-subtitle text-white">We're here for you 24/7. Reach out to us for any medical emergency or general inquiries.</p>
                    
                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <i class="fas fa-map-marker-alt fa-2x text-primary"></i>
                            </div>
                            <div>
                                <h5>Address</h5>
                                <p class="mb-0 opacity-75">123 Healthcare Ave, Medical District, NY 10001</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <i class="fas fa-phone fa-2x text-primary"></i>
                            </div>
                            <div>
                                <h5>Phone</h5>
                                <p class="mb-0 opacity-75">+1 (555) 123-4567</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-envelope fa-2x text-primary"></i>
                            </div>
                            <div>
                                <h5>Email</h5>
                                <p class="mb-0 opacity-75">info@healthcareplus.com</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="contact-form">
                        <h3 class="mb-4">Send us a Message</h3>
                        <form>
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="text" class="form-control" placeholder="First Name" required>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" placeholder="Last Name" required>
                                </div>
                            </div>
                            <input type="email" class="form-control" placeholder="Email Address" required>
                            <textarea class="form-control" rows="4" placeholder="Your Message" required></textarea>
                            <button type="submit" class="btn btn-primary w-100 btn-lg">Send Message</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h4><i class="fas fa-heartbeat me-2"></i>HealthCare Plus</h4>
                    <p class="opacity-75">Providing quality healthcare services with compassion and excellence since 2008.</p>
                    <div class="social-links">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook fa-2x"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-twitter fa-2x"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-instagram fa-2x"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-linkedin fa-2x"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white-50">About Us</a></li>
                        <li><a href="#" class="text-white-50">Services</a></li>
                        <li><a href="#" class="text-white-50">Doctors</a></li>
                        <li><a href="#" class="text-white-50">Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5>Services</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white-50">Emergency Care</a></li>
                        <li><a href="#" class="text-white-50">Surgery</a></li>
                        <li><a href="#" class="text-white-50">Laboratory</a></li>
                        <li><a href="#" class="text-white-50">Pharmacy</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 mb-4">
                    <h5>Emergency</h5>
                    <p class="opacity-75">24/7 Emergency Hotline:</p>
                    <h3 class="text-danger">911</h3>
                    <p class="opacity-75 mt-3">For non-emergency inquiries:</p>
                    <h5 class="text-primary">+1 (555) 123-4567</h5>
                </div>
            </div>
            <hr class="my-4 opacity-25">
            <div class="text-center">
                <p class="opacity-75">&copy; 2024 HealthCare Plus. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Navbar background change on scroll
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.background = 'rgba(255, 255, 255, 0.95)';
                navbar.style.backdropFilter = 'blur(10px)';
            } else {
                navbar.style.background = 'white';
                navbar.style.backdropFilter = 'none';
            }
        });
    </script>
</body>
</html>
