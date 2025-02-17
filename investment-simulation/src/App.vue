<template>
  <div v-if="!loggedIn">
    <Login @loginSuccess="loggedIn = true" />
  </div>
  
  <div v-else>
    <button @click="logout">Log Out</button>
    <button @click="showSetup = true">Simulation Startup</button>

    <SimulationSetup v-if="showSetup" @startSimulation="startSimulation" />
    
    <div v-if="activeSimulation">
      <InvestmentChart :investmentData="investmentData" />
      <button @click="toggleSimulation">{{ isPaused ? 'Run' : 'Pause' }}</button>
    </div>
  </div>
</template>

<script>
import Login from "./components/Login.vue";
import SimulationSetup from "./components/SimulationSetup.vue";
import InvestmentChart from "./components/InvestmentChart.vue";

export default {
  components: { Login, SimulationSetup, InvestmentChart },
  data() {
    return {
      loggedIn: false,
      showSetup: false,
      activeSimulation: false,
      isPaused: false,
      investmentData: [],
    };
  },
  methods: {
    logout() {
      localStorage.removeItem("currentUser");
      this.loggedIn = false;
    },
    startSimulation(data) {
      this.investmentData = data;
      this.showSetup = false;
      this.activeSimulation = true;
    },
    toggleSimulation() {
      this.isPaused = !this.isPaused;
    },
  },
};
</script>
