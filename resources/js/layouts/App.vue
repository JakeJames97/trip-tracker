<template>
  <div>
    <Navigation/>
    <main class="content">
      <div class="container">
        <Notification/>
        <router-view :key="route.fullPath"/>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import {ref, watch} from 'vue';
import {useRoute, useRouter} from 'vue-router';
import {useAuthStore} from '@/stores/useAuthStore.ts';
import BaseButton from '@/components/BaseButton.vue';
import Notification from '@/components/Notification.vue';
import NotificationBell from '@/components/NotificationBell.vue';
import {Bars3Icon, XMarkIcon} from '@heroicons/vue/24/outline';
import Navigation from "@/components/Navigation.vue";

const route = useRoute();
const router = useRouter();
const auth = useAuthStore();

const menuOpen = ref(false);

const navLinks = [
  {name: 'home', label: 'Home'},
  {name: 'dashboard', label: 'Dashboard'},
  {name: 'trips', label: 'My Trips'},
  {name: 'discover', label: 'Discover'},
];

watch(() => route.fullPath, () => {
  menuOpen.value = false;
});

async function handleLogout() {
  menuOpen.value = false;
  await auth.logout();
  router.push({name: 'login'});
}
</script>
