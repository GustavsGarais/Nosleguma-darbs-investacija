<template>
  <div class="flex flex-col gap-2 text-sm">
    <div class="flex gap-2">
      <button
        @click="$emit('toggle')"
        class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white font-bold rounded-xl"
      >
        {{ simulation.isRunning ? '⏸ Pause' : '▶ Start' }}
      </button>

      <button
        @click="$emit('reset')"
        class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-bold rounded-xl"
      >
        🔄 Reset
      </button>

      <!-- 💾 Save button next to Reset -->
      <button
        @click="$emit('save')"
        class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-bold rounded-xl"
      >
        💾 Save
      </button>
    </div>

    <div>
      <label class="block mb-1 font-medium">⚡ Speed (ms)</label>
      <input
        type="number"
        :value="simulation.speed"
        @input="$emit('change-speed', Number($event.target.value))"
        class="w-full px-2 py-1 rounded border dark:bg-gray-700 dark:text-white"
      />
    </div>
  </div>
</template>


<script>
export default {
  name: 'SimulationControl',
  props: {
    simulation: Object
  },
  emits: ['toggle', 'reset', 'change-speed', 'save']
}
</script>

