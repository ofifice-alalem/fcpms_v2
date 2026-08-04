<template>
  <span :class="['status-pill', type]">
    <span v-if="pulse" class="w-1.5 h-1.5 rounded-full animate-pulse" :class="pulseBg"></span>
    <slot />
  </span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  type: {
    type: String,
    default: 'completed', // completed, pending, overdue
  },
  pulse: {
    type: Boolean,
    default: false,
  },
});

const pulseBg = computed(() => {
  if (props.type === 'completed') return 'bg-emerald-500';
  if (props.type === 'pending') return 'bg-amber-500';
  return 'bg-red-500';
});
</script>
