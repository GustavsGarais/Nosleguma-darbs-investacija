<template>
    <div v-if="simulationEnded" class="p-4 bg-white shadow-md rounded-lg">
      <h2 class="text-xl font-bold mb-2">Investment Report</h2>
      <p>Total Simulations: {{ investments.length }}</p>
      <ul>
        <li v-for="inv in investments" :key="inv.name">
          <strong>{{ inv.name }}:</strong> Final Value: {{ inv.value.toFixed(2) }} | Growth: {{ ((inv.value / inv.startValue) * 100 - 100).toFixed(2) }}%
        </li>
      </ul>
      <button @click="saveSimulation" class="mt-3 bg-blue-500 text-white p-2 rounded">Save Simulation</button>
      <button @click="exportAsCSV" class="mt-3 ml-2 bg-green-500 text-white p-2 rounded">Export as CSV</button>
      <button @click="exportAsJSON" class="mt-3 ml-2 bg-gray-500 text-white p-2 rounded">Export as JSON</button>
    </div>
  </template>
  
  <script>
  export default {
    props: ["investments", "simulationEnded", "username"],
    methods: {
      async saveSimulation() {
        try {
          const response = await fetch("http://localhost:3000/save-simulation", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ username: this.username, simulationData: this.investments })
          });
          const data = await response.json();
          console.log(data.message);
        } catch (error) {
          console.error("Error saving simulation:", error);
        }
      },
      exportAsCSV() {
        let csv = "Investment Name,Final Value,Growth (%)\n";
        this.investments.forEach(inv => {
          csv += `${inv.name},${inv.value.toFixed(2)},${((inv.value / inv.startValue) * 100 - 100).toFixed(2)}%\n`;
        });
        this.downloadFile(csv, "simulation_results.csv", "text/csv");
      },
      exportAsJSON() {
        const jsonString = JSON.stringify(this.investments, null, 2);
        this.downloadFile(jsonString, "simulation_results.json", "application/json");
      },
      downloadFile(content, filename, type) {
        const blob = new Blob([content], { type });
        const link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
      }
    }
  };
  </script>
  