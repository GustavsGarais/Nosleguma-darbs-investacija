// src/utils/simulationStorage.js

const STORAGE_KEY = "userSimulations";

// Get simulations for a specific user
export function getUserSimulations(userId) {
  const data = localStorage.getItem(STORAGE_KEY);
  const simulations = data ? JSON.parse(data) : {};
  return simulations[userId] || []; // Return user's simulations or an empty array
}

export function loginUser(username, password) {
    // Login logic here...
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
