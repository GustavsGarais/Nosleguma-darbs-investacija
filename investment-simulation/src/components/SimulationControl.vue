<script>
import { ref, watch } from "vue";

export default {
  props: ["investmentData"],
  setup(props) {
    const running = ref(true);

    // Function to update investment randomly
    const updateInvestment = () => {
      if (!running.value) return;

      const change = (Math.random() - 0.5) * 5; // Random change between -2.5 and +2.5
      props.investmentData.value += change;

      // Ensure value doesn't go below 0
      if (props.investmentData.value < 0) {
        props.investmentData.value = 0;
      }
    };

    // Run every 3 seconds
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
