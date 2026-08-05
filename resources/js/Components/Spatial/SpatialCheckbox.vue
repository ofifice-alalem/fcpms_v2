<template>
  <input
    type="checkbox"
    :checked="isChecked"
    class="custom-checkbox cursor-pointer"
    @change="handleChange"
  />
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  modelValue: {
    type: [Boolean, Array],
    default: false,
  },
  value: {
    type: [String, Number, Boolean],
    default: null,
  },
});

const emit = defineEmits(['update:modelValue', 'change']);

const isChecked = computed(() => {
  if (Array.isArray(props.modelValue)) {
    return props.modelValue.includes(props.value);
  }
  return Boolean(props.modelValue);
});

function handleChange(event) {
  const checked = event.target.checked;
  if (Array.isArray(props.modelValue)) {
    const updated = [...props.modelValue];
    if (checked) {
      if (!updated.includes(props.value)) updated.push(props.value);
    } else {
      const index = updated.indexOf(props.value);
      if (index !== -1) updated.splice(index, 1);
    }
    emit('update:modelValue', updated);
  } else {
    emit('update:modelValue', checked);
  }
  emit('change', checked);
}
</script>
