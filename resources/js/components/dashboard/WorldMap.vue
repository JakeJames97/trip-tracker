<template>
  <div ref="mapContainer" class="world-map">
    <div class="world-map__heading">
      <h4 class="world-map__title">Your Travel Map</h4>
      <p class="world-map__subtitle">Countries you've visited</p>
    </div>
    <WorldMap />
  </div>
</template>

<script setup lang="ts">
import {nextTick, type PropType, useTemplateRef, watch} from 'vue'
import WorldMap from '@/assets/world.svg?component';

const props = defineProps({
  highlighted: {
    type: Array as PropType<String[]>,
    default: () => [],
  },
});

const mapContainerRef = useTemplateRef<SVGElement>('mapContainer');

async function updateMap() {
  await nextTick()
  const svg = mapContainerRef.value?.querySelector('svg') as SVGElement;
  if (svg && props.highlighted.length > 0) {
    props.highlighted.forEach((highlight) => {
      svg.querySelector(`#${highlight}`)?.style.setProperty('--country-fill', '#1f7a4d');
    });
  }
}

watch(
  () => props.highlighted,
  updateMap,
  { immediate: true }
)
</script>

<style scoped lang="scss">
@use './../../../css/common/colours';
@use './../../../css/common/typography';

.world-map {
  display: flex;
  flex-direction: column;
  padding: 20px;
  background: colours.$colour-surface;
  border: 1px solid colours.$colour-border;
  border-radius: 12px;
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

  :deep(path) {
    fill: var(--country-fill, #d1d5db);
  }
}
</style>
