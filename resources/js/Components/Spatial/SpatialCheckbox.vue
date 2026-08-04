<template>
  <input
    type="checkbox"
    :checked="modelValue"
    class="custom-checkbox"
    @change="$emit('update:modelValue', $event.target.checked)"
  />
</template>

<script setup>
defineProps({
  modelValue: Boolean,
});

defineEmits(['update:modelValue']);
</script>
