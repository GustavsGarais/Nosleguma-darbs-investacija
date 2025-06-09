<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

require 'db_connect.php';

$data = json_decode(file_get_contents("php://input"));

if (
    !$data->user_id || !$data->sim_name || 
    !isset($data->initial_investment) || !isset($data->num_investors) ||
    !isset($data->growth_rate) || !isset($data->risk_appetite) || !isset($data->market_influence)
) {
    echo json_encode(["success" => false, "message" => "Missing fields."]);
    exit;
}

$stmt = $db->prepare("INSERT INTO simulations (user_id, sim_name, initial_investment, num_investors, growth_rate, risk_appetite, market_influence)
                      VALUES (?, ?, ?, ?, ?, ?, ?)");
$result = $stmt->execute([
    $data->user_id, $data->sim_name, $data->initial_investment, $data->num_investors,
    $data->growth_rate, $data->risk_appetite, $data->market_influence
]);

if ($result) {
    echo json_encode(["success" => true, "message" => "Simulation saved successfully."]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to save simulation."]);
}
?>
