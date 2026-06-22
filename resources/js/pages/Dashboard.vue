<template>
  <div class="dashboard">
    <div class="dashboard__controls">
      <TripFilters
        v-if="!loading && !error && tripsStore.trips.length"
      />
      <BaseButton @click="createOpen = true">Create trip</BaseButton>
    </div>

    <p v-if="loading">Loading…</p>
    <p v-else-if="error" class="dashboard__error">{{ error }}</p>
    <p v-else-if="tripsStore.trips.length === 0" class="dashboard__empty">
      No trips yet. Create your first one to get started.
    </p>
    <p v-else-if="tripsStore.trips.length === 0" class="dashboard__empty">
      No matching trips.
    </p>

    <div v-else class="dashboard__grid">
      <TripCard v-for="trip in tripsStore.trips" :key="trip.id" :trip="trip"/>
    </div>
  </div>

  <Pagination
    v-if="tripsStore.paginationMeta"
    :last-page="tripsStore.paginationMeta.last_page"
    :current-page="tripsStore.paginationMeta.current_page"
  />

  <TripForm v-model:open="createOpen" @saved="onCreated"/>
</template>

<script setup lang="ts">
import {onMounted, ref, watch} from 'vue';
import {useTripsStore} from '@/stores/useTripsStore.ts';
import TripCard from '@/components/TripCard.vue';
import TripFilters from '@/components/TripFilters.vue';
import * as tripsApi from '@/api/trips';
import type {Trip} from '@/types/trips.ts';
import {useApiRequest} from "@/composables/useApiRequest.ts";
import BaseButton from "@/components/BaseButton.vue";
import TripForm from "@/components/modals/TripForm.vue";
import {useNotificationStore} from '@/stores/useNotificationStore.ts';
import Pagination from "@/components/Pagination.vue";
import {useRoute} from "vue-router";

const route = useRoute();
const notify = useNotificationStore();
const tripsStore = useTripsStore();
const {loading, error, execute} = useApiRequest();

const createOpen = ref(false);
async function loadTrips(page: number, status?: string) {
  const result = await execute(() => tripsApi.getTrips(page, status));
  if (result) {
    tripsStore.setTrips(result.data);
    tripsStore.setPaginationMeta(result.meta);
  }
}

function onCreated(trip: Trip) {
  tripsStore.addTrip(trip);
  notify.success('Trip has been successfully created!');
}

onMounted(() => {
  const page = Number(route.query.page) || 1;
  const status = (route.query.status as string) || undefined;
  loadTrips(page, status);
});

watch(
  () => route.query,
  () => {
    const page = Number(route.query.page) || 1;
    const status = (route.query.status as string) || undefined;
    loadTrips(page, status);
  },
);
</script>
