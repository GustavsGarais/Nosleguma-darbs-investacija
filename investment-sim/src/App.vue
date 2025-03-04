<template>
  <div :class="{ dark: darkMode }">
    <button @click="toggleTheme">
      {{ darkMode ? "Light Mode" : "Dark Mode" }}
    </button>
    <router-view />
  </div>
</template>

<script>
export default {
  data() {
    return {
      darkMode: false
    };
  },
  created() {
    const savedTheme = localStorage.getItem('darkMode');
    this.darkMode = savedTheme === 'true';
  },
  methods: {
    toggleTheme() {
      this.darkMode = !this.darkMode;
      localStorage.setItem('darkMode', this.darkMode);
    }
  }
};
</script>

<style>
.dark {
  background-color: #121212;
  color: white;
}
</style>
