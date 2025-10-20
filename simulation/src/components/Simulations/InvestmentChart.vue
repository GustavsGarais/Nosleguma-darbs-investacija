<template>
  <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow-md">
    <div class="flex items-center justify-between mb-2">
      <h3 class="text-lg font-bold dark:text-white">
        📈 {{ simulation?.name || ('Simulation #' + (simulation?.id ?? 'Unknown')) }} – €{{ latestValue }}
      </h3>
      <div class="flex gap-2 text-xs">
        <label><input type="checkbox" v-model="show.series.value" /> Value</label>
        <label><input type="checkbox" v-model="show.series.inflationAdjusted" /> Real</label>
        <label><input type="checkbox" v-model="show.series.interest" /> Interest</label>
      </div>
    </div>

    <div style="height: 350px">
      <Line :data="chartData" :options="chartOptions" />
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
  data() {
    return {
      show: {
        series: {
          value: true,
          inflationAdjusted: true,
          interest: false,
        }
      }
    }
  },
  computed: {
    chartData() {
      const labels = this.simulation.data.map((point, index) => point.label ?? index)

      const lines = []
      if (this.show.series.value) {
        const dataValues = this.simulation.data.map(point => typeof point.value === 'number' ? point.value : point.value?.amount || 0)
        lines.push({
          label: 'Value (nominal)',
          data: dataValues,
          fill: false,
          borderColor: '#10b981',
          tension: 0.3
        })
      }
      if (this.show.series.inflationAdjusted) {
        const realValues = this.simulation.data.map(point => point.inflationAdjusted ?? null)
        lines.push({
          label: 'Value (real)',
          data: realValues,
          fill: false,
          borderColor: '#3b82f6',
          borderDash: [6, 6],
          tension: 0.3
        })
      }
      if (this.show.series.interest) {
        const interest = this.simulation.data.map(point => point.interestEarned ?? 0)
        lines.push({
          label: 'Interest (period)',
          data: interest,
          fill: false,
          borderColor: '#f59e0b',
          tension: 0.3,
          yAxisID: 'y1'
        })
      }
      return { labels, datasets: lines }
    },
    chartOptions() {
      return {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: { beginAtZero: false },
          y1: {
            position: 'right',
            beginAtZero: true,
            grid: { drawOnChartArea: false }
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
