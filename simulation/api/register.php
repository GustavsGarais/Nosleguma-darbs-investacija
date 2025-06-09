<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json");

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require 'db_connect.php';

$data = json_decode(file_get_contents("php://input"));

if (!$data->username || !$data->password) {
    echo json_encode(["success" => false, "message" => "Missing fields."]);
    exit;
}

try {
    $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->execute([$data->username, $data->password]);
    echo json_encode(["success" => true, "message" => "Registered successfully."]);
} catch (PDOException $e) {
    if (str_contains($e->getMessage(), 'UNIQUE')) {
        echo json_encode(["success" => false, "message" => "Username already exists."]);
    } else {
        echo json_encode(["success" => false, "message" => "Database error."]);
    }
}
?>
