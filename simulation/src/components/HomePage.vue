<template>
  <div class="homepage">
    <section class="hero">
      <h1>📈 Investify</h1>
      <p class="tagline">Simulate. Learn. Dominate the markets.</p>
      <button @click="$emit('navigate', 'LoginPage')" class="cta-button">🚀 Start Simulation</button>
    </section>

    <section class="grid-section">
      <div class="feature-tile" @click="$emit('navigate', 'SimulationInfoPage')">
        <div class="tile-content">
          <h2>📊 Simulation Basics</h2>
          <p>Understand how simulations work and analyze investment outcomes.</p>
        </div>
      </div>

      <div class="feature-tile" @click="$emit('navigate', 'BeginnerGuidePage')">
        <div class="tile-content">
          <h2>📚 Beginner's Guide</h2>
          <p>Start with simple steps to learn how to invest confidently.</p>
        </div>
      </div>
    </section>

    <footer class="footer">
      &copy; 2025 Investify. Built for learning and simulation only.
    </footer>
  </div>
</template>

<script setup>
// No JS needed unless you want to add more logic.
</script>

<style scoped>
.homepage {
  padding: 100px 20px 40px;
  max-width: 1200px;
  margin: 0 auto;
  font-family: 'Inter', sans-serif;
  color: var(--text-color);
}

.hero {
  text-align: center;
  margin-bottom: 4rem;
}

.hero h1 {
  font-size: 4rem;
  margin-bottom: 0.5rem;
}

.tagline {
  font-size: 1.4rem;
  color: var(--text-muted-color);
  margin-bottom: 1.5rem;
}

.cta-button {
  padding: 0.8rem 2rem;
  background-color: #4CAF50;
  color: white;
  font-size: 1.2rem;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.cta-button:hover {
  background-color: #45a049;
}

.grid-section {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 2rem;
  margin-bottom: 4rem;
}

.feature-tile {
  position: relative;
  border-radius: 16px;
  overflow: hidden;
  cursor: pointer;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.feature-tile:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.2);
}

.tile-image {
  width: 100%;
  height: 180px;
  object-fit: cover;
}

.tile-content {
  background-color: var(--background-blur);
  padding: 1.5rem;
  color: var(--text-color);
  backdrop-filter: blur(5px);
}

.tile-content h2 {
  font-size: 1.4rem;
  margin-bottom: 0.5rem;
}

.tile-content p {
  font-size: 1rem;
  color: var(--text-muted-color);
}

.footer {
  text-align: center;
  font-size: 0.9rem;
  color: var(--text-muted-color);
}
</style>
