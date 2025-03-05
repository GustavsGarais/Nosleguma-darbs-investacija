<template>
<div :class="{ dark: isDarkMode }">
<div class="app-container">
  <header class="p-4 text-center">
    <button @click="toggleTheme" class="p-2 rounded bg-gray-300 dark:bg-gray-700">
      {{ isDarkMode ? "Light Mode" : "Dark Mode" }}
    </button>
  </header>
  <main class="p-4">
    <router-view />
  </main>
</div>
</div>
</template>

<script>
export default {
data() {
return {
  isDarkMode: localStorage.getItem("theme") === "dark",
};
},
methods: {
toggleTheme() {
  this.isDarkMode = !this.isDarkMode;
  localStorage.setItem("theme", this.isDarkMode ? "dark" : "light");
  document.documentElement.classList.toggle("dark", this.isDarkMode);
},
},
mounted() {
document.documentElement.classList.toggle("dark", this.isDarkMode);
},
};
</script>

<style>
/* Light mode (default) */
body {
background-color: white;
color: black;
transition: background-color 0.3s, color 0.3s;
}

/* Dark mode */
.dark body {
background-color: #121212;
color: white;
}

/* Ensure all content adapts */
.app-container {
min-height: 100vh;
display: flex;
flex-direction: column;
}
</style>