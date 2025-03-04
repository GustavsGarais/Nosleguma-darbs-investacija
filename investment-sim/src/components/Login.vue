<template>
  <div class="login-container">
    <h2>{{ isRegistering ? "Register" : "Login" }}</h2>
    <form @submit.prevent="handleSubmit">
      <label>Username:</label>
      <input type="text" v-model="username" required />

      <label>Password:</label>
      <input type="password" v-model="password" required />

      <button type="submit">{{ isRegistering ? "Register" : "Login" }}</button>
      <p @click="toggleMode" class="toggle-text">
        {{ isRegistering ? "Already have an account? Login" : "No account? Register" }}
      </p>
    </form>

    <p v-if="error" class="error">{{ error }}</p>
  </div>
</template>

<script>
export default {
  data() {
    return {
      username: "",
      password: "",
      isRegistering: false, // ✅ Now correctly defined
      error: ""
    };
  },
  methods: {
    toggleMode() {
      this.isRegistering = !this.isRegistering;
      this.error = ""; // Clear errors when switching mode
    },
    async handleSubmit() {
  this.error = "";
  const endpoint = this.isRegistering ? "register" : "login";

  try {
    const response = await fetch(`http://localhost:3000/${endpoint}`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ username: this.username, password: this.password })
    });

    const data = await response.json();
    if (!data.success) {
      throw new Error(data.message || "Request failed");
    }

    alert(this.isRegistering ? "Registration successful!" : "Login successful!");

    // ✅ Redirect to simulation page after successful login
    if (!this.isRegistering) {
      this.$router.push("/simulation");
    }
  } catch (err) {
    this.error = err.message;
  }
}

  }
};
</script>

<style>
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
.toggle-text {
  cursor: pointer;
  color: blue;
  text-decoration: underline;
}
.error {
  color: red;
}
</style>
