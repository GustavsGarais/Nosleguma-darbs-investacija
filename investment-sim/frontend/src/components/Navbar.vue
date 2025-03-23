<template>
    <nav>
      <router-link to="/">Home</router-link>
      <router-link to="/login">Login</router-link>
      <router-link to="/register">Register</router-link>
    </nav>
  </template>
  
  <style scoped>
  nav {
    background: #333;
    padding: 1rem;
  }
  nav a {
    color: white;
    margin-right: 10px;
  }
  </style>
  