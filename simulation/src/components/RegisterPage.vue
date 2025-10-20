<template>
  <div class="auth-page">
    <div class="card" style="max-width:420px;margin:2rem auto;padding:1rem">
      <h2>Create an account</h2>

      <form @submit.prevent="onSubmit">
        <div class="input-group">
          <label>Username</label>
          <input v-model="username" required />
        </div>

        <div class="input-group">
          <label>Password</label>
          <input v-model="password" type="password" required />
        </div>

        <div class="input-group">
          <label>Repeat Password</label>
          <input v-model="repeatPassword" type="password" required />
        </div>

        <p class="muted">{{ message }}</p>

        <div style="display:flex;gap:.6rem;margin-top:.6rem">
          <button class="btn btn-primary" type="submit">Register</button>
          <button class="btn btn-ghost" type="button" @click="$emit('navigate','LoginPage')">Back to login</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return { username:'', password:'', repeatPassword:'', message:'' }
  },
  methods: {
    async onSubmit(){
      if(this.password !== this.repeatPassword){ this.message = 'Passwords do not match'; return }
      try{
        const auth = await import('../services/authService')
        const res = await auth.register({ username:this.username, password:this.password })
        this.message = res.message || ''
        if(res.success) {
          // Auto-navigate to login so user can sign in
          this.$emit('navigate','LoginPage')
        }
      }catch(err){
        console.error(err)
        this.message = 'Registration failed'
      }
    }
  }
}
</script>

<style scoped>
.auth-page{padding:2rem}
.input-group{margin-bottom:.6rem}
label{display:block;font-weight:600;margin-bottom:.25rem}
input{width:100%;padding:.5rem;border-radius:.5rem;border:1px solid #d1d5db}
</style>
