<template>
  <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow-md">
    <h3 class="text-lg font-bold mb-2 dark:text-white">
      📈 Simulation {{ simulation.id }} – €{{ latestValue.toFixed(2) }}
    </h3>

    <LineChart
      :width="400"
      :height="200"
      :data="formattedChartData"
    >
      <Line type="monotone" dataKey="value" stroke="#10b981" stroke-width="2" dot={false} />
      <XAxis dataKey="time" hide />
      <YAxis :domain="['auto', 'auto']" />
      <Tooltip />
      <CartesianGrid stroke="#ccc" stroke-dasharray="5 5" />
    </LineChart>
  </div>
</template>

<script>
import {
  LineChart,
  Line,
  XAxis,
  YAxis,
  Tooltip,
  CartesianGrid
} from 'recharts'

export default {
  name: 'InvestmentChart',
  components: {
    LineChart,
    Line,
    XAxis,
    YAxis,
    Tooltip,
    CartesianGrid
  },
  props: {
    simulation: Object
  },
  computed: {
    formattedChartData() {
      return this.simulation.history.map((value, index) => ({
        time: index,
        value: value
      }))
    },
    latestValue() {
      if (this.simulation.history.length === 0) return this.simulation.settings.initialInvestment
      return this.simulation.history[this.simulation.history.length - 1]
    }
  }
}
</script>
  
<style scoped>
  .chart-container {
    text-align: center;
    margin: 0 auto;
  }
  canvas {
    border: 1px solid #ccc;
    display: block;
    margin: 0 auto;
  }
  .positive { color: limegreen; transition: color 0.3s ease; }
  .negative { color: red; transition: color 0.3s ease; }

</style>
  