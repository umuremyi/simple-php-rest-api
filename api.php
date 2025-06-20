<?php
// Simple REST API in PHP
// Store users in memory using a static array

// Enable CORS and set content type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Get the HTTP method
$method = $_SERVER['REQUEST_METHOD'];

// Static array to store users
static $users = [];

// Function to generate a unique ID (UUID v4)
function generate_uuid() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

// Parse the URL path
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path_parts = explode('/', trim($path, '/'));

// Basic routing
if ($path_parts === 'users') {
    if ($method === 'POST' && count($path_parts) === 1) {
        // Create user
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['name']) || !isset($input['email'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing name or email']);
            exit;
        }
        $id = generate_uuid();
        $user = ['id' => $id, 'name' => $input['name'], 'email' => $input['email']];
        $users[$id] = $user;
        http_response_code(201);
        echo json_encode($user);
        exit;
    } elseif ($method === 'GET' && count($path_parts) === 2) {
        // Get user by id
        $id = $path_parts;
        if (isset($users[$id])) {
            echo json_encode($users[$id]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
        }
        exit;
    }
}

// If no route matched
http_response_code(404);
echo json_encode(['error' => 'Not found']);
exit;
?>
