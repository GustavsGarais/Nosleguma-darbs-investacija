<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json");

require 'db_connect.php';

$stmt = $db->prepare("SELECT username FROM users WHERE last_active >= datetime('now', '-5 minutes')");
$stmt->execute();
$onlineUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(["success" => true, "online_users" => $onlineUsers]);
?>
