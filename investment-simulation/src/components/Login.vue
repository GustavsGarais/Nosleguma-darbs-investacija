<script>
import { ref, onMounted } from "vue";

export default {
  setup() {
    const username = ref("");
    const password = ref("");
    const newUsername = ref("");
    const newPassword = ref("");
    const isLoggedIn = ref(false);
    const showRegister = ref(false);
    const users = ref([]);
    const showSimulationSetup = ref(false);

    // Load users from localStorage
    onMounted(() => {
      const savedUsers = localStorage.getItem("users");
      if (savedUsers) {
        users.value = JSON.parse(savedUsers);
      }
    });

    const findUser = (user) => users.value.find(u => u.username === user);

    const login = () => {
      const user = findUser(username.value);
      if (user && user.password === password.value) {
        isLoggedIn.value = true;
      } else {
        alert("Invalid username or password.");
      }
    };

    const saveUsers = () => {
      localStorage.setItem("users", JSON.stringify(users.value));
    };

    const register = () => {
      if (findUser(newUsername.value)) {
        alert("Username already exists!");
        return;
      }
      if (!/[A-Z]/.test(newPassword.value) || !/\d/.test(newPassword.value)) {
        alert("Password must contain at least one uppercase letter and one number.");
        return;
      }
      
      users.value.push({ username: newUsername.value, password: newPassword.value });
      saveUsers();
      alert("Registration successful! Please login.");
      showRegister.value = false;
    };

    return { 
      username, password, newUsername, newPassword, login, register, 
      isLoggedIn, showRegister, showSimulationSetup 
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
      <h2>Welcome!</h2>
      <button @click="isLoggedIn = false">Logout</button>
      <button @click="showSimulationSetup = true">Simulation Startup</button>

      <div v-if="showSimulationSetup" class="simulation-setup">
        <h3>Setup Simulation</h3>
        <input type="number" placeholder="Initial Value" />
        <input type="number" placeholder="Investors" />
        <input type="number" placeholder="Volatility" />
        <button>Done</button>
      </div>
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

.login-box, .dashboard {
  text-align: center;
}

.simulation-setup {
  margin-top: 20px;
  padding: 10px;
  border: 1px solid #ddd;
  display: inline-block;
  background: #f9f9f9;
}
</style>
