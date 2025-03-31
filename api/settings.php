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
                'image_url' => '', // Added default value
                'created_at' => date('Y-m-d H:i:s'), // Default for new record
                'updated_at' => date('Y-m-d H:i:s'), // Default for new record
            ];
        }
        $settings['contacts'] = json_decode($settings['contacts'] ?? '[]', true) ?? [];
        $settings['social_media_links'] = json_decode($settings['social_media_links'] ?? '[]', true) ?? [];
        $settings['team_members'] = json_decode($settings['team_members'] ?? '[]', true) ?? [];
        echo json_encode($settings);
        break;

    case 'PUT':
        authenticate(true); // Admin only
        error_log("Authenticated user role: " . ($authData['role'] ?? 'unknown')); // Safe logging
        $data = json_decode(file_get_contents("php://input"), true) ?? [];
        $query = "INSERT INTO settings (
                    id, website_title, about_us_description, shutdown_mode, 
                    contacts, social_media_links, team_members,
                    website_name, phone_number, email, address, facebook, twitter, instagram, image_url,
                    created_at
                  ) VALUES (
                    1, :title, :about, :shutdown, :contacts, :social, :team,
                    :website_name, :phone, :email, :address, :facebook, :twitter, :instagram, :image_url,
                    NOW()
                  ) ON DUPLICATE KEY UPDATE 
                    website_title = :title, about_us_description = :about, shutdown_mode = :shutdown, 
                    contacts = :contacts, social_media_links = :social, team_members = :team,
                    website_name = :website_name, phone_number = :phone, email = :email, 
                    address = :address, facebook = :facebook, twitter = :twitter, instagram = :instagram,
                    image_url = :image_url, updated_at = NOW()";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':title', $data['website_title'] ?? '', PDO::PARAM_STR);
        $stmt->bindValue(':about', $data['about_us_description'] ?? '', PDO::PARAM_STR);
        $stmt->bindValue(':shutdown', $data['shutdown_mode'] ?? 0, PDO::PARAM_INT);
        $stmt->bindValue(':contacts', json_encode($data['contacts'] ?? []), PDO::PARAM_STR);
        $stmt->bindValue(':social', json_encode($data['social_media_links'] ?? []), PDO::PARAM_STR);
        $stmt->bindValue(':team', json_encode($data['team_members'] ?? []), PDO::PARAM_STR);
        $stmt->bindValue(':website_name', $data['website_name'] ?? '', PDO::PARAM_STR);
        $stmt->bindValue(':phone', $data['phone_number'] ?? '', PDO::PARAM_STR);
        $stmt->bindValue(':email', $data['email'] ?? '', PDO::PARAM_STR);
        $stmt->bindValue(':address', $data['address'] ?? '', PDO::PARAM_STR);
        $stmt->bindValue(':facebook', $data['facebook'] ?? '', PDO::PARAM_STR);
        $stmt->bindValue(':twitter', $data['twitter'] ?? '', PDO::PARAM_STR);
        $stmt->bindValue(':instagram', $data['instagram'] ?? '', PDO::PARAM_STR);
        $stmt->bindValue(':image_url', $data['image_url'] ?? '', PDO::PARAM_STR); // Added binding
        $stmt->execute();
        echo json_encode(['message' => 'Settings updated']);
        break;
}
?>