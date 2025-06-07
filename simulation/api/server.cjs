const express = require('express');
const cors = require('cors');
const sqlite3 = require('sqlite3').verbose();
const bodyParser = require('body-parser');
const path = require('path');

const app = express();
const port = 3000;

app.use(cors());
app.use(bodyParser.json());

// Connect to SQLite database
const dbPath = path.join(__dirname, 'database.db');
const db = new sqlite3.Database(dbPath, (err) => {
  if (err) console.error('Database opening error:', err);
  else console.log('Database connected at', dbPath);
});

// Create users table if not exists
db.run(`CREATE TABLE IF NOT EXISTS users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  username TEXT UNIQUE,
  password TEXT
)`);

// Register endpoint
app.post('/register', (req, res) => {
  const { username, password } = req.body;
  if (!username || !password) return res.json({ success: false, message: 'Missing fields.' });

  db.run(
    `INSERT INTO users (username, password) VALUES (?, ?)`,
    [username, password],
    function (err) {
      if (err) {
        if (err.message.includes('UNIQUE')) {
          return res.json({ success: false, message: 'Username already exists.' });
        } else {
          return res.json({ success: false, message: 'Database error.' });
        }
      }
      return res.json({ success: true, message: 'Registered successfully.' });
    }
  );
});

// Login endpoint
app.post('/login', (req, res) => {
  const { username, password } = req.body;
  if (!username || !password) return res.json({ success: false, message: 'Missing fields.' });

  db.get(
    `SELECT * FROM users WHERE username = ? AND password = ?`,
    [username, password],
    (err, row) => {
      if (err) return res.json({ success: false, message: 'Database error.' });
      if (row) return res.json({ success: true, message: 'Login successful.' });
      else return res.json({ success: false, message: 'Invalid username or password.' });
    }
  );
});

app.listen(port, () => {
  console.log(`Server running on http://localhost:${port}`);
});
