<?php
include_once 'auth_check.php';
include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

header('Content-Type: application/json');

if ($_POST) {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add_department':
            $query = "INSERT INTO departments (name, description) VALUES (:name, :description)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':name', $_POST['name']);
            $stmt->bindParam(':description', $_POST['description']);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Department added successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add department']);
            }
            break;
            
        case 'get_department':
            $dept_id = $_POST['department_id'] ?? '';
            $query = "SELECT * FROM departments WHERE id = :id";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $dept_id);
            $stmt->execute();
            
            if ($department = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo json_encode(['success' => true, 'department' => $department]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Department not found']);
            }
            break;
            
        case 'update_department':
            $dept_id = $_POST['department_id'] ?? '';
            $query = "UPDATE departments SET name = :name, description = :description WHERE id = :id";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':name', $_POST['name']);
            $stmt->bindParam(':description', $_POST['description']);
            $stmt->bindParam(':id', $dept_id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Department updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update department']);
            }
            break;
            
        case 'delete_department':
            $dept_id = $_POST['department_id'] ?? '';
            
            // Check if department has doctors
            $check_query = "SELECT COUNT(*) FROM doctors WHERE department_id = :id";
            $check_stmt = $db->prepare($check_query);
            $check_stmt->bindParam(':id', $dept_id);
            $check_stmt->execute();
            $doctor_count = $check_stmt->fetchColumn();
            
            if ($doctor_count > 0) {
                echo json_encode(['success' => false, 'message' => 'Cannot delete department with assigned doctors']);
                break;
            }
            
            $query = "DELETE FROM departments WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $dept_id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Department deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete department']);
            }
            break;
    }
}
?>
