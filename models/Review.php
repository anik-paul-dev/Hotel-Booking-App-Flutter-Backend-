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

    public function getAllWithRoomName($roomId = null) {
        $query = "SELECT r.*, rm.name AS room_name, u.picture AS user_picture 
                  FROM $this->table r 
                  LEFT JOIN rooms rm ON r.room_id = rm.id 
                  LEFT JOIN users u ON r.user_id = u.firebase_uid";
        if ($roomId) $query .= " WHERE r.room_id = :room_id";
        $stmt = $this->conn->prepare($query);
        if ($roomId) $stmt->bindParam(':room_id', $roomId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecentHighRatedReviews($minRating = 4, $limit = 10) {
        $query = "SELECT r.*, rm.name AS room_name, u.picture AS user_picture 
                  FROM $this->table r 
                  LEFT JOIN rooms rm ON r.room_id = rm.id 
                  LEFT JOIN users u ON r.user_id = u.firebase_uid 
                  WHERE r.rating >= :minRating 
                  ORDER BY r.date DESC 
                  LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':minRating', $minRating);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
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
            ':room_id' => is_scalar($data['room_id']) ? $data['room_id'] : '',
            ':rating' => is_scalar($data['rating']) ? $data['rating'] : 0,
            ':review_text' => is_scalar($data['review_text']) ? $data['review_text'] : ''
        ];
        $stmt->execute($params);
        return $this->conn->lastInsertId();
    }

    public function delete($id) {
        $roomQuery = "SELECT room_id FROM $this->table WHERE id = :id";
        $roomStmt = $this->conn->prepare($roomQuery);
        $roomStmt->bindParam(':id', $id);
        $roomStmt->execute();
        $row = $roomStmt->fetch(PDO::FETCH_ASSOC);
        $roomId = $row ? $row['room_id'] : null;

        $query = "DELETE FROM $this->table WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $roomId ? $roomId : $stmt->rowCount();
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