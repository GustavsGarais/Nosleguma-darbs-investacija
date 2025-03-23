<template>
    <div>
      <h2>Login</h2>
      <form @submit.prevent="handleLogin">
        <input v-model="username" placeholder="Username" />
        <input v-model="password" type="password" placeholder="Password" />
        <button type="submit">Login</button>
      </form>
    </div>
  </template>
  
  <script setup>
  import { ref } from "vue";
  import { useAuthStore } from "../store/auth.js";
  import { useRouter } from "vue-router";
  
  const authStore = useAuthStore();
  const router = useRouter();
  const username = ref("");
  const password = ref("");
  
  const handleLogin = async () => {
    await authStore.login(username.value, password.value);
    router.push("/dashboard");
  };
  </script>
  