<template>
  <div class="p-4 bg-gray-800 text-white rounded-lg shadow-lg flex flex-col gap-4">
    <label class="text-lg">Simulation Speed:</label>
    <input
      type="range"
      v-model="speed"
      min="100"
      max="2000"
      step="100"
      class="w-full cursor-pointer"
    />
    <span class="text-sm text-gray-300">{{ speed }}ms</span>
  </div>
</template>

<script>
export default {
  data() {
    return {
      speed: 1000
    };
  },
  watch: {
    speed(newSpeed) {
      this.$emit('update-speed', newSpeed);
    }
  }
};
</script>
