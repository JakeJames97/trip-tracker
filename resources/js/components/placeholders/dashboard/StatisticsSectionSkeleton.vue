<template>
  <section class="statistics-section">
    <div v-for="n in 5" :key="n" class="stat-card">
      <span class="skeleton skeleton--icon"/>
      <div class="stat-card__body">
        <span class="skeleton skeleton--label"/>
        <span class="skeleton skeleton--value"/>
      </div>
    </div>
  </section>
</template>

<style lang="scss" scoped>
@use './../../../../css/common/colours';

.skeleton {
  display: block;
  background: colours.$colour-skeleton-gradient;
  background-size: 200% 100%;
  border-radius: 6px;

  &--icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
  }

  &--label {
    width: 60%;
    height: 12px;
    margin-bottom: 8px;
  }

  &--value {
    width: 40%;
    height: 24px;
  }
}
</style>
