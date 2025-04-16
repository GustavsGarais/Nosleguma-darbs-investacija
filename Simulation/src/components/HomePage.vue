<template>
  <div class="page-fill">
    <h1>Investment Simulator</h1>
    <p>Understand how markets work — safely and interactively.</p>
    <button @click="$emit('navigate', 'LoginPage')">Start Simulation</button>
  </div>
</template>

<script>
export default {}
</script>

<style scoped>
.homepage {
  text-align: center;
  margin-top: 20vh;
}

h1 {
  font-family: monospace;
  font-size: 2.5rem;
}

p {
  margin: 1rem 0 2rem;
  font-style: italic;
}

button {
  padding: 0.6rem 1.2rem;
  font-size: 1rem;
  border: none;
  border-radius: 8px;
  background-color: #282c34;
  color: white;
  cursor: pointer;
}
.page-container {
  min-height: 100vh; /* Ensures it takes full viewport height */
  background-color: var(--background-color);
  color: var(--text-color);
  transition: background-color 0.3s ease;
}
</style>
