<template>
  <div class="simulation">

    <div v-if="username">
      <p>Logged in as: {{ username }}</p>
      <button @click="saveSimulation">💾 Save Simulation</button>
      <button @click="loadSimulation">📂 Load Simulation</button>
    </div>
    
    <div>
      <h2>Investment Simulation</h2>
      <button @click="saveFavorite">Save as Favorite</button>
      <input v-model="favoriteName" placeholder="Favorite Name" />
      <button @click="loadFavorite">Load Favorite</button>
    </div>

    <div class="investment-controls">
      <input v-model="newInvestment.name" placeholder="Investment Name" />
      <input v-model.number="newInvestment.value" type="number" placeholder="Starting Value" />
      <input v-model.number="newInvestment.volatility" type="number" step="0.1" placeholder="Volatility" />
      <button @click="addInvestment">➕ Add Investment</button>
    </div>

    <div class="investment-list">
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Value</th>
            <th>Volatility</th>
            <th>Remove</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(investment, index) in investmentData" :key="index">
            <td>{{ investment.name }}</td>
            <td>{{ investment.value.toFixed(2) }}</td>
            <td>{{ investment.volatility }}</td>
            <td><button @click="removeInvestment(index)">❌</button></td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="simulation-controls">
      <button @click="startSimulation" :disabled="running">▶ Start</button>
      <button @click="stopSimulation" :disabled="!running">⏸ Pause</button>
    </div>

    <!-- Display Random Market Event -->
    <p v-if="marketEvent" class="event-message">{{ marketEvent }}</p>

    <!-- Dynamic Parameters Section -->
    <div class="dynamic-parameters">
      <label>Number of Investors:</label>
      <input v-model.number="numInvestors" type="number" min="1" class="border p-1">
      
      <label>Market Influence Factor:</label>
      <input v-model.number="marketInfluence" type="number" step="0.1" min="0" max="5" class="border p-1">
      
      <label>Risk Appetite:</label>
      <input v-model.number="riskAppetite" type="number" step="0.1" min="0" max="5" class="border p-1">
      
      <label>Investment Growth Rate (%):</label>
      <input v-model.number="growthRate" type="number" step="0.1" min="-5" max="10" class="border p-1">
    </div>

  </div>
</template>

<script>
export default {
  data() {
    return {
      username: '',
      investmentData: [],
      newInvestment: { name: '', value: 100, volatility: 1 },
      running: false,
      interval: null,
      marketEvent: "",
      
      // Dynamic Parameters
      numInvestors: 100,
      marketInfluence: 2.0,
      riskAppetite: 2.5,
      growthRate: 1.5
    };
  },

  created() {
    this.getSession();
  },
  
  methods: {
    async getSession() {
      const response = await fetch('http://localhost:3000/session');
      const data = await response.json();
      if (data.loggedIn) {
        this.username = data.username;
        this.loadSimulation();
      }
    },

    randomMarketEvent() { // random events
      const events = [
        { text: "📉 Market Crash! Investments drop by 20%", effect: -0.2 },
        { text: "📈 Booming Economy! Investments increase by 15%", effect: 0.15 },
        { text: "⚖️ Regulatory Change! Some investments decrease by 10%", effect: -0.1 },
        { text: "🔔 Positive News! Investments increase by 10%", effect: 0.1 },
        { text: "🔻 Sudden Dip! Investments drop by 5%", effect: -0.05 }
      ];
      
      const event = events[Math.floor(Math.random() * events.length)];
      this.marketEvent = event.text;

      this.investmentData.forEach(inv => {
        inv.value = Math.max(0, inv.value * (1 + event.effect));
      });

      setTimeout(() => { this.marketEvent = ""; }, 5000); // Clear event after 5 seconds
    },

    addInvestment() {
      if (this.newInvestment.name && this.newInvestment.value > 0) {
        this.investmentData.push({ ...this.newInvestment });
        this.newInvestment = { name: '', value: 100, volatility: 1 }; // Reset input fields
      }
    },
    removeInvestment(index) {
      this.investmentData.splice(index, 1);
    },
    startSimulation() {
      if (this.investmentData.length === 0) return;
      this.running = true;
      this.interval = setInterval(this.updateInvestments, 1000);
    },
    stopSimulation() {
      this.running = false;
      clearInterval(this.interval);
    },
    updateInvestments() {
      this.investmentData.forEach(inv => {
        const change = (Math.random() * 2 - 1) * inv.volatility;
        inv.value = Math.max(0, inv.value + change); // Prevent negative values
      });

      // Apply dynamic parameters
      this.adjustInvestmentGrowth();
      
      // Introduce random market events occasionally (10% chance per update)
      if (Math.random() < 0.1) {
        this.randomMarketEvent();
      }
    },

    adjustInvestmentGrowth() {
      let volatility = (Math.random() - 0.5) * this.riskAppetite;
      let investorEffect = (this.numInvestors > 50) ? 1 : 1.2;
      this.investmentData.forEach(inv => {
        inv.value *= 1 + ((this.growthRate / 100) + volatility) * investorEffect;
      });
    },

    async saveSimulation() {
      if (!this.username) return;
      await fetch('http://localhost:3000/save-simulation', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username: this.username, simulationData: this.investmentData })
      });
    },
    async loadSimulation() {
      if (!this.username) return;
      const response = await fetch(`http://localhost:3000/load-simulation?username=${this.username}`);
      const data = await response.json();
      if (data.success) {
        this.investmentData = data.simulation;
      }
    }
  }
};
</script>

<style scoped>
.simulation {
  padding: 20px;
  border: 1px solid #ddd;
  border-radius: 8px;
  max-width: 600px;
  margin: auto;
}
button {
  margin: 5px;
  padding: 8px;
  cursor: pointer;
}
.investment-controls input {
  margin-right: 5px;
  padding: 5px;
}
.investment-list table {
  width: 100%;
  margin-top: 10px;
  border-collapse: collapse;
}
.investment-list th, .investment-list td {
  border: 1px solid #ddd;
  padding: 5px;
  text-align: center;
}
.event-message {
  padding: 10px;
  margin-top: 10px;
  background-color: #ffeb3b;
  color: #000;
  font-weight: bold;
  border-radius: 5px;
  text-align: center;
}
.dynamic-parameters {
  margin-top: 20px;
}
.dynamic-parameters input {
  margin-bottom: 10px;
  padding: 5px;
}
</style>
