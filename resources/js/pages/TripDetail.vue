<template>
  <div class="trip-detail">
    <div class="trip-detail__topbar">
      <button type="button" class="trip-detail__back" @click="router.push({ name: 'discover' })">
        <ChevronLeftIcon class="trip-detail__back-icon" />
        Back to trips
      </button>
      <LikeButton
        v-if="trip && auth.isAuthenticated"
        :trip-id="trip.id"
        :is-liked="trip.is_liked"
        :likes-count="trip.likes_count"
      />
    </div>

    <TripDetailSkeleton v-if="loading"/>

    <p v-else-if="error" class="empty-state">
      {{ error }}
    </p>

    <div v-else-if="trip">
      <header class="trip-detail__header">
        <div>
          <div class="trip-detail__title">
            <div class="trip-detail__title-group">
              <h1 class="trip-detail__name">{{ trip.name }}</h1>
              <StatusPill :status="trip.status"/>
              <span class="trip-detail__visibility">{{ trip.is_public ? 'Public' : 'Private' }}</span>
            </div>
            <div v-if="auth.isAuthenticated" class="trip-detail__actions">
              <template v-if="trip.is_owner">
                <BaseButton variant="primary" @click="editOpen = true">Edit</BaseButton>
                <BaseButton variant="danger" @click="confirmOpen = true">Delete</BaseButton>
              </template>
              <CloneButton :tripId="trip.id" v-else/>
            </div>
          </div>
          <p class="trip-detail__dates">{{ formatDateRange(trip.start_date, trip.end_date) }}</p>
        </div>
      </header>

      <p v-if="trip.description" class="trip-detail__description">{{ trip.description }}</p>

      <DestinationList
        :trip-id="trip.id"
        :destinations="trip.destinations ?? []"
        :editable="trip.is_owner ?? false"
      />

      <ConfirmDialog
        v-model:open="confirmOpen"
        title="Delete trip?"
        message="Deleting this trip will also remove its destinations and tasks. This can’t be undone."
        confirm-label="Delete trip"
        :loading="deleting"
        @confirm="handleDelete"
      />

      <TripForm v-model:open="editOpen" :trip="trip" @saved="onSaved" :key="trip.id"/>
    </div>
  </div>
</template>

<script setup lang="ts">
import {onMounted, ref} from 'vue';
import {useRoute, useRouter} from 'vue-router';
import {storeToRefs} from 'pinia';
import {useTripStore} from '@/stores/useTripStore.ts';
import {useApiRequest} from '@/composables/useApiRequest';
import * as tripsApi from '@/api/trips';
import {formatDateRange} from '@/lib/date';
import { ChevronLeftIcon } from '@heroicons/vue/24/solid'
import StatusPill from '@/components/StatusPill.vue';
import BaseButton from '@/components/BaseButton.vue';
import ConfirmDialog from '@/components/modals/ConfirmDialog.vue';
import TripForm from '@/components/modals/TripForm.vue';
import DestinationList from '@/components/DestinationList.vue';
import type {Trip} from '@/types/trips.ts';
import {useNotificationStore} from '@/stores/useNotificationStore.ts';
import TripDetailSkeleton from "@/components/placeholders/TripDetailSkeleton.vue";
import LikeButton from "@/components/LikeButton.vue";
import CloneButton from "@/components/CloneButton.vue";

const notify = useNotificationStore();
const route = useRoute();
const router = useRouter();
const tripStore = useTripStore();
import { useAuthStore } from '@/stores/useAuthStore.ts';

const {trip} = storeToRefs(tripStore);
const auth = useAuthStore();

const {loading, error, execute: executeLoad} = useApiRequest();
const {loading: deleting, execute: executeDelete} = useApiRequest();

const confirmOpen = ref(false);
const editOpen = ref(false);

const id = route.params.id as string;

async function loadTrip() {
  tripStore.setTrip(null);
  const result = await executeLoad(() => tripsApi.getTrip(id));
  if (result) {
    tripStore.setTrip(result);
  } else {
    error.value = 'This trip doesn’t exist or has been deleted.';
    notify.error(error.value);
    router.push({name: 'dashboard'});
  }
}

async function handleDelete() {
  if (!trip.value) return;
  const result = await executeDelete(() => tripsApi.deleteTrip(trip.value!.id));
  if (result !== undefined) {
    notify.success('Trip has been successfully deleted!');
    router.push({name: 'dashboard'});
  } else {
    confirmOpen.value = false;
  }
}

function onSaved(updated: Trip) {
  tripStore.setTrip(updated);
}

onMounted(loadTrip);
</script>
