<template>
  <div>
    <h2>Investment Chart</h2>
    <div v-if="selectedSimulation">
      <canvas ref="chartCanvas"></canvas>
      <p>Current Investment Value: {{ selectedSimulation.data[selectedSimulation.data.length - 1].toFixed(2) }}</p>
    </div>
    <p v-else>No simulation selected.</p>
  </div>
</template>

<script>
import { ref, watch, onMounted } from "vue";
import Chart from "chart.js/auto";
import { getUserSimulations } from "/server/utils/simulationStorage.js";  // Fixed import path

export default {
  props: ["userId", "selectedSimulationId"],
  setup(props) {
    const chartCanvas = ref(null);
    const chartInstance = ref(null);
    const simulations = ref([]);
    const selectedSimulation = ref(null);

    // Fetch user's simulations
    onMounted(async () => {
      simulations.value = await getUserSimulations(props.userId);
      selectedSimulation.value = simulations.value.find(sim => sim.id === props.selectedSimulationId);
      renderChart();
    });

    watch(() => props.selectedSimulationId, (newId) => {
      selectedSimulation.value = simulations.value.find(sim => sim.id === newId);
      renderChart();
    });

    const renderChart = () => {
      if (!selectedSimulation.value || !chartCanvas.value) return;
      
      if (chartInstance.value) {
        chartInstance.value.destroy();
      }

      chartInstance.value = new Chart(chartCanvas.value, {
        type: "line",
        data: {
          labels: selectedSimulation.value.data.map((_, index) => `Day ${index + 1}`),
          datasets: [{
            label: "Investment Value",
            data: selectedSimulation.value.data,
            borderColor: "#42A5F5",
            fill: false,
          }],
        },
      });
    };

    return { chartCanvas, selectedSimulation };
  },
};
</script>

<style>
canvas {
  max-width: 100%;
  height: 400px;
}
</style>
