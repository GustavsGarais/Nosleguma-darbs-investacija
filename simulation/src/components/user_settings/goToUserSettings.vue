<template>
  <div class="settings-page">
    <div class="settings-box">
      <h2>User Settings</h2>

      <div class="info-section">
        <p><strong>Username:</strong> {{ username }}</p>
        <button @click="showUsernameChange = true">Change Username</button>
      </div>

      <div class="info-section">
        <p><strong>User ID:</strong> {{ userId }}</p>
      </div>

      <div class="info-section">
        <p><strong>Simulations Saved:</strong> {{ simulationCount }}</p>
      </div>

      <div v-if="showUsernameChange" class="change-section">
        <input v-model="newUsername" type="text" placeholder="New Username" />
        <button @click="updateUsername">Update Username</button>
      </div>

      <hr />

      <div class="change-section">
        <h3>Change Password</h3>
        <input v-model="currentPassword" type="password" placeholder="Current Password" />
        <input v-model="newPassword" type="password" placeholder="New Password" />
        <input v-model="repeatNewPassword" type="password" placeholder="Repeat New Password" />
        <button @click="updatePassword">Update Password</button>
      </div>

      <hr />

      <div class="danger-zone">
        <h3>Danger Zone</h3>
        <button class="delete-button">Delete Account</button>
      </div>

      <p class="message">{{ message }}</p>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      username: localStorage.getItem("loggedInUser"),
      userId: localStorage.getItem("loggedInUserId"),
      simulationCount: 0,
      showUsernameChange: false,
      newUsername: "",
      currentPassword: "",
      newPassword: "",
      repeatNewPassword: "",
      message: "",
    };
  },
  mounted() {
    this.fetchSimulations();
  },
  methods: {
    async fetchSimulations() {
      try {
        const response = await fetch("http://localhost:8000/saving_simulations/get_simulation.php", {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ user_id: this.userId })
        })
        const data = await response.json();
        this.simulationCount = Array.isArray(data.simulations) ? data.simulations.length : 0;
      } catch (error) {
        console.error("Failed to load simulations:", error);
      }
    },
    async updateUsername() {
      try {
        const response = await fetch("http://localhost:8000/update_username.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            user_id: this.userId,
            new_username: this.newUsername,
          }),
        });

        const data = await response.json();
        this.message = data.message;

        if (data.success) {
          this.username = this.newUsername;
          localStorage.setItem("loggedInUser", this.newUsername);
          this.showUsernameChange = false;
        }
      } catch (error) {
        console.error("Username update failed:", error);
      }
    },
    async updatePassword() {
      if (this.newPassword !== this.repeatNewPassword) {
        this.message = "New passwords do not match.";
        return;
      }

      try {
        const response = await fetch("http://localhost:8000/update_password.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            user_id: this.userId,
            current_password: this.currentPassword,
            new_password: this.newPassword,
          }),
        });

        const data = await response.json();
        this.message = data.message;

        if (data.success) {
          this.currentPassword = "";
          this.newPassword = "";
          this.repeatNewPassword = "";
        }
      } catch (error) {
        console.error("Password update failed:", error);
      }
    },
  },
};
</script>

<style scoped>
.settings-page {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  padding: 20px;
  background: var(--background-gradient);
}

.settings-box {
  background: rgba(255, 255, 255, 0.06);
  padding: 30px;
  border-radius: 20px;
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.4);
  max-width: 450px;
  width: 100%;
  color: var(--text-color);
}

.settings-box h2 {
  text-align: center;
  margin-bottom: 20px;
}

.info-section,
.change-section {
  margin-bottom: 20px;
}

.info-section p {
  margin: 0 0 5px;
}

input {
  display: block;
  width: 100%;
  margin-bottom: 12px;
  padding: 10px;
  border: none;
  border-radius: 8px;
  background: rgba(255, 255, 255, 0.1);
  color: var(--text-color);
}

button {
  display: inline-block;
  padding: 10px 20px;
  margin-top: 5px;
  margin-right: 5px;
  border: none;
  border-radius: 8px;
  background: var(--button-gradient);
  color: white;
  cursor: pointer;
  transition: background 0.3s;
}

button:hover {
  background: var(--button-gradient-hover);
}

.danger-zone {
  margin-top: 30px;
}

.delete-button {
  background: #ff4e4e;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 8px;
}

.message {
  margin-top: 15px;
  color: #ffd700;
  font-size: 14px;
  text-align: center;
}
</style>
