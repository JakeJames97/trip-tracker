<template>
  <div class="trip-detail-skeleton" aria-hidden="true">
    <div class="trip-detail-skeleton__header">
      <div class="trip-detail-skeleton__header-left">
        <div class="trip-detail-skeleton__title-row">
          <div class="skeleton-block trip-detail-skeleton__name"></div>
          <div class="skeleton-block trip-detail-skeleton__pill"></div>
        </div>
        <div class="skeleton-block trip-detail-skeleton__dates"></div>
      </div>
      <div class="trip-detail-skeleton__actions">
        <div class="skeleton-block trip-detail-skeleton__button"></div>
        <div class="skeleton-block trip-detail-skeleton__button"></div>
      </div>
    </div>

    <div class="skeleton-block trip-detail-skeleton__description"></div>

    <div class="skeleton-block trip-detail-skeleton__section-title"></div>

    <div class="trip-detail-skeleton__destinations">
      <div v-for="n in 2" :key="n" class="trip-detail-skeleton__destination">
        <div class="skeleton-block trip-detail-skeleton__dest-name"></div>
        <div class="skeleton-block trip-detail-skeleton__dest-dates"></div>
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
@use './../../../css/common/colours';

.skeleton-block {
  background: colours.$colour-border;
  border-radius: 6px;
  animation: skeleton-pulse 1.4s ease-in-out infinite;
}

.trip-detail-skeleton {
  display: flex;
  flex-direction: column;
  gap: 20px;

  &__header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
  }

  &__header-left {
    display: flex;
    flex-direction: column;
    gap: 10px;
    flex: 1;
  }

  &__title-row {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  &__name {
    width: 240px;
    height: 28px;
  }

  &__pill {
    width: 80px;
    height: 24px;
    border-radius: 6px;
  }

  &__dates {
    width: 180px;
    height: 16px;
  }

  &__actions {
    display: flex;
    gap: 8px;
    flex-shrink: 0;
  }

  &__button {
    width: 80px;
    height: 38px;
    border-radius: 8px;
  }

  &__description {
    width: 70%;
    height: 16px;
  }

  &__section-title {
    width: 120px;
    height: 22px;
    margin-top: 8px;
  }

  &__destinations {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  &__destination {
    padding: 18px 20px;
    background: colours.$colour-surface;
    border: 1px solid colours.$colour-border;
    border-radius: 10px;
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  &__dest-name {
    width: 40%;
    height: 18px;
  }

  &__dest-dates {
    width: 25%;
    height: 14px;
  }
}
</style>
