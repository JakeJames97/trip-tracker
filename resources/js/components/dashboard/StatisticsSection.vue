<template>
  <StatisticsSectionSkeleton v-if="loading" />
  <section v-else class="statistics-section">
    <article v-for="card in cards" :key="card.label" class="stat-card">
      <span class="stat-card__icon" :class="`stat-card__icon--${card.variant}`">
        <component :is="card.icon" class="stat-card__icon-svg"/>
      </span>
      <div class="stat-card__body">
        <span class="stat-card__label">{{ card.label }}</span>
        <span class="stat-card__value">{{ card.value }}</span>
        <span class="stat-card__sub">{{ card.sub }}</span>
      </div>
    </article>
  </section>
</template>

<script setup lang="ts">
import {computed, type PropType} from 'vue';
import {
  BriefcaseIcon,
  GlobeAltIcon,
  HeartIcon,
  MapPinIcon,
  CheckCircleIcon,
} from '@heroicons/vue/24/outline';
import type {DashboardStats} from '@/types/dashboard';
import StatisticsSectionSkeleton from "@/components/placeholders/dashboard/StatisticsSectionSkeleton.vue";

const props = defineProps({
  stats: {
    type: Object as PropType<DashboardStats>,
    required: true,
  },
  loading: {
    type: Boolean,
    default: false,
  },
});

const worldPercent = computed(() => Math.round((props.stats.countries.length / 195) * 100));

const cards = computed(() => [
  {
    variant: 'trips',
    icon: BriefcaseIcon,
    label: 'Total Trips',
    value: props.stats.total_trips,
    sub: 'Trips created',
  },
  {
    variant: 'countries',
    icon: GlobeAltIcon,
    label: 'Countries Visited',
    value: props.stats.countries.length,
    sub: `${worldPercent.value}% of the world`,
  },
  {
    variant: 'likes',
    icon: HeartIcon,
    label: 'Likes Received',
    value: props.stats.likes_received,
    sub: 'From other travellers',
  },
  {
    variant: 'destinations',
    icon: MapPinIcon,
    label: 'Destinations',
    value: props.stats.total_destinations_planned,
    sub: 'Places planned',
  },
  {
    variant: 'tasks',
    icon: CheckCircleIcon,
    label: 'Tasks to do',
    value: props.stats.tasks_to_do,
    sub: 'Across your trips',
  },
]);
</script>
