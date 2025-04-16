<template>
    <div>
      <h1>Investment Simulation</h1>
      <p>Welcome, {{ user }}!</p>
      <button @click="logout">Logout</button>
      <p>This is where your simulation will go.</p>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        user: ''
      }
    },
    mounted() {
      const loggedIn = localStorage.getItem('loggedInUser');
      if (!loggedIn) {
        this.$emit('navigate', 'LoginPage');
      } else {
        this.user = loggedIn;
      }
    },
    methods: {
      logout() {
        localStorage.removeItem('loggedInUser');
        this.$emit('navigate', 'HomePage');
      }
    }
  }
  </script>
  