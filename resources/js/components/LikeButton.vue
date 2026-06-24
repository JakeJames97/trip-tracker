<template>
  <button
    type="button"
    class="like-button"
    :class="{ 'like-button--liked': liked }"
    @click.prevent="handleToggle"
  >
    <HeartIcon class="like-button__icon"/>
    <span class="like-button__count">{{ count }}</span>
  </button>
</template>

<script setup lang="ts">
import {ref} from 'vue';
import HeartIcon from '@/icons/heart.svg?component';
import {useApiRequest} from "@/composables/useApiRequest.ts";
import * as tripsApi from "@/api/trips.ts";
import {useNotificationStore} from "@/stores/useNotificationStore.ts";

const props = defineProps({
  tripId: {
    type: String,
    required: true
  },
  isLiked: {
    type: Boolean,
    default: false
  },
  likesCount: {
    type: Number,
    default: 0
  },
});

const notify = useNotificationStore();
const {loading, error, execute} = useApiRequest();

const liked = ref(props.isLiked);
const count = ref(props.likesCount);

async function handleToggle() {
  if (loading.value) {
    return;
  }

  const result = await execute(() => tripsApi.toggleLike(props.tripId));

  if (!result || error.value) {
    notify.error('An error occurred when trying to like this trip.');
    return;
  }

  liked.value = result.data.is_liked;
  count.value = result.data.likes_count;
}
</script>
