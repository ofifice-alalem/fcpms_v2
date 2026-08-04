<template>
  <div
    @click="$emit('select', value)"
    class="task-option-card flex flex-col justify-between space-y-3"
    :class="{ 'active': selected }"
  >
    <div class="flex items-center justify-between">
      <span
        class="w-8 h-8 rounded-full flex items-center justify-center font-bold"
        :class="iconBgClass"
      >
        <slot name="icon">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
          </svg>
        </slot>
      </span>
      <span v-if="pillText" :class="['status-pill text-[10px]', pillType]">{{ pillText }}</span>
    </div>
    <div>
      <h4 class="font-black text-sm text-white">{{ title }}</h4>
      <p v-if="subtitle" class="text-xs text-white/50 mt-1">{{ subtitle }}</p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  value: [String, Number],
  selected: Boolean,
  title: String,
  subtitle: String,
  pillText: String,
  pillType: {
    type: String,
    default: 'completed',
  },
});

defineEmits(['select']);

const iconBgClass = computed(() => {
  if (props.pillType === 'completed') return 'bg-emerald-500/20 text-emerald-500';
  if (props.pillType === 'pending') return 'bg-amber-500/20 text-amber-500';
  return 'bg-red-500/20 text-red-500';
});
</script>
