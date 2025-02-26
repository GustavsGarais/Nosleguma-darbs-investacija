<script>
import { ref, onMounted } from "vue";
import { loginUser, registerUser } from "/server/utils/simulationStorage";

export default {
  setup() {
    const username = ref("");
    const password = ref("");
    const newUsername = ref("");
    const newPassword = ref("");
    const isLoggedIn = ref(false);
    const showRegister = ref(false);
    const showSimulationSetup = ref(false);
    const currentUser = ref(null);

    onMounted(() => {
      const savedUser = localStorage.getItem("currentUser");
      if (savedUser) {
        isLoggedIn.value = true;
        currentUser.value = JSON.parse(savedUser);
      }
    });

    const login = async () => {
      const user = await loginUser(username.value, password.value);
      if (user) {
        isLoggedIn.value = true;
        currentUser.value = user;
        localStorage.setItem("currentUser", JSON.stringify(user));
      } else {
        alert("Invalid username or password.");
      }
    };

    const register = async () => {
      try {
        const success = await registerUser(newUsername.value, newPassword.value);
        if (success) {
          alert("Registration successful! Please login.");
          showRegister.value = false;
        } else {
          alert("Registration failed. Username may already exist.");
        }
      } catch (error) {
        console.error("Error registering user:", error);
        alert("An error occurred during registration.");
      }
    };

    return {
      username,
      password,
      newUsername,
      newPassword,
      login,
      register,
      isLoggedIn,
      showRegister,
      showSimulationSetup,
      currentUser,
    };
  },
};
</script>

<template>
  <div class="container">
    <div v-if="!isLoggedIn" class="login-box">
      <h2 v-if="!showRegister">Login</h2>
      <h2 v-else>Register</h2>

      <div v-if="!showRegister">
        <input v-model="username" placeholder="Username" />
        <input v-model="password" type="password" placeholder="Password" />
        <button @click="login">Login</button>
        <button @click="showRegister = true">Register</button>
      </div>

      <div v-else>
        <input v-model="newUsername" placeholder="New Username" />
        <input v-model="newPassword" type="password" placeholder="New Password" />
        <button @click="register">Register</button>
        <button @click="showRegister = false">Back</button>
      </div>
    </div>

    <div v-else class="dashboard">
      <h2>Welcome, {{ currentUser.username }}!</h2>
      <button @click="isLoggedIn = false">Logout</button>
      <button @click="showSimulationSetup = true">Simulation Startup</button>
    </div>
  </div>
</template>
