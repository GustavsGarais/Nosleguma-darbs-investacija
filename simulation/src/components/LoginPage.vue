<template>
  <div class="login-box">
    <h2>Login / Register</h2>
    <form @submit.prevent="login">
      <div class="user-box">
        <input v-model="username" type="text" required />
        <label>Username</label>
      </div>
      <div class="user-box">
        <input v-model="password" type="password" required />
        <label>Password</label>
      </div>
      <p style="color: white;">{{ message }}</p>
      <button type="button" @click="login">Login</button>
      <button type="button" @click="register">Register</button>
    </form>
  </div>
</template>

<script>
export default {
  data() {
    return {
      username: "",
      password: "",
      message: "",
    };
  },
  methods: {
    async login() {
      try {
        const response = await fetch("http://localhost:3000/login", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            username: this.username,
            password: this.password,
          }),
        });
        const data = await response.json();
        this.message = data.message;

        // ✅ Use event-based navigation instead of Vue Router
        if (data.success) {
          this.$emit("navigate", "SimulationPage");
        }
      } catch (error) {
        console.error("Login failed:", error);
        this.message = "Login failed.";
      }
    },
    async register() {
      try {
        const response = await fetch("http://localhost:3000/register", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            username: this.username,
            password: this.password,
          }),
        });
        const data = await response.json();
        this.message = data.message;
      } catch (error) {
        console.error("Registration failed:", error);
        this.message = "Registration failed.";
      }
    },
  },
};
</script>

<style scoped>
.top-bar {
  position: absolute;
  top: 20px;
  right: 20px;
  display: flex;
  gap: 10px;
}

.top-bar button {
  background: var(--background-blur);
  color: var(--text-color);
  padding: 6px 12px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.top-bar button:hover {
  opacity: 0.85;
}

.login-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  width: 100%;
  position: relative;
  padding: 60px 20px;
  box-sizing: border-box;
}

.login-box {
  background-color: var(--background-blur);
  backdrop-filter: blur(5px);
  padding: 2rem;
  border-radius: 15px;
  box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
  width: 300px;
  max-width: 90vw;
  text-align: center;
  color: var(--text-color);
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin: auto 0;
}

h2 {
  margin-bottom: 1rem;
}

input {
  display: block;
  width: 100%;
  padding: 0.6rem;
  margin-bottom: 1rem;
  border-radius: 5px;
  border: 1px solid #ccc;
}

.button-row {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  gap: 0.6rem;
}

.button-row button {
  flex: 1 1 auto;
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  background: var(--background-blur);
  color: var(--text-color);
}

.button-row button:hover {
  opacity: 0.85;
}

.error {
  color: red;
  margin-top: 1rem;
}
</style>
