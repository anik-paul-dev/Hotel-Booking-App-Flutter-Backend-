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
        $stmt->execute([
            ':user_id' => $data['user_id'],
            ':user_name' => $data['user_name'],
            ':room_id' => $data['room_id'],
            ':rating' => $data['rating'],
            ':review_text' => $data['review_text']
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