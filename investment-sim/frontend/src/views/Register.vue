<template>
    <div>
      <h2>Register</h2>
      <form @submit.prevent="registerUser">
        <input v-model="username" placeholder="Username" />
        <input v-model="password" type="password" placeholder="Password" />
        <button type="submit">Register</button>
      </form>
    </div>
  </template>
  
  <script setup>
  import { ref } from "vue";
  import axios from "axios";
  import { useRouter } from "vue-router";
  
  const username = ref("");
  const password = ref("");
  const router = useRouter();
  
  const registerUser = async () => {
    await axios.post("http://localhost:3001/api/auth/register", {
      username: username.value,
      password: password.value
    });
    router.push("/login");
  };
  </script>
  