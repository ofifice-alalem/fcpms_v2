<template>
  <div class="skeleton" :class="[height, width, rounded]"></div>
</template>

<script setup>
defineProps({
  height: {
    type: String,
    default: 'h-12',
  },
  width: {
    type: String,
    default: 'w-full',
  },
  rounded: {
    type: String,
    default: 'rounded-lg',
  },
});
</script>
