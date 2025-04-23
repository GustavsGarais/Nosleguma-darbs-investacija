<template>
    <header class="top-bar">
      <div class="top-bar-content">
        <h1 class="logo">📈 Investify</h1>
        <button @click="toggleTheme" class="theme-toggle">Toggle Theme</button>
      </div>
    </header>
  </template>
  
  <script setup>
  defineProps({
    toggleTheme: Function
  })
  </script>
  
  <style scoped>
  .top-bar {
    width: 100%;
    position: fixed;
    top: 0;
    left: 0;
    background: rgba(20, 20, 20, 0.8);
    backdrop-filter: blur(10px);
    padding: 1rem 2rem;
    display: flex;
    justify-content: center;
    z-index: 999;
    box-shadow: 0 2px 10px rgba(0,0,0,0.5);
  }
  
  .top-bar-content {
    width: 100%;
    max-width: 1200px;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  
  .logo {
    font-size: 1.8rem;
    color: #fff;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .theme-toggle {
    padding: 0.5rem 1rem;
    background: #444;
    color: #fff;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.3s ease;
  }
  
  .theme-toggle:hover {
    background: #666;
  }
  </style>
  