<template>
  <div class="flex flex-col items-center">
    <!-- Only show chart for focused simulation -->
    <InvestmentChart v-if="focusedSimulation" :simulation="focusedSimulation" />
  </div>
</template>

<script>
import { getSimulations, saveSimulation } from '@/api/simulationService.js';

export default {
  data() {
    return {
      user: null,         // set upon login
      simulations: [],
      currentSettings: {}
    };
  },
  methods: {
    async onLogin(userData) {
      this.user = userData;
      await this.fetchSimulations();
    },
    async fetchSimulations() {
      const resp = await getSimulations(this.user.id);
      if (resp.data.success) this.simulations = resp.data.simulations;
      else alert('Error loading simulations');
    },
    async onSaveSimulation(name) {
      const resp = await saveSimulation(this.user.id, name, this.currentSettings);
      if (resp.data.success) {
        this.simulations.push({
          name,
          settings: this.currentSettings,
          created_at: new Date().toISOString()
        });
      } else {
        alert('Save failed');
      }
    }
  }
};
</script>

  
  <style scoped>
  .focused {
    display: flex;
    justify-content: center;
    align-items: center;
    /* Additional styling for centering */
  }
  
  .unfocused {
    display: flex;
    justify-content: flex-start;
    /* Additional styling for left alignment */
  }
  </style>
  