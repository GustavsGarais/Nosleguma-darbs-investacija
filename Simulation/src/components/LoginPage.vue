<template>
    <div>
      <h2>Login</h2>
      <form @submit.prevent="handleLogin">
        <input v-model="username" placeholder="Username" required />
        <input v-model="password" type="password" placeholder="Password" required />
        <button type="submit">Login</button>
      </form>
      <p>Or <a href="#" @click.prevent="register">Register</a></p>
      <p style="color:red">{{ error }}</p>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        username: '',
        password: '',
        error: ''
      }
    },
    methods: {
      handleLogin() {
        const stored = JSON.parse(localStorage.getItem(this.username));
        if (stored && stored.password === this.password) {
          localStorage.setItem('loggedInUser', this.username);
          this.$emit('navigate', 'SimulationPage');
        } else {
          this.error = 'Invalid username or password';
        }
      },
      register() {
        const exists = localStorage.getItem(this.username);
        if (exists) {
          this.error = 'User already exists!';
        } else {
          localStorage.setItem(this.username, JSON.stringify({ password: this.password }));
          localStorage.setItem('loggedInUser', this.username);
          this.$emit('navigate', 'SimulationPage');
        }
      }
    }
  }
  </script>
  