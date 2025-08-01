<?php
include_once 'config/database.php';
include_once 'classes/Doctor.php';

$database = new Database();
$db = $database->getConnection();

$doctor = new Doctor($db);
$doctors_stmt = $doctor->read();
$doctors = $doctors_stmt->fetchAll(PDO::FETCH_ASSOC);

$selected_doctor_id = isset($_GET['doctor_id']) ? $_GET['doctor_id'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment - HealthCare Plus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }

        .appointment-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 800px;
            margin: 0 auto;
        }

        .card-header {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .card-body {
            padding: 40px;
        }

        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e2e8f0;
            padding: 15px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .btn-primary {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border: none;
            border-radius: 10px;
            padding: 15px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
        }

        .section-title {
            color: #2563eb;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .section-title i {
            margin-right: 10px;
        }

        .alert {
            border-radius: 10px;
            border: none;
        }

        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgba(255,255,255,0.2);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 10px 20px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: rgba(255,255,255,0.3);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-btn">
            <i class="fas fa-arrow-left me-2"></i>Back to Home
        </a>
        
        <div class="appointment-card">
            <div class="card-header">
                <h2><i class="fas fa-calendar-plus me-3"></i>Book Your Appointment</h2>
                <p class="mb-0 opacity-90">Fill in the details below to schedule your visit with our expert doctors</p>
            </div>
            
            <div class="card-body">
                <div id="alertContainer"></div>
                
                <form id="appointmentForm">
                    <!-- Patient Information -->
                    <h4 class="section-title">
                        <i class="fas fa-user"></i>Patient Information
                    </h4>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Full Name *</label>
                            <input type="text" class="form-control" id="patient_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email Address *</label>
                            <input type="email" class="form-control" id="patient_email" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone Number *</label>
                            <input type="tel" class="form-control" id="patient_phone" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date of Birth</label>
                            <input type="date" class="form-control" id="date_of_birth">
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Gender</label>
                            <select class="form-select" id="gender">
                                <option value="other">Prefer not to say</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Blood Group</label>
                            <select class="form-select" id="blood_group">
                                <option value="">Select Blood Group</option>
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>
                            </select>
                        </div>
                    </div>

                    <!-- Appointment Details -->
                    <h4 class="section-title">
                        <i class="fas fa-stethoscope"></i>Appointment Details
                    </h4>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Select Doctor *</label>
                            <select class="form-select" id="doctor_id" required>
                                <option value="">Choose a doctor...</option>
                                <?php foreach($doctors as $doc): ?>
                                <option value="<?php echo $doc['id']; ?>" 
                                        <?php echo ($selected_doctor_id == $doc['id']) ? 'selected' : ''; ?>
                                        data-fee="<?php echo $doc['consultation_fee']; ?>">
                                    <?php echo htmlspecialchars($doc['name']); ?> - <?php echo htmlspecialchars($doc['specialization']); ?>
                                    ($<?php echo $doc['consultation_fee']; ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Preferred Date *</label>
                            <input type="date" class="form-control" id="appointment_date" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Preferred Time *</label>
                            <select class="form-select" id="appointment_time" required>
                                <option value="">Select time...</option>
                                <option value="09:00:00">9:00 AM</option>
                                <option value="09:30:00">9:30 AM</option>
                                <option value="10:00:00">10:00 AM</option>
                                <option value="10:30:00">10:30 AM</option>
                                <option value="11:00:00">11:00 AM</option>
                                <option value="11:30:00">11:30 AM</option>
                                <option value="14:00:00">2:00 PM</option>
                                <option value="14:30:00">2:30 PM</option>
                                <option value="15:00:00">3:00 PM</option>
                                <option value="15:30:00">3:30 PM</option>
                                <option value="16:00:00">4:00 PM</option>
                                <option value="16:30:00">4:30 PM</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Consultation Fee</label>
                            <div class="form-control bg-light" id="consultation_fee">Select a doctor first</div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Reason for Visit *</label>
                        <textarea class="form-control" id="reason" rows="3" 
                                  placeholder="Please describe your symptoms or reason for the appointment..." required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Address</label>
                        <textarea class="form-control" id="address" rows="2" 
                                  placeholder="Your full address..."></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Medical History / Allergies</label>
                        <textarea class="form-control" id="medical_history" rows="2" 
                                  placeholder="Any medical conditions, allergies, or current medications..."></textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                            <i class="fas fa-calendar-check me-2"></i>Book Appointment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px;">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-check-circle me-2"></i>Appointment Booked Successfully!</h5>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <i class="fas fa-calendar-check fa-4x text-success mb-3"></i>
                        <h4>Your appointment has been confirmed!</h4>
                    </div>
                    <div id="appointmentDetails"></div>
                    <div class="alert alert-info mt-3">
                        <h6><i class="fas fa-info-circle me-2"></i>What's next?</h6>
                        <ul class="mb-0">
                            <li>You will receive a confirmation email shortly</li>
                            <li>Please arrive 15 minutes before your appointment</li>
                            <li>Bring a valid ID and insurance card</li>
                            <li>Bring any relevant medical records</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="location.reload()">Book Another</button>
                    <button type="button" class="btn btn-primary" onclick="window.location.href='index.php'">Go Home</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('appointment_date').min = today;

        // Update consultation fee when doctor is selected
        document.getElementById('doctor_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const fee = selectedOption.getAttribute('data-fee');
            const feeDisplay = document.getElementById('consultation_fee');
            
            if (fee) {
                feeDisplay.textContent = '$' + fee;
                feeDisplay.classList.remove('bg-light');
                feeDisplay.classList.add('bg-success', 'text-white');
            } else {
                feeDisplay.textContent = 'Select a doctor first';
                feeDisplay.classList.remove('bg-success', 'text-white');
                feeDisplay.classList.add('bg-light');
            }
        });

        // Form submission
        document.getElementById('appointmentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Booking...';
            submitBtn.disabled = true;
            
            const formData = {
                patient_name: document.getElementById('patient_name').value,
                patient_email: document.getElementById('patient_email').value,
                patient_phone: document.getElementById('patient_phone').value,
                date_of_birth: document.getElementById('date_of_birth').value,
                gender: document.getElementById('gender').value,
                blood_group: document.getElementById('blood_group').value,
                doctor_id: document.getElementById('doctor_id').value,
                appointment_date: document.getElementById('appointment_date').value,
                appointment_time: document.getElementById('appointment_time').value,
                reason: document.getElementById('reason').value,
                address: document.getElementById('address').value,
                medical_history: document.getElementById('medical_history').value
            };

            fetch('process_appointment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                
                if (data.success) {
                    // Show appointment details in modal
                    document.getElementById('appointmentDetails').innerHTML = `
                        <div class="row">
                            <div class="col-md-6"><strong>Patient:</strong> ${data.appointment.patient_name}</div>
                            <div class="col-md-6"><strong>Doctor:</strong> ${data.appointment.doctor_name}</div>
                            <div class="col-md-6"><strong>Date:</strong> ${data.appointment.appointment_date}</div>
                            <div class="col-md-6"><strong>Time:</strong> ${data.appointment.appointment_time}</div>
                            <div class="col-12 mt-2"><strong>Appointment ID:</strong> #${data.appointment.id}</div>
                        </div>
                    `;
                    
                    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();
                } else {
                    showAlert('danger', data.message || 'Error booking appointment. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                showAlert('danger', 'An error occurred while booking the appointment. Please try again.');
            });
        });

        function showAlert(type, message) {
            const alertContainer = document.getElementById('alertContainer');
            alertContainer.innerHTML = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
        }
    </script>
</body>
</html>
