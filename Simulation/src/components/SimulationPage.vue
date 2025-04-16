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
