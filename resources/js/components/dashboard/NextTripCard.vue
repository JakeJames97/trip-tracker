<template>
  <NextTripSkeleton v-if="loading" />
  <article v-else class="next-trip-card">
    <div class="next-trip-card__head">
      <h4 class="next-trip-card__title">Next Trip</h4>
      <PaperAirplaneIcon class="next-trip-card__icon" />
    </div>

    <template v-if="trip">
      <div class="next-trip-card__image" :style="imageStyle" />

      <div class="next-trip-card__info">
        <h3 class="next-trip-card__name">{{ trip.name }}</h3>
        <span class="next-trip-card__countdown">{{ countdown }}</span>
      </div>

      <div class="next-trip-card__meta">
        <div class="next-trip-card__meta-item">
          <MapPinIcon class="next-trip-card__meta-icon" />
          <span class="next-trip-card__meta-value">{{ trip.destinations?.length }}</span>
          <span class="next-trip-card__meta-label">Destinations</span>
        </div>
        <div class="next-trip-card__meta-item">
          <CurrencyPoundIcon class="next-trip-card__meta-icon" />
          <span class="next-trip-card__meta-value">{{ trip.budget_formatted }}</span>
          <span class="next-trip-card__meta-label">Budget</span>
        </div>
        <div class="next-trip-card__meta-item">
          <CalendarIcon class="next-trip-card__meta-icon" />
          <span class="next-trip-card__meta-value">{{ formatDeparture(trip.start_date) }}</span>
          <span class="next-trip-card__meta-label">Departure</span>
        </div>
      </div>

      <BaseButton
        variant="primary"
        @click="goToTrip"
      >
        Continue Planning
      </BaseButton>
    </template>

    <div v-else class="next-trip-card__empty">
      <p class="next-trip-card__empty-text">No upcoming trips planned.</p>
      <BaseButton
        variant="primary"
        @click="goToTrips"
      >
        Plan a trip
      </BaseButton>
    </div>
  </article>
</template>

<script setup lang="ts">
import { computed, type PropType } from 'vue';
import { dayjs } from '@/lib/date.ts';
import {
  PaperAirplaneIcon,
  MapPinIcon,
  CurrencyPoundIcon,
  CalendarIcon,
} from '@heroicons/vue/24/outline';
import type { Trip } from '@/types/trips';
import BaseButton from "@/components/BaseButton.vue";
import router from "@/router";
import {imageForCountry} from "@/helpers";
import StatisticsSectionSkeleton from "@/components/placeholders/dashboard/StatisticsSectionSkeleton.vue";
import NextTripSkeleton from "@/components/placeholders/dashboard/NextTripSkeleton.vue";

const props = defineProps({
  trip: {
    type: Object as PropType<Trip | null>,
    default: null,
  },
  loading: {
    type: Boolean,
    default: false,
  },
});

const primaryDestinationCode = props.trip?.destinations?.[0]?.country_code;

const countdown = computed(() => {
  if (!props.trip) return '';
  const days = dayjs(props.trip.start_date).diff(dayjs(), 'day');
  if (days <= 0) {
    return 'Departing today';
  }
  if (days === 1) {
    return 'Tomorrow';
  }
  return `${days} days to go`;
});

const imageStyle = computed(() => {
  const image = imageForCountry(primaryDestinationCode);

  if (image) {
    return { backgroundImage: `url(${image})` };
  }

  return { background: 'linear-gradient(135deg, #1a4a30, #2d8a5f)' };
});

function goToTrip() {
  router.push({ name: 'trip', params: { id: props.trip?.id } });
}

function goToTrips() {
  router.push({ name: 'trips' });
}

function formatDeparture(date: string): string {
  return dayjs(date).format('D MMM');
}
</script>

<style scoped lang="scss">
@use './../../../css/common/colours';
@use './../../../css/common/typography';

.next-trip-card {
  display: flex;
  flex-direction: column;
  padding: 20px;
  background: colours.$colour-surface;
  border: 1px solid colours.$colour-border;
  border-radius: 16px;

  &__head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
  }

  &__title {
    font-size: typography.$font-size-lg;
    font-weight: 600;
    margin: 0;
  }

  &__icon {
    width: 20px;
    height: 20px;
    color: colours.$colour-accent;
  }

  &__image {
    height: 140px;
    border-radius: 12px;
    background-size: cover;
    background-position: center;
    margin-bottom: 14px;
  }

  &__info {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 18px;
  }

  &__name {
    font-size: typography.$font-size-lg;
    font-weight: 600;
    margin: 0;
  }

  &__countdown {
    font-size: typography.$font-size-sm;
    color: colours.$colour-accent;
    font-weight: 500;
  }

  &__meta {
    display: flex;
    justify-content: space-between;
    padding: 14px 0;
    border-top: 1px solid colours.$colour-border;
    border-bottom: 1px solid colours.$colour-border;
    margin-bottom: 18px;
  }

  &__meta-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    text-align: center;
  }

  &__meta-icon {
    width: 18px;
    height: 18px;
    color: colours.$colour-text-muted;
  }

  &__meta-value {
    font-size: typography.$font-size-base;
    font-weight: 600;
  }

  &__meta-label {
    font-size: typography.$font-size-xs;
    color: colours.$colour-text-muted;
  }

  &__cta {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px;
    background: colours.$colour-accent;
    color: #fff;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 500;
    font-size: typography.$font-size-base;
    transition: background 0.15s ease;
  }

  &__cta-icon {
    width: 16px;
    height: 16px;
  }

  &__empty {
    display: flex;
    flex-direction: column;
    gap: 14px;
    padding: 20px 0;
    text-align: center;
  }

  &__empty-text {
    margin: 0;
    color: colours.$colour-text-muted;
  }
}
</style>
