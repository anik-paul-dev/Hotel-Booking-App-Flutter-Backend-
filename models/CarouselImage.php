<?php

class CarouselImage {
    private $conn;
    private $table = 'carousel_images';

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
        $query = "INSERT INTO $this->table (image_url) VALUES (:image_url)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':image_url', $data['image_url']);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    public function delete($id) {
        $query = "DELETE FROM $this->table WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }
}

?>