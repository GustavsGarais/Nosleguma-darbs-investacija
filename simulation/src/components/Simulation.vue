<template>
  <div>
    <!-- Simulation content -->
    <InvestmentChart v-if="isFocused" :data="growthData" />
  </div>
</template>

<script>
import InvestmentChart from './InvestmentChart.vue'

export default {
  components: {
    InvestmentChart
  },
  props: {
    isFocused: {
      type: Boolean,
      default: false
    },
    growthData: {
      type: Array,
      default: () => []
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
  