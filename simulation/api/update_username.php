<?php
header('Content-Type: application/json');
include 'db_connection.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['user_id']) || !isset($data['new_username'])) {
    echo json_encode(['success' => false, 'message' => 'Missing data.']);
    exit;
}

$user_id = $data['user_id'];
$new_username = $data['new_username'];

$stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
if ($stmt->execute([$new_username, $user_id])) {
    echo json_encode(['success' => true, 'message' => 'Username updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update username.']);
}
?>
