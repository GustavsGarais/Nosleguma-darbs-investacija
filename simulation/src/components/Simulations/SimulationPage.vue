<template>
  <div class="sim-page">
    <h1 class="sim-title">__</h1>

    <div class="username-box">
      <button @click="showSettings = !showSettings" class="btn-primary">
        ⚙️ {{ showSettings ? 'Back to Simulations' : 'User Settings' }}
      </button>
    </div>

    <div v-if="showSettings">
      <GoToUserSettings />
    </div>

    <div v-else>
      <div class="text-center mb-2">
        <button @click="addSimulation" class="btn-primary">
          + Add New Simulation
        </button>
      </div>

      <!-- The key wrapper with horizontal flex layout -->
      <div class="simulation-page-container sim-content">
        <!-- Side simulations -->
        <div class="simulation-list side-sim-list">
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
            <div class="flex gap-2 mt-1">
              <button @click="focusedId = sim.id" class="btn-primary">
                Focus
              </button>
              <button @click="deleteSimulation(sim.id)" class="btn-danger">
                Delete
              </button>
            </div>
          </div>
        </div>

        <!-- Focused simulation -->
        <div class="focused-sim-container">
          <div v-if="focusedSimulation" class="active-simulation">
            <div class="flex items-center justify-between mb-3">
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
              @save="saveSimulation"
            />
            <div class="login-box" style="margin-top: 12px; text-align: left;">
              <h3 class="text-md font-semibold mb-2">Summary</h3>
              <ul style="list-style: none; padding: 0; margin: 0; display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 8px;">
                <li><strong>Current</strong>: €{{ focusedSimulation.currentValue.toFixed(2) }}</li>
                <li><strong>Invested</strong>: €{{ totalContributions.toFixed(2) }}</li>
                <li><strong>Gain</strong>: €{{ (focusedSimulation.currentValue - totalContributions).toFixed(2) }}</li>
                <li><strong>Real (est.)</strong>: €{{ latestRealValue.toFixed(2) }}</li>
                <li><strong>CAGR (est.)</strong>: {{ displayCagr }}</li>
              </ul>
            </div>
            <InvestmentChart :simulation="focusedSimulation" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>



<script>
import axios from 'axios'
import SimulationSetup from '@/components/Simulations/SimulationSetup.vue'
import SimulationControl from '@/components/Simulations/SimulationControl.vue'
import InvestmentChart from '@/components/Simulations/InvestmentChart.vue'
import GoToUserSettings from '@/components/user_settings/goToUserSettings.vue'
import './SimulationPage.css'

export default {
  name: 'SimulationPage',
  components: {
    SimulationSetup,
    SimulationControl,
    InvestmentChart,
    GoToUserSettings
  },
  data() {
    return {
      showSettings: false,
      username: '',
      nextId: 1,
      focusedId: null,
      simulations: [],
      saveMessage: ''
    }
  },
  computed: {
    focusedSimulation() {
      return this.simulations.find(sim => sim.id === this.focusedId)
    },
    totalContributions() {
      if (!this.focusedSimulation) return 0
      const months = this.focusedSimulation.data.length
      const monthly = this.focusedSimulation.settings.monthlyContribution || 0
      return months * monthly
    },
    latestRealValue() {
      if (!this.focusedSimulation) return 0
      if (this.focusedSimulation.data.length === 0) {
        return this.focusedSimulation.settings.initialInvestment || 0
      }
      const last = this.focusedSimulation.data[this.focusedSimulation.data.length - 1]
      return last.inflationAdjusted ?? (typeof last.value === 'number' ? last.value : (last.value?.amount || 0))
    },
    displayCagr() {
      if (!this.focusedSimulation) return '—'
      const dataLen = this.focusedSimulation.data.length
      if (dataLen < 12) return '—'
      const years = dataLen / 12
      const start = this.focusedSimulation.settings.initialInvestment || 0
      const end = this.focusedSimulation.currentValue || 0
      if (start <= 0 || end <= 0) return '—'
      const cagr = Math.pow(end / start, 1 / years) - 1
      return (cagr * 100).toFixed(2) + '%'
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

          const randomness = (Math.random() * 2 - 1)
          const riskImpact = randomness * sim.settings.riskAppetite * sim.settings.marketInfluence
          const adjustedReturn = monthlyReturnRate + riskImpact
          const interestEarned = sim.currentValue * adjustedReturn

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
    },
    async loadSavedSimulations() {
      const userId = localStorage.getItem('loggedInUserId')
      if (!userId) return
      try {
        const res = await axios.post('http://localhost:8000/saving_simulations/get_simulation.php', { user_id: userId })
        if (!res.data?.success) return
        const savedSims = Array.isArray(res.data.simulations) ? res.data.simulations : []
        this.simulations = savedSims.map((sim) => {
          const settings = sim.settings || {}
          return {
            id: this.nextId++,
            name: sim.name || sim.sim_name || `Simulation ${this.nextId - 1}`,
            currentValue: settings.initialInvestment ?? sim.initial_investment ?? 1000,
            trend: 'neutral',
            isRunning: false,
            interval: null,
            speed: 1000,
            settings: {
              initialInvestment: settings.initialInvestment ?? sim.initial_investment ?? 1000,
              investors: settings.investors ?? sim.num_investors ?? 10,
              growthRate: settings.growthRate ?? sim.growth_rate ?? 0.05,
              riskAppetite: settings.riskAppetite ?? sim.risk_appetite ?? 0.5,
              marketInfluence: settings.marketInfluence ?? sim.market_influence ?? 0.7,
              monthlyContribution: settings.monthlyContribution ?? 100,
              inflationRate: settings.inflationRate ?? 0.02
            },
            data: []
          }
        })
        if (this.simulations.length > 0) this.focusedId = this.simulations[0].id
      } catch (err) {
        console.error('Error fetching simulations:', err)
      }
    },
    async saveSimulation() {
      if (!this.focusedSimulation) {
        this.saveMessage = 'No simulation is focused.'
        return
      }
      const userId = localStorage.getItem('loggedInUserId')
      if (!userId) {
        this.saveMessage = 'You must be logged in to save simulations.'
        return
      }
      try {
        const payload = {
          user_id: userId,
          name: this.focusedSimulation.name,
          settings: this.focusedSimulation.settings
        }
        const res = await axios.post('http://localhost:8000/saving_simulations/save_simulation.php', payload)
        this.saveMessage = res.data?.success ? 'Simulation saved successfully!' : (res.data?.message || 'Save failed')
      } catch (err) {
        console.error(err)
        this.saveMessage = 'Error saving simulation.'
      }
    }
  },
  mounted() {
    this.username = localStorage.getItem('username') || 'Guest'
    this.loadSavedSimulations()
  },
  beforeDestroy() {
    this.simulations.forEach(sim => {
      if (sim.interval) clearInterval(sim.interval)
    })
  }
}
</script>
