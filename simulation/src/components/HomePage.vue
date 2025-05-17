<template>
  <div class="homepage">
    <TopBar :toggleTheme="toggleTheme" :showLogin="true" :showLoginModal="() => showLoginModal = true" @navigate="navigateTo" />

    <section class="hero">
      <h1>📈 Investify</h1>
      <p class="tagline">Simulate. Learn. Dominate the markets.</p>
    </section>

    <section class="nav-cards">
      <div class="card" @click="navigateTo('SimulationPage')">
        <h2>🎮 Start Simulation</h2>
        <p>Launch your custom investment simulation with adjustable parameters.</p>
      </div>

      <div class="card" @click="navigateTo('SimulationInfoPage')">
        <h2>📊 Simulation Basics</h2>
        <p>Learn how simulations work, and what each stat means for your portfolio.</p>
      </div>

      <div class="card" @click="navigateTo('BeginnerGuidePage')">
        <h2>📚 Beginner's Guide</h2>
        <p>Follow this quick-start guide and dive right in.</p>
      </div>

      <div class="card" @click="navigateTo('ChangelogPage')">
        <h2>📝 Changelog</h2>
        <p>See what's new, improved, or broken since the last update.</p>
      </div>
    </section>

    <footer class="footer">
      <p>&copy; 2025 Investify Project. Built for learning and simulation only.</p>
    </footer>

    <LoginPage v-if="showLoginModal" @close="showLoginModal = false" />
  </div>
</template>

<script setup>
import TopBar from '@/components/TopBar.vue'
import LoginPage from '@/components/LoginPage.vue'
import { ref } from 'vue'

const showLoginModal = ref(false)

function toggleTheme() {
  document.body.classList.toggle('dark-theme')
}

function navigateTo(page) {
  console.log('Navigating to:', page)
  // This is where you’d integrate router.push() or page state swapping
}
</script>

<style scoped>
.homepage {
  max-width: 1200px;
  margin: 100px auto 60px;
  padding: 0 2rem;
  font-family: 'Inter', sans-serif;
  color: var(--text-color);
}

.hero {
  text-align: center;
  margin-bottom: 3rem;
}

.hero h1 {
  font-size: 4rem;
  margin-bottom: 0.5rem;
}

.tagline {
  font-size: 1.4rem;
  color: var(--text-muted-color);
}

.nav-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 1.5rem;
}

.card {
  background: var(--card-bg);
  border-radius: 18px;
  padding: 2rem;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
  cursor: pointer;
  transition: transform 0.2s ease, box-shadow 0.3s ease;
  text-align: left;
}

.card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
}

.card h2 {
  font-size: 1.6rem;
  margin-bottom: 0.8rem;
}

.card p {
  font-size: 1.05rem;
  color: var(--text-muted-color);
  line-height: 1.5;
}

.footer {
  text-align: center;
  margin-top: 4rem;
  font-size: 0.9rem;
  color: var(--text-muted-color);
}
</style>
