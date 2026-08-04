<template>
  <div class="spatial-card" :class="paddingClass">
    <slot />
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  padding: {
    type: String,
    default: 'p-6',
  },
});

const paddingClass = computed(() => props.padding);
</script>
