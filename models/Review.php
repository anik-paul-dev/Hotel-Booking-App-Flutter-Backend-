<?php

class Review {
    private $conn;
    private $table = 'reviews';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll($roomId = null) {
        $query = "SELECT * FROM $this->table";
        if ($roomId) $query .= " WHERE room_id = :room_id";
        $stmt = $this->conn->prepare($query);
        if ($roomId) $stmt->bindParam(':room_id', $roomId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO $this->table (user_id, user_name, room_id, rating, review_text) 
                  VALUES (:user_id, :user_name, :room_id, :rating, :review_text)";
        $stmt = $this->conn->prepare($query);
        $params = [
            ':user_id' => is_scalar($data['user_id']) ? $data['user_id'] : '',
            ':user_name' => is_scalar($data['user_name']) ? $data['user_name'] : '',
            ':room_id' => is_scalar($data['room_id']) ? $data['room_id'] : '', // Fixed
            ':rating' => is_scalar($data['rating']) ? $data['rating'] : 0,
            ':review_text' => is_scalar($data['review_text']) ? $data['review_text'] : ''
        ];
        $stmt->execute($params);
        return $this->conn->lastInsertId();
    }

    public function delete($id) {
        $query = "DELETE FROM $this->table WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function getAverageRating($roomId) {
        $query = "SELECT AVG(rating) as avg_rating FROM $this->table WHERE room_id = :room_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':room_id', $roomId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['avg_rating'] ?? 0;
    }
}

?>