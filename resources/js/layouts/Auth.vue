<template>
  <div class="auth-page">
    <div class="auth-card">
      <router-view />
    </div>
  </div>
</template>

<style scoped lang="scss">
@use '../../css/variables';

.auth-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
}

.auth-card {
  width: 100%;
  max-width: 480px;
  background: variables.$color-surface;
  border: 1px solid variables.$color-border;
  border-radius: 12px;
  padding: 36px 24px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 8px 24px rgba(0, 0, 0, 0.06);
}
</style>
