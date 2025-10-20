<template>
  <div :class="themeClass" class="app-wrapper">
    <TopBar 
      :toggle-theme="toggleTheme" 
      @navigate="navigate"
    />

    <div class="content-container">
      <component 
        :is="currentPage" 
        @navigate="navigate" 
        @toggleTheme="toggleTheme"
      />
    </div>
  </div>
</template>

<script>
import HomePage from './components/HomePage.vue'
import LoginPage from './components/LoginPage.vue'
import SimulationPage from "@/components/Simulations/SimulationPage.vue";
import TopBar from './components/TopBar.vue'
import BeginnerGuidePage from './components/BeginnerGuidePage.vue'
import SimulationInfoPage from './components/SimulationInfoPage.vue'
import UserSettingsPage from "./components/user_settings/goToUserSettings.vue";

export default {
  components: { 
    HomePage,
    LoginPage,
    SimulationPage,
    SimulationInfoPage,
    BeginnerGuidePage,
    TopBar,
    UserSettingsPage
  },
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
      document.body.classList.toggle('dark', this.darkMode)
      document.body.classList.toggle('light', !this.darkMode)
    }
  }
}
</script>

<style>
.app-wrapper {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  transition: background-color 0.3s ease;
}

body, html, #app {
  margin: 0;
  padding: 0;
  height: 100%;
  font-family: 'Segoe UI', sans-serif;
}

.dark-theme {
  background: linear-gradient(to bottom right, purple, black);
  color: white;
  --background-blur: rgba(255, 255, 255, 0.07);
  --text-color: white;
}

.light-theme {
  background: linear-gradient(to bottom right, #edc988, #5b8c5a);
  color: black;
  --background-blur: rgba(0, 0, 0, 0.07);
  --text-color: black;
}

.content-container {
  flex: 1;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 2rem;
  margin-top: 80px;
  width: 100%;
}
</style>
