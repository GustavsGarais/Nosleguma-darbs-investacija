import express from "express";
import cors from "cors";
import sqlite3 from "sqlite3";
import bodyParser from "body-parser";
import path from "path";
import { fileURLToPath } from "url";

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const app = express();
const PORT = 3000;

// Middleware
app.use(cors());
app.use(bodyParser.json());

// SQLite setup
const dbPath = path.join(__dirname, "database.db");
const db = new sqlite3.Database(dbPath, (err) => {
  if (err) return console.error("Database connection error:", err);
  console.log("Connected to SQLite database.");
});

// Create table if not exists
db.run(`
  CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE,
    password TEXT
  )
`);

// Register route
app.post("/register", (req, res) => {
  const { username, password } = req.body;
  const query = "INSERT INTO users (username, password) VALUES (?, ?)";
  db.run(query, [username, password], (err) => {
    if (err) {
      console.error("Register error:", err.message);
      return res.json({ success: false, message: "User already exists or error occurred." });
    }
    res.json({ success: true, message: "User registered successfully." });
  });
});

// Login route
app.post("/login", (req, res) => {
  const { username, password } = req.body;
  const query = "SELECT * FROM users WHERE username = ? AND password = ?";
  db.get(query, [username, password], (err, row) => {
    if (err) {
      console.error("Login error:", err.message);
      return res.json({ success: false, message: "Login failed." });
    }
    if (row) {
      res.json({ success: true, message: "Login successful." });
    } else {
      res.json({ success: false, message: "Incorrect username or password." });
    }
  });
});

// Start server
app.listen(PORT, () => {
  console.log(`API server running at http://localhost:${PORT}`);
});
