import Vue from 'vue';
import Router from 'vue-router';
import Home from '../components/Home.vue'; // Import the new Home.vue component

Vue.use(Router);

export default new Router({
  routes: [
    {
      path: '/',
      name: 'Home',
      component: Home, // Set the Home component for the root path
    },
  ],
});
