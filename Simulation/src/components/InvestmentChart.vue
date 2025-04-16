<template>
    <div class="chart-container">
      <canvas ref="canvas" width="600" height="300"></canvas>
    </div>
  </template>
  
  <script>
  export default {
    name: 'InvestmentChart',
    props: {
      data: {
        type: Array,
        default: () => []
      }
    },
    watch: {
      data() {
        this.drawChart()
      }
    },
    mounted() {
      this.drawChart()
    },
    methods: {
      drawChart() {
        const canvas = this.$refs.canvas
        if (!canvas || this.data.length < 2) return
        const ctx = canvas.getContext('2d')
        ctx.clearRect(0, 0, canvas.width, canvas.height)
        const values = this.data.map(pt => pt.value)
        const min = Math.min(...values)
        const max = Math.max(...values)
        const len = this.data.length
        const padding = 40
        const w = canvas.width - padding * 2
        const h = canvas.height - padding * 2
        ctx.beginPath()
        this.data.forEach((pt, i) => {
          const x = padding + (i / (len - 1)) * w
          const y = padding + h - ((pt.value - min) / (max - min)) * h
          if (i === 0) ctx.moveTo(x, y)
          else ctx.lineTo(x, y)
        })
        ctx.stroke()
        // axes
        ctx.beginPath()
        ctx.moveTo(padding, padding)
        ctx.lineTo(padding, padding + h)
        ctx.lineTo(padding + w, padding + h)
        ctx.stroke()
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
  </style>
  