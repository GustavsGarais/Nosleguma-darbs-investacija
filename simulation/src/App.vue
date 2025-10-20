<template>
  <div :class="['app-wrapper', themeClass]">
    <TopBar :toggle-theme="toggleTheme" :current-user="user" @navigate="navigate" @user-changed="user = $event" />

    <main class="content-container">
      <div class="container">
        <component :is="currentPage" @navigate="navigate" @toggleTheme="toggleTheme" @user-changed="user = $event" />
      </div>
    </main>
  </div>
</template>

<script>
import HomePage from './components/HomePage.vue'
import LoginPage from './components/LoginPage.vue'
import RegisterPage from './components/RegisterPage.vue'
import authService from './services/authService'
import SimulationPage from "@/components/Simulations/SimulationPage.vue";
import TopBar from './components/TopBar.vue'
import BeginnerGuidePage from './components/BeginnerGuidePage.vue'
import SimulationInfoPage from './components/SimulationInfoPage.vue'
import UserSettingsPage from "./components/user_settings/goToUserSettings.vue";

export default {
  components: {
    HomePage,
    LoginPage,
    RegisterPage,
    SimulationPage,
    SimulationInfoPage,
    BeginnerGuidePage,
    TopBar,
    UserSettingsPage
  },
  data() {
    return {
      currentPage: 'HomePage',
      darkMode: JSON.parse(localStorage.getItem('darkMode')) || false,
      user: null
    }
  },
  mounted() {
    // Populate user from local storage on app mount
    try {
      this.user = authService.currentUser()
    } catch (e) {
      // ignore
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
      // Apply theme class to root app wrapper so CSS variables and styles can respond
      // Keep the old mechanism for compatibility
      document.body.classList.toggle('dark', this.darkMode)
      document.body.classList.toggle('light', !this.darkMode)
    }
  }
}
</script>

<style>
.content-container{flex:1}
</style>
