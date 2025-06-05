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
        if (data.success) {
          this.$router.push("/simulation");
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
.login-box {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 400px;
  padding: 40px;
  transform: translate(-50%, -50%);
  background: rgba(0,0,0,0.8);
  box-shadow: 0 15px 25px rgba(0,0,0,0.6);
  border-radius: 10px;
  color: #fff;
}
.user-box {
  position: relative;
}
.user-box input {
  width: 100%;
  padding: 10px;
  background: transparent;
  border: none;
  border-bottom: 1px solid #fff;
  color: #fff;
}
.user-box label {
  position: absolute;
  top: 10px;
  left: 0;
  color: #fff;
  pointer-events: none;
  transition: 0.5s;
}
.user-box input:focus ~ label,
.user-box input:valid ~ label {
  top: -20px;
  left: 0;
  color: #03e9f4;
  font-size: 12px;
}
button {
  background: #03e9f4;
  border: none;
  padding: 10px 20px;
  margin-right: 10px;
  color: #fff;
  cursor: pointer;
}
</style>
