<template>
  <div>
    <h2>Currently Online:</h2>
    <ul>
      <li v-for="user in onlineUsers" :key="user.username">{{ user.username }}</li>
    </ul>
  </div>
</template>

<script>
export default {
  data() {
    return {
      onlineUsers: []
    };
  },
  mounted() {
    this.fetchOnlineUsers();
    setInterval(this.fetchOnlineUsers, 10000); // Refresh every 10 seconds
  },
  methods: {
    fetchOnlineUsers() {
      fetch("http://localhost:8000/get_online_users.php")
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            this.onlineUsers = data.online_users;
          }
        });
    }
  }
};
</script>

<style scoped>
h2 {
  color: var(--text-color);
}
</style>
