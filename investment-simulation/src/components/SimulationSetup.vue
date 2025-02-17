<template>
    <div class="container">
      <h2>Set Simulation Values</h2>
      <input v-model.number="startValue" placeholder="Starting Value" />
      <input v-model.number="investors" placeholder="Investors" />
      <input v-model.number="volatility" placeholder="Volatility (1-100)" />
  
      <button @click="startSimulation">Done</button>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        startValue: 100,
        investors: 10,
        volatility: 5,
      };
    },
    methods: {
      startSimulation() {
        const simulationData = Array(10).fill(this.startValue);
        this.$emit("startSimulation", simulationData);
      },
    },
  };
  </script>
  