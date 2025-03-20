<template>
    <div class="login-container">
      <h1>Login</h1>
      <form @submit.prevent="handleLogin">
        <label for="username">Username</label>
        <input type="text" id="username" v-model="username" required>
        
        <label for="password">Password</label>
        <input type="password" id="password" v-model="password" required>
        
        <button type="submit">Login</button>
      </form>
    </div>
  </template>
  
  <script>
  import { ref } from 'vue';
  import { useRouter } from 'vue-router';
  
  export default {
    setup() {
      const username = ref('');
      const password = ref('');
      const router = useRouter();
  
      const handleLogin = async () => {
        if (!username.value || !password.value) {
          alert('Please enter both username and password.');
          return;
        }
  
        try {
          const response = await fetch('http://localhost:8000/api/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username: username.value, password: password.value })
          });
  
          if (response.ok) {
            alert('Login successful');
            router.push('/');
          } else {
            const error = await response.json();
            alert(error.message || 'Login failed');
          }
        } catch (error) {
          alert('An error occurred. Please try again.');
        }
      };
  
      return { username, password, handleLogin };
    }
  };
  </script>
  
  <style scoped>
  .login-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100vh;
    background-color: #f0f0f0;
  }
  
  form {
    display: flex;
    flex-direction: column;
    width: 300px;
  }
  
  label {
    margin-top: 10px;
  }
  
  input {
    padding: 10px;
    margin-top: 5px;
    border-radius: 8px;
    border: 1px solid #ccc;
  }
  
  button {
    margin-top: 20px;
    padding: 10px;
    border-radius: 8px;
    border: none;
    background-color: #007bff;
    color: white;
    cursor: pointer;
  }
  
  button:hover {
    background-color: #0056b3;
  }
  </style>
  