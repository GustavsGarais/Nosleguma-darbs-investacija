<script>
export default {
  props: {
    investmentData: Array,
  },
};
</script>

<template>
  <div>
    <h3>Investment Chart (Placeholder)</h3>
    <ul>
      <li v-for="(value, index) in investmentData" :key="index">
        Value: {{ value.toFixed(2) }}
      </li>
    </ul>
  </div>
</template>
