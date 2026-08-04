<template>
  <button
    :type="type"
    :disabled="disabled || loading"
    :class="[
      variant === 'icon' ? 'spatial-icon-btn' : 'spatial-button',
      variantClass,
      customClass
    ]"
  >
    <span v-if="loading" class="animate-spin w-4 h-4 border-2 border-white border-t-transparent rounded-full"></span>
    <slot />
  </button>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  type: {
    type: String,
    default: 'button',
  },
  variant: {
    type: String,
    default: 'primary', // primary, secondary, danger, success, warning, info, purple, icon
  },
  loading: {
    type: Boolean,
    default: false,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  customClass: {
    type: String,
    default: '',
  },
});

const variantClass = computed(() => {
  switch (props.variant) {
    case 'success':
      return '!bg-gradient-to-r !from-emerald-600 !to-emerald-500 hover:!from-emerald-500 hover:!to-emerald-600 !text-white !shadow-lg !shadow-emerald-500/30';
    case 'danger':
      return '!bg-gradient-to-r !from-red-600 !to-red-500 hover:!from-red-500 hover:!to-red-600 !text-white !shadow-lg !shadow-red-500/30';
    case 'warning':
      return '!bg-gradient-to-r !from-amber-500 !to-amber-600 hover:!from-amber-400 hover:!to-amber-500 !text-white !shadow-lg !shadow-amber-500/30';
    case 'info':
      return '!bg-gradient-to-r !from-blue-600 !to-indigo-600 hover:!from-blue-500 hover:!to-indigo-500 !text-white !shadow-lg !shadow-indigo-500/30';
    case 'purple':
      return '!bg-gradient-to-r !from-purple-600 !to-indigo-600 hover:!from-purple-500 hover:!to-indigo-500 !text-white !shadow-lg !shadow-purple-500/30';
    case 'secondary':
      return '!bg-black/10 dark:!bg-white/10 hover:!bg-black/20 dark:hover:!bg-white/20 !text-slate-800 dark:!text-white border border-black/10 dark:border-white/15 !shadow-none';
    case 'primary':
    default:
      return '!bg-gradient-to-r !from-blue-600 !to-blue-500 hover:!from-blue-500 hover:!to-blue-600 !text-white !shadow-lg !shadow-blue-500/30';
  }
});
</script>
