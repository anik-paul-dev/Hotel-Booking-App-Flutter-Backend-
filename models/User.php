<?php
class User {
    private $conn;
    private $table = 'users';

    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function getAll() {
        $query = "SELECT id, firebase_uid, name, email, phone_number, role FROM $this->table";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO $this->table (firebase_uid, name, email, phone_number, role, picture, address, pincode, date_of_birth) 
                  VALUES (:firebase_uid, :name, :email, :phone_number, :role, :picture, :address, :pincode, :date_of_birth)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':firebase_uid' => $data['firebase_uid'],
            ':name' => $data['name'] ?? 'Unknown',
            ':email' => $data['email'],
            ':phone_number' => $data['phone_number'] ?? '',
            ':role' => $data['role'] ?? 'user',
            ':picture' => $data['picture'] ?? '',
            ':address' => $data['address'] ?? '',
            ':pincode' => $data['pincode'] ?? '',
            ':date_of_birth' => $data['date_of_birth'] ?? '',
        ]);
        return $this->conn->lastInsertId();
    }

    public function getByFirebaseUid($firebaseUid) {
        $query = "SELECT * FROM $this->table WHERE firebase_uid = :firebase_uid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':firebase_uid', $firebaseUid);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>