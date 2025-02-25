<script>
import { ref } from "vue";

export default {
  setup(_, { emit }) {
    const username = ref("");
    const showRegister = ref(false);
    const newUsername = ref("");
    const newPassword = ref("");

    const login = async () => {
      if (!username.value) return alert("Enter a username");

      try {
        const response = await fetch("http://localhost:3000/login", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ username: username.value }),
        });

        const data = await response.json();
        if (response.ok) {
          emit("loginSuccess", data.username);
        } else {
          alert(data.error);
        }
      } catch (error) {
        alert("Error connecting to server");
      }
    };

    return { username, showRegister, newUsername, newPassword, login };
  },
};
</script>

<template>
  <div class="container">
    <div v-if="!showRegister">
      <h2>Login</h2>
      <input v-model="username" placeholder="Username" />
      <button @click="login">Login</button>
      <button @click="showRegister = true">Register</button>
    </div>

    <div v-else>
      <h2>Register</h2>
      <input v-model="newUsername" placeholder="New Username" />
      <input v-model="newPassword" type="password" placeholder="New Password" />
      <button @click="showRegister = false">Back</button>
    </div>
  </div>
</template>

<style>
.container {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100vh;
}

input {
  margin: 5px;
  padding: 5px;
}
</style>
