// server.js
const fs = require('fs');
const path = require('path');
const express = require('express');
const cors = require('cors');

const app = express();
const USERS_DIR = path.join(__dirname, 'users');

if (!fs.existsSync(USERS_DIR)) fs.mkdirSync(USERS_DIR);

app.use(cors());
app.use(express.json());

app.post('/api/login', (req, res) => {
  const { username, password } = req.body;
  const userFile = path.join(USERS_DIR, `${username}.json`);

  if (fs.existsSync(userFile)) {
    const userData = JSON.parse(fs.readFileSync(userFile));
    if (userData.password === password) {
      return res.json({ success: true, user: userData });
    } else {
      return res.status(401).json({ message: 'Incorrect password' });
    }
  } else {
    const newUser = { username, password };
    fs.writeFileSync(userFile, JSON.stringify(newUser, null, 2));
    return res.json({ success: true, user: newUser });
  }
});

app.listen(3000, () => console.log('Login server running at http://localhost:3000'));
