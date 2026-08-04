<template>
  <div class="progress-bar w-full" :class="height">
    <div
      class="fill"
      :class="colorClass"
      :style="{ width: `${percentage}%` }"
    ></div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  percentage: {
    type: Number,
    default: 0,
  },
  color: {
    type: String,
    default: 'primary', // primary, success, warning, danger
  },
  height: {
    type: String,
    default: 'h-2',
  },
});

const colorClass = computed(() => {
  if (props.color === 'success') return 'bg-emerald-500';
  if (props.color === 'warning') return 'bg-amber-500';
  if (props.color === 'danger') return 'bg-red-500';
  return 'bg-primary';
});
</script>
