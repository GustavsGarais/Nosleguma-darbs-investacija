<template>
  <div class="home-wrapper">

    <!-- Top Bar -->
    <div class="top-bar">
      <button @click="$emit('toggleTheme')">Toggle Theme</button>
      <button @click="$emit('navigate', 'LoginPage')">Login</button>
    </div>

    <!-- Hero Section -->
    <div class="section-box">
      <h1>📊 Investify</h1>
      <p>Master the Market — Risk-Free</p>
      <button @click="$emit('navigate', 'LoginPage')">🚀 Start Simulation</button>
    </div>

    <!-- About Section -->
    <div class="section-box">
      <h2>Why Choose Investify?</h2>
      <p>Invest safely, test strategies, and learn without risking real money.</p>
    </div>

    <!-- Features Section -->
    <div class="section-box">
      <h2>✨ Key Features</h2>
      <ul>
        <li>📈 Real-Time Simulation</li>
        <li>🎛️ Customizable Parameters</li>
        <li>🎓 Learn by Doing</li>
      </ul>
    </div>

    <!-- Testimonial Section -->
    <div class="section-box">
      <p><em>"I never thought I'd understand market shifts — until I tried Investify!"</em></p>
      <p>— A Happy User</p>
    </div>

  </div>
</template>

<script setup>
// No script logic needed here
</script>

<style scoped>
/* Reuse top-bar from LoginPage */
.top-bar {
  position: absolute;
  top: 20px;
  right: 20px;
  display: flex;
  gap: 10px;
}

.top-bar button {
  background: var(--background-blur);
  color: var(--text-color);
  padding: 6px 12px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.top-bar button:hover {
  opacity: 0.85;
}

/* Main wrapper */
.home-wrapper {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 2rem;
  padding: 80px 20px;
  min-height: 100vh;
  box-sizing: border-box;
}

/* Box for each section */
.section-box {
  background-color: var(--background-blur);
  backdrop-filter: blur(5px);
  padding: 2rem;
  border-radius: 15px;
  box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
  width: 90%;
  max-width: 500px;
  text-align: center;
  color: var(--text-color);
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.section-box h1 {
  font-size: 2rem;
  margin-bottom: 0.5rem;
}

.section-box h2 {
  font-size: 1.6rem;
  margin-bottom: 0.5rem;
}

.section-box p,
.section-box li {
  font-size: 1rem;
}

.section-box button {
  padding: 0.6rem 1.2rem;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  background: var(--background-blur);
  color: var(--text-color);
  font-weight: bold;
}

.section-box button:hover {
  opacity: 0.85;
}

ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

li {
  margin-bottom: 0.5rem;
}
</style>
