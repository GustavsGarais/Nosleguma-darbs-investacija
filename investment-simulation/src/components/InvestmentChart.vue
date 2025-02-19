<script>
import { ref } from "vue";
import InvestmentChart from "./InvestmentChart.vue";

export default {
  components: { InvestmentChart },
  setup() {
    const simulationStarted = ref(false);
    const investmentData = ref([]); // Array to store investment values

    const startSimulation = () => {
      simulationStarted.value = true;
      investmentData.value = [1000]; // Initial investment value

      // Simulate investment growth over time
      let value = 1000;
      setInterval(() => {
        value += Math.random() * 100 - 50; // Random increase/decrease
        investmentData.value.push(value);
      }, 1000);
    };

    return { simulationStarted, investmentData, startSimulation };
  },
};
</script>

<template>
  <div>
    <button @click="startSimulation" v-if="!simulationStarted">
      Start Simulation
    </button>

    <div v-if="simulationStarted">
      <InvestmentChart :investmentData="investmentData" />
      <p>Current Investment Value: {{ investmentData[investmentData.length - 1].toFixed(2) }}</p>
    </div>
  </div>
</template>
