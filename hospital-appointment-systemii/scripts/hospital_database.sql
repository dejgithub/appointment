-- Create database
CREATE DATABASE IF NOT EXISTS hospital_appointment_system;
USE hospital_appointment_system;

-- Doctors table
CREATE TABLE doctors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    specialization VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(15) NOT NULL,
    experience_years INT NOT NULL,
    qualification VARCHAR(200) NOT NULL,
    consultation_fee DECIMAL(10,2) NOT NULL,
    image_url VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Patients table
CREATE TABLE patients (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(15) NOT NULL,
    date_of_birth DATE NOT NULL,
    gender ENUM('male', 'female', 'other') NOT NULL,
    address TEXT NOT NULL,
    emergency_contact VARCHAR(100),
    emergency_phone VARCHAR(15),
    blood_group VARCHAR(5),
    allergies TEXT,
    medical_history TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Appointments table
CREATE TABLE appointments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    reason TEXT NOT NULL,
    status ENUM('scheduled', 'confirmed', 'completed', 'cancelled') DEFAULT 'scheduled',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);

-- Doctor schedules table
CREATE TABLE doctor_schedules (
    id INT PRIMARY KEY AUTO_INCREMENT,
    doctor_id INT NOT NULL,
    day_of_week ENUM('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday') NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);

-- Departments table
CREATE TABLE departments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    head_doctor_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (head_doctor_id) REFERENCES doctors(id)
);

-- Add department_id to doctors table
ALTER TABLE doctors ADD COLUMN department_id INT,
ADD FOREIGN KEY (department_id) REFERENCES departments(id);

-- Insert sample data
INSERT INTO departments (name, description) VALUES
('Cardiology', 'Heart and cardiovascular diseases'),
('Neurology', 'Brain and nervous system disorders'),
('Orthopedics', 'Bone and joint problems'),
('Pediatrics', 'Child healthcare'),
('Dermatology', 'Skin diseases and conditions'),
('General Medicine', 'General health and wellness');

INSERT INTO doctors (name, specialization, email, phone, experience_years, qualification, consultation_fee, department_id) VALUES
('Dr. Sarah Johnson', 'Cardiologist', 'sarah.johnson@hospital.com', '+1234567890', 15, 'MD, FACC', 150.00, 1),
('Dr. Michael Chen', 'Neurologist', 'michael.chen@hospital.com', '+1234567891', 12, 'MD, PhD', 200.00, 2),
('Dr. Emily Davis', 'Orthopedic Surgeon', 'emily.davis@hospital.com', '+1234567892', 10, 'MD, MS Ortho', 180.00, 3),
('Dr. Robert Wilson', 'Pediatrician', 'robert.wilson@hospital.com', '+1234567893', 8, 'MD, DCH', 120.00, 4),
('Dr. Lisa Anderson', 'Dermatologist', 'lisa.anderson@hospital.com', '+1234567894', 7, 'MD, DDV', 130.00, 5),
('Dr. James Brown', 'General Physician', 'james.brown@hospital.com', '+1234567895', 20, 'MBBS, MD', 100.00, 6);

INSERT INTO doctor_schedules (doctor_id, day_of_week, start_time, end_time) VALUES
(1, 'monday', '09:00:00', '17:00:00'),
(1, 'wednesday', '09:00:00', '17:00:00'),
(1, 'friday', '09:00:00', '17:00:00'),
(2, 'tuesday', '10:00:00', '18:00:00'),
(2, 'thursday', '10:00:00', '18:00:00'),
(3, 'monday', '08:00:00', '16:00:00'),
(3, 'tuesday', '08:00:00', '16:00:00'),
(3, 'wednesday', '08:00:00', '16:00:00'),
(4, 'monday', '09:00:00', '17:00:00'),
(4, 'tuesday', '09:00:00', '17:00:00'),
(4, 'wednesday', '09:00:00', '17:00:00'),
(4, 'thursday', '09:00:00', '17:00:00'),
(4, 'friday', '09:00:00', '17:00:00'),
(5, 'tuesday', '10:00:00', '18:00:00'),
(5, 'friday', '10:00:00', '18:00:00'),
(6, 'monday', '08:00:00', '20:00:00'),
(6, 'tuesday', '08:00:00', '20:00:00'),
(6, 'wednesday', '08:00:00', '20:00:00'),
(6, 'thursday', '08:00:00', '20:00:00'),
(6, 'friday', '08:00:00', '20:00:00');
