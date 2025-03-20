<template>
  <div :class="['app', theme]">
    <header>
      <button @click="toggleTheme">Toggle {{ theme === 'light' ? 'Dark' : 'Light' }} Mode</button>
    </header>
    <router-view></router-view>
  </div>
</template>

<script>
import { defineComponent, ref, provide } from 'vue';
import { useRouter } from 'vue-router';

export default defineComponent({
  name: 'App',
  setup() {
    const router = useRouter();
    const theme = ref('light');

    const toggleTheme = () => {
      theme.value = theme.value === 'light' ? 'dark' : 'light';
    };

    provide('theme', theme);
    provide('toggleTheme', toggleTheme);

    return { router, theme, toggleTheme };
  }
});
</script>

<style>
body, html {
  margin: 0;
  padding: 0;
  font-family: Arial, sans-serif;
}

#app {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100vh;
}

header {
  position: absolute;
  top: 10px;
  right: 10px;
}

button {
  padding: 10px 20px;
  border: none;
  cursor: pointer;
  font-size: 16px;
  border-radius: 20px;
}

button:hover {
  opacity: 0.9;
}

.light {
  background: linear-gradient(to bottom, #d4edda, #a3c293);
  color: #1d3b20;
}

.dark {
  background: linear-gradient(to bottom, #6a0572, #003b8b);
  color: #e1e1ff;
}
</style>
