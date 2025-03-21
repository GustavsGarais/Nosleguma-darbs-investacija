<template>
    <div class="p-4 bg-white shadow-md rounded-lg">
      <h2 class="text-xl font-bold mb-2">User Dashboard</h2>
      <button @click="loadSimulations" class="bg-blue-500 text-white p-2 rounded">Load Past Simulations</button>
  
      <ul v-if="simulations.length">
        <li v-for="sim in simulations" :key="sim.name" class="mt-2 p-2 border rounded">
          <strong>{{ sim.name }}</strong>
          <button @click="viewSimulation(sim)" class="ml-2 bg-green-500 text-white p-1 rounded">View</button>
        </li>
      </ul>
  
      <div v-if="selectedSimulation" class="mt-4 p-4 border rounded">
        <h3 class="text-lg font-bold">Simulation Details</h3>
        <pre>{{ JSON.stringify(selectedSimulation.data, null, 2) }}</pre>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        simulations: [],
        selectedSimulation: null
      };
    },
    methods: {
      async loadSimulations() {
        const response = await fetch(`http://localhost:3000/load-simulations?username=user123`);
        this.simulations = await response.json();
      },
      viewSimulation(sim) {
        this.selectedSimulation = sim;
      }
    }
  };
  </script>
  