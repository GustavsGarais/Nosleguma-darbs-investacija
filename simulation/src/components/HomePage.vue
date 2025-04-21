<template>
  <div class="home-page">
    <header class="header">
      <h1>📈 Investify</h1>
    </header>

    <section class="hero">
      <h2>Welcome to Investify</h2>
      <p>
        Our investment simulator helps you understand how different factors affect your investments over time. 
        Whether you're a beginner or just curious, you can experiment with values like market influence, risk appetite, 
        and growth rates — all in a risk-free environment.
      </p>
      <button @click="$emit('navigate', 'LoginPage')" class="cta-button">Start Now</button>
    </section>

    <footer class="footer">
      <p>&copy; 2025 Investify</p>
    </footer>
  </div>
</template>

<style scoped>
.home-page {
  font-family: Arial, sans-serif;
  text-align: center;
  padding: 20px;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 0;
}

.nav a {
  margin: 0 10px;
  text-decoration: none;
  color: #333;
}

.hero {
  margin: 40px 0;
  max-width: 800px;
  margin-left: auto;
  margin-right: auto;
}

.hero p {
  font-size: 1.1em;
  line-height: 1.6;
  margin: 20px 0;
}

.cta-button {
  padding: 10px 25px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 1em;
  cursor: pointer;
  transition: background-color 0.3s;
}

.cta-button:hover {
  background-color: #0056b3;
}

.footer {
  margin-top: 60px;
  font-size: 0.9em;
  color: #666;
}
</style>
