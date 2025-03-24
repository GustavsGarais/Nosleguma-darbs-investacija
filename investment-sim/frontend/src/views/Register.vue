<template>
  <div class="register">
    <h2>Register</h2>
    <form @submit.prevent="registerUser">
      <label for="email">Email:</label>
      <input type="email" v-model="email" required />

      <label for="password">Password:</label>
      <input type="password" v-model="password" required />

      <button type="submit">Register</button>
    </form>

    <p>Already have an account? <router-link to="/login">Login here</router-link></p>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";

const email = ref("");
const password = ref("");
const router = useRouter();

const registerUser = async () => {
  try {
    const response = await fetch("http://localhost:3000/api/register", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ email: email.value, password: password.value }),
    });

    if (response.ok) {
      alert("Registration successful!");
      router.push("/login");
    } else {
      alert("Registration failed!");
    }
  } catch (error) {
    console.error("Error registering:", error);
  }
};
</script>
