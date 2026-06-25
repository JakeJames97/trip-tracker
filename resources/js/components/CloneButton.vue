<template>
  <BaseButton variant="primary" :disabled="cloning" @click="handleClone">
    {{ cloning ? 'Cloning…' : 'Clone trip' }}
  </BaseButton>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router';
import { useApiRequest } from '@/composables/useApiRequest';
import { useNotificationStore } from '@/stores/useNotificationStore';
import * as tripsApi from '@/api/trips';
import BaseButton from '@/components/BaseButton.vue';

const props = defineProps({
  tripId: {
    type: String,
    required: true
  },
});

const router = useRouter();
const notify = useNotificationStore();
const { loading: cloning, execute } = useApiRequest();

async function handleClone() {
  const result = await execute(() => tripsApi.cloneTrip(props.tripId));
  if (result) {
    notify.success('Trip cloned to your trips!');
    router.push({ name: 'trip', params: { id: result.id } });
  }
}
</script>
