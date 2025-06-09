<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require 'db_connect.php';

$data = json_decode(file_get_contents("php://input"));

if (!$data->user_id || !$data->name || !$data->settings) {
    echo json_encode(["success" => false, "message" => "Missing fields."]);
    exit;
}

try {
    $stmt = $db->prepare("INSERT INTO simulations (user_id, name, settings) VALUES (?, ?, ?)");
    $stmt->execute([$data->user_id, $data->name, json_encode($data->settings)]);
    echo json_encode(["success" => true, "message" => "Simulation saved."]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Error saving simulation: " . $e->getMessage()]);
}
?>
