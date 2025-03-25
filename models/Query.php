<?php
class Query {
    private $conn;
    private $table = 'queries';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM $this->table";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markAsRead($id, $isRead) {
        $query = "UPDATE $this->table SET is_read = :is_read WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':is_read', $isRead, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM $this->table WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    public function create($name, $email, $phone, $subject, $message, $date, $isRead) {
        $query = "INSERT INTO $this->table (name, email, phone, subject, message, date, is_read) 
                  VALUES (:name, :email, :phone, :subject, :message, :date, :is_read)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':subject', $subject);
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':is_read', $isRead, PDO::PARAM_INT);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }
}
?>