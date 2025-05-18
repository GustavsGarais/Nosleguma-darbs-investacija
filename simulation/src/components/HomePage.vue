<template>
  <div class="homepage">
    <section class="hero">
      <h1>📈 Welcome to Investify</h1>
      <p class="tagline">Test strategies. Track trends. Learn risk-free.</p>
      <button @click="$emit('navigate', 'LoginPage')" class="cta-button">Start Simulating</button>
    </section>

    <section class="grid-section">
      <div class="feature-tile" @click="$emit('navigate', 'SimulationInfoPage')">
        <div class="tile-content">
          <h2>📊 What is a Simulation?</h2>
          <p>Discover how investment simulations help sharpen your market sense without real money on the line.</p>
        </div>
      </div>

      <div class="feature-tile" @click="$emit('navigate', 'BeginnerGuidePage')">
        <div class="tile-content">
          <h2>📚 Getting Started</h2>
          <p>New to investing? Follow our beginner-friendly guide to confidently start experimenting.</p>
        </div>
      </div>
    </section>

    <footer class="footer">
      &copy; 2025 Investify. This platform is for educational simulations only.
    </footer>
  </div>
</template>

<script setup>
// no JS needed for now
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
  font-size: 1.35rem;
  color: var(--text-muted-color);
  margin-bottom: 1.5rem;
}

.cta-button {
  padding: 0.85rem 2.2rem;
  background-color: #4CAF50;
  color: #fff;
  font-size: 1.15rem;
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

.tile-content {
  background-color: var(--background-blur);
  padding: 1.5rem;
  color: var(--text-color);
  backdrop-filter: blur(5px);
}

.tile-content h2 {
  font-size: 1.35rem;
  margin-bottom: 0.6rem;
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
