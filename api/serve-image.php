<?php
// Log to a file for debugging
file_put_contents(__DIR__ . '/../debug.log', "Request: " . $_SERVER['REQUEST_URI'] . "\n", FILE_APPEND);

$filename = basename($_GET['file'] ?? '');
$filePath = __DIR__ . '/../uploads/' . $filename;

file_put_contents(__DIR__ . '/../debug.log', "Looking for: $filePath\n", FILE_APPEND);

if (!file_exists($filePath)) {
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Image not found', 'path' => $filePath]);
    file_put_contents(__DIR__ . '/../debug.log', "File not found: $filePath\n", FILE_APPEND);
    exit;
}

$extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
$mimeTypes = [
    'jpeg' => 'image/jpeg',
    'jpg' => 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif',
];
$contentType = $mimeTypes[$extension] ?? 'application/octet-stream';
header("Content-Type: $contentType");

header("Access-Control-Allow-Origin: http://localhost:64944");
header("Access-Control-Allow-Methods: GET");

file_put_contents(__DIR__ . '/../debug.log', "Serving: $filePath\n", FILE_APPEND);
readfile($filePath);
exit;
?>