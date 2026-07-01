<template>
  <section class="featured-destinations">
    <div class="featured-destinations__head">
      <h2 class="featured-destinations__title">Featured destinations</h2>
    </div>

    <div class="featured-destinations__grid">
      <RouterLink
        v-for="destination in featuredDestinations"
        :key="destination.code"
        :to="{ name: 'discover', query: { country: destination.code } }"
        class="destination-tile"
      >
        <span class="destination-tile__image" :style="{ backgroundImage: `url(${destination.image})` }"></span>
        <span class="destination-tile__cover"></span>
        <span class="destination-tile__name">
          <span :class="getFlagClass(destination.code)"/>
          {{ destination.name }}
        </span>
      </RouterLink>
    </div>
  </section>
</template>

<script setup lang="ts">
import japan from "@/images/destinations/jp.jpg";
import bosnia from "@/images/destinations/ba.jpg";
import croatia from "@/images/destinations/hr.jpg";
import unitedStates from "@/images/destinations/us.jpg";
import hungary from "@/images/destinations/hu.jpg";
import germany from "@/images/destinations/de.jpg";
import {getFlagClass} from "@/helpers.ts";

interface FeaturedDestination {
  code: string;
  name: string;
  image: string;
}

const featuredDestinations: FeaturedDestination[] = [
  {code: 'JP', name: 'Japan', image: japan},
  {code: 'BA', name: 'Bosnia & Herzegovina', image: bosnia},
  {code: 'HR', name: 'Croatia', image: croatia},
  {code: 'US', name: 'United States', image: unitedStates},
  {code: 'HU', name: 'Hungary', image: hungary},
  {code: 'DE', name: 'Germany', image: germany},
];

</script>
