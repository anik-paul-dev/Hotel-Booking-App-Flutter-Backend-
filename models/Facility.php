<?php

class Facility {
    private $conn;
    private $table = 'facilities';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM $this->table";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO $this->table (name, description, image) VALUES (:name, :description, :image)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':name' => $data['name'],
            ':description' => $data['description'],
            ':image' => $data['image']
        ]);
        return $this->conn->lastInsertId();
    }

    public function delete($id) {
        $query = "DELETE FROM $this->table WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->rowCount();
    }
}

?>