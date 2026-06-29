<template>
  <header class="header">
    <div class="header__pill">
      <div class="header__inner">
        <div class="header__brand">
          <router-link :to="{ name: 'home' }">
            <img src="@/images/Logo.png" alt="logo" />
          </router-link>
        </div>

        <nav class="header__nav header__nav--desktop">
          <router-link
            v-for="link in navLinks"
            :key="link.name"
            :to="{ name: link.name }"
            class="header__link"
          >
            {{ link.label }}
          </router-link>
        </nav>

        <div class="header__actions">
          <template v-if="auth.isAuthenticated">
            <NotificationBell />
            <BaseButton
              variant="outline"
              class="header__action--desktop"
              @click="handleLogout"
            >
              Log out
            </BaseButton>
          </template>

          <template v-else>
            <BaseButton
              variant="outline"
              class="header__action--desktop"
              @click="goToLogin"
            >
              Log in
            </BaseButton>
            <BaseButton
              variant="primary"
              class="header__action--desktop"
              @click="goToRegister"
            >
              Sign up
            </BaseButton>
          </template>

          <button
            type="button"
            class="header__burger"
            :aria-label="menuOpen ? 'Close menu' : 'Open menu'"
            :aria-expanded="menuOpen"
            @click="menuOpen = !menuOpen"
          >
            <Bars3Icon v-if="!menuOpen" class="header__burger-icon" />
            <XMarkIcon v-else class="header__burger-icon" />
          </button>
        </div>
      </div>
    </div>

    <transition name="menu">
      <nav v-if="menuOpen" class="header__nav header__nav--mobile">
        <router-link
          v-for="link in navLinks"
          :key="link.name"
          :to="{ name: link.name }"
          class="header__link header__link--mobile"
          @click="menuOpen = false"
        >
          {{ link.label }}
        </router-link>

        <div class="header__menu-auth">
          <template v-if="auth.isAuthenticated">
            <BaseButton variant="outline" class="header__menu-auth-btn" @click="handleLogout">
              Log out
            </BaseButton>
          </template>
          <template v-else>
            <BaseButton variant="outline" class="header__menu-auth-btn" @click="goToLogin">
              Log in
            </BaseButton>
            <BaseButton variant="outline" class="header__menu-auth-btn" @click="goToRegister">
              Sign up
            </BaseButton>
          </template>
        </div>
      </nav>
    </transition>
  </header>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/useAuthStore.ts';
import BaseButton from '@/components/BaseButton.vue';
import NotificationBell from '@/components/NotificationBell.vue';
import { Bars3Icon, XMarkIcon } from '@heroicons/vue/24/outline';

const route = useRoute();
const router = useRouter();
const auth = useAuthStore();

const menuOpen = ref(false);

const allNavLinks = [
  { name: 'home', label: 'Home', requiresAuth: false },
  { name: 'discover', label: 'Discover', requiresAuth: false },
  { name: 'dashboard', label: 'Dashboard', requiresAuth: true },
  { name: 'trips', label: 'My Trips', requiresAuth: true },
];

const navLinks = computed(() =>
  allNavLinks.filter((link) => !link.requiresAuth || auth.isAuthenticated),
);

function goToLogin() {
  menuOpen.value = false;
  router.push({ name: 'login' });
}

function goToRegister() {
  menuOpen.value = false;
  router.push({ name: 'register' });
}

watch(() => route.fullPath, () => {
  menuOpen.value = false;
});

async function handleLogout() {
  menuOpen.value = false;
  await auth.logout();
  router.push({ name: 'login' });
}
</script>
