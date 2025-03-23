import { createRouter, createWebHistory } from 'vue-router';
import Login from '../components/Login.vue';
import Simulation from '../components/Simulation.vue';

const routes = [
    { path: '/', component: Login },
    { path: '/simulation', component: Simulation }
]

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;