<?php
class Patient {
    private $conn;
    private $table_name = "patients";

    public $id;
    public $name;
    public $email;
    public $phone;
    public $date_of_birth;
    public $gender;
    public $address;
    public $emergency_contact;
    public $emergency_phone;
    public $blood_group;
    public $allergies;
    public $medical_history;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create patient
    function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET name=:name, email=:email, phone=:phone, 
                      date_of_birth=:date_of_birth, gender=:gender, 
                      address=:address, emergency_contact=:emergency_contact, 
                      emergency_phone=:emergency_phone, blood_group=:blood_group, 
                      allergies=:allergies, medical_history=:medical_history";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->date_of_birth = htmlspecialchars(strip_tags($this->date_of_birth));
        $this->gender = htmlspecialchars(strip_tags($this->gender));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->emergency_contact = htmlspecialchars(strip_tags($this->emergency_contact));
        $this->emergency_phone = htmlspecialchars(strip_tags($this->emergency_phone));
        $this->blood_group = htmlspecialchars(strip_tags($this->blood_group));
        $this->allergies = htmlspecialchars(strip_tags($this->allergies));
        $this->medical_history = htmlspecialchars(strip_tags($this->medical_history));

        // Bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":date_of_birth", $this->date_of_birth);
        $stmt->bindParam(":gender", $this->gender);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":emergency_contact", $this->emergency_contact);
        $stmt->bindParam(":emergency_phone", $this->emergency_phone);
        $stmt->bindParam(":blood_group", $this->blood_group);
        $stmt->bindParam(":allergies", $this->allergies);
        $stmt->bindParam(":medical_history", $this->medical_history);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Find patient by email
    function findByEmail() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    // Get patient by ID
    function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }
}
?>
