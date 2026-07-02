<template>
  <WorldMapSkeleton v-if="loading" />
  <div v-else ref="mapContainer" class="world-map">
    <div class="world-map__heading">
      <h4 class="world-map__title">Your Travel Map</h4>
      <p class="world-map__subtitle">Countries you've visited</p>
    </div>
    <span class="world-map__legend">
      <span class="world-map__legend-dot" />
      Visited
    </span>
    <WorldMap />
  </div>
</template>

<script setup lang="ts">
import {nextTick, onMounted, type PropType, useTemplateRef} from 'vue'
import WorldMap from '@/assets/world.svg?component';
import WorldMapSkeleton from "@/components/placeholders/dashboard/WorldMapSkeleton.vue";

const props = defineProps({
  highlighted: {
    type: Array as PropType<string[]>,
    default: () => [],
  },
  loading: {
    type: Boolean,
    default: false,
  },
});

const mapContainerRef = useTemplateRef<SVGElement>('mapContainer');

onMounted(async () => {
  await nextTick();
  const svg = mapContainerRef.value?.querySelector('svg');
  if (!svg) return;
  props.highlighted.forEach((code) => {
    const el = svg.querySelector(`[id="${code}"]`) as SVGElement | null;
    el?.style.setProperty('--country-fill', '#1f7a4d');
  });
});
</script>

<style scoped lang="scss">
@use './../../../css/common/colours';
@use './../../../css/common/typography';
@use './../../../css/common/breakpoints';

.world-map {
  position: relative;
  display: flex;
  flex-direction: column;
  padding: 24px;
  background: colours.$colour-surface;
  border: 1px solid colours.$colour-border;
  border-radius: 16px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 1px 2px rgba(0, 0, 0, 0.06);
  align-items: center;
  gap: 12px;

  &__heading {
    align-self: flex-start;
    display: flex;
    flex-direction: column;
    gap: 16px;
  }

  &__title {
    font-size: typography.$font-size-xl;
    margin: 0;
  }

  &__subtitle {
    font-size: typography.$font-size-base;
    margin: 0;
  }

  &__legend {
    position: absolute;
    top: 20px;
    right: 20px;
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: typography.$font-size-sm;
    color: colours.$colour-text-muted;
  }

  &__legend-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: colours.$colour-accent;
  }

  :deep(svg) {
    height: auto;
    width: auto;
    max-width: 100%;
    display: block;
    margin: 0 auto;

    @include breakpoints.above(breakpoints.$tablet) {
      height: 620px;
    }
  }

  :deep(path) {
    fill: var(--country-fill, #d1d5db);
  }
}
</style>
