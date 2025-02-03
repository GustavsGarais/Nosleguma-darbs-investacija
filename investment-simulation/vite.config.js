import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
  plugins: [vue()],
  root: './src', // Make sure Vite knows to look inside the src folder
  server: {
    port: 5173,
    open: true,
  },
});
