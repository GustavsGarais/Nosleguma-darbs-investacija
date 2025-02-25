<script>
import { ref } from "vue";
import InvestmentGraph from "./InvestmentGraph.vue";
export default {
  components: { InvestmentGraph },
  setup() {
    const simulationStarted = ref(false);
    const investmentData = ref([]); // Store investment values

    const startSimulation = () => {
      simulationStarted.value = true;
      investmentData.value = [1000]; // Initial investment value

      let value = 1000;
      setInterval(() => {
        value += Math.random() * 100 - 50; // Simulate fluctuations
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
      <InvestmentGraph :investmentData="investmentData" />
      <p>Current Investment Value: {{ investmentData[investmentData.length - 1].toFixed(2) }}</p>
    </div>
  </div>
</template>
