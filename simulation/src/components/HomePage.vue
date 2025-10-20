<template>
  <div class="homepage">
    <template>
      <div class="homepage">
        <section class="hero card">
          <div class="container" style="display:flex;gap:1.25rem;align-items:center;flex-wrap:wrap">
            <div style="flex:1;min-width:260px">
              <h1>Investify — Simulate to learn</h1>
              <p class="muted">Paper-trade strategies, test ideas across market scenarios, and learn what works without risking money.</p>
              <div style="margin-top:1rem;display:flex;gap:.6rem;flex-wrap:wrap">
                <button class="btn btn-primary" @click="$emit('navigate','SimulationPage')">Start a simulation</button>
                <button class="btn btn-ghost" @click="$emit('navigate','BeginnerGuidePage')">Beginner's guide</button>
              </div>
            </div>

            <div style="width:360px;min-width:220px">
              <div class="card" style="padding:.6rem;background:linear-gradient(90deg,rgba(79,70,229,0.12),rgba(59,130,246,0.08))">
                <strong>Quick stats</strong>
                <div style="display:flex;justify-content:space-between;margin-top:.6rem">
                  <div>
                    <div class="muted">Simulations</div>
                    <div style="font-weight:700">128</div>
                  </div>
                  <div>
                    <div class="muted">Active users</div>
                    <div style="font-weight:700">27</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <section class="container" style="margin-top:1.25rem">
          <h2>Features</h2>
          <div class="feature-grid">
            <div class="feature-card card">
              <div class="icon">📊</div>
              <h3>Interactive charts</h3>
              <p class="muted">Visualize portfolio performance and asset breakdowns.</p>
            </div>
            <div class="feature-card card">
              <div class="icon">⚙️</div>
              <h3>Custom strategies</h3>
              <p class="muted">Set rules, rebalance frequency and risk parameters.</p>
            </div>
            <div class="feature-card card">
              <div class="icon">💾</div>
              <h3>Save & share</h3>
              <p class="muted">Persist simulations to your account and share with others.</p>
            </div>
          </div>
        </section>

        <section class="container" style="margin-top:1.25rem">
          <h2>Frequently asked</h2>
          <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem;margin-top:1rem">
            <div class="card">
              <strong>Is this real trading?</strong>
              <p class="muted">No — simulations are for learning and educational purposes only.</p>
            </div>
            <div class="card">
              <strong>Do I need an account?</strong>
              <p class="muted">You can explore as a guest, but saving simulations requires an account.</p>
            </div>
          </div>
        </section>

        <footer class="container footer muted" style="margin-top:1.8rem;padding-bottom:2rem">
          <div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap">
            <div>© 2025 Investify</div>
            <div style="display:flex;gap:.6rem">
              <button class="btn btn-ghost">Privacy</button>
              <button class="btn btn-ghost">Terms</button>
            </div>
          </div>
        </footer>
      </div>
    </template>

    <script>
    export default { name: 'HomePage' }
    </script>

    <style scoped>
    .homepage{padding-top:0}
    .hero h1{font-size:1.6rem;margin:0}
    .feature-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem;margin-top:.6rem}
    .feature-card{padding:1rem;cursor:pointer;transition:transform .15s ease}
    .feature-card:hover{transform:translateY(-6px)}
    .icon{font-size:1.4rem;margin-bottom:.6rem}
    .footer{margin-top:1.6rem}
    </style>
