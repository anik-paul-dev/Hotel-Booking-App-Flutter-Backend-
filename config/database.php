<?php

class Database {
    private $host = 'localhost';
    private $db_name = 'booking_hotel';
    private $username = 'root'; // Replace with your MySQL username
    private $password = '';     // Replace with your MySQL password
    public $conn;

    public function connect() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=$this->host;dbname=$this->db_name", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection Error: " . $e->getMessage();
        }
        return $this->conn;
    }
}

?>