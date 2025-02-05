<template>
    <div class="investment-box">
      <h2>Login</h2>
      <input v-model="username" placeholder="Username" />
      <input v-model="password" type="password" placeholder="Password" />
      <button @click="login">Login</button>
      <p v-if="error" style="color: red;">{{ error }}</p>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        username: "",
        password: "",
        error: "",
      };
    },
    methods: {
      login() {
        const users = JSON.parse(localStorage.getItem('users')) || [];
        const user = users.find(
          (u) => u.username === this.username && u.password === this.password
        );
  
        if (user) {
          this.$emit('login-success');
        } else {
          this.error = 'Invalid credentials';
        }
      },
    },
  };
  </script>
  