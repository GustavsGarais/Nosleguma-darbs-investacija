<!-- eslint-disable vue/multi-word-component-names -->
<template>
    <div :class="{'dark-mode': isDarkMode}">
      <h1>Welcome to the Home Page!</h1>
      <button @click="toggleDarkMode">
        Toggle Dark Mode
      </button>
    </div>
  </template>

  <script>
  export default {
    name: "Home",
    data() {
      return {
        isDarkMode: false,  // This will control the dark mode
      };
    },
    methods: {
      toggleDarkMode() {
        this.isDarkMode = !this.isDarkMode; // Toggle the value
      },
    },
  };
  </script>

  <style scoped>
  /* Default white background */
  div {
    background-color: white;
    color: black;
    transition: background-color 0.3s, color 0.3s;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
  }

  /* Dark mode styles */
  .dark-mode {
    background-color: #333;
    color: white;
  }

  button {
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
  }
  </style>
