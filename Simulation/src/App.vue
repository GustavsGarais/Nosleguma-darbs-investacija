<template>
  <div>
    <component :is="currentPage" @navigate="navigate" />
  </div>
</template>

<script>
import HomePage from './components/HomePage.vue'
import LoginPage from './components/LoginPage.vue'
import SimulationPage from './components/SimulationPage.vue'

export default {
  data() {
    return {
      currentPage: 'HomePage'
    }
  },
  components: {
    HomePage,
    LoginPage,
    SimulationPage
  },
  methods: {
    navigate(page) {
      this.currentPage = page
    }
  }
}
</script>
