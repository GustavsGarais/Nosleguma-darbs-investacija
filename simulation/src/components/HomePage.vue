<template>
  <div class="homepage">

    <section class="hero">
      <h2>Your Simulation Hub</h2>
      <p>Experiment with strategies, test market trends, and track performance risk-free.</p>
      <button class="primary-btn" @click="$emit('navigate', 'LoginPage')">Start Simulating</button>
    </section>

    <section class="feature-grid">
      <div class="feature-card" @click="$emit('navigate', 'BeginnerGuidePage')">
        <div class="icon">📚</div>
        <h3>Beginner’s Guide</h3>
        <p>New to investing? Learn the basics and start your first simulation today.</p>
      </div>
    </section>

    <footer class="footer">
      <p>&copy; 2025 Investify — Simulations for educational purposes only.</p>
    </footer>
  </div>
</template>

<script setup>
// No JS logic for now
</script>

<style scoped>
.homepage {
  max-width: 1100px;
  margin: 0 auto;
  padding: 2rem;
  font-family: 'Inter', sans-serif;
  color: var(--text-color);
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 3rem;
}

.header h1 {
  font-size: 2rem;
  font-weight: 700;
}

.login-btn {
  padding: 0.5rem 1.2rem;
  background-color: #0F62FE;
  color: #fff;
  border: none;
  border-radius: 6px;
  font-size: 0.95rem;
  cursor: pointer;
}

.login-btn:hover {
  background-color: #0043CE;
}

.hero {
  text-align: center;
  margin-bottom: 4rem;
}

.hero h2 {
  font-size: 2.5rem;
  margin-bottom: 0.5rem;
}

.hero p {
  font-size: 1.15rem;
  color: var(--text-muted-color);
  margin-bottom: 1.5rem;
}

.primary-btn {
  padding: 0.8rem 2rem;
  background-color: #0F62FE;
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  cursor: pointer;
}

.primary-btn:hover {
  background-color: #0043CE;
}

.feature-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 4rem;
}

.feature-card {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 12px;
  padding: 1.5rem;
  text-align: center;
  cursor: pointer;
  transition: all 0.3s ease;
}

.feature-card:hover {
  background: rgba(255, 255, 255, 0.07);
  transform: translateY(-4px);
}

.icon {
  font-size: 2rem;
  margin-bottom: 1rem;
}

.feature-card h3 {
  font-size: 1.25rem;
  margin-bottom: 0.5rem;
}

.feature-card p {
  font-size: 0.95rem;
  color: var(--text-muted-color);
}

.footer {
  text-align: center;
  font-size: 0.85rem;
  color: var(--text-muted-color);
}
</style>
