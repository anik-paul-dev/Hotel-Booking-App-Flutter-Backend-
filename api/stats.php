<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/auth.php';

$db = (new Database())->connect();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $roomsCount = $db->query("SELECT COUNT(*) as count FROM rooms")->fetch(PDO::FETCH_ASSOC)['count'];
    $customersCount = $db->query("SELECT COUNT(*) as count FROM users")->fetch(PDO::FETCH_ASSOC)['count'];
    $reviewsCount = $db->query("SELECT COUNT(*) as count FROM reviews")->fetch(PDO::FETCH_ASSOC)['count'];
    // Replace staff count with team_members count from settings
    $settings = $db->query("SELECT team_members FROM settings WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
    $teamMembersCount = $settings ? count(json_decode($settings['team_members'], true)) : 0;

    $stats = [
        'rooms' => "$roomsCount+",
        'customers' => "$customersCount+",
        'reviews' => "$reviewsCount+",
        'team_members' => "$teamMembersCount+" // Changed from 'staff' to 'team_members'
    ];
    echo json_encode($stats);
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}
?>