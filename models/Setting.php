<?php

class Setting {
    private $conn;
    private $table = 'settings';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM $this->table";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($key, $value) {
        $query = "INSERT INTO $this->table (setting_key, setting_value) 
                  VALUES (:key, :value) 
                  ON DUPLICATE KEY UPDATE setting_value = :value";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':key' => $key, ':value' => $value]);
        return $stmt->rowCount();
    }
}

?>