const express = require('express');
const fs = require('fs');
const path = require('path');
const cors = require('cors');
const bodyParser = require('body-parser');

const app = express();
const PORT = 3000;
const USERS_DIR = path.join(__dirname, 'users');
const sessionFile = path.join(__dirname, 'current_session.txt');

// Ensure 'users' directory exists
if (!fs.existsSync(USERS_DIR)) {
    fs.mkdirSync(USERS_DIR);
}

// Middleware
app.use(cors({
    origin: (origin, callback) => {
        if (!origin || origin.startsWith("http://localhost")) {
            callback(null, true);
        } else {
            callback(new Error("Not allowed by CORS"));
        }
    },
    credentials: true
}));
app.use(bodyParser.json());

// Utility function to get user folder path
const getUserFolder = (username) => path.join(USERS_DIR, username);

// **User Registration**
app.post('/register', (req, res) => {
    const { username, password } = req.body;
    const userFile = path.join(USERS_DIR, `${username}.txt`);

    if (fs.existsSync(userFile)) {
        return res.json({ success: false, message: 'User already exists' });
    }

    if (!/(?=.*[A-Z])(?=.*\d)/.test(password)) {
        return res.json({ success: false, message: 'Password must contain at least one uppercase letter and one number' });
    }

    fs.writeFileSync(userFile, password);
    res.json({ success: true, message: 'Registration successful' });
});

// **User Login**
app.post('/login', (req, res) => {
    const { username, password } = req.body;
    const userFile = path.join(USERS_DIR, `${username}.txt`);

    if (!fs.existsSync(userFile)) {
        return res.json({ success: false, message: 'User not found' });
    }

    const storedPassword = fs.readFileSync(userFile, 'utf8').trim();
    if (storedPassword === password) {
        fs.writeFileSync(sessionFile, username); // Save session
        res.json({ success: true, message: 'Login successful', username });
    } else {
        res.json({ success: false, message: 'Incorrect password' });
    }
});

// **Check Active Session**
app.get('/session', (req, res) => {
    if (fs.existsSync(sessionFile)) {
        const username = fs.readFileSync(sessionFile, 'utf8').trim();
        res.json({ loggedIn: true, username });
    } else {
        res.json({ loggedIn: false });
    }
});

// **User Logout**
app.post('/logout', (req, res) => {
    if (fs.existsSync(sessionFile)) {
        fs.unlinkSync(sessionFile);
    }
    res.json({ success: true, message: 'Logged out successfully' });
});

// **Save Investment Simulation**
app.post('/save-simulation', (req, res) => {
    const { username, simulationData } = req.body;
    const userFolder = getUserFolder(username);
   
    if (!fs.existsSync(userFolder)) {
        fs.mkdirSync(userFolder);
    }

    const simulationFile = path.join(userFolder, 'simulation.json');
    fs.writeFileSync(simulationFile, JSON.stringify(simulationData, null, 2));

    res.json({ success: true, message: 'Simulation saved!' });
});

// **Load Investment Simulation**
app.get("/load-simulations", (req, res) => {
    const { username } = req.query;
    if (!username) return res.status(400).json({ error: "Username required" });

    const userDir = `${USERS_DIR}${username}`;
    if (!fs.existsSync(userDir)) return res.json([]);

    const files = fs.readdirSync(userDir).filter(file => file.endsWith(".json"));
    const simulations = files.map(file => ({
        name: file,
        data: JSON.parse(fs.readFileSync(`${userDir}/${file}`, "utf-8"))
    }));

    res.json(simulations);
});

// **Save Favorite Simulation**
app.post('/save-favorite', (req, res) => {
    const { username, simulationName, simulationData } = req.body;
    const userFolder = getUserFolder(username);
   
    if (!fs.existsSync(userFolder)) {
        fs.mkdirSync(userFolder);
    }

    const favoriteFile = path.join(userFolder, `${simulationName}.json`);
    fs.writeFileSync(favoriteFile, JSON.stringify(simulationData, null, 2));

    res.json({ success: true, message: 'Simulation saved as favorite!' });
});

// **Load Favorite Simulation**
app.get('/load-favorite', (req, res) => {
    const { username, simulationName } = req.query;
    const userFolder = getUserFolder(username);
    const favoriteFile = path.join(userFolder, `${simulationName}.json`);

    if (fs.existsSync(favoriteFile)) {
        const data = fs.readFileSync(favoriteFile, 'utf8');
        res.json({ success: true, simulation: JSON.parse(data) });
    } else {
        res.json({ success: false, message: 'No favorite simulation found.' });
    }
});

// **Start Server**
app.listen(PORT, () => {
    console.log(`✅ Server running on http://localhost:${PORT}`);
});
