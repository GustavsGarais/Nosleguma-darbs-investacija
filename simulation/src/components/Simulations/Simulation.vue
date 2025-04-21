<template>
  <div class="flex flex-col items-center">
    <!-- Only show chart for focused simulation -->
    <InvestmentChart v-if="focusedSimulation" :simulation="focusedSimulation" />
  </div>
</template>

<script>
import InvestmentChart from './InvestmentChart.vue'

export default {
  name: 'Simulation',
  components: {
    InvestmentChart
  },
  data() {
    return {
      simulations: [
        {
          id: 1,
          data: [
            { value: 100 },
            { value: 105 },
            { value: 110 }
          ],
          settings: {
            initialInvestment: 100
          }
        },
        {
          id: 2,
          data: [
            { value: 100 },
            { value: 98 },
            { value: 102 }
          ],
          settings: {
            initialInvestment: 100
          }
        }
      ],
      focusedId: 1 // ← this changes which simulation is "focused"
    }
  },
  computed: {
    focusedSimulation() {
      return this.simulations.find(sim => sim.id === this.focusedId)
    }
  }
}
</script>
  
  <style scoped>
  .focused {
    display: flex;
    justify-content: center;
    align-items: center;
    /* Additional styling for centering */
  }
  
  .unfocused {
    display: flex;
    justify-content: flex-start;
    /* Additional styling for left alignment */
  }
  </style>
  