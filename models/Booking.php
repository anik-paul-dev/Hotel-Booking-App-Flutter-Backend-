<?php
class Booking {
    private $conn;
    private $table = 'bookings';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll($userId = null, $newOnly = false) {
        $query = "SELECT * FROM $this->table";
        if ($userId) $query .= " WHERE user_id = :user_id";
        if ($newOnly) $query .= ($userId ? " AND" : " WHERE") . " status = 'booked' AND booking_date > DATE_SUB(NOW(), INTERVAL 7 DAY)";
        $query .= " ORDER BY booking_date DESC"; // Added for consistent sorting
        $stmt = $this->conn->prepare($query);
        if ($userId) $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO $this->table (user_id, room_id, room_name, price_per_night, check_in, check_out, total_amount, order_id, status, booking_date) 
                  VALUES (:user_id, :room_id, :room_name, :price_per_night, :check_in, :check_out, :total_amount, :order_id, :status, NOW())";
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
}
?>