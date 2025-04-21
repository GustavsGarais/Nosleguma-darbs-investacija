<template>
  <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow-md space-y-4">
    <!-- Chart Title -->
    <h3 class="text-lg font-bold mb-2 dark:text-white">
      📈 Simulation ID: {{ typeof simulation.id }} – Value: {{ typeof latestValue }}
    </h3>

    <!-- Growth Line Chart -->
    <LineChart
      v-if="formattedChartData.length > 0"
      :width="400"
      :height="200"
      :data="formattedChartData"
    >
      <CartesianGrid stroke="#ccc" strokeDasharray="5 5" />
      <XAxis dataKey="time" hide />
      <YAxis :domain="['auto', 'auto']" />
      <Tooltip />
      <Line
        type="monotone"
        dataKey="value"
        stroke="#10b981"
        stroke-width="2"
        :dot="false"
      />
    </LineChart>

    <!-- Save Button -->
    <div class="flex justify-end">
      <button
        class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition"
        @click="onSave"
      >
        Save
      </button>
    </div>
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
    simulation: {
      type: Object,
      required: true
    }
  },
  computed: {
    formattedChartData() {
      if (!this.simulation || !this.simulation.data) return []
      return this.simulation.data.map((point, index) => ({
        time: index,
        value: point.value
      }))
    },
    latestValue() {
      const data = this.simulation?.data || []
      if (data.length === 0) {
        return (
          this.simulation?.settings?.initialInvestment?.toFixed(2) || '0.00'
        )
      }
      return data[data.length - 1].value.toFixed(2)
    }
  },
  methods: {
    onSave() {
      console.log('💾 Save button clicked – implement logic later!')
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
.positive {
  color: limegreen;
  transition: color 0.3s ease;
}
.negative {
  color: red;
  transition: color 0.3s ease;
}
</style>
