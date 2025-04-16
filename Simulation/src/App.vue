<template>
  <div :class="themeClass">
    <button @click="toggleTheme" class="theme-toggle">Toggle Theme</button>
    <component :is="currentPage" @navigate="navigate" />
  </div>
</template>

<script>
import HomePage from './components/HomePage.vue'
import LoginPage from './components/LoginPage.vue'
import SimulationPage from './components/SimulationPage.vue'

export default {
  components: { HomePage, LoginPage, SimulationPage },
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
function toggleTheme() {
  document.body.classList.toggle('dark-theme');
}

</script>

<style>
.app-wrapper {
  min-height: 100vh;
  background-color: var(--background-color);
  transition: background-color 0.3s ease;
}
body, html, #app {
  margin: 0;
  padding: 0;
  height: 100%;
  font-family: 'Segoe UI', sans-serif;
}

/* Theme Styles */
.dark-theme {
  background: linear-gradient(to bottom right, purple, black);
  color: white;
}

.light-theme {
  background: linear-gradient(to bottom right, #edc988, #5b8c5a);
  color: black;
}

/* Toggle Button Style */
.theme-toggle {
  position: absolute;
  top: 10px;
  right: 10px;
  padding: 0.5rem 1rem;
  cursor: pointer;
  background: #444;
  color: #fff;
  border: none;
  border-radius: 5px;
  z-index: 100;
}
</style>
