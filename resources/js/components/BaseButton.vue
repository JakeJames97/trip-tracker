<template>
  <button
    :type="type"
    :disabled="disabled"
    class="btn"
    :class="[`btn--${variant}`, { 'btn--full': fullWidth }]"
  >
    <slot />
  </button>
</template>

<script setup lang="ts">
import { type PropType } from 'vue';

defineProps({
  variant: {
    type: String as PropType<'primary' | 'outline' | 'danger'>,
    default: 'primary',
  },
  fullWidth: {
    type: Boolean,
    default: false,
  },
  type: {
    type: String as PropType<'button' | 'submit'>,
    default: 'button',
  },
  disabled: {
    type: Boolean,
    default: false,
  },
});
</script>
