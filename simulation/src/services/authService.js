// Simple authentication service that talks to the backend PHP endpoints.
// Uses relative paths so it works when served from the same origin.

const API = {
  login: '/api/login.php',
  register: '/api/register.php',
  logout: '/api/logout.php'
}

async function postJson(url, body) {
  const res = await fetch(url, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(body)
  })
  return res.json()
}

export async function login({ username, password }) {
  return postJson(API.login, { username, password })
}

export async function register({ username, password }) {
  return postJson(API.register, { username, password })
}

export function logout() {
  // If backend supports logout, call it; otherwise clear local storage.
  try { fetch(API.logout, { method: 'POST' }).catch(()=>{}) } catch(e){}
  localStorage.removeItem('username')
  localStorage.removeItem('loggedInUserId')
}

export function currentUser() {
  const id = localStorage.getItem('loggedInUserId')
  const username = localStorage.getItem('username')
  return id ? { id, username } : null
}

export default { login, register, logout, currentUser }
