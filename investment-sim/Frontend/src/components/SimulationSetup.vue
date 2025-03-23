<template>
  <div>
    <h3>Custom Investment Creation</h3>

    <div class="tooltip">
      <input v-model="newInvestment.name" placeholder="Investment Name" />
      <span class="tooltiptext">Give your investment a unique name.</span>
    </div>

    <div class="tooltip">
      <input type="number" v-model="newInvestment.value" placeholder="Initial Value" />
      <span class="tooltiptext">Starting value of the investment.</span>
    </div>

    <div class="tooltip">
      <input type="number" v-model="newInvestment.volatility" step="0.1" placeholder="Volatility" />
      <span class="tooltiptext">Higher volatility means more unpredictable price changes.</span>
    </div>

    <div class="tooltip">
      <select v-model="newInvestment.behavior">
        <option value="steady">Steady Growth</option>
        <option value="random">Random Fluctuation</option>
        <option value="crash">Periodic Crash</option>
      </select>
      <span class="tooltiptext">Choose how your investment behaves over time.</span>
    </div>

    <button @click="addInvestment">Add Investment</button>
  </div>
</template>

<script>
export default {
  data() {
    return {
      newInvestment: {
        name: "",
        value: 0,
        volatility: 1,
        behavior: "steady",
      }
    };
  },
  methods: {
    addInvestment() {
      console.log("New investment added:", this.newInvestment);
      // Here you can send the newInvestment data to the parent component or store it
    }
  }
};
</script>
