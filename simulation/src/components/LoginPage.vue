<template>
  <div class="login-wrapper" style="background: linear-gradient(135deg, #D4C17A, #94C973);">
    <button class="top-left-back" @click="$emit('navigate', 'HomePage')">← Back</button>
    <div class="login-box">
      <h2>Sign in</h2>

      <div class="button-row">
        <button :class="{ active: mode === 'login' }" @click="mode = 'login'">Login</button>
        <button :class="{ active: mode === 'register' }" @click="mode = 'register'">Register</button>
      </div>

      <input v-model="username" type="text" placeholder="Username" />
      <input v-model="password" type="password" placeholder="Password" />

      <button class="submit-btn" @click="mode === 'login' ? login() : register()">
        {{ mode === 'login' ? 'Login' : 'Register' }}
      </button>

      <div class="error" v-if="error">{{ error }}</div>
    </div>
  </div>
</template>


<script setup>
import { ref } from 'vue'

const username = ref('')
const password = ref('')
const error = ref('')
const mode = ref('login')

function login() {
  if (username.value === 'admin' && password.value === '1234') {
    error.value = ''
    alert('Login successful!')
  } else {
    error.value = 'Incorrect username or password.'
  }
}

function register() {
  if (username.value.length >= 3 && password.value.length >= 4) {
    error.value = 'Registration successful!'
    mode.value = 'login'
  } else {
    error.value = 'Username and password must be at least 3 and 4 characters.'
  }
}
</script>

<style scoped>
.login-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  width: 100%;
  position: relative;
  background-size: cover;
  background-position: center;
}

.top-left-back {
  position: absolute;
  top: 20px;
  left: 20px;
  background-color: rgba(0, 0, 0, 0.5);
  color: #fff;
  padding: 8px 14px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.3s;
}

.top-left-back:hover {
  background-color: rgba(0, 0, 0, 0.8);
}

.login-box {
  background-color: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  padding: 2.5rem;
  border-radius: 20px;
  box-shadow: 0 0 25px rgba(0, 0, 0, 0.4);
  width: 320px;
  text-align: center;
}

h2 {
  margin-bottom: 1.5rem;
  font-size: 2rem;
  color: #fff;
}

input {
  display: block;
  width: 100%;
  padding: 0.75rem;
  margin-bottom: 1rem;
  border-radius: 8px;
  border: none;
  outline: none;
  background: rgba(255, 255, 255, 0.9);
  font-size: 1rem;
}

.button-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 1rem;
  gap: 0.75rem;
}

.button-row button {
  flex: 1;
  padding: 0.6rem;
  border: none;
  border-radius: 8px;
  background-color: rgba(255, 255, 255, 0.3);
  color: #fff;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.3s;
}

.button-row button:hover {
  background-color: rgba(255, 255, 255, 0.5);
}

.button-row button.active {
  background-color: #4CAF50;
}

.submit-btn {
  width: 100%;
  padding: 0.75rem;
  border: none;
  border-radius: 8px;
  background-color: #4CAF50;
  color: #fff;
  font-size: 1.05rem;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.3s;
}

.submit-btn:hover {
  background-color: #45a049;
}

.error {
  color: #ff4e4e;
  margin-top: 1rem;
  font-size: 0.95rem;
}
</style>

