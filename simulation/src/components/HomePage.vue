<template>
  <div class="home-page flex flex-col min-h-screen bg-gradient-to-br from-purple-800 to-indigo-900 text-white">

    <!-- Header -->
    <header class="flex justify-between items-center px-8 py-4 shadow-lg bg-white/5 backdrop-blur-sm">
      <h1 class="text-2xl font-bold flex items-center gap-2">
        📊 Investify
      </h1>
      <div class="flex items-center gap-4">
        <button @click="$emit('toggleTheme')" class="px-4 py-2 bg-gray-700 rounded hover:bg-gray-600 transition">Toggle Theme</button>
        <button @click="$emit('navigate', 'LoginPage')" class="px-4 py-2 bg-green-500 rounded hover:bg-green-600 transition">Login</button>
      </div>
    </header>

    <!-- Hero Section -->
    <section class="flex flex-col items-center justify-center flex-grow text-center px-6 py-16 space-y-6">
      <h2 class="text-6xl font-bold drop-shadow-lg">Master the Market — Risk-Free</h2>
      <p class="text-xl max-w-3xl text-gray-300 leading-relaxed">
        Simulate real market trends, adjust risk strategies, and test your skills without risking a cent. Perfect for students, beginners, or anyone curious about how investments behave.
      </p>
      <button
        @click="$emit('navigate', 'LoginPage')"
        class="px-8 py-4 rounded-full bg-blue-500 hover:bg-blue-600 text-white text-lg font-semibold shadow-lg transition"
      >
        🚀 Start Your First Simulation
      </button>
    </section>

    <!-- About Section -->
    <section class="px-6 py-12 text-center bg-white/5 backdrop-blur-sm">
      <h3 class="text-3xl font-bold mb-4">Why Choose Investify?</h3>
      <p class="text-lg max-w-3xl mx-auto text-gray-300 leading-relaxed">
        Investing can be complicated — so we built a safe, playful, and educational platform where you can explore market dynamics, growth models, and risk without any real money.
      </p>
    </section>

    <!-- Features Section -->
    <section class="px-6 py-16 text-center">
      <h3 class="text-3xl font-bold mb-8">✨ Key Features</h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
        <div>
          <div class="text-5xl mb-4">📈</div>
          <h4 class="text-xl font-semibold mb-2">Real-Time Simulation</h4>
          <p class="text-gray-300">Track investment value changes with market volatility in a live-like environment.</p>
        </div>
        <div>
          <div class="text-5xl mb-4">🎛️</div>
          <h4 class="text-xl font-semibold mb-2">Customizable Parameters</h4>
          <p class="text-gray-300">Adjust growth rates, investor count, and risk appetite to control your outcomes.</p>
        </div>
        <div>
          <div class="text-5xl mb-4">🎓</div>
          <h4 class="text-xl font-semibold mb-2">Learn by Doing</h4>
          <p class="text-gray-300">Test strategies safely, gain insights, and build your investment instincts.</p>
        </div>
      </div>
    </section>

    <!-- Testimonial Section -->
    <section class="px-6 py-12 text-center bg-white/5 backdrop-blur-sm">
      <p class="italic text-xl text-gray-300 max-w-3xl mx-auto">
        "I never thought I'd understand market shifts — until I tried Investify. It's addictive, insightful, and actually fun!"
      </p>
      <p class="mt-4 text-gray-400">— A Happy User</p>
    </section>

    <!-- Footer -->
    <footer class="text-center py-4 text-gray-400 text-sm bg-white/5 backdrop-blur-sm shadow-inner">
      <p>&copy; 2025 Investify. Created by Gustavs Garais. Made with Vue.js & Laravel.</p>
    </footer>

  </div>
</template>

<script setup>
// no logic needed
</script>

<style scoped>
.home-page {
  font-family: Arial, sans-serif;
  text-align: center;
}

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
</style>
