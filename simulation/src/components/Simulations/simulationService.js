import axios from 'axios';

const API = axios.create({
  baseURL: 'http://localhost:8000', // adjust if needed
  headers: { 'Content-Type': 'application/json' }
});

export function getSimulations(userId) {
  return API.post('/get_simulations.php', { user_id: userId });
}

export function saveSimulation(userId, name, settings) {
  return API.post('/save_simulation.php', { user_id: userId, name, settings });
}
