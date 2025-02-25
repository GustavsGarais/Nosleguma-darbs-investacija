const express = require("express");
const fs = require("fs");
const path = require("path");
const cors = require("cors");
const { ensureUserFolder, saveSimulation, getSimulations } = require("./utils/utils");

const app = express();
app.use(express.json());
app.use(cors());

const USERS_DIR = path.join(__dirname, "data/users");
if (!fs.existsSync(USERS_DIR)) {
  fs.mkdirSync(USERS_DIR, { recursive: true });
}

// User Login / Register Endpoint
app.post("/login", (req, res) => {
  const { username, password } = req.body;
  const userFile = path.join(USERS_DIR, `${username}.json`);

  if (fs.existsSync(userFile)) {
    const userData = JSON.parse(fs.readFileSync(userFile, "utf-8"));
    if (userData.password === password) {
      ensureUserFolder(username);
      return res.json({ success: true, message: "Login successful", username });
    } else {
      return res.status(401).json({ success: false, message: "Invalid password" });
    }
  } else {
    // Register new user
    const newUser = { username, password, simulations: [] };
    fs.writeFileSync(userFile, JSON.stringify(newUser, null, 2));
    ensureUserFolder(username);
    return res.json({ success: true, message: "User registered successfully", username });
  }
});

// Save Simulation Data
app.post("/save-simulation", (req, res) => {
  const { username, simulationName, data } = req.body;
  if (!username || !simulationName || !data) {
    return res.status(400).json({ success: false, message: "Missing data" });
  }
  saveSimulation(username, simulationName, data);
  res.json({ success: true, message: "Simulation saved successfully" });
});

// Get User Simulations
app.get("/get-simulations", (req, res) => {
  const { username } = req.query;
  if (!username) {
    return res.status(400).json({ success: false, message: "Username is required" });
  }
  const simulations = getSimulations(username);
  res.json({ success: true, simulations });
});

const PORT = 5000;
app.listen(PORT, () => {
  console.log(`Server running on http://localhost:${PORT}`);
});
