<template>
  <div class="space-y-2 text-sm">
    <div>
      <label class="block mb-1 font-medium">💵 Initial Investment</label>
      <input
        v-model.number="localSettings.initialInvestment"
        type="number"
        class="w-full px-2 py-1 rounded border dark:bg-gray-700 dark:text-white"
      />
    </div>

    <div>
      <label class="block mb-1 font-medium">👥 Number of Investors</label>
      <input
        v-model.number="localSettings.investors"
        type="number"
        class="w-full px-2 pcalcy-1 rounded border dark:bg-gray-700 dark:text-white"
      />
    </div>

    <div>
      <label class="block mb-1 font-medium">📈 Growth Rate (e.g., 0.03 = 3%)</label>
      <input
        v-model.number="localSettings.growthRate"
        type="number"
        step="0.01"
        class="w-full px-2 py-1 rounded border dark:bg-gray-700 dark:text-white"
      />
    </div>

    <div>
      <label class="block mb-1 font-medium">⚖️ Risk Appetite (0 to 1)</label>
      <input
        v-model.number="localSettings.riskAppetite"
        type="number"
        step="0.01"
        min="0"
        max="1"
        class="w-full px-2 py-1 rounded border dark:bg-gray-700 dark:text-white"
      />
    </div>

    <div>
      <label class="block mb-1 font-medium">🌐 Market Influence (0 to 1)</label>
      <input
        v-model.number="localSettings.marketInfluence"
        type="number"
        step="0.01"
        min="0"
        max="1"
        class="w-full px-2 py-1 rounded border dark:bg-gray-700 dark:text-white"
      />
    </div>
  </div>
</template>

<script>
export default {
  name: 'SimulationSetup',
  props: {
    simulation: Object
  },
  data() {
    return {
      localSettings: { ...this.simulation.settings }
    }
  },
  watch: {
    localSettings: {
      handler(val) {
        this.$emit('update-settings', val)
      },
      deep: true
    }
  }
}
</script>
