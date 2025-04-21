<template>
  <div class="w-full h-screen flex flex-col px-4">
    <h1 class="text-3xl font-bold text-center mt-4 mb-6">Investment Simulations</h1>

    <div class="mb-4 text-center">
      <button
        @click="addSimulation"
        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition"
      >
        + Add New Simulation
      </button>
    </div>

    <!-- Main Content Layout: Side Simulations + Focused Simulation -->
    <div class="flex flex-row gap-6 flex-1 overflow-hidden">
      <!-- Side Simulations -->
      <div class="w-64 flex flex-col gap-4 overflow-y-auto">
        <div
          v-for="sim in simulations.filter(s => s.id !== focusedId)"
          :key="sim.id"
          class="login-box bg-gray-800 text-white p-4 rounded shadow"
        >
          <p class="text-sm font-semibold">Simulation {{ sim.id }}</p>
          <p class="text-xs text-gray-300">💰 €{{ sim.currentValue.toFixed(2) }}</p>
          <button
            @click="focusedId = sim.id"
            class="text-blue-300 hover:underline text-xs mt-1"
          >
            Focus
          </button>
        </div>
      </div>

      <!-- Focused Simulation -->
      <div class="flex-1 overflow-auto">
        <div
          v-if="focusedSimulation"
          class="login-box w-full bg-gray-900 text-white p-6 rounded shadow"
        >
          <h2 class="text-xl font-bold mb-4">Simulation {{ focusedSimulation.id }}</h2>

          <SimulationSetup
            :simulation="focusedSimulation"
            @update-settings="updateSettings(focusedSimulation.id, $event)"
          />

          <SimulationControl
            :simulation="focusedSimulation"
            @toggle="toggleSimulation(focusedSimulation.id)"
            @reset="resetSimulation(focusedSimulation.id)"
            @change-speed="changeSpeed(focusedSimulation.id, $event)"
          />

          <InvestmentChart :simulation="focusedSimulation" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import SimulationSetup from '@/components/SimulationSetup.vue'
import SimulationControl from '@/components/SimulationControl.vue'
import InvestmentChart from '@/components/InvestmentChart.vue'

export default {
  name: 'SimulationPage',
  components: {
    SimulationSetup,
    SimulationControl,
    InvestmentChart
  },
  data() {
    return {
      nextId: 1,
      focusedId: null,
      simulations: []
    }
  },
  computed: {
    focusedSimulation() {
      return this.simulations.find(sim => sim.id === this.focusedId)
    }
  },
  methods: {
    addSimulation() {
      const newSim = {
        id: this.nextId++,
        currentValue: 1000,
        trend: 'neutral',
        isRunning: false,
        interval: null,
        speed: 1000,
        settings: {
          initialInvestment: 1000,
          investors: 10,
          growthRate: 0.03,
          riskAppetite: 0.5,
          marketInfluence: 0.7
        },
        data: []
      }
      this.simulations.push(newSim)
      this.focusedId = newSim.id
    },
    updateSettings(id, newSettings) {
      const sim = this.simulations.find(s => s.id === id)
      if (sim) {
        sim.settings = { ...sim.settings, ...newSettings }
      }
    },
    toggleSimulation(id) {
      const sim = this.simulations.find(s => s.id === id)
      if (!sim) return

      if (sim.isRunning) {
        clearInterval(sim.interval)
        sim.interval = null
        sim.isRunning = false
      } else {
        sim.isRunning = true
        sim.interval = setInterval(() => {
          const randomFactor = (Math.random() - 0.5) * sim.settings.riskAppetite
          const growth = sim.settings.growthRate + randomFactor * sim.settings.marketInfluence
          const newValue = sim.currentValue * (1 + growth)

          sim.trend = newValue > sim.currentValue ? 'up' : 'down'
          sim.currentValue = newValue
          sim.data.push({
            label: new Date().toLocaleTimeString(),
            value: sim.currentValue
          })

          if (sim.data.length > 50) sim.data.shift()
        }, sim.speed)
      }
    },
    resetSimulation(id) {
      const sim = this.simulations.find(s => s.id === id)
      if (!sim) return

      clearInterval(sim.interval)
      sim.currentValue = sim.settings.initialInvestment
      sim.data = []
      sim.isRunning = false
      sim.trend = 'neutral'
      sim.interval = null
    },
    changeSpeed(id, newSpeed) {
      const sim = this.simulations.find(s => s.id === id)
      if (!sim) return

      const wasRunning = sim.isRunning
      this.toggleSimulation(id) // stop it
      sim.speed = newSpeed

      if (wasRunning) {
        this.toggleSimulation(id) // restart with new speed
      }
    }
  },
  beforeDestroy() {
    this.simulations.forEach(sim => {
      if (sim.interval) clearInterval(sim.interval)
    })
  }
}
</script>

<style scoped>
body.light {
  background: linear-gradient(135deg, #fceabb 0%, #f8b500 100%);
}

body.dark {
  background: linear-gradient(135deg, #1f1c2c 0%, #928dab 100%);
}

.login-box {
  background-color: rgba(255, 255, 255, 0.07);
  backdrop-filter: blur(5px);
  padding: 2rem;
  border-radius: 15px;
  box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
  text-align: center;
}

.login-box {
  background-color: rgba(255, 255, 255, 0.07);
  backdrop-filter: blur(5px);
  padding: 2rem;
  border-radius: 15px;
  box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
  text-align: center;
}

</style>
