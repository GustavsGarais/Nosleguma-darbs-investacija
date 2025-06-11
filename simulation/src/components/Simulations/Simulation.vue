<template>
  <div>
    <InvestmentChart :simulation="simulation" />
  </div>
</template>

<script>
import InvestmentChart from './InvestmentChart.vue';

export default {
  props: {
    simulation: Object
  },
  components: {
    InvestmentChart
  }
};
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
  .simulation-sidebar {
  width: 300px;
  }
</style>
  