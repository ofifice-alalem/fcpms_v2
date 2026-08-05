<template>
  <button
    type="button"
    :title="title"
    :disabled="disabled"
    :class="[
      'spatial-icon-button transition-all duration-250 cursor-pointer flex items-center justify-center rounded-[16px] border border-black/10 dark:border-white/12 shadow-sm hover:scale-105 active:scale-95 disabled:opacity-50 disabled:pointer-events-none',
      sizeClasses,
      variantClasses
    ]"
    @click="$emit('click', $event)"
  >
    <slot />
  </button>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  variant: {
    type: String,
    default: 'default', // default, primary, info, warning, danger
  },
  size: {
    type: String,
    default: 'md', // sm (36px), md (42px), lg (48px)
  },
  title: String,
  disabled: Boolean,
});

defineEmits(['click']);

const sizeClasses = computed(() => {
  switch (props.size) {
    case 'sm': return 'w-8 h-8 text-xs';
    case 'lg': return 'w-12 h-12 text-base';
    default: return 'w-10 h-10 text-sm';
  }
});

const variantClasses = computed(() => {
  switch (props.variant) {
    case 'primary':
      return 'bg-blue-500/10 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 hover:bg-blue-600 hover:text-white dark:hover:bg-blue-500 hover:shadow-blue-500/30 hover:border-blue-500/40';
    case 'info':
      return 'bg-indigo-500/10 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-600 hover:text-white dark:hover:bg-indigo-500 hover:shadow-indigo-500/30 hover:border-indigo-500/40';
    case 'success':
      return 'bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-600 hover:text-white dark:hover:bg-emerald-500 hover:shadow-emerald-500/30 hover:border-emerald-500/40';
    case 'warning':
      return 'bg-amber-500/10 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 hover:bg-amber-500 hover:text-white dark:hover:bg-amber-500 hover:shadow-amber-500/30 hover:border-amber-500/40';
    case 'danger':
      return 'bg-red-500/10 dark:bg-red-500/20 text-red-600 dark:text-red-400 hover:bg-red-600 hover:text-white dark:hover:bg-red-500 hover:shadow-red-500/30 hover:border-red-500/40';
    default:
      return 'bg-slate-100 dark:bg-white/10 text-slate-700 dark:text-white hover:bg-slate-200 dark:hover:bg-white/20 hover:border-black/20 dark:hover:border-white/30';
  }
});
</script>
