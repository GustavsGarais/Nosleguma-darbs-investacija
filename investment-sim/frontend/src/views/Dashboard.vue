<template>
  <div class="dashboard">
    <h1>Welcome to Your Dashboard</h1>
    <p>Your investment simulations will appear here.</p>

    <button @click="logout">Logout</button>
  </div>
</template>

<script setup>
import { useRouter } from "vue-router";

const router = useRouter();

const logout = () => {
  localStorage.removeItem("userToken"); // Clear session
  router.push("/login");
};
</script>
