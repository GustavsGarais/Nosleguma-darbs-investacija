<template>
  <header class="top-bar">
    <div class="top-bar-content">
      <h1 class="logo">📈 Investify</h1>
      <div class="button-group">
        <button @click="emitNavigate('HomePage')" class="nav-button">🏠 Home</button>
        <button v-if="showLogin" @click="showLoginModal" class="nav-button">🔐 Login</button>
        <button @click="toggleTheme" class="theme-toggle">🌙 Toggle Theme</button>
      </div>
    </div>
  </header>
</template>

<script setup>
const props = defineProps({
  toggleTheme: Function,
  showLogin: Boolean,
  showLoginModal: Function
})

const emit = defineEmits(['navigate'])

function emitNavigate(page) {
  emit('navigate', page)
}
</script>

<style scoped>
.top-bar {
  width: 100%;
  position: fixed;
  top: 0;
  left: 0;
  background: rgba(20, 20, 20, 0.85);
  backdrop-filter: blur(10px);
  padding: 1rem 2rem;
  display: flex;
  justify-content: center;
  z-index: 999;
  box-shadow: 0 2px 10px rgba(0,0,0,0.4);
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
}

.button-group {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.nav-button, .theme-toggle {
  padding: 0.5rem 1rem;
  background: #444;
  color: #fff;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.3s ease;
}

.nav-button:hover, .theme-toggle:hover {
  background: #666;
}
</style>
