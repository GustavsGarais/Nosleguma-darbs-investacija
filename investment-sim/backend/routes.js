// Backend/routes.js
import express from 'express';
import pool from './db.js';
import session from 'express-session';
import dotenv from 'dotenv';

dotenv.config();
const router = express.Router();

router.use(session({
  secret: process.env.SESSION_SECRET,
  resave: false,
  saveUninitialized: true,
  cookie: { secure: false }
}));

// Register Route
router.post('/register', async (req, res) => {
  const { username, password } = req.body;

  if (!/[A-Z]/.test(password) || !/\d/.test(password)) {
    return res.status(400).json({ message: 'Password must contain at least one uppercase letter and one number.' });
  }

  try {
    const userExists = await pool.query('SELECT * FROM users WHERE username = $1', [username]);
    if (userExists.rows.length > 0) {
      return res.status(400).json({ message: 'Username already exists' });
    }
    await pool.query('INSERT INTO users (username, password) VALUES ($1, $2)', [username, password]);
    res.status(201).json({ message: 'User registered successfully' });
  } catch (err) {
    console.error(err);
    res.status(500).json({ message: 'Error registering user' });
  }
});

// Login Route
router.post('/login', async (req, res) => {
  const { username, password } = req.body;

  try {
    const user = await pool.query('SELECT * FROM users WHERE username = $1 AND password = $2', [username, password]);
    if (user.rows.length === 0) {
      return res.status(400).json({ message: 'Invalid username or password' });
    }

    req.session.userId = user.rows[0].id;
    res.status(200).json({ message: 'Login successful' });
  } catch (err) {
    console.error(err);
    res.status(500).json({ message: 'Error logging in' });
  }
});

// Logout Route
router.post('/logout', (req, res) => {
  req.session.destroy((err) => {
    if (err) {
      return res.status(500).json({ message: 'Error logging out' });
    }
    res.status(200).json({ message: 'Logout successful' });
  });
});

export default router;
