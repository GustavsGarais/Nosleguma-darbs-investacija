<template>
    <div class="investment-box">
      <h2>Investment Dashboard</h2>
      <button @click="createSimulation">Create New Simulation</button>
      <div v-for="(investment, index) in investments" :key="index">
        <p>{{ investment.name }} - Value: {{ investment.value }}</p>
        <button @click="pauseSimulation(index)">Pause</button>
        <button @click="resumeSimulation(index)">Resume</button>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        investments: [],
      };
    },
    methods: {
      createSimulation() {
        this.investments.push({
          name: `Investment ${this.investments.length + 1}`,
          value: 100,
          isRunning: true,
        });
      },
      pauseSimulation(index) {
        this.investments[index].isRunning = false;
      },
      resumeSimulation(index) {
        this.investments[index].isRunning = true;
      },
    },
  };
  </script>
  