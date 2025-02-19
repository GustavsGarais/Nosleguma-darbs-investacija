<script>
import { ref } from "vue";
import InvestmentChart from "./InvestmentChart.vue";
import SimulationControl from "./SimulationControl.vue";

export default {
  components: { InvestmentChart, SimulationControl },
  setup() {
    const initialValue = ref(1000);
    const investors = ref(10);
    const volatility = ref(5);
    const isRunning = ref(false);
    const investmentData = ref([]);
    const simulationStarted = ref(false);
    let interval = null;

    const startSimulation = () => {
      if (interval) clearInterval(interval);
      investmentData.value = [initialValue.value];
      simulationStarted.value = true;

      interval = setInterval(() => {
        if (!isRunning.value) return;
        let lastValue = investmentData.value[investmentData.value.length - 1];
        let change = (Math.random() * volatility.value * 2 - volatility.value) * (investors.value / 10);
        investmentData.value.push(Math.max(0, lastValue + change));
      }, 1000);
    };

    const toggleSimulation = () => {
      isRunning.value = !isRunning.value;
    };

    const updateInvestment = (newValue) => {
      investmentData.value.push(newValue);
    };

    return {
      initialValue,
      investors,
      volatility,
      isRunning,
      investmentData,
      simulationStarted,
      startSimulation,
      toggleSimulation,
      updateInvestment,
    };
  },
};
</script>

<template>
  <div class="simulation-container">
    <h3>Setup Simulation</h3>
    <input v-model.number="initialValue" type="number" placeholder="Initial Value" />
    <input v-model.number="investors" type="number" placeholder="Investors" />
    <input v-model.number="volatility" type="number" placeholder="Volatility" />
    <button @click="startSimulation">Done</button>
    <button v-if="simulationStarted" @click="toggleSimulation">{{ isRunning ? "Pause" : "Run" }}</button>

    <InvestmentChart v-if="simulationStarted" :investmentData="investmentData" />
    <SimulationControl v-if="simulationStarted" :investmentData="investmentData" @update-investment="updateInvestment" />
  </div>
</template>
