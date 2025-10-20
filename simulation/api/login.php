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

if (!$data || empty($data->username) || empty($data->password)) {
    echo json_encode(["success" => false, "message" => "Missing fields."]);
    exit;
}

$stmt = $db->prepare("SELECT id, username FROM users WHERE username = ? AND password = ?");
$stmt->execute([$data->username, $data->password]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    // Update last_active if the column exists
    try {
        $updateStmt = $db->prepare("UPDATE users SET last_active = datetime('now') WHERE id = ?");
        $updateStmt->execute([$user['id']]);
    } catch (PDOException $e) {
        // ignore if column doesn't exist
    }

    echo json_encode([
        "success" => true,
        "message" => "Login successful.",
        "user_id" => (int)$user['id'],
        "username" => $user['username']
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Invalid username or password."]);
}

?>
