<template>
  <div class="login-wrapper">
    <button class="top-left-back" @click="$emit('navigate', 'HomePage')">← Back</button>

    <div class="login-box">
      <h2>Login</h2>
      <input v-model="username" placeholder="Username" />
      <input v-model="password" type="password" placeholder="Password" />
      <div class="button-row">
        <button @click="login">Login</button>
        <button @click="$emit('navigate', 'HomePage')">Back</button>
      </div>
      <p v-if="error" class="error">{{ error }}</p>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue'

export default {
  setup(_, { emit }) {
    const username = ref('')
    const password = ref('')
    const error = ref('')

    const login = () => {
      if (username.value === 'admin' && password.value === '1234') {
        emit('navigate', 'SimulationPage')
      } else {
        error.value = 'Incorrect username or password.'
      }
    }

    return { username, password, login, error }
  }
}
</script>

<style scoped>
.login-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  height: calc(100vh - 80px);
  width: 100%;
  position: relative;
  margin-top: 80px;
}

.top-left-back {
  position: absolute;
  top: 20px;
  left: 20px;
  background-color: #007bff;
  color: white;
  padding: 6px 12px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.top-left-back:hover {
  background-color: #0056b3;
}

.login-box {
  background-color: rgba(255, 255, 255, 0.07);
  backdrop-filter: blur(5px);
  padding: 2rem;
  border-radius: 15px;
  box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
  width: 300px;
  text-align: center;
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
  gap: 1rem;
}

button {
  flex: 1;
  padding: 0.5rem;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

.error {
  color: red;
  margin-top: 1rem;
}
</style>
