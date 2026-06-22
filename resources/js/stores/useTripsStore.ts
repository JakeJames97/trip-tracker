import { ref } from 'vue';
import { defineStore } from 'pinia';
import type { Trip } from '@/types/trips.ts';
import type {PaginationMeta} from "@/types/pagination.ts";

export const useTripsStore = defineStore('trips', () => {
  const trips = ref<Trip[]>([]);
  const paginationMeta = ref<PaginationMeta | null>(null);

  function setTrips(value: Trip[]) {
    trips.value = value;
  }

  function setPaginationMeta(value: PaginationMeta) {
    paginationMeta.value = value;
  }

  function addTrip(value: Trip) {
    trips.value = [value, ...trips.value];
  }

  return { trips, paginationMeta, setTrips, addTrip, setPaginationMeta };
});
