<?php
class Doctor {
    private $conn;
    private $table_name = "doctors";

    public $id;
    public $name;
    public $specialization;
    public $email;
    public $phone;
    public $experience_years;
    public $qualification;
    public $consultation_fee;
    public $image_url;
    public $status;
    public $department_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Read all active doctors
    function read() {
        $query = "SELECT d.*, dep.name as department_name 
                  FROM " . $this->table_name . " d
                  LEFT JOIN departments dep ON d.department_id = dep.id
                  WHERE d.status = 'active'
                  ORDER BY d.name";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Get doctor by ID
    function getById($id) {
        $query = "SELECT d.*, dep.name as department_name 
                  FROM " . $this->table_name . " d
                  LEFT JOIN departments dep ON d.department_id = dep.id
                  WHERE d.id = :id AND d.status = 'active'
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    // Get doctors by department
    function getByDepartment($department_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE department_id = :department_id AND status = 'active'
                  ORDER BY name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":department_id", $department_id);
        $stmt->execute();
        return $stmt;
    }
}
?>
