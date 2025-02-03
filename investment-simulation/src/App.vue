<template>
  <div :class="{ 'dark-mode': isDarkMode }">
    <Sidebar @toggle-theme="toggleTheme" />
    <component :is="currentView" @login-success="handleLogin" />
  </div>
</template>

<script>
import Sidebar from './components/Sidebar.vue';
import Login from './components/Login.vue';
import Dashboard from './components/Dashboard.vue';

export default {
  components: { Sidebar, Login, Dashboard },
  data() {
    return {
      isLoggedIn: false,
      isDarkMode: false,
    };
  },
  computed: {
    currentView() {
      return this.isLoggedIn ? Dashboard : Login;
    },
  },
  methods: {
    handleLogin() {
      this.isLoggedIn = true;
    },
    toggleTheme() {
      this.isDarkMode = !this.isDarkMode;
    },
  },
};
</script>
