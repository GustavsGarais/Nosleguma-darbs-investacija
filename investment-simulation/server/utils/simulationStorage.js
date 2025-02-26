// src/utils/simulationStorage.js

const STORAGE_KEY = "userSimulations";
const USER_KEY = "users"; // Store all users

// Get simulations for a specific user
export function getUserSimulations(userId) {
  const data = localStorage.getItem(STORAGE_KEY);
  const simulations = data ? JSON.parse(data) : {};
  return simulations[userId] || []; // Return user's simulations or an empty array
}

// Login logic
export function loginUser(username, password) {
  const users = JSON.parse(localStorage.getItem(USER_KEY) || "{}");

  if (users[username] && users[username].password === password) {
    return { username }; // Return user object if login is successful
  }
  return null; // Invalid credentials
}

// Register new user
export function registerUser(username, password) {
  const users = JSON.parse(localStorage.getItem(USER_KEY) || "{}");

  // Check if user already exists
  if (users[username]) {
    return false; // Username already exists
  }

  // Register the new user
  users[username] = { password }; // Store password (consider using hashing in a real app)
  localStorage.setItem(USER_KEY, JSON.stringify(users));

  return true; // Successful registration
}

// Save a new simulation
export function saveUserSimulation(userId, simulation) {
  const data = localStorage.getItem(STORAGE_KEY);
  const simulations = data ? JSON.parse(data) : {};

  if (!simulations[userId]) {
    simulations[userId] = [];
  }

  simulations[userId].push(simulation);
  localStorage.setItem(STORAGE_KEY, JSON.stringify(simulations));
}

// Delete a simulation by ID
export function deleteUserSimulation(userId, simulationId) {
  const data = localStorage.getItem(STORAGE_KEY);
  if (!data) return;

  const simulations = JSON.parse(data);
  if (!simulations[userId]) return;

  simulations[userId] = simulations[userId].filter(sim => sim.id !== simulationId);
  localStorage.setItem(STORAGE_KEY, JSON.stringify(simulations));
}
