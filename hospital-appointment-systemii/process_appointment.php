<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

include_once 'config/database.php';
include_once 'classes/Patient.php';
include_once 'classes/Doctor.php';
include_once 'classes/Appointment.php';

$database = new Database();
$db = $database->getConnection();

$patient = new Patient($db);
$doctor = new Doctor($db);
$appointment = new Appointment($db);

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid input data']);
    exit;
}

// Validate required fields
$required_fields = ['patient_name', 'patient_email', 'patient_phone', 'doctor_id', 'appointment_date', 'appointment_time', 'reason'];
foreach ($required_fields as $field) {
    if (empty($input[$field])) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
        exit;
    }
}

try {
    // Check if time slot is available
    if (!$appointment->isTimeSlotAvailable($input['doctor_id'], $input['appointment_date'], $input['appointment_time'])) {
        echo json_encode(['success' => false, 'message' => 'This time slot is already booked. Please choose another time.']);
        exit;
    }

    // Check if patient exists
    $patient->email = $input['patient_email'];
    $existing_patient = $patient->findByEmail();
    
    if ($existing_patient) {
        $patient_id = $existing_patient['id'];
    } else {
        // Create new patient
        $patient->name = $input['patient_name'];
        $patient->email = $input['patient_email'];
        $patient->phone = $input['patient_phone'];
        $patient->date_of_birth = !empty($input['date_of_birth']) ? $input['date_of_birth'] : '1990-01-01';
        $patient->gender = $input['gender'] ?? 'other';
        $patient->address = $input['address'] ?? 'Not provided';
        $patient->blood_group = $input['blood_group'] ?? '';
        $patient->medical_history = $input['medical_history'] ?? '';
        $patient->emergency_contact = '';
        $patient->emergency_phone = '';
        $patient->allergies = '';

        if ($patient->create()) {
            $patient_id = $db->lastInsertId();
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create patient record']);
            exit;
        }
    }

    // Create appointment
    $appointment->patient_id = $patient_id;
    $appointment->doctor_id = $input['doctor_id'];
    $appointment->appointment_date = $input['appointment_date'];
    $appointment->appointment_time = $input['appointment_time'];
    $appointment->reason = $input['reason'];
    $appointment->status = 'scheduled';

    if ($appointment->create()) {
        $appointment_id = $db->lastInsertId();
        
        // Get doctor details for response
        $doctor_details = $doctor->getById($input['doctor_id']);
        
        // Format time for display
        $time_formatted = date('g:i A', strtotime($input['appointment_time']));
        $date_formatted = date('F j, Y', strtotime($input['appointment_date']));
        
        echo json_encode([
            'success' => true,
            'message' => 'Appointment booked successfully!',
            'appointment' => [
                'id' => $appointment_id,
                'patient_name' => $input['patient_name'],
                'doctor_name' => $doctor_details['name'],
                'appointment_date' => $date_formatted,
                'appointment_time' => $time_formatted
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to book appointment']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>
