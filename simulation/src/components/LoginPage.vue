<template>
  <div class="login-page">
    <div class="login-box">
      <h2>{{ isRegistering ? 'Register' : 'Login' }}</h2>

      <form @submit.prevent="isRegistering ? register() : login()">
        <div class="user-box">
          <input v-model="username" type="text" required />
          <label>Username</label>
        </div>

        <div class="user-box">
          <input v-model="password" type="password" required />
          <label>Password</label>
        </div>

        <div v-if="isRegistering" class="user-box">
          <input v-model="repeatPassword" type="password" required />
          <label>Repeat Password</label>
        </div>

        <p class="message">{{ message }}</p>

        <div class="button-group">
          <button type="submit">
            {{ isRegistering ? 'Register' : 'Login' }}
          </button>
          <button type="button" @click="toggleMode">
            Switch to {{ isRegistering ? 'Login' : 'Register' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      username: "",
      password: "",
      repeatPassword: "",
      isRegistering: false,
      message: "",
    };
  },
  methods: {
    toggleMode() {
      this.isRegistering = !this.isRegistering;
      this.message = "";
      this.username = "";
      this.password = "";
      this.repeatPassword = "";
    },
    async login() {
      try {
        const response = await fetch("http://localhost:8000/login.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            username: this.username,
            password: this.password,
          }),
        });

        const data = await response.json();
        this.message = data.message;

        if (data.success) {
          // Save both username and user_id
          localStorage.setItem("username", this.username);
          localStorage.setItem("loggedInUserId", data.user_id);

          // Navigate to simulation page
          this.$emit("navigate", "SimulationPage");
        }
      } catch (error) {
        console.error("Login failed:", error);
        this.message = "Login failed.";
      }
    },
    async register() {
      if (this.password !== this.repeatPassword) {
        this.message = "Passwords do not match.";
        return;
      }

      try {
        const response = await fetch("http://localhost:8000/register.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            username: this.username,
            password: this.password,
          }),
        });

        const data = await response.json();
        this.message = data.message;

        if (data.success) {
          this.toggleMode();
        }
      } catch (error) {
        console.error("Registration failed:", error);
        this.message = "Registration failed.";
      }
    },
  },
};
</script>

<style scoped>
.login-page {
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background: var(--background-color);
  padding: 1rem;
  box-sizing: border-box;
}

.login-box {
  width: 350px;
  height: 450px;
  background: var(--background-blur);
  padding: 30px;
  border-radius: 16px;
  color: var(--text-color);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
  transition: all 0.3s ease;
  backdrop-filter: blur(5px);
}

h2 {
  text-align: center;
  margin-bottom: 20px;
  font-size: 1.75rem;
}

.user-box {
  position: relative;
  margin-bottom: 20px;
}

.user-box input {
  width: 100%;
  padding: 10px;
  background: transparent;
  border: none;
  border-bottom: 2px solid var(--text-color);
  color: var(--text-color);
  font-size: 1rem;
  outline: none;
}

.user-box label {
  position: absolute;
  top: 10px;
  left: 0;
  pointer-events: none;
  transition: 0.2s ease;
  color: var(--text-muted-color);
}

.user-box input:focus ~ label,
.user-box input:not(:placeholder-shown) ~ label {
  top: -14px;
  font-size: 0.75rem;
  color: var(--text-color);
}

.button-group {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

button {
  padding: 0.85rem;
  background: #4CAF50;
  color: #fff;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 1rem;
  transition: background-color 0.3s ease;
}

button:hover {
  background: #45a049;
}

.message {
  color: #ff8080;
  font-size: 0.85rem;
  text-align: center;
  min-height: 20px;
}
</style>
