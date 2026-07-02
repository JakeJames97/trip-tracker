<template>
  <article class="next-trip-card">
    <div class="next-trip-card__head">
      <span class="skeleton skeleton--title"/>
    </div>
    <span class="skeleton skeleton--image"/>
    <div class="next-trip-card__info">
      <span class="skeleton skeleton--name"/>
      <span class="skeleton skeleton--countdown"/>
    </div>
    <div class="next-trip-card__meta">
      <span v-for="n in 3" :key="n" class="skeleton skeleton--meta"/>
    </div>
    <span class="skeleton skeleton--button"/>
  </article>
</template>

<style lang="scss" scoped>
@use './../../../../css/common/colours';

.next-trip-card {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.skeleton {
  display: block;
  background: colours.$colour-skeleton-gradient;
  background-size: 200% 100%;
  border-radius: 6px;

  &--title {
    width: 80px;
    height: 18px;
  }

  &--image {
    height: 140px;
    border-radius: 12px;
    margin-bottom: 14px;
  }

  &--name {
    width: 60%;
    height: 20px;
  }

  &--countdown {
    width: 40%;
    height: 14px;
    margin-top: 6px;
  }

  &--meta {
    width: 50px;
    height: 32px;
  }

  &--button {
    width: 100%;
    height: 44px;
    border-radius: 10px;
  }
}

.next-trip-card__info {
  margin-bottom: 18px;
}

.next-trip-card__meta {
  display: flex;
  justify-content: space-between;
  padding: 14px 0;
  margin-bottom: 18px;
}
</style>
