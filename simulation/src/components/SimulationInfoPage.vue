<template>
  <div class="info-wrapper">
    <h1>📊 How Simulations Work</h1>
    <p>Simulations on Investify are virtual models of real-world investment markets, letting you experiment with risk-free strategies.</p>

    <div class="info-section">
      <h2>🔍 What You'll See</h2>
      <ul>
        <li><strong>Initial Value:</strong> Starting amount for your simulated investment.</li>
        <li><strong>Number of Investors:</strong> Affects volatility — more investors means a stabler simulation.</li>
        <li><strong>Market Influence Factor:</strong> How sensitive your investment is to external events.</li>
        <li><strong>Risk Appetite:</strong> Controls how aggressively your simulated investors react.</li>
        <li><strong>Investment Growth Rate:</strong> The baseline growth trend over time.</li>
      </ul>
    </div>

    <div class="info-section">
      <h2>💡 Why Simulate?</h2>
      <p>Practice strategy-building without real-world consequences. Watch how different factors affect outcomes and learn to manage risks smartly.</p>
    </div>

    <div class="button-group">
      <button @click="$emit('navigate', 'HomePage')">⬅️ Back to Home</button>
      <button @click="$emit('navigate', 'LoginPage')">🚀 Start Simulating</button>
    </div>
  </div>
</template>

<script setup>
// No logic needed yet
</script>

<style scoped>
.info-wrapper {
  max-width: 800px;
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

.info-section {
  margin-bottom: 2rem;
}

.info-section h2 {
  font-size: 1.8rem;
  margin-bottom: 0.8rem;
}

.info-section ul {
  list-style: none;
  padding: 0;
}

.info-section li {
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
