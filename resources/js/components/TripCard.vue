<template>
  <router-link :to="{ name: 'trip', params: { id: trip.id } }" class="trip-card">
    <div class="trip-card__body">
      <div class="trip-card__top">
        <h3 class="trip-card__name">{{ trip.name }}</h3>
        <StatusPill :status="trip.status" />
      </div>

      <p class="trip-card__dates">{{ formatDateRange(trip.start_date, trip.end_date) }}</p>

      <div class="trip-card__meta">
        <p>
          {{ trip.destinations_count }}
          {{ trip.destinations_count === 1 ? 'destination' : 'destinations' }}
        </p>
        <p v-if="trip.user" class="trip-card__owner">
          by {{ trip.is_owner ? 'you' : trip.user.username }}
        </p>
      </div>
    </div>
  </router-link>
</template>

<script setup lang="ts">
import { formatDateRange } from '@/lib/date';
import type { Trip } from '@/types/trips.ts';
import type {PropType} from "vue";
import StatusPill from "@/components/StatusPill.vue";

defineProps({
  trip: {
    type: Object as PropType<Trip>,
    required: true,
  },
});

</script>
