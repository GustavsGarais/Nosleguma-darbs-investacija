<template>
  <div class="sim-page">
    <h2>Investment Simulation</h2>
    <p v-if="!started">Define your simulation parameters below</p>
    <SimulationSetup v-if="!started" @start="startSimulation" />
    <div v-else>
      <SimulationControl
        :isPaused="paused"
        :speed="speed"
        @togglePause="togglePause"
        @speedChange="updateSpeed"
      />
      <InvestmentChart :data="history" />
    </div>
  </div>
</template>

<script>
import SimulationSetup from './SimulationSetup.vue'
import SimulationControl from './SimulationControl.vue'
import InvestmentChart from './InvestmentChart.vue'

export default {
  name: 'SimulationPage',
  components: { SimulationSetup, SimulationControl, InvestmentChart },
  data() {
    return {
      started: false,
      paused: false,
      speed: 1,
      initialParams: null,
      currentValue: 0,
      history: []
    }
  },
  methods: {
    startSimulation(params) {
      this.initialParams = params
      this.currentValue = params.startValue
      this.history = [ { time: 0, value: this.currentValue } ]
      this.started = true
      this.runLoop()
    },
    runLoop() {
      const step = () => {
        if (!this.started) return
        if (!this.paused) {
          const { marketInfluence, riskAppetite, growthRate } = this.initialParams
          const change = ((Math.random() - 0.5) * riskAppetite * marketInfluence) / 100 + (growthRate / 100)
          this.currentValue += this.currentValue * change
          this.history.push({ time: this.history.length, value: this.currentValue })
        }
        setTimeout(step, 1000 / this.speed)
      }
      step()
    },
    togglePause() {
      this.paused = !this.paused
    },
    updateSpeed(newSpeed) {
      this.speed = newSpeed
    }
  }
}
</script>

<style scoped>

.page-container {
  min-height: 100vh; /* Ensures it takes full viewport height */
  background-color: var(--background-color);
  color: var(--text-color);
  transition: background-color 0.3s ease;
}

.sim-page {
  padding: 2rem;
  text-align: center;
}
button {
  padding: 0.5rem 1rem;
  margin-top: 1rem;
  background: #444;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}
</style>
