import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';  // Import Vue plugin

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [vue()] // Add Vue plugin to the plugins array
});
