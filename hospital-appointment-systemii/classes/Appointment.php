<?php
class Appointment {
    private $conn;
    private $table_name = "appointments";

    public $id;
    public $patient_id;
    public $doctor_id;
    public $appointment_date;
    public $appointment_time;
    public $reason;
    public $status;
    public $notes;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create appointment
    function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET patient_id=:patient_id, doctor_id=:doctor_id, 
                      appointment_date=:appointment_date, appointment_time=:appointment_time, 
                      reason=:reason, status=:status";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->patient_id = htmlspecialchars(strip_tags($this->patient_id));
        $this->doctor_id = htmlspecialchars(strip_tags($this->doctor_id));
        $this->appointment_date = htmlspecialchars(strip_tags($this->appointment_date));
        $this->appointment_time = htmlspecialchars(strip_tags($this->appointment_time));
        $this->reason = htmlspecialchars(strip_tags($this->reason));
        $this->status = htmlspecialchars(strip_tags($this->status));

        // Bind values
        $stmt->bindParam(":patient_id", $this->patient_id);
        $stmt->bindParam(":doctor_id", $this->doctor_id);
        $stmt->bindParam(":appointment_date", $this->appointment_date);
        $stmt->bindParam(":appointment_time", $this->appointment_time);
        $stmt->bindParam(":reason", $this->reason);
        $stmt->bindParam(":status", $this->status);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Read appointments
    function read() {
        $query = "SELECT a.id, a.appointment_date, a.appointment_time, a.reason, a.status,
                         p.name as patient_name, p.phone as patient_phone, p.email as patient_email,
                         d.name as doctor_name, d.specialization
                  FROM " . $this->table_name . " a
                  LEFT JOIN patients p ON a.patient_id = p.id
                  LEFT JOIN doctors d ON a.doctor_id = d.id
                  ORDER BY a.appointment_date DESC, a.appointment_time DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Get appointments by patient
    function getByPatient($patient_id) {
        $query = "SELECT a.*, d.name as doctor_name, d.specialization
                  FROM " . $this->table_name . " a
                  LEFT JOIN doctors d ON a.doctor_id = d.id
                  WHERE a.patient_id = :patient_id
                  ORDER BY a.appointment_date DESC, a.appointment_time DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":patient_id", $patient_id);
        $stmt->execute();
        return $stmt;
    }

    // Check if time slot is available
    function isTimeSlotAvailable($doctor_id, $date, $time) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " 
                  WHERE doctor_id = :doctor_id 
                  AND appointment_date = :appointment_date 
                  AND appointment_time = :appointment_time 
                  AND status != 'cancelled'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":doctor_id", $doctor_id);
        $stmt->bindParam(":appointment_date", $date);
        $stmt->bindParam(":appointment_time", $time);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'] == 0;
    }

    // Update appointment status
    function updateStatus() {
        $query = "UPDATE " . $this->table_name . " 
                  SET status = :status 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
