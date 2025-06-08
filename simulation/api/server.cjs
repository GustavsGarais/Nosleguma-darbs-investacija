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

// Create simulations table if not exists
db.run(`CREATE TABLE IF NOT EXISTS simulations (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id INTEGER NOT NULL,
  sim_name TEXT NOT NULL,
  initial_investment REAL NOT NULL,
  num_investors INTEGER NOT NULL,
  growth_rate REAL NOT NULL,
  risk_appetite REAL NOT NULL,
  market_influence REAL NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id)
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
      if (row) return res.json({ success: true, message: 'Login successful.', user: row });
      else return res.json({ success: false, message: 'Invalid username or password.' });
    }
  );
});

// Save simulation endpoint
app.post('/save-simulation', (req, res) => {
  const {
    user_id,
    sim_name,
    initial_investment,
    num_investors,
    growth_rate,
    risk_appetite,
    market_influence
  } = req.body;

  // Validate required fields
  if (
    !user_id || !sim_name || initial_investment == null || num_investors == null ||
    growth_rate == null || risk_appetite == null || market_influence == null
  ) {
    return res.json({ success: false, message: 'Missing fields.' });
  }

  // Insert into database
  db.run(
    `INSERT INTO simulations (user_id, sim_name, initial_investment, num_investors, growth_rate, risk_appetite, market_influence)
     VALUES (?, ?, ?, ?, ?, ?, ?)`,
    [user_id, sim_name, initial_investment, num_investors, growth_rate, risk_appetite, market_influence],
    function (err) {
      if (err) {
        console.error(err);
        return res.json({ success: false, message: 'Database error.' });
      }
      return res.json({ success: true, message: 'Simulation saved successfully.', simulation_id: this.lastID });
    }
  );
});

// Get simulations by user ID
app.get('/simulations/:user_id', (req, res) => {
  const user_id = req.params.user_id;

  db.all(
    `SELECT * FROM simulations WHERE user_id = ?`,
    [user_id],
    (err, rows) => {
      if (err) {
        console.error(err);
        return res.json({ success: false, message: 'Database error.' });
      }
      return res.json({ success: true, simulations: rows });
    }
  );
});

app.listen(port, () => {
  console.log(`Server running on http://localhost:${port}`);
});
