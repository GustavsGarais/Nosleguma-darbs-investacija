<template>
  <div class="container">
    <h2 v-if="!registering">Login</h2>
    <h2 v-else>Register</h2>

    <input v-model="username" placeholder="Username" />
    <input v-model="password" type="password" placeholder="Password" />
    
    <button v-if="!registering" @click="login">Login</button>
    <button v-if="!registering" @click="registering = true">Register</button>
    <button v-if="registering" @click="register">Create Account</button>
  </div>
</template>

<script>
export default {
  data() {
    return {
      username: "",
      password: "",
      registering: false,
    };
  },
  methods: {
    login() {
      const users = JSON.parse(localStorage.getItem("users")) || {};
      if (users[this.username] && users[this.username] === this.password) {
        localStorage.setItem("currentUser", this.username);
        this.$emit("loginSuccess");
      } else {
        alert("Invalid login!");
      }
    },
    register() {
      if (!/[A-Z]/.test(this.password) || !/[0-9]/.test(this.password)) {
        alert("Password must contain at least one uppercase letter and one number.");
        return;
      }

      const users = JSON.parse(localStorage.getItem("users")) || {};
      if (users[this.username]) {
        alert("Username already exists!");
      } else {
        users[this.username] = this.password;
        localStorage.setItem("users", JSON.stringify(users));
        alert("Account created! You can log in now.");
        this.registering = false;
      }
    },
  },
};
</script>
