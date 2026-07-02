<template>
  <RecentActivitySkeleton v-if="loading" />
  <article v-else class="recent-activity">
    <div class="recent-activity__head">
      <h4 class="recent-activity__title">Recent activity</h4>
    </div>

    <ul v-if="activity.length" class="recent-activity__list">
      <li v-for="item in activity" :key="item.id" class="activity-item">
        <span class="activity-item__icon" :class="`activity-item__icon--${iconVariant(item)}`">
          <component :is="iconFor(item)" class="activity-item__icon-svg"/>
        </span>
        <div class="activity-item__body">
          <p class="activity-item__text">{{ item.data.message }}</p>
          <time class="activity-item__time">{{ formatRelativeTime(item.created_at) }}</time>
        </div>
      </li>
    </ul>

    <p v-else class="recent-activity__empty">No recent activity yet.</p>
  </article>
</template>

<script setup lang="ts">
import {computed} from 'vue';
import {useApiNotificationStore} from '@/stores/useApiNotificationStore.ts';
import {HeartIcon, DocumentDuplicateIcon, GlobeAltIcon, BellIcon} from '@heroicons/vue/24/solid';
import type {Notification} from '@/types/notifications';
import {formatRelativeTime} from "@/lib/date.ts";
import RecentActivitySkeleton from "@/components/placeholders/dashboard/RecentActivitySkeleton.vue";

defineProps({
  loading: {
    type: Boolean,
    default: false,
  },
});

const store = useApiNotificationStore();

const activity = computed(() => store.notifications.slice(0, 4));

function iconFor(item: Notification) {
  switch (item.data.type) {
    case 'trip_liked': {
      return HeartIcon;
    }
    case 'trip_cloned': {
      return DocumentDuplicateIcon;
    }
    case 'trip_status_changed': {
      return GlobeAltIcon;
    }
    default:
      return BellIcon;
  }
}

function iconVariant(item: Notification): string {
  switch (item.data.type) {
    case 'trip_liked': {
      return 'like';
    }
    case 'trip_cloned': {
      return 'clone';
    }
    case 'trip_status_changed': {
      return 'status';
    }
    default:
      return 'default';
  }
}
</script>

<style scoped lang="scss">
@use './../../../css/common/colours';
@use './../../../css/common/typography';

.recent-activity {
  display: flex;
  flex-direction: column;
  padding: 20px;
  background: colours.$colour-surface;
  border: 1px solid colours.$colour-border;
  border-radius: 16px;

  &__head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
  }

  &__title {
    font-size: typography.$font-size-lg;
    font-weight: 600;
    margin: 0;
  }

  &__all {
    font-size: typography.$font-size-sm;
    color: colours.$colour-accent;
    text-decoration: none;
    font-weight: 500;
  }

  &__list {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 14px;
  }

  &__empty {
    color: colours.$colour-text-muted;
    font-size: typography.$font-size-sm;
    margin: 0;
    padding: 12px 0;
  }
}

.activity-item {
  display: flex;
  gap: 12px;

  &__icon {
    flex-shrink: 0;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;

    &--like {
      background: #f43f5e;
    }

    &--clone {
      background: #8b5cf6;
    }

    &--status {
      background: #10b981;
    }

    &--default {
      background: #f59e0b;
    }
  }

  &__icon-svg {
    width: 16px;
    height: 16px;
  }

  &__body {
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 0;
  }

  &__text {
    font-size: typography.$font-size-sm;
    color: colours.$colour-text;
    margin: 0;
    line-height: 1.4;
  }

  &__time {
    font-size: typography.$font-size-xs;
    color: colours.$colour-text-muted;
  }
}
</style>
