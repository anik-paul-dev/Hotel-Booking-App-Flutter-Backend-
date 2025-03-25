<?php

class Room {
    private $conn;
    private $table = 'rooms';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM $this->table WHERE is_active = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllRooms() { // New method for all rooms
        $query = "SELECT * FROM $this->table";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM $this->table WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO $this->table (name, price_per_night, description, features, facilities, max_adults, max_children, rating, area, images, is_active) 
                  VALUES (:name, :price_per_night, :description, :features, :facilities, :max_adults, :max_children, :rating, :area, :images, :is_active)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':name' => $data['name'],
            ':price_per_night' => $data['price_per_night'],
            ':description' => $data['description'],
            ':features' => json_encode($data['features']),
            ':facilities' => json_encode($data['facilities']),
            ':max_adults' => $data['max_adults'],
            ':max_children' => $data['max_children'],
            ':rating' => $data['rating'],
            ':area' => $data['area'],
            ':images' => json_encode($data['images']),
            ':is_active' => $data['is_active']
        ]);
        return $this->conn->lastInsertId();
    }

    public function update($data) {
        $query = "UPDATE $this->table SET 
                  name = :name, 
                  price_per_night = :price_per_night, 
                  description = :description, 
                  features = :features, 
                  facilities = :facilities, 
                  max_adults = :max_adults, 
                  max_children = :max_children, 
                  rating = :rating, 
                  area = :area, 
                  images = :images, 
                  is_active = :is_active 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':id' => $data['id'],
            ':name' => $data['name'],
            ':price_per_night' => $data['price_per_night'],
            ':description' => $data['description'],
            ':features' => json_encode($data['features']),
            ':facilities' => json_encode($data['facilities']),
            ':max_adults' => $data['max_adults'],
            ':max_children' => $data['max_children'],
            ':rating' => $data['rating'],
            ':area' => $data['area'],
            ':images' => json_encode($data['images']),
            ':is_active' => $data['is_active']
        ]);
        return $stmt->rowCount();
    }

    public function updateStatus($id, $is_active) {
        $query = "UPDATE $this->table SET is_active = :is_active WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id, ':is_active' => $is_active]);
        return $stmt->rowCount();
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