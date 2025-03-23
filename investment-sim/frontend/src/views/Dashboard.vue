<template>
    <div>
      <h2>Welcome to your Dashboard</h2>
      <button @click="logout">Logout</button>
    </div>
  </template>
  
  <script setup>
  import { useAuthStore } from "../store/auth.js";
  import { useRouter } from "vue-router";
  
  const authStore = useAuthStore();
  const router = useRouter();
  
  const logout = () => {
    authStore.logout();
    router.push("/login");
  };
  </script>
  