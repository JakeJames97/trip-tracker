<template>
  <div class="trip-filters">
    <button
      v-for="filter in filters"
      :key="filter.value"
      type="button"
      class="trip-filters__filter"
      :class="{ 'trip-filters__filter--active': currentFilter === filter.value }"
      @click="setFilter(filter.value)"
    >
      {{ filter.label }}
    </button>
  </div>
</template>

<script setup lang="ts">
import type { TripStatus } from '@/types/trips.ts';
import {ref} from "vue";
import {useRoute, useRouter} from "vue-router";

type Filter = TripStatus | 'all';

const router = useRouter();
const route = useRoute();

const currentFilter = ref(<Filter>route.query.status || 'all');

function setFilter(status: Filter) {
  currentFilter.value = status;
  if (status === 'all') {
    router.push({ query: { page: '1' } });
    return;
  }
  router.push({ query: { ...route.query, status, page: '1' } });
}

const filters: { value: Filter; label: string }[] = [
  { value: 'all', label: 'All' },
  { value: 'planned', label: 'Planned' },
  { value: 'in_progress', label: 'In progress' },
  { value: 'completed', label: 'Completed' },
];
</script>
