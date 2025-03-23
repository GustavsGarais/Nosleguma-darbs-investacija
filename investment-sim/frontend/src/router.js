import { createRouter, createWebHistory } from "vue-router";
import Home from "./views/Home.vue";
import Login from "./views/Login.vue";
import Register from "./views/Register.vue";
import Dashboard from "./views/Dashboard.vue";

const routes = [
  { path: "/Home", component: Home },
  { path: "/Login", component: Login },
  { path: "/Register", component: Register },
  { path: "/Dashboard", component: Dashboard, meta: { requiresAuth: true } }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
