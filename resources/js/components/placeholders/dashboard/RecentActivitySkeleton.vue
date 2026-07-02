<template>
  <article class="recent-activity">
    <div class="recent-activity__head">
      <span class="skeleton skeleton--title"/>
    </div>
    <ul class="recent-activity__list">
      <li v-for="n in 4" :key="n" class="activity-item">
        <span class="skeleton skeleton--icon"/>
        <div class="activity-item__body">
          <span class="skeleton skeleton--text"/>
          <span class="skeleton skeleton--time"/>
        </div>
      </li>
    </ul>
  </article>
</template>

<style lang="scss" scoped>
@use './../../../../css/common/colours';

.recent-activity {
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
    width: 120px;
    height: 18px;
  }

  &--icon {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    flex-shrink: 0;
  }

  &--text {
    width: 90%;
    height: 13px;
  }

  &--time {
    width: 40%;
    height: 11px;
    margin-top: 6px;
  }
}

.recent-activity__list {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.activity-item {
  display: flex;
  gap: 12px;
}

.activity-item__body {
  flex: 1;
}
</style>
