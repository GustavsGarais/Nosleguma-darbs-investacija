<script>
import { ref, watch } from "vue";

export default {
  props: ["investmentData"],
  emits: ["update-investment"],
  setup(props, { emit }) {
    const running = ref(true);

    const updateInvestment = () => {
      if (!running.value) return;

      const lastValue = props.investmentData[props.investmentData.length - 1] || 0;
      const change = (Math.random() - 0.5) * 10; // Random change in range -5 to +5
      const newValue = Math.max(0, lastValue + change);

      emit("update-investment", newValue); // Notify parent to update the data
    };

    setInterval(updateInvestment, 3000);

    return { running };
  },
};
</script>

<template>
  <button @click="running = !running">
    {{ running ? "Pause" : "Run" }}
  </button>
</template>
