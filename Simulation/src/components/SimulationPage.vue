<template>
  <div class="p-6">
    <h1 class="text-3xl font-bold mb-4 text-center">Investment Simulations</h1>

    <button
      @click="addSimulation"
      class="mb-6 px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-xl"
    >
      + Add New Simulation
    </button>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
      <div
        v-for="simulation in simulations"
        :key="simulation.id"
        class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow"
      >
        <h2 class="text-xl font-semibold mb-2">Simulation {{ simulation.id }}</h2>

        <SimulationSetup
          :simulation="simulation"
          @update-settings="updateSettings(simulation.id, $event)"
        />

        <SimulationControl
          :simulation="simulation"
          @toggle="toggleSimulation(simulation.id)"
          @reset="resetSimulation(simulation.id)"
          @change-speed="changeSpeed(simulation.id, $event)"
        />

        <InvestmentChart :simulation="simulation" />

        <div class="mt-4">
          <p>
            💰 Current Value:
            <span
              :class="{
                'text-green-500': simulation.trend === 'up',
                'text-red-500': simulation.trend === 'down'
              }"
              class="font-bold"
            >
              {{ simulation.currentValue.toFixed(2) }} €
            </span>
          </p>
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
      simulations: []
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
</style>
