<?php
include_once 'auth_check.php';
include_once '../config/database.php';
include_once '../classes/Appointment.php';

$database = new Database();
$db = $database->getConnection();

header('Content-Type: application/json');

if ($_POST) {
    $action = $_POST['action'] ?? '';
    $appointment_id = $_POST['appointment_id'] ?? '';
    
    switch ($action) {
        case 'update_status':
            $new_status = $_POST['new_status'] ?? '';
            
            $query = "UPDATE appointments SET status = :status WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':status', $new_status);
            $stmt->bindParam(':id', $appointment_id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Appointment status updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update appointment status']);
            }
            break;
            
        case 'get_appointment':
            $query = "SELECT a.*, p.name as patient_name, p.email as patient_email, p.phone as patient_phone,
                             p.date_of_birth, p.gender, p.address, p.blood_group, p.medical_history,
                             d.name as doctor_name, d.specialization, d.consultation_fee
                      FROM appointments a
                      LEFT JOIN patients p ON a.patient_id = p.id
                      LEFT JOIN doctors d ON a.doctor_id = d.id
                      WHERE a.id = :id";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $appointment_id);
            $stmt->execute();
            
            if ($appointment = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo json_encode(['success' => true, 'appointment' => $appointment]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Appointment not found']);
            }
            break;
            
        case 'delete_appointment':
            $query = "DELETE FROM appointments WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $appointment_id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Appointment deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete appointment']);
            }
            break;
    }
}
?>
