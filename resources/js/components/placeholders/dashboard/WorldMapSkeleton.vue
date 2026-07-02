<template>
  <div class="world-map">
    <div class="world-map__heading">
      <span class="skeleton skeleton--title"/>
      <span class="skeleton skeleton--subtitle"/>
    </div>
    <span class="skeleton skeleton--canvas"/>
  </div>
</template>

<style lang="scss" scoped>
@use './../../../../css/common/breakpoints';
@use './../../../../css/common/colours';

.world-map {
  display: flex;
  flex-direction: column;
  padding: 20px;
  background: colours.$colour-surface;
  border: 1px solid colours.$colour-border;
  border-radius: 16px;

  &__heading {
    align-self: flex-start;
  }
}

.skeleton {
  display: block;
  background: colours.$colour-skeleton-gradient;
  background-size: 200% 100%;
  border-radius: 6px;
  animation: skeleton-shimmer 1.4s ease infinite;

  &--title {
    width: 140px;
    height: 20px;
    margin-bottom: 8px;
  }

  &--subtitle {
    width: 180px;
    height: 14px;
  }

  &--canvas {
    width: 100%;
    border-radius: 12px;
    margin-top: 12px;
    height: 340px;

    @include breakpoints.above(breakpoints.$tablet) {
      height: 620px;
    }
  }
}
</style>
