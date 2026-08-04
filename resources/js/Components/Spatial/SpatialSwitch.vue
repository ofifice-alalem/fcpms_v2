<template>
  <label class="spatial-switch">
    <input
      type="checkbox"
      :checked="modelValue"
      @change="$emit('update:modelValue', $event.target.checked)"
    />
    <span class="spatial-switch-slider"></span>
  </label>
</template>

<script setup>
defineProps({
  modelValue: Boolean,
});

defineEmits(['update:modelValue']);
</script>
