<script>
import { ref, onMounted, watch } from "vue";
import { Chart, registerables } from "chart.js";

Chart.register(...registerables);

export default {
  props: {
    investmentData: Array,
  },
  setup(props) {
    const chartRef = ref(null);
    let chartInstance = null;

    const createChart = () => {
      if (chartInstance) chartInstance.destroy();

      chartInstance = new Chart(chartRef.value, {
        type: "line",
        data: {
          labels: props.investmentData.map((_, i) => i + 1),
          datasets: [
            {
              label: "Investment Value",
              data: props.investmentData,
              borderColor: "blue",
              backgroundColor: "lightblue",
              fill: false,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
        },
      });
    };

    onMounted(createChart);
    watch(() => props.investmentData, createChart, { deep: true });

    return { chartRef };
  },
};
</script>

<template>
  <canvas ref="chartRef"></canvas>
</template>
