<template>
  <div class="progress-bar w-full bg-slate-200/80 dark:bg-white/10 rounded-full overflow-hidden p-0.5" :class="height">
    <div
      class="fill h-full rounded-full transition-all duration-700 ease-out shadow-sm"
      :class="computedColorClass"
      :style="{ width: `${clampedPercentage}%` }"
    ></div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  percentage: {
    type: Number,
    default: null,
  },
  value: {
    type: Number,
    default: null,
  },
  color: {
    type: String,
    default: 'auto', // auto, primary, success, warning, danger
  },
  height: {
    type: String,
    default: 'h-2.5',
  },
});

const clampedPercentage = computed(() => {
  const raw = props.percentage !== null ? props.percentage : (props.value !== null ? props.value : 0);
  return Math.min(100, Math.max(0, Number(raw) || 0));
});

const computedColorClass = computed(() => {
  if (props.color === 'success') return 'bg-gradient-to-r from-emerald-500 to-teal-400 shadow-emerald-500/20';
  if (props.color === 'warning') return 'bg-gradient-to-r from-amber-500 to-orange-400 shadow-amber-500/20';
  if (props.color === 'danger') return 'bg-gradient-to-r from-rose-500 to-red-500 shadow-rose-500/20';
  if (props.color === 'primary') return 'bg-gradient-to-r from-blue-600 to-indigo-500 shadow-blue-500/20';

  // Auto dynamic color based on achievement
  const val = clampedPercentage.value;
  if (val >= 100) {
    return 'bg-gradient-to-r from-emerald-500 via-teal-400 to-emerald-400 shadow-md shadow-emerald-500/30 animate-pulse';
  } else if (val >= 60) {
    return 'bg-gradient-to-r from-blue-500 to-cyan-400 shadow-md shadow-blue-500/20';
  } else if (val >= 30) {
    return 'bg-gradient-to-r from-amber-500 to-yellow-400 shadow-md shadow-amber-500/20';
  }
  return 'bg-gradient-to-r from-slate-400 to-slate-500';
});
</script>
