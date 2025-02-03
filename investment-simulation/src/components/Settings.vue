<template>
    <div class="investment-box">
      <h2>Settings</h2>
      <label>Font Size:</label>
      <select v-model="fontSize" @change="applySettings">
        <option>Small</option>
        <option>Medium</option>
        <option>Large</option>
      </select>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        fontSize: 'Medium',
      };
    },
    methods: {
      applySettings() {
        document.body.style.fontSize = this.fontSize === 'Large' ? '18px' : this.fontSize === 'Small' ? '12px' : '14px';
      },
    },
  };
  </script>
  