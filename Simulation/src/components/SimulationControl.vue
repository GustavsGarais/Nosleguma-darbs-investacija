<template>
    <div class="control-container">
      <button @click="$emit('togglePause')">
        {{ isPaused ? 'Resume' : 'Pause' }}
      </button>
      <label>
        Speed:
        <input
          type="range"
          v-model.number="localSpeed"
          min="0.5"
          max="5"
          step="0.5"
          @change="speedChanged"
        />
        {{ localSpeed }}x
      </label>
    </div>
  </template>
  
  <script>
  export default {
    name: 'SimulationControl',
    props: {
      isPaused: Boolean,
      speed: Number
    },
    data() {
      return {
        localSpeed: this.speed
      }
    },
    methods: {
      speedChanged() {
        this.$emit('speedChange', this.localSpeed)
      }
    }
  }
  </script>
  
  <style scoped>
  .control-container {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    margin: 1rem 0;
  }
  input[type="range"] {
    vertical-align: middle;
  }
  button {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }
  </style>