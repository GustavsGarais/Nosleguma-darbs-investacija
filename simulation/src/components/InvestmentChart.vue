<template>
  <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow-md">
    <h3 class="text-lg font-bold mb-2 dark:text-white">
      📈 Simulation #{{ simulation?.id ?? 'Unknown' }} – €{{ latestValue }}
    </h3>

    <Line :data="chartData" :options="chartOptions" />

    <div class="flex justify-end mt-4">
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
import { Line } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  PointElement,
  LinearScale,
  CategoryScale
} from 'chart.js'

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  LineElement,
  PointElement,
  LinearScale,
  CategoryScale
)

export default {
  name: 'InvestmentChart',
  components: { Line },
  props: {
    simulation: {
      type: Object,
      required: true
    }
  },
  computed: {
    chartData() {
      const labels = this.simulation.data.map((_, index) => index)
      const dataValues = this.simulation.data.map(point =>
        typeof point.value === 'number' ? point.value : point.value?.amount || 0
      )
      return {
        labels,
        datasets: [
          {
            label: 'Investment Value',
            data: dataValues,
            fill: false,
            borderColor: '#10b981',
            tension: 0.4
          }
        ]
      }
    },
    chartOptions() {
      return {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: false
          }
        }
      }
    },
    latestValue() {
      if (
        !this.simulation ||
        !Array.isArray(this.simulation.data) ||
        this.simulation.data.length === 0
      ) {
        return (
          this.simulation?.settings?.initialInvestment?.toFixed(2) ?? '0.00'
        )
      }
      const last = this.simulation.data[this.simulation.data.length - 1]
      const value = typeof last.value === 'number'
        ? last.value
        : last.value?.amount ?? 0
      return value.toFixed(2)
    }
  },
  methods: {
    onSave() {
      console.log('💾 Save clicked for Simulation', this.simulation?.id)
    }
  }
}
</script>

<style scoped>
div {
  height: 350px;
}
</style>
