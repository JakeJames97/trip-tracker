<template>
  <div class="dashboard">
    <header class="dashboard__welcome">
      <h1 class="dashboard__greeting">Welcome back, {{ username }}! 👋</h1>
      <p class="dashboard__subline">Here's what's happening with your travels.</p>
    </header>

    <StatisticsSection :stats="stats"/>
    <WorldMap :highlighted="stats.countries"/>
  </div>
</template>

<script setup lang="ts">
import {ref, computed, onMounted} from 'vue';
import {useAuthStore} from '@/stores/useAuthStore';
import * as dashboardApi from '@/api/dashboard';
import type {DashboardStats} from '@/types/dashboard';
import type {Trip} from '@/types/trips';
import StatisticsSection from "@/components/dashboard/StatisticsSection.vue";
import WorldMap from "@/components/dashboard/WorldMap.vue";

const auth = useAuthStore();
const username = computed(() => auth.user?.username ?? '');

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
