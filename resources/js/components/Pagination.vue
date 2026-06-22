<template>
  <nav v-if="lastPage > 1" class="pagination" aria-label="Pagination">
    <BaseButton
      class="pagination__button"
      variant="ghost"
      :disabled="currentPage === 1"
      @click="goToPage(currentPage - 1)"
    >
      Previous
    </BaseButton>

    <template v-for="(page, index) in pages" :key="index">
      <span v-if="page === '...'" class="pagination__ellipsis">…</span>
      <BaseButton
        v-else
        class="pagination__button"
        :class="{ 'pagination__button--active': page === currentPage }"
        :disabled="page === currentPage"
        variant="ghost"
        @click="goToPage(page)"
      >
        {{ page }}
      </BaseButton>
    </template>

    <BaseButton
      class="pagination__button"
      variant="ghost"
      type="button"
      :disabled="currentPage === lastPage"
      @click="goToPage(currentPage + 1)"
    >
      Next
    </BaseButton>
  </nav>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import {useRouter, useRoute} from "vue-router";
import BaseButton from "@/components/BaseButton.vue";

const props = defineProps({
  currentPage: { type: Number, required: true },
  lastPage: { type: Number, required: true },
  spread: { type: Number, default: 5 },
});

const router = useRouter();
const route = useRoute();

const pages = computed<(number | '...')[]>(() => {
  const { currentPage, lastPage, spread } = props;

  if (lastPage <= spread) {
    return [...Array(lastPage).keys()].map((i) => i + 1);
  }

  const side = Math.floor(spread / 2);
  const start = Math.max(currentPage - side, 1);
  const end = Math.min(currentPage + side, lastPage);

  const pages: (number | '...')[] = [];

  if (start > 1) {
    pages.push(1);
    if (start > 2) {
      pages.push('...');
    }
  }

  for (let p = start; p <= end; p++) {
    pages.push(p);
  }

  if (end < lastPage) {
    if (end < lastPage - 1) {
      pages.push('...');
    }
    pages.push(lastPage);
  }

  return pages;
});

function goToPage(page: number) {
  router.push({ query: { ...route.query, page: String(page) } });
}
</script>
