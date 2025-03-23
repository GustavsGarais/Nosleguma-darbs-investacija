<template>
  <div class="investment-chart">
    <div class="chart-preview" @click="expandChart">
      <h3 class="text-lg font-bold">Investment Performance</h3>
      <ResponsiveContainer width="100%" height="200px">
        <LineChart :data="chartData">
          <XAxis dataKey="time" />
          <YAxis />
          <Tooltip />
          <Legend />
          <Line type="monotone" dataKey="value" stroke="#82ca9d" />
        </LineChart>
      </ResponsiveContainer>
    </div>

    <div v-if="expanded" class="expanded-chart-overlay" @click="expanded = false">
      <div class="expanded-chart" @click.stop>
        <h2 class="text-2xl font-bold mb-4">Detailed Investment Chart</h2>
        <ResponsiveContainer width="90%" height="400px">
          <LineChart :data="chartData">
            <XAxis dataKey="time" />
            <YAxis />
            <Tooltip />
            <Legend />
            <Line type="monotone" dataKey="value" stroke="#ff7300" />
          </LineChart>
        </ResponsiveContainer>
        <button class="mt-3 bg-red-500 text-white p-2 rounded" @click="expanded = false">
          Close
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { LineChart, Line, XAxis, YAxis, Tooltip, Legend, ResponsiveContainer } from "recharts";

export default {
  components: { LineChart, Line, XAxis, YAxis, Tooltip, Legend, ResponsiveContainer },
  props: ["investmentData"],
  data() {
    return {
      expanded: false
    };
  },
  computed: {
    chartData() {
      return this.investmentData.map((point, index) => ({
        time: index + 1,
        value: point.value
      }));
    }
  },
  methods: {
    expandChart() {
      this.expanded = true;
    }
  }
};
</script>

<style scoped>
.investment-chart {
  cursor: pointer;
  transition: 0.3s;
}

.expanded-chart-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
}

.expanded-chart {
  background: white;
  padding: 20px;
  border-radius: 8px;
  text-align: center;
}
</style>
