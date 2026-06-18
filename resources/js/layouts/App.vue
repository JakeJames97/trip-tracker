<template>
  <div>
    <header class="header">
      <div class="header__inner container">
        <div class="header__brand">
          <span class="header__logo">T</span>
          <span class="header__title">Trip tracker</span>
        </div>

        <nav class="nav">
          <router-link
            v-for="link in navLinks"
            :key="link.name"
            :to="{ name: link.name }"
            class="nav__link"
          >
            {{ link.label }}
          </router-link>
        </nav>

        <BaseButton variant="outline" @click="handleLogout">Log out</BaseButton>
      </div>
    </header>

    <main class="content">
      <div class="container">
        <router-view />
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/useAuthStore.ts';
import BaseButton from "@/components/BaseButton.vue";

const router = useRouter();
const auth = useAuthStore();

const navLinks = [{ name: 'dashboard', label: 'Dashboard' }];

async function handleLogout() {
  await auth.logout();
  router.push({ name: 'login' });
}
</script>

<style scoped lang="scss">
@use '../../css/colours';
@use '../../css/typography';
@use '../../css/breakpoints';

.header {
  background: colours.$colour-surface;
  border-bottom: 1px solid colours.$colour-border;

  &__inner {
    max-width: 1280px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 18px 0;

    @include breakpoints.above(breakpoints.$tablet) {
      gap: 32px;
    }
  }

  &__brand {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  &__logo {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: colours.$colour-accent;
    color: #fff;
    font-weight: 700;
    font-size: typography.$font-size-sm;
    flex-shrink: 0;
  }

  &__title {
    font-weight: 700;
    font-size: typography.$font-size-base;
    white-space: nowrap;
  }
}

.nav {
  display: flex;
  gap: 8px;

  @include breakpoints.above(breakpoints.$tablet) {
    flex: 1;
  }

  &__link {
    padding: 8px 12px;
    border-radius: 8px;
    font-size: typography.$font-size-sm;
    font-weight: 500;
    color: colours.$colour-text-muted;
    text-decoration: none;

    &:hover {
      background: colours.$colour-bg;
      color: colours.$colour-text;
    }

    &.router-link-exact-active {
      background: colours.$colour-bg;
      color: colours.$colour-accent;
    }
  }
}

.container {
  max-width: 1280px;
  margin: 0 auto;
  padding-left: 24px;
  padding-right: 24px;
}

.content {
  padding: 40px 0;
}
</style>
