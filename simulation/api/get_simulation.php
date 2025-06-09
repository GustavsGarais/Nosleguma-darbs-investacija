<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require 'db_connect.php';

$user_id = $_GET['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(["success" => false, "message" => "Missing user_id."]);
    exit;
}

$stmt = $db->prepare("SELECT * FROM simulations WHERE user_id = ?");
$stmt->execute([$user_id]);
$simulations = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(["success" => true, "simulations" => $simulations]);
?>
