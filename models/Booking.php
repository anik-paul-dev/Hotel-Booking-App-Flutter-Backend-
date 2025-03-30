<?php
class Booking {
    private $conn;
    private $table = 'bookings';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll($userId = null, $newOnly = false, $roomId = null) {
        $query = "SELECT * FROM $this->table";
        $conditions = [];
        if ($userId) $conditions[] = "user_id = :user_id";
        if ($roomId) $conditions[] = "room_id = :room_id";
        if ($newOnly) $conditions[] = "status = 'booked' AND booking_date > DATE_SUB(NOW(), INTERVAL 7 DAY)";
        if (!empty($conditions)) $query .= " WHERE " . implode(" AND ", $conditions);
        $query .= " ORDER BY booking_date DESC";
        $stmt = $this->conn->prepare($query);
        if ($userId) $stmt->bindParam(':user_id', $userId);
        if ($roomId) $stmt->bindParam(':room_id', $roomId);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Query executed: $query");
        error_log("Results count: " . count($results));
        return $results;
    }

    public function create($data) {
        $query = "INSERT INTO $this->table (user_id, room_id, room_name, price_per_night, check_in, check_out, total_amount, order_id, status, booking_date, is_assigned) 
                  VALUES (:user_id, :room_id, :room_name, :price_per_night, :check_in, :check_out, :total_amount, :order_id, :status, NOW(), :is_assigned)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->bindParam(':room_id', $data['room_id']);
        $stmt->bindParam(':room_name', $data['room_name']);
        $stmt->bindParam(':price_per_night', $data['price_per_night']);
        $stmt->bindParam(':check_in', $data['check_in']);
        $stmt->bindParam(':check_out', $data['check_out']);
        $stmt->bindParam(':total_amount', $data['total_amount']);
        $stmt->bindParam(':order_id', $data['order_id']);
        $stmt->bindParam(':status', $data['status']);
        $isAssigned = 0; // Default to not assigned
        $stmt->bindParam(':is_assigned', $isAssigned);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    public function cancel($id) {
        $query = "UPDATE $this->table SET status = 'cancelled' WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function assign($id) { // New method
        $query = "UPDATE $this->table SET is_assigned = 1 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->rowCount();
    }
}
?>