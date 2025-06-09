// src/api/simulationService.js
import axios from 'axios';

const BASE_URL = 'http://localhost:8000'; // or your backend host

export function getSimulations(userId) {
  return axios.post(`${BASE_URL}/get_simulations.php`, { userId });
}

export function saveSimulation(userId, name, settings) {
  return axios.post(`${BASE_URL}/save_simulation.php`, {
    userId,
    name,
    settings: JSON.stringify(settings)
  });
}
