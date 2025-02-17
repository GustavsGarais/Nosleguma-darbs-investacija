<script>
import { ref, onMounted, watch } from "vue";
import Chart from "chart.js/auto";

export default {
  props: ["investmentData"],
  setup(props) {
    const chartRef = ref(null);
    const chartInstance = ref(null);

    // Function to create the chart
    const createChart = () => {
      if (chartRef.value) {
        chartInstance.value = new Chart(chartRef.value, {
          type: "line",
          data: {
            labels: ["Start"],
            datasets: [
              {
                label: "Investment Value",
                data: [props.investmentData.value],
                borderColor: "green",
                backgroundColor: "rgba(0, 255, 0, 0.2)",
                borderWidth: 2,
              },
            ],
          },
          options: {
            responsive: true,
            scales: {
              x: { display: true },
              y: { display: true },
            },
          },
        });
      }
    };

    // Function to update chart when values change
    const updateChart = () => {
      if (chartInstance.value) {
        const dataset = chartInstance.value.data.datasets[0];
        dataset.data.push(props.investmentData.value);
        chartInstance.value.data.labels.push(dataset.data.length);
        chartInstance.value.update();
      }
    };

    // Watch investment data and update chart
    watch(() => props.investmentData.value, updateChart);

    onMounted(createChart);

    return { chartRef };
  },
};
</script>

<template>
  <canvas ref="chartRef"></canvas>
</template>
