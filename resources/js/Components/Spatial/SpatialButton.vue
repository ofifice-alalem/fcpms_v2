<template>
  <button
    :type="type"
    :disabled="disabled || loading"
    :class="[
      variant === 'icon' ? 'spatial-icon-btn' : 'spatial-button',
      variant === 'danger' ? '!bg-red-500/20 hover:!bg-red-500/30 !text-red-400 border border-red-500/30' : '',
      variant === 'secondary' ? '!bg-white/10 hover:!bg-white/20 !text-white border border-white/15 !shadow-none' : '',
      variant === 'success' ? '!bg-emerald-500 hover:!bg-emerald-600 !text-white' : '',
      customClass
    ]"
  >
    <span v-if="loading" class="animate-spin w-4 h-4 border-2 border-white border-t-transparent rounded-full"></span>
    <slot />
  </button>
</template>

<script setup>
defineProps({
  type: {
    type: String,
    default: 'button',
  },
  variant: {
    type: String,
    default: 'primary', // primary, secondary, danger, success, icon
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
</script>
