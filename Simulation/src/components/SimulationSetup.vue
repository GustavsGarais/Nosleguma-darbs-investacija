<template>
  <div class="setup-container">
    <h3>Setup Simulation</h3>
    <form @submit.prevent="start">
      <div class="form-group">
        <label>Starting Value <input v-model.number="startValue" type="number" required />
        </label>
      </div>
      <div class="form-group">
        <label>Number of Investors:
          <input v-model.number="numInvestors" type="number" required />
        </label>
      </div>
      <div class="form-group">
        <label>Market Influence Factor:
          <input v-model.number="marketInfluence" type="number" required />
        </label>
      </div>
      <div class="form-group">
        <label>Risk Appetite (%):
          <input v-model.number="riskAppetite" type="number" required />
        </label>
      </div>
      <div class="form-group">
        <label>Growth Rate (%):
          <input v-model.number="growthRate" type="number" required />
        </label>
      </div>
      <button type="submit">Start Simulation</button>
    </form>
  </div>
</template>

<script>
export default {
  name: 'SimulationSetup',
  data() {
    return {
      startValue: 1000,
      numInvestors: 1,
      marketInfluence: 1,
      riskAppetite: 2,
      growthRate: 0
    }
  },
  methods: {
    start() {
      this.$emit('start', {
        startValue: this.startValue,
        numInvestors: this.numInvestors,
        marketInfluence: this.marketInfluence,
        riskAppetite: this.riskAppetite,
        growthRate: this.growthRate
      })
    }
  }
}
</script>

<style scoped>
.setup-container {
  max-width: 400px;
  margin: 2rem auto;
  padding: 1rem;
  border: 1px solid var(--text-color);
  border-radius: 8px;
}
.form-group {
  margin-bottom: 1rem;
  display: flex;
  flex-direction: column;
}
input {
  padding: 0.5rem;
  border-radius: 4px;
  border: 1px solid #ccc;
  width: 100%;
}
button {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
</style>
