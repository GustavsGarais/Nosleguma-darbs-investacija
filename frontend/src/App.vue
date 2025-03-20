<template>
  <div :class="['app', theme]">
    <header>
      <button @click="toggleTheme">Toggle {{ theme === 'light' ? 'Dark' : 'Light' }} Mode</button>
      <button @click="goToLogin">Login</button>
    </header>
    <main>
      <h1>Welcome to the Investment Simulation</h1>
      <p>Explore and simulate investment strategies to learn how they work.</p>
    </main>
  </div>
</template>

<script>
import { ref } from 'vue';

export default {
  setup() {
    const theme = ref('light');

    const toggleTheme = () => {
      theme.value = theme.value === 'light' ? 'dark' : 'light';
    };

    const goToLogin = () => {
      // Redirect to login (placeholder for now)
      alert('Redirecting to login...');
    };

    return { theme, toggleTheme, goToLogin };
  }
};
</script>

<style scoped>
.app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  font-family: Arial, sans-serif;
}

header {
  position: absolute;
  top: 20px;
  right: 20px;
}

button {
  margin: 0 10px;
  padding: 10px 20px;
  border: none;
  cursor: pointer;
  font-size: 16px;
  border-radius: 20px;
}

button:hover {
  opacity: 0.9;
}

.light {
  background: linear-gradient(to bottom, #d4edda, #a3c293);
  color: #1d3b20;
}

.dark {
  background: linear-gradient(to bottom, #6a0572, #003b8b);
  color: #e1e1ff;
}
</style>