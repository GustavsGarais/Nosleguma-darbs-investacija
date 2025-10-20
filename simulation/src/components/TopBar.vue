<!-- src/components/TopBar.vue -->
<template>
  <header class="top-bar">
    <div class="container top-bar-content">
      <div class="brand">
        <button class="logo btn btn-ghost" aria-label="Home" @click="$emit('navigate','HomePage')">📈 Investify</button>
      </div>

      <nav class="nav" role="navigation">
        <button class="btn btn-ghost" @click="$emit('navigate','HomePage')">Home</button>
        <button v-if="!user" class="btn btn-ghost" @click="$emit('navigate','LoginPage')">Login</button>
        <div v-else class="user-actions" style="display:flex;gap:.5rem;align-items:center">
          <span class="muted">{{ user.username }}</span>
          <button class="btn btn-ghost" @click="logout">Logout</button>
        </div>
        <button class="btn btn-light" @click="toggleTheme">🌗</button>
      </nav>
    </div>
  </header>
</template>

<script setup>
import { ref } from 'vue'
import authService from '../services/authService'

const emits = defineEmits(['navigate','user-changed'])
const props = defineProps({
  toggleTheme: Function,
  currentUser: { type: Object, default: null },
  showLogin: { type: Boolean, default: true }
})

function logout() {
  authService.logout()
  emits('user-changed', null)
  emits('navigate','HomePage')
}
</script>

<style scoped>
.top-bar{position:fixed;inset:0 0 auto 0;z-index:999;background:var(--bg-dark-a);backdrop-filter:blur(6px);padding:0.6rem 0;box-shadow:0 6px 20px rgba(0,0,0,0.12)}
.top-bar-content{display:flex;align-items:center;justify-content:space-between;gap:1rem}
.logo{font-size:1.05rem;font-weight:700}
.nav{display:flex;align-items:center;gap:.6rem}
.muted{color:var(--muted)}
.btn{font-weight:600}
</style>
