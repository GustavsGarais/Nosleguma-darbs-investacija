<template>
  <div class="p-6">
    <h1 class="text-3xl font-bold mb-6 text-center">Investment Simulations</h1>

    <div class="mb-6 text-center">
      <button
        @click="addSimulation"
        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
      >
        + Add New Simulation
      </button>
    </div>

    <div class="flex gap-6">
      <!-- Side simulations (condensed) -->
      <div class="w-1/3 space-y-4" v-if="simulations.length > 1">
        <div
          v-for="sim in simulations.filter(s => s.id !== focusedId)"
          :key="sim.id"
          class="bg-white dark:bg-gray-700 p-4 rounded-xl shadow"
        >
          <p class="text-sm font-semibold dark:text-white">Simulation {{ sim.id }}</p>
          <p class="text-xs text-gray-500">€{{ sim.currentValue.toFixed(2) }}</p>
          <button @click="focusedId = sim.id" class="text-blue-600 hover:underline text-xs mt-1">
            Focus
          </button>
        </div>
      </div>

      <!-- Focused simulation -->
      <div class="flex-1" v-if="focusedSimulation">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md">
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
            time: new Date().toLocaleTimeString(),
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
  background: linear-gradient(to right, #f5e9b3, #a8d87b);
}
body.dark {
  background: linear-gradient(to right, #4a148c, #000000);
}
body {
  display: block;
  text-align: left;
}

#app {
  max-width: 100%;
  margin: 0;
  padding: 0;
}
</style>