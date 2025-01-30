<template>
  <div class="investment-box" ref="box">
    <div class="draggable" @mousedown="startDrag"></div>
    <h2 class="current-value">
      Current Value: {{ formattedValue }}
      <span :class="arrowClass">{{ arrowSymbol }}</span>
    </h2>
    <canvas ref="investmentChart"></canvas>
    
    <div class="controls">
      <button @click="toggleSimulation">{{ running ? "Stop" : "Continue" }}</button>
    </div>

    <div class="zoom-controls">
      <button @click="zoomIn">Zoom In</button>
      <button @click="zoomOut">Zoom Out</button>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from "vue";
import Chart from "chart.js/auto";

export default {
  setup() {
    const investmentChart = ref(null);
    const box = ref(null);
    const currentValue = ref(100);
    let lastValue = 100;
    let chartInstance = null;
    const investmentData = [100];
    let years = 0;
    let running = ref(true);
    let intervalId = null;

    function simulateGrowth() {
      if (!running.value) return;
      let growthRate = 0.05; // 5% base growth
      let randomFactor = Math.random() * 0.04 - 0.02; // Random ±2% variation
      let slowdownChance = Math.random();

      if (slowdownChance < 0.2) {
        growthRate *= 0.5; // 20% chance to slow growth
      }

      lastValue = currentValue.value;
      currentValue.value *= 1 + (growthRate + randomFactor);
      investmentData.push(currentValue.value);
      years++;

      updateChart();
    }

    function updateChart() {
      if (chartInstance) {
        chartInstance.data.labels.push(years.toString());
        chartInstance.data.datasets[0].data = investmentData;
        chartInstance.update();
      }
    }

    function toggleSimulation() {
      running.value = !running.value;
    }

    function zoomIn() {
      chartInstance.options.scales.y.min = Math.max(0, chartInstance.options.scales.y.min - 10);
      chartInstance.options.scales.y.max += 10;
      chartInstance.update();
    }

    function zoomOut() {
      chartInstance.options.scales.y.min += 10;
      chartInstance.options.scales.y.max = Math.max(10, chartInstance.options.scales.y.max - 10);
      chartInstance.update();
    }

    function startDrag(event) {
      let startX = event.clientX;
      let startY = event.clientY;
      let startLeft = box.value.offsetLeft;
      let startTop = box.value.offsetTop;

      function onMouseMove(e) {
        box.value.style.left = `${startLeft + e.clientX - startX}px`;
        box.value.style.top = `${startTop + e.clientY - startY}px`;
      }

      function onMouseUp() {
        document.removeEventListener("mousemove", onMouseMove);
        document.removeEventListener("mouseup", onMouseUp);
      }

      document.addEventListener("mousemove", onMouseMove);
      document.addEventListener("mouseup", onMouseUp);
    }

    onMounted(() => {
      const ctx = investmentChart.value.getContext("2d");

      chartInstance = new Chart(ctx, {
        type: "line",
        data: {
          labels: ["0"],
          datasets: [
            {
              label: "Investment Growth",
              data: investmentData,
              borderColor: "blue",
              borderWidth: 2,
              fill: false,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            x: { grid: { color: "#444" }, ticks: { color: "white" } },
            y: { grid: { color: "#444" }, ticks: { color: "white" } },
          },
        },
      });

      intervalId = setInterval(simulateGrowth, 3000); // Tick every 3 seconds
    });

    onUnmounted(() => {
      clearInterval(intervalId);
    });

    return {
      investmentChart,
      box,
      formattedValue: computed(() => currentValue.value.toFixed(2)),
      arrowClass: computed(() => (currentValue.value > lastValue ? "up-arrow" : "down-arrow")),
      arrowSymbol: computed(() => (currentValue.value > lastValue ? "↑" : "↓")),
      toggleSimulation,
      running,
      zoomIn,
      zoomOut,
      startDrag,
    };
  },
};
</script>

