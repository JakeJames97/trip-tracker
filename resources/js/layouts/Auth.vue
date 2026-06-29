<template>
  <div class="auth-layout">
    <div class="auth-layout__card">
      <aside class="auth-layout__brand">
        <Contours class="auth-layout__contours"/>
        <div class="auth-layout__brand-top">
          <router-link :to="{ name: 'home' }" class="auth-layout__logo">Aerova</router-link>
        </div>
        <div class="auth-layout__brand-copy">
          <h2 class="auth-layout__tagline">Plan your next<br/>adventure</h2>
          <p class="auth-layout__blurb">Map destinations, track your plans, and discover trips from travellers around
            the world.</p>
        </div>
      </aside>

      <main class="auth-layout__panel">
        <div class="auth-layout__inner">
          <router-view/>
        </div>
      </main>
    </div>
  </div>
</template>
<script lang="ts" setup>
import Contours from '@/icons/contours.svg?component';
</script>
