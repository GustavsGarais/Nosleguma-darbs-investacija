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

        if (data.success) {
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

        if (data.success) {
          this.toggleMode(); // Go to login after successful register
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
  background: rgba(20, 20, 20, 0.85);
  padding: 1rem;
  box-sizing: border-box;
}

.login-box {
  width: 350px;
  background: rgba(0, 0, 0, 0.85);
  padding: 30px;
  border-radius: 15px;
  color: white;
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.8);
  transition: all 0.3s ease;
}

h2 {
  text-align: center;
  margin-bottom: 20px;
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
  border-bottom: 2px solid #fff;
  color: white;
  font-size: 16px;
  outline: none;
}

.user-box label {
  position: absolute;
  top: 10px;
  left: 0;
  pointer-events: none;
  transition: 0.2s ease;
  color: #aaa;
}

.user-box input:focus ~ label,
.user-box input:not(:placeholder-shown) ~ label {
  top: -15px;
  font-size: 12px;
  color: #fff;
}

.button-group {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

button {
  padding: 10px;
  background: #5b8c5a;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.3s ease;
}

button:hover {
  background: #497146;
}

.message {
  color: #ff8080;
  font-size: 14px;
  text-align: center;
  min-height: 20px;
}
</style>
