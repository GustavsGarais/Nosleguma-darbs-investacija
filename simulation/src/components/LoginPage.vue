<template>
  <div class="login-wrapper">
    <!-- Top Bar -->
    <div class="top-bar">
      <button @click="$emit('navigate', 'HomePage')">Back</button>
    </div>

    <!-- Login Box -->
    <div class="login-box">
      <h2>Login</h2>
      <input type="text" placeholder="Username" v-model="username" />
      <input type="password" placeholder="Password" v-model="password" />
      <div class="button-row">
        <button @click="login">Login</button>
        <button @click="register">Register</button>
      </div>
      <div class="error" v-if="error">{{ error }}</div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      username: '',
      password: '',
      error: ''
    }
  },
  methods: {
    login() {
      if (!this.username || !this.password) {
        this.error = 'Please enter both username and password.'
      } else if (this.username === 'admin' && this.password === '1234') {
        this.error = ''
        alert('Logged in successfully!')
        this.$emit('navigate', 'SimulationPage')
      } else {
        this.error = 'Invalid username or password.'
      }
    },
    register() {
      if (!this.username || !this.password) {
        this.error = 'Please fill out both fields.'
      } else {
        this.error = ''
        alert(`Registering as ${this.username}`)
      }
    },
  }
}
</script>

<style scoped>
/* Top Bar with Back and Theme Toggle buttons */
.top-bar {
  position: absolute;
  top: 20px;
  right: 20px;
  display: flex;
  gap: 10px;
}

.top-bar button {
  background: var(--background-blur);
  color: var(--text-color);
  padding: 6px 12px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.top-bar button:hover {
  opacity: 0.85;
}

.login-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  width: 100%;
  position: relative;
  padding: 60px 20px;
  box-sizing: border-box;
}

.login-box {
  background-color: var(--background-blur);
  backdrop-filter: blur(5px);
  padding: 2rem;
  border-radius: 15px;
  box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
  width: 300px;
  max-width: 90vw;
  text-align: center;
  color: var(--text-color);
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin: auto 0; /* <--- this is the key to push it away from top and bottom */
}

h2 {
  margin-bottom: 1rem;
}

input {
  display: block;
  width: 100%;
  padding: 0.6rem;
  margin-bottom: 1rem;
  border-radius: 5px;
  border: 1px solid #ccc;
}

.button-row {
  display: flex;
  justify-content: space-between;
  gap: 0.6rem;
}

.button-row button {
  flex: 0 0 auto;
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  background: var(--background-blur);
  color: var(--text-color);
}

.button-row button:hover {
  opacity: 0.85;
}

.error {
  color: red;
  margin-top: 1rem;
}
</style>
