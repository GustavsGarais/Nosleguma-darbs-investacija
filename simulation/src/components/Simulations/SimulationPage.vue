<template>
  <div class="sim-page">
    <h2></h2>
    <h2></h2>
    <h1 class="sim-title">Investment Simulations</h1>

    <div class="text-center mb-4">
      <button @click="addSimulation" class="btn-primary">
        + Add New Simulation
      </button>
    </div>

    <div class="sim-content">
      <!-- Side Simulations -->
      <div class="side-sim-list">
        <div
          v-for="sim in simulations.filter(s => s.id !== focusedId)"
          :key="sim.id"
          class="side-sim"
        >
          <input
            v-model="sim.name"
            class="text-sm font-semibold w-full bg-transparent border-b border-gray-500 mb-1"
          />
          <p class="text-xs text-gray-300">💰 €{{ sim.currentValue.toFixed(2) }}</p>
          <div class="flex gap-2">
            <button @click="focusedId = sim.id" class="btn-primary">
              Focus
            </button>
            <button @click="deleteSimulation(sim.id)" class="btn-danger">
              Delete
            </button>
          </div>
        </div>
      </div>

      <!-- Focused Simulation -->
      <div class="focused-sim-container">
        <div v-if="focusedSimulation" class="focused-sim">
          <div class="flex items-center justify-between mb-2">
            <input
              v-model="focusedSimulation.name"
              class="text-lg font-bold bg-transparent border-b border-gray-400 flex-1"
            />
            <button
              @click="deleteSimulation(focusedSimulation.id)"
              class="btn-danger ml-2"
            >
              Delete
            </button>
          </div>

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
import SimulationSetup from '@/components/Simulations/SimulationSetup.vue'
import SimulationControl from '@/components/Simulations/SimulationControl.vue'
import InvestmentChart from '@/components/Simulations/InvestmentChart.vue'
import './SimulationPage.css'

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
        name: `Simulation ${this.nextId - 1}`,
        currentValue: 1000,
        trend: 'neutral',
        isRunning: false,
        interval: null,
        speed: 1000,
        settings: {
          initialInvestment: 1000,
          investors: 10,
          growthRate: 0.05,
          riskAppetite: 0.5,
          marketInfluence: 0.7,
          monthlyContribution: 100,
          inflationRate: 0.02
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
    // ✅ Toggle simulation state
    toggleSimulation(id) {
      const sim = this.simulations.find(s => s.id === id)
      if (!sim) return

      if (sim.isRunning) {
        clearInterval(sim.interval)
        sim.interval = null
        sim.isRunning = false
      } else {
        sim.isRunning = true

        const monthlyContribution = sim.settings.monthlyContribution || 100
        const annualReturn = sim.settings.growthRate || 0.05
        const annualInflation = sim.settings.inflationRate || 0.02
        const monthlyReturnRate = annualReturn / 12
        const monthlyInflationRate = annualInflation / 12

        sim.interval = setInterval(() => {
          sim.currentValue += monthlyContribution

          // ✅ Generate new randomness each tick
          const randomness = (Math.random() * 2 - 1) // range -1 to 1
          const riskImpact = randomness * sim.settings.riskAppetite * sim.settings.marketInfluence
          const adjustedReturn = monthlyReturnRate + riskImpact
          const interestEarned = sim.currentValue * adjustedReturn

          // ✅ Apply interest (gain or loss), cap at 0
          sim.currentValue = Math.max(0, sim.currentValue + interestEarned)

          const monthsElapsed = sim.data.length
          const inflationAdjusted = sim.currentValue / Math.pow(1 + monthlyInflationRate, monthsElapsed)

          const now = new Date()
          const label = now.toLocaleDateString('en-GB', { month: 'short', year: 'numeric' })

          sim.data.push({
            label,
            value: sim.currentValue,
            inflationAdjusted,
            contributions: (monthsElapsed + 1) * monthlyContribution,
            interestEarned
          })

          if (sim.data.length > 50) sim.data.shift()

          const lastValue = sim.data.length > 1 ? sim.data[sim.data.length - 2].value : sim.currentValue
          sim.trend = sim.currentValue > lastValue ? 'up' : 'down'
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
      this.toggleSimulation(id)
      sim.speed = newSpeed

      if (wasRunning) {
        this.toggleSimulation(id)
      }
    },
    deleteSimulation(id) {
      const sim = this.simulations.find(s => s.id === id)
      if (sim?.interval) clearInterval(sim.interval)

      this.simulations = this.simulations.filter(s => s.id !== id)

      if (this.focusedId === id) {
        this.focusedId = this.simulations.length > 0 ? this.simulations[0].id : null
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
