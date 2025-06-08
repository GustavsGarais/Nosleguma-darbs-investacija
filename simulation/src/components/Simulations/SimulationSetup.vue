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

<style scoped>
.simulation-setup {
  max-width: 600px;
  margin: 0 auto;
  padding: 1.5rem;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 1rem;
  backdrop-filter: blur(6px);
}

.input-group {
  margin-bottom: 1.25rem;
}

.label {
  display: flex;
  align-items: center;
  font-weight: 600;
  margin-bottom: 0.4rem;
  font-size: 0.95rem;
  color: #1f2937;
}

.tooltip-icon {
  margin-left: 0.4rem;
  cursor: pointer;
  font-size: 0.85rem;
  color: #3b82f6;
  position: relative;
}

.tooltip-icon::after {
  content: attr(data-tooltip);
  position: absolute;
  bottom: 125%;
  left: 50%;
  transform: translateX(-50%);
  background-color: #333;
  color: #fff;
  font-size: 0.75rem;
  padding: 0.3rem 0.5rem;
  border-radius: 0.25rem;
  white-space: nowrap;
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.2s;
  z-index: 10;
}

.tooltip-icon:hover::after {
  opacity: 1;
}

.custom-input {
  width: 100%;
  padding: 0.5rem 0.75rem;
  font-size: 0.95rem;
  border: 2px solid #d1d5db;
  border-radius: 0.5rem;
  background-color: #f9fafb;
  color: #111827;
  transition: border-color 0.2s ease-in-out, box-shadow 0.2s;
}

.custom-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
}
</style>
