<template>
    <div class="sidebar">
      <h3>Menu</h3>
      <button @click="$emit('toggle-theme')">Toggle Dark Mode</button>
      <button @click="goToSettings">Settings</button>
    </div>
  </template>
  
  <script>
  export default {
    methods: {
      goToSettings() {
        this.$emit('change-view', 'Settings');
      },
    },
  };
  </script>
  