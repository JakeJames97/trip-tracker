<template>
  <div class="discover">
    <div class="discover__controls" v-if="!error">
      <TripFilters />
    </div>

    <p v-if="error" class="discover__error">{{ error }}</p>
    <p v-else-if="!loading && tripsStore.trips.length === 0" class="discover__empty">
      No public trips to explore yet. Check back soon.
    </p>

    <div v-else class="discover__grid">
      <TripCardSkeleton v-if="loading" v-for="index in 10" :key="index" />
      <TripCard v-else v-for="trip in tripsStore.trips" :key="trip.id" :trip="trip"/>
    </div>
  </div>

  <Pagination
    v-if="tripsStore.paginationMeta"
    :last-page="tripsStore.paginationMeta.last_page"
    :current-page="tripsStore.paginationMeta.current_page"
  />
</template>

<script setup lang="ts">
import {onMounted, watch} from 'vue';
import {useTripsStore} from '@/stores/useTripsStore.ts';
import TripCard from '@/components/TripCard.vue';
import TripFilters from '@/components/TripFilters.vue';
import * as tripsApi from '@/api/trips';
import {useApiRequest} from "@/composables/useApiRequest.ts";
import Pagination from "@/components/Pagination.vue";
import {useRoute} from "vue-router";
import TripCardSkeleton from "@/components/placeholders/TripCardSkeleton.vue";

const route = useRoute();
const tripsStore = useTripsStore();
const {loading, error, execute} = useApiRequest();

async function loadTrips(page: number, status?: string) {
  tripsStore.setTrips([]);
  const result = await execute(() => tripsApi.discoverTrips(page, status));
  if (result) {
    tripsStore.setTrips(result.data);
    tripsStore.setPaginationMeta(result.meta);
  }
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
