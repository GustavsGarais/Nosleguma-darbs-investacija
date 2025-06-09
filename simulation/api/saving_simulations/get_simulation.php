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

if (!$data->user_id) {
    echo json_encode(["success" => false, "message" => "Missing user ID."]);
    exit;
}

try {
    $stmt = $db->prepare("SELECT * FROM simulations WHERE user_id = ?");
    $stmt->execute([$data->user_id]);
    $simulations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Decode JSON settings for frontend use
    foreach ($simulations as &$sim) {
        $sim['settings'] = json_decode($sim['settings']);
    }

    echo json_encode(["success" => true, "simulations" => $simulations]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Error retrieving simulations."]);
}
?>
