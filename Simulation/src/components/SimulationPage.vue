<template>
  <div class="sim-page">
    <h2>Simulation Running...</h2>
    <p>Welcome, {{ user }}</p>
    <button @click="logout">Logout</button>
  </div>
</template>

<script>
export default {
  data() {
    return {
      user: ''
    }
  },
  mounted() {
    const loggedIn = localStorage.getItem('loggedInUser')
    if (!loggedIn) {
      this.$emit('navigate', 'LoginPage')
    } else {
      this.user = loggedIn
    }
  },
  methods: {
    logout() {
      localStorage.removeItem('loggedInUser')
      this.$emit('navigate', 'HomePage')
    }
  }
}
</script>

<style scoped>

.page-container {
  min-height: 100vh; /* Ensures it takes full viewport height */
  background-color: var(--background-color);
  color: var(--text-color);
  transition: background-color 0.3s ease;
}

.sim-page {
  padding: 2rem;
  text-align: center;
}
button {
  padding: 0.5rem 1rem;
  margin-top: 1rem;
  background: #444;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}
</style>
