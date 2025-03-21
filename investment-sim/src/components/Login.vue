<template>
  <div class="login-container">
    <h2>{{ isRegistering ? "Register" : "Login" }}</h2>

    <form @submit.prevent="handleAuth">
      <label>Username:</label>
      <input type="text" v-model="username" required />

      <label>Password:</label>
      <input type="password" v-model="password" required />

      <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>

      <button type="submit">{{ isRegistering ? "Register" : "Login" }}</button>
    </form>

    <button @click="toggleMode">
      {{ isRegistering ? "Switch to Login" : "Switch to Register" }}
    </button>
  </div>
</template>

<script>
export default {
  data() {
    return {
      username: "",
      password: "",
      isRegistering: false,
      errorMessage: "",
    };
  },
  methods: {
    async handleAuth() {
      const endpoint = this.isRegistering ? "register" : "login";
      try {
        const response = await fetch(`http://localhost:8000/api/${endpoint}`, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          credentials: "include", // Required for Laravel sessions
          body: JSON.stringify({
            username: this.username,
            password: this.password,
          }),
        });

        const data = await response.json();
        if (response.ok) {
          console.log(`${this.isRegistering ? "Registered" : "Logged in"} successfully`);
          this.$router.push("/simulation"); // Redirect after login
        } else {
          this.errorMessage = data.error || "An error occurred.";
        }
      } catch (error) {
        this.errorMessage = "Connection error. Make sure the backend is running.";
      }
    },
    toggleMode() {
      this.isRegistering = !this.isRegistering;
      this.errorMessage = ""; // Clear errors when switching modes
    },
  },
};
</script>

<style scoped>
.login-container {
  width: 300px;
  margin: auto;
  text-align: center;
}
input {
  display: block;
  margin: 5px auto;
  padding: 5px;
  width: 100%;
}
button {
  margin-top: 10px;
  padding: 10px;
  cursor: pointer;
}
.error-message {
  color: red;
}
</style>
