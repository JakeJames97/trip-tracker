import { createRouter, createWebHistory } from 'vue-router';
import {useAuthStore} from "@/stores/useAuthStore.ts";

const router = createRouter({
    history: createWebHistory(),
    routes: [
      {
        path: '/',
        component: () => import('../layouts/Auth.vue'),
        meta: { requiresGuest: true },
        children: [
          { path: 'login', name: 'login', component: () => import('../pages/Login.vue') },
          { path: 'register', name: 'register', component: () => import('../pages/Register.vue') },
        ],
      },
      {
        path: '/',
        component: () => import('../layouts/App.vue'),
        meta: { requiresAuth: true },
        children: [
          { path: 'dashboard', name: 'dashboard', component: () => import('../pages/Dashboard.vue') },
        ],
      },
    ],
});

router.beforeEach((to) => {
  const auth = useAuthStore();

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return { name: 'login' };
  }

  if (to.meta.requiresGuest && auth.isAuthenticated) {
    return { name: 'dashboard' };
  }
});

export default router;
