<template>
  <div :class="themeClass" class="app-wrapper">
    <TopBar :toggle-theme="toggleTheme" />

    <!-- Theme Toggle Button -->
    <button class="theme-toggle" @click="toggleTheme">
      Toggle Theme
    </button>

    <div class="content-container">
      <component :is="currentPage" @navigate="navigate" />
    </div>
  </div>
</template>

<script>
import HomePage from './components/HomePage.vue'
import LoginPage from './components/LoginPage.vue'
import SimulationPage from './components/Simulations/SimulationPage.vue'
import TopBar from './components/TopBar.vue'

export default {
  components: { HomePage, LoginPage, SimulationPage, TopBar },
  data() {
    return {
      currentPage: 'HomePage',
      darkMode: JSON.parse(localStorage.getItem('darkMode')) || false
    }
  },
  computed: {
    themeClass() {
      return this.darkMode ? 'dark-theme' : 'light-theme'
    }
  },
  methods: {
    navigate(page) {
      this.currentPage = page
    },
    toggleTheme() {
      this.darkMode = !this.darkMode
      localStorage.setItem('darkMode', this.darkMode)
    }
  }
}
</script>

<style>
.app-wrapper {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background-color: var(--background-color);
  transition: background-color 0.3s ease;
}

body, html, #app {
  margin: 0;
  padding: 0;
  height: 100%;
  font-family: 'Segoe UI', sans-serif;
}

/* Theme styles */
.dark-theme {
  --background-color: black;
  background: linear-gradient(to bottom right, purple, black);
  color: white;
}

.light-theme {
  --background-color: white;
  background: linear-gradient(to bottom right, #edc988, #5b8c5a);
  color: black;
}

/* Theme toggle button */
.theme-toggle {
  position: fixed;
  top: 20px;
  right: 20px;
  background: rgba(255, 255, 255, 0.2);
  color: #fff;
  border: none;
  padding: 8px 16px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: bold;
  transition: all 0.3s;
  z-index: 999;
}

.theme-toggle:hover {
  background: rgba(255, 255, 255, 0.4);
}

.content-container {
  flex: 1;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 2rem;
  margin-top: 80px; /* Push content down below the top bar */
}
</style>
