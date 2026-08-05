<template>
  <div class="spatial-card" :class="[paddingClass, $attrs.class]">
    <slot />
  </div>
</template>

<script setup>
import { computed } from 'vue';

defineOptions({
  inheritAttrs: false,
});

const props = defineProps({
  padding: {
    type: String,
    default: 'p-6',
  },
});

const paddingClass = computed(() => props.padding);
</script>
