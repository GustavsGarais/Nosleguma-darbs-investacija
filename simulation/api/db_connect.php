<?php
$pdo = new PDO('sqlite:../database.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
try {
    $db = new PDO('sqlite:' . __DIR__ . '/database.db');  // ← This is important
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Optional: Create users table if it doesn't exist
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        last_active DATETIME
    )");

    // Ensure last_active column exists for older DBs
    try {
        $db->exec("ALTER TABLE users ADD COLUMN last_active DATETIME");
    } catch (PDOException $ignored) {
        // Column may already exist; ignore error
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "DB Connection failed: " . $e->getMessage()]);
    exit();
}
// Create simulations table if it doesn't exist
$db->exec("CREATE TABLE IF NOT EXISTS simulations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    name TEXT,
    settings TEXT,         -- JSON string with simulation settings
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id) REFERENCES users(id)
)");

?>
