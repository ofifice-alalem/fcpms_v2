<template>
  <button
    :type="type"
    :class="[
      'spatial-btn-primary transition-all duration-200 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed',
      variant === 'secondary' ? 'bg-white/10 hover:bg-white/20 text-white shadow-none' : '',
      variant === 'danger' ? 'bg-red-600/80 hover:bg-red-600 text-white shadow-none' : '',
      customClass
    ]"
    :disabled="disabled"
  >
    <slot />
  </button>
</template>

<script setup>
defineProps({
  type: {
    type: String,
    default: 'button'
  },
  variant: {
    type: String,
    default: 'primary'
  },
  disabled: {
    type: Boolean,
    default: false
  },
  customClass: {
    type: String,
    default: ''
  }
});
</script>
