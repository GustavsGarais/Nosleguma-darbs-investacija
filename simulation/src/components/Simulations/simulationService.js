// src/components/Simulations/simulationService.js
import axios from 'axios'

const BASE_URL = 'http://localhost:8000'

export function getSimulations(userId) {
  // Backend expects JSON body with user_id
  return axios.post(`${BASE_URL}/saving_simulations/get_simulation.php`, { user_id: userId })
}

export function saveSimulation(userId, name, settings) {
  // Backend expects user_id, name, and settings (as object; API json_encodes)
  return axios.post(`${BASE_URL}/saving_simulations/save_simulation.php`, {
    user_id: userId,
    name,
    settings
  })
}
