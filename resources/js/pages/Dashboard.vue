<template>
  <div class="dashboard">
    <header class="dashboard__welcome">
      <div>
        <h1 class="dashboard__greeting">Welcome back, {{ username }}! 👋</h1>
        <p class="dashboard__subline">Here's what's happening with your travels.</p>
      </div>
      <BaseButton @click="createOpen = true">Create trip</BaseButton>
    </header>

    <StatisticsSection :stats="stats"/>
    <div class="dashboard__main">
      <WorldMap :highlighted="stats.countries" :key="stats.countries.length" />
      <NextTripCard :trip="nextTrip" class="dashboard__next-trip" :key="nextTrip?.id" />
    </div>
  </div>
  <TripForm v-model:open="createOpen" @saved="onCreated"/>
</template>

<script setup lang="ts">
import {ref, computed, onMounted} from 'vue';
import {useAuthStore} from '@/stores/useAuthStore';
import * as dashboardApi from '@/api/dashboard';
import type {DashboardStats} from '@/types/dashboard';
import type {Trip} from '@/types/trips';
import StatisticsSection from "@/components/dashboard/StatisticsSection.vue";
import WorldMap from "@/components/dashboard/WorldMap.vue";
import NextTripCard from "@/components/dashboard/NextTripCard.vue";
import BaseButton from "@/components/BaseButton.vue";
import TripForm from "@/components/modals/TripForm.vue";
import {useNotificationStore} from "@/stores/useNotificationStore.ts";

const auth = useAuthStore();
const notify = useNotificationStore();
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
  const data = await dashboardApi.getDashboardData();
  stats.value = data.data.stats;
  nextTrip.value = data.data.next_trip;
}

onMounted(load);
</script>
