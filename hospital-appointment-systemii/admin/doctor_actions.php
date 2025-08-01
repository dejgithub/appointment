<?php
include_once 'auth_check.php';
include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

header('Content-Type: application/json');

if ($_POST) {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add_doctor':
            $query = "INSERT INTO doctors (name, specialization, email, phone, experience_years, qualification, consultation_fee, department_id, status) 
                      VALUES (:name, :specialization, :email, :phone, :experience_years, :qualification, :consultation_fee, :department_id, :status)";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':name', $_POST['name']);
            $stmt->bindParam(':specialization', $_POST['specialization']);
            $stmt->bindParam(':email', $_POST['email']);
            $stmt->bindParam(':phone', $_POST['phone']);
            $stmt->bindParam(':experience_years', $_POST['experience_years']);
            $stmt->bindParam(':qualification', $_POST['qualification']);
            $stmt->bindParam(':consultation_fee', $_POST['consultation_fee']);
            $stmt->bindParam(':department_id', $_POST['department_id'] ?: null);
            $stmt->bindParam(':status', $_POST['status']);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Doctor added successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add doctor']);
            }
            break;
            
        case 'get_doctor':
            $doctor_id = $_POST['doctor_id'] ?? '';
            $query = "SELECT d.*, dep.name as department_name FROM doctors d 
                      LEFT JOIN departments dep ON d.department_id = dep.id 
                      WHERE d.id = :id";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $doctor_id);
            $stmt->execute();
            
            if ($doctor = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo json_encode(['success' => true, 'doctor' => $doctor]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Doctor not found']);
            }
            break;
            
        case 'update_doctor':
            $doctor_id = $_POST['doctor_id'] ?? '';
            $query = "UPDATE doctors SET 
                      name = :name, 
                      specialization = :specialization, 
                      email = :email, 
                      phone = :phone, 
                      experience_years = :experience_years, 
                      qualification = :qualification, 
                      consultation_fee = :consultation_fee, 
                      department_id = :department_id, 
                      status = :status 
                      WHERE id = :id";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':name', $_POST['name']);
            $stmt->bindParam(':specialization', $_POST['specialization']);
            $stmt->bindParam(':email', $_POST['email']);
            $stmt->bindParam(':phone', $_POST['phone']);
            $stmt->bindParam(':experience_years', $_POST['experience_years']);
            $stmt->bindParam(':qualification', $_POST['qualification']);
            $stmt->bindParam(':consultation_fee', $_POST['consultation_fee']);
            $stmt->bindParam(':department_id', $_POST['department_id'] ?: null);
            $stmt->bindParam(':status', $_POST['status']);
            $stmt->bindParam(':id', $doctor_id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Doctor updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update doctor']);
            }
            break;
            
        case 'update_status':
            $doctor_id = $_POST['doctor_id'] ?? '';
            $new_status = $_POST['new_status'] ?? '';
            
            $query = "UPDATE doctors SET status = :status WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':status', $new_status);
            $stmt->bindParam(':id', $doctor_id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Doctor status updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update doctor status']);
            }
            break;
            
        case 'delete_doctor':
            $doctor_id = $_POST['doctor_id'] ?? '';
            
            // Check if doctor has appointments
            $check_query = "SELECT COUNT(*) FROM appointments WHERE doctor_id = :id AND status IN ('scheduled', 'confirmed')";
            $check_stmt = $db->prepare($check_query);
            $check_stmt->bindParam(':id', $doctor_id);
            $check_stmt->execute();
            $active_appointments = $check_stmt->fetchColumn();
            
            if ($active_appointments > 0) {
                echo json_encode(['success' => false, 'message' => 'Cannot delete doctor with active appointments']);
                break;
            }
            
            $query = "DELETE FROM doctors WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $doctor_id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Doctor deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete doctor']);
            }
            break;
    }
}
?>
