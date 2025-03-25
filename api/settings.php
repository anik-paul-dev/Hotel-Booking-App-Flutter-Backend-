<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/auth.php';

$db = (new Database())->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $query = "SELECT * FROM settings WHERE id = 1";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $settings = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$settings) {
            $settings = [
                'id' => 1,
                'website_title' => '',
                'about_us_description' => '',
                'shutdown_mode' => 0,
                'contacts' => '[]',
                'social_media_links' => '[]',
                'team_members' => '[]',
                'website_name' => '',
                'phone_number' => '',
                'email' => '',
                'address' => '',
                'facebook' => '',
                'twitter' => '',
                'instagram' => '',
            ];
        } else {
            $settings['contacts'] = json_decode($settings['contacts'], true);
            $settings['social_media_links'] = json_decode($settings['social_media_links'], true);
            $settings['team_members'] = json_decode($settings['team_members'], true);
        }
        echo json_encode($settings);
        break;

    case 'PUT':
        authenticate(true); // Admin only
        error_log("Authenticated user role: " . $authData['role']); //later
        $data = json_decode(file_get_contents("php://input"), true);
        $query = "INSERT INTO settings (
                    id, website_title, about_us_description, shutdown_mode, 
                    contacts, social_media_links, team_members,
                    website_name, phone_number, email, address, facebook, twitter, instagram
                  ) VALUES (
                    1, :title, :about, :shutdown, :contacts, :social, :team,
                    :website_name, :phone, :email, :address, :facebook, :twitter, :instagram
                  ) ON DUPLICATE KEY UPDATE 
                    website_title = :title, about_us_description = :about, shutdown_mode = :shutdown, 
                    contacts = :contacts, social_media_links = :social, team_members = :team,
                    website_name = :website_name, phone_number = :phone, email = :email, 
                    address = :address, facebook = :facebook, twitter = :twitter, instagram = :instagram";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':title', $data['website_title']);
        $stmt->bindParam(':about', $data['about_us_description']);
        $stmt->bindParam(':shutdown', $data['shutdown_mode'], PDO::PARAM_INT);
        $stmt->bindParam(':contacts', json_encode($data['contacts']));
        $stmt->bindParam(':social', json_encode($data['social_media_links']));
        $stmt->bindParam(':team', json_encode($data['team_members']));
        $stmt->bindParam(':website_name', $data['website_name']);
        $stmt->bindParam(':phone', $data['phone_number']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':address', $data['address']);
        $stmt->bindParam(':facebook', $data['facebook']);
        $stmt->bindParam(':twitter', $data['twitter']);
        $stmt->bindParam(':instagram', $data['instagram']);
        $stmt->execute();
        echo json_encode(['message' => 'Settings updated']);
        break;
}
?>