<template>
  <div
    @click.stop="toggle"
    :class="[
      'w-5 h-5 rounded-[7px] border-2 flex items-center justify-center cursor-pointer transition-all duration-200 shrink-0 select-none',
      isChecked
        ? 'bg-primary border-primary shadow-md shadow-primary/30 scale-105'
        : 'bg-black/5 dark:bg-white/10 border-slate-300 dark:border-white/20 hover:border-primary/60 hover:scale-105'
    ]"
  >
    <svg
      v-if="isChecked"
      class="w-3.5 h-3.5 text-white"
      fill="none"
      stroke="currentColor"
      viewBox="0 0 24 24"
    >
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M5 13l4 4L19 7" />
    </svg>
  </div>
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

function toggle() {
  if (Array.isArray(props.modelValue)) {
    const updated = [...props.modelValue];
    const index = updated.indexOf(props.value);
    if (index === -1) {
      updated.push(props.value);
    } else {
      updated.splice(index, 1);
    }
    emit('update:modelValue', updated);
    emit('change', updated.includes(props.value));
  } else {
    const newValue = !isChecked.value;
    emit('update:modelValue', newValue);
    emit('change', newValue);
  }
}
</script>
