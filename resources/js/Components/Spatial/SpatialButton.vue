<template>
  <button
    :type="type"
    :disabled="disabled || loading"
    :class="[
      'transition-all duration-200 flex items-center justify-center gap-2.5 font-black text-xs cursor-pointer shrink-0 border select-none',
      sizeClasses,
      variantClass,
      customClass
    ]"
    @click="$emit('click', $event)"
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
    default: 'primary', // primary, secondary, danger, success, warning, info, purple
  },
  size: {
    type: String,
    default: 'md', // sm, md, lg
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

defineEmits(['click']);

const sizeClasses = computed(() => {
  switch (props.size) {
    case 'sm': return 'px-3 py-1.5 rounded-xl text-xs';
    case 'lg': return 'px-6 py-3.5 rounded-2xl text-sm';
    case 'md':
    default: return 'px-5 py-2.5 rounded-2xl text-xs';
  }
});

const variantClass = computed(() => {
  switch (props.variant) {
    case 'success':
      return 'bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-600 text-white shadow-xl shadow-emerald-500/25 hover:shadow-emerald-500/40 hover:scale-[1.02] active:scale-[0.98] border-white/20';
    case 'danger':
      return 'bg-gradient-to-r from-red-600 to-red-500 hover:from-red-500 hover:to-red-600 text-white shadow-xl shadow-red-500/25 hover:shadow-red-500/40 hover:scale-[1.02] active:scale-[0.98] border-white/20';
    case 'warning':
      return 'bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-white shadow-xl shadow-amber-500/25 hover:shadow-amber-500/40 hover:scale-[1.02] active:scale-[0.98] border-white/20';
    case 'info':
      return 'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white shadow-xl shadow-indigo-500/25 hover:shadow-indigo-500/40 hover:scale-[1.02] active:scale-[0.98] border-white/20';
    case 'purple':
      return 'bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white shadow-xl shadow-purple-500/25 hover:shadow-purple-500/40 hover:scale-[1.02] active:scale-[0.98] border-white/20';
    case 'ghost':
      return 'bg-transparent hover:bg-slate-100 dark:hover:bg-white/10 text-slate-700 dark:text-white border-transparent shadow-none hover:scale-[1.01] active:scale-[0.99]';
    case 'danger-ghost':
    case 'danger-subtle':
      return 'bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 border-rose-500/20 shadow-none hover:scale-[1.01] active:scale-[0.99]';
    case 'secondary':
      return 'bg-black/5 dark:bg-white/10 hover:bg-black/10 dark:hover:bg-white/20 text-slate-800 dark:text-white border-black/10 dark:border-white/15 shadow-none hover:scale-[1.01] active:scale-[0.99]';
    case 'primary':
    default:
      return 'bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-500 hover:from-blue-500 hover:to-indigo-600 text-white shadow-xl shadow-blue-500/25 hover:shadow-blue-500/40 hover:scale-[1.02] active:scale-[0.98] border-white/20';
  }
});
</script>
