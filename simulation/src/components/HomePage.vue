<template>
  <div class="home-page flex flex-col min-h-screen bg-gradient-to-br from-purple-800 to-indigo-900 text-white">
    <header class="header flex items-center justify-center py-6 shadow-lg bg-white/5 backdrop-blur-sm">
    </header>

    <section class="hero flex flex-col items-center justify-center flex-grow text-center px-6">
      <h2 class="text-5xl font-bold mb-6 drop-shadow-lg">Welcome to Investify</h2>
      <p class="text-lg max-w-2xl mb-8 text-gray-300 leading-relaxed">
        Our investment simulator helps you understand how different factors affect your investments over time.
        Whether you're a beginner or just curious, you can experiment with market influence, risk appetite,
        and growth rates — all in a risk-free environment.
      </p>
      <button
        @click="$emit('navigate', 'LoginPage')"
        class="cta-button px-8 py-4 rounded-full bg-green-500 hover:bg-green-600 text-white text-lg font-semibold shadow-lg transition duration-300"
      >
        🚀 Start Now
      </button>
    </section>

    <footer class="footer text-center py-4 text-gray-400 text-sm bg-white/5 backdrop-blur-sm shadow-inner">
      <p>&copy; 2025 Investify. All rights reserved.</p>
    </footer>
  </div>
</template>

<script setup>
// No logic needed for now.
</script>

<style scoped>
.hero {
  animation: fadeIn 1s ease-in-out both;
}

@keyframes fadeIn {
  0% {
    opacity: 0;
    transform: translateY(20px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

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
