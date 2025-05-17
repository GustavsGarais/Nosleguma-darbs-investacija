<template>
  <div class="guide-wrapper">
    <h1>📚 Beginner’s Guide to Investify</h1>
    <p>New to investment simulations? No worries — here’s your quick guide to get started and make the most of Investify.</p>

    <div class="guide-section">
      <h2>🎯 What is Investify?</h2>
      <p>Investify is a virtual investment simulator where you can experiment with market scenarios, growth rates, risks, and investor behavior — all without losing real money.</p>
    </div>

    <div class="guide-section">
      <h2>🚀 Getting Started</h2>
      <ul>
        <li>Register or log in to your account.</li>
        <li>Set your <strong>Initial Value</strong>, <strong>Number of Investors</strong>, <strong>Risk Appetite</strong>, and other parameters.</li>
        <li>Click <em>Start Simulation</em> and watch your investments rise and fall over time.</li>
      </ul>
    </div>

    <div class="guide-section">
      <h2>📈 Reading the Simulation</h2>
      <ul>
        <li><strong>Graphs:</strong> Track your investment’s value changes over time.</li>
        <li><strong>Simulation Log:</strong> See updates about market events, fluctuations, and investor reactions.</li>
        <li><strong>Pause / Resume:</strong> Control your simulation speed and timing.</li>
      </ul>
    </div>

    <div class="guide-section">
      <h2>💡 Pro Tips</h2>
      <ul>
        <li>More investors usually means a stabler simulation, but it can slow growth.</li>
        <li>High risk appetite increases volatility — good for short bursts, dangerous long term.</li>
        <li>Experiment with different Market Influence settings to see how events impact your investments.</li>
      </ul>
    </div>

    <div class="button-group">
      <button @click="$emit('navigate', 'HomePage')">⬅️ Back to Home</button>
      <button @click="$emit('navigate', 'SimulationInfoPage')">📊 Simulation Basics</button>
    </div>
  </div>
</template>

<script setup>
// no script needed yet
</script>

<style scoped>
.guide-wrapper {
  max-width: 850px;
  margin: 120px auto 60px;
  padding: 2rem;
  background: var(--background-blur);
  backdrop-filter: blur(6px);
  border-radius: 16px;
  box-shadow: 0 5px 20px rgba(0,0,0,0.4);
  color: var(--text-color);
  font-family: 'Roboto', sans-serif;
}

h1 {
  font-size: 2.6rem;
  margin-bottom: 1rem;
  text-align: center;
}

p {
  font-size: 1.2rem;
  margin-bottom: 1.5rem;
  text-align: center;
}

.guide-section {
  margin-bottom: 2rem;
}

.guide-section h2 {
  font-size: 1.8rem;
  margin-bottom: 0.8rem;
}

.guide-section ul {
  list-style: disc;
  padding-left: 1.5rem;
}

.guide-section li {
  margin-bottom: 0.8rem;
  font-size: 1.15rem;
}

.button-group {
  display: flex;
  justify-content: center;
  gap: 1rem;
  margin-top: 2rem;
}

.button-group button {
  padding: 0.75rem 1.5rem;
  background-color: #4CAF50;
  color: #fff;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 1rem;
  transition: background-color 0.3s ease;
}

.button-group button:hover {
  background-color: #45a049;
}
</style>
