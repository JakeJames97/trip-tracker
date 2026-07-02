<template>
  <div class="dashboard">
    <header class="dashboard__welcome">
      <div>
        <h1 class="dashboard__greeting">Welcome back, {{ username }}! 👋</h1>
        <p class="dashboard__subline">Here's what's happening with your travels.</p>
      </div>
      <BaseButton @click="createOpen = true">Create trip</BaseButton>
    </header>

    <StatisticsSection :stats="stats" :loading="loading"/>
    <div class="dashboard__main">
      <WorldMap :highlighted="stats.countries" :loading="loading" :key="stats.countries.length"/>
      <div class="dashboard__main--right">
        <NextTripCard :trip="nextTrip" :loading="loading" class="dashboard__next-trip" :key="nextTrip?.id"/>
        <RecentActivity :loading="loading"/>
      </div>
    </div>
  </div>
  <TripForm v-model:open="createOpen" @saved="onCreated"/>
</template>

<script setup lang="ts">
import {ref, computed, onMounted} from 'vue';
import {useAuthStore} from '@/stores/useAuthStore';
import * as dashboardApi from '@/api/dashboard';
import {useApiRequest} from '@/composables/useApiRequest';
import type {DashboardStats} from '@/types/dashboard';
import type {Trip} from '@/types/trips';
import StatisticsSection from "@/components/dashboard/StatisticsSection.vue";
import WorldMap from "@/components/dashboard/WorldMap.vue";
import NextTripCard from "@/components/dashboard/NextTripCard.vue";
import BaseButton from "@/components/BaseButton.vue";
import TripForm from "@/components/modals/TripForm.vue";
import {useNotificationStore} from "@/stores/useNotificationStore.ts";
import RecentActivity from "@/components/dashboard/RecentActivity.vue";

const auth = useAuthStore();
const notify = useNotificationStore();
const {loading, execute} = useApiRequest();
const username = computed(() => auth.user?.username ?? '');


const createOpen = ref(false);

function onCreated() {
  notify.success('Trip has been successfully created!');
}

const stats = ref<DashboardStats>({
  total_trips: 0,
  countries: [],
  likes_received: 0,
  total_destinations_planned: 0,
  tasks_to_do: 0
});

const nextTrip = ref<Trip | null>(null);

async function load() {
  const result = await execute(() => dashboardApi.getDashboardData());
  if (result) {
    stats.value = result.data.stats;
    nextTrip.value = result.data.next_trip;
  } else {
    notify.error('An error has occurred attempting to loading the dashboard!');
  }
}

onMounted(load);
</script>
