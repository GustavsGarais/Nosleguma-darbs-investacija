<template>
  <div class="login-container">
    <div class="login-box">
      <h2>{{ isRegistering ? 'Register' : 'Login' }}</h2>
      <input v-model="username" placeholder="Username" />
      <input type="password" v-model="password" placeholder="Password" />
      <button @click="isRegistering ? register() : login()">
        {{ isRegistering ? 'Create Account' : 'Login' }}
      </button>
      <p @click="isRegistering = !isRegistering" class="switch">
        {{ isRegistering ? 'Have an account? Login' : 'New user? Register here' }}
      </p>
      <p class="error">{{ error }}</p>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      username: '',
      password: '',
      isRegistering: false,
      error: ''
    }
  },
  methods: {
    login() {
      const data = JSON.parse(localStorage.getItem(this.username))
      if (data && data.password === this.password) {
        localStorage.setItem('loggedInUser', this.username)
        this.$emit('navigate', 'SimulationPage')
      } else {
        this.error = 'Invalid credentials.'
      }
    },
    register() {
      if (localStorage.getItem(this.username)) {
        this.error = 'User already exists.'
      } else {
        localStorage.setItem(this.username, JSON.stringify({ password: this.password }))
        localStorage.setItem('loggedInUser', this.username)
        this.$emit('navigate', 'SimulationPage')
      }
    }
  }
}
</script>

<style scoped>
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 90vh;
}

.login-box {
  background-color: rgba(255, 255, 255, 0.05);
  padding: 2rem;
  border-radius: 10px;
  backdrop-filter: blur(5px);
  text-align: center;
  width: 300px;
  box-shadow: 0 0 10px #000;
}

input {
  display: block;
  width: 100%;
  padding: 0.6rem;
  margin-bottom: 1rem;
  border: none;
  border-radius: 5px;
}

button {
  width: 100%;
  padding: 0.6rem;
  border: none;
  border-radius: 5px;
  background: #282c34;
  color: white;
  cursor: pointer;
}

.switch {
  margin-top: 1rem;
  color: #888;
  cursor: pointer;
  font-size: 0.9rem;
}

.error {
  color: red;
  margin-top: 0.5rem;
}
</style>
