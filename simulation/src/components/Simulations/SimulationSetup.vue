<template>
  <div class="simulation-setup">
    <div v-for="(field, key) in fields" :key="key" class="input-group">
      <label class="label">
        {{ field.icon }} {{ field.label }}
        <span class="tooltip-icon" :data-tooltip="field.tooltip">❔</span>
      </label>
      <input
        v-model.number="localSettings[key]"
        :type="field.type"
        :step="field.step"
        :min="field.min"
        :max="field.max"
        class="custom-input"
        :class="{ 'dark-input': isDarkTheme }"
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
      localSettings: { ...this.simulation.settings },
      fields: {
        initialInvestment: {
          label: 'Initial Investment',
          icon: '💵',
          type: 'number',
          tooltip: 'The amount of money you invest initially.'
        },
        investors: {
          label: 'Number of Investors',
          icon: '👥',
          type: 'number',
          tooltip: 'How many participants are contributing investments.'
        },
        growthRate: {
          label: 'Growth Rate (e.g., 0.03 = 3%)',
          icon: '📈',
          type: 'number',
          step: 0.01,
          tooltip: 'Expected return per cycle (0.03 = 3%).'
        },
        riskAppetite: {
          label: 'Risk Appetite (0 to 1)',
          icon: '⚖️',
          type: 'number',
          step: 0.01,
          min: 0,
          max: 1,
          tooltip: 'Higher values mean more risk-taking.'
        },
        marketInfluence: {
          label: 'Market Influence (0 to 1)',
          icon: '🌐',
          type: 'number',
          step: 0.01,
          min: 0,
          max: 1,
          tooltip: 'How much the market impacts your investments.'
        }
      }
    };
  },
  computed: {
    isDarkTheme() {
      return document.body.classList.contains('dark');
    }
  },
  watch: {
    localSettings: {
      handler(val) {
        this.$emit('update-settings', val);
      },
      deep: true
    }
  }
};
</script>
