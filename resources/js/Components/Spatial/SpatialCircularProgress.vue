<template>
  <div class="relative flex flex-col items-center justify-center select-none">
    <svg :width="size" :height="size" class="transform -rotate-90">
      <!-- Background Track Ring -->
      <circle
        :cx="size / 2"
        :cy="size / 2"
        :r="radius"
        class="stroke-slate-200 dark:stroke-white/10"
        :stroke-width="strokeWidth"
        fill="transparent"
      />

      <!-- Progress Track Ring -->
      <circle
        :cx="size / 2"
        :cy="size / 2"
        :r="radius"
        :class="strokeColorClass"
        :stroke-width="strokeWidth"
        stroke-linecap="round"
        fill="transparent"
        :style="{
          strokeDasharray: circumference,
          strokeDashoffset: strokeDashoffset,
          transition: 'stroke-dashoffset 1s cubic-bezier(0.4, 0, 0.2, 1)',
        }"
      />
    </svg>

    <!-- Center Content Overlay -->
    <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
      <slot>
        <span class="text-xl font-black font-mono tracking-tight text-slate-900 dark:text-white">
          {{ clampedPercentage.toFixed(0) }}%
        </span>
      </slot>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  value: {
    type: Number,
    default: 0,
  },
  percentage: {
    type: Number,
    default: null,
  },
  size: {
    type: Number,
    default: 120,
  },
  strokeWidth: {
    type: Number,
    default: 9,
  },
  color: {
    type: String,
    default: 'auto',
  },
});

const clampedPercentage = computed(() => {
  const raw = props.percentage !== null ? props.percentage : props.value;
  return Math.min(100, Math.max(0, Number(raw) || 0));
});

const radius = computed(() => (props.size - props.strokeWidth * 2) / 2);
const circumference = computed(() => 2 * Math.PI * radius.value);

const strokeDashoffset = computed(() => {
  const progress = clampedPercentage.value / 100;
  return circumference.value * (1 - progress);
});

const strokeColorClass = computed(() => {
  if (props.color === 'emerald') return 'stroke-emerald-500';
  if (props.color === 'blue') return 'stroke-blue-500';
  if (props.color === 'indigo') return 'stroke-indigo-500';

  const val = clampedPercentage.value;
  if (val >= 100) return 'stroke-emerald-500 drop-shadow-[0_0_8px_rgba(16,185,129,0.5)]';
  if (val >= 60) return 'stroke-blue-500 drop-shadow-[0_0_8px_rgba(59,130,246,0.4)]';
  if (val >= 30) return 'stroke-amber-500 drop-shadow-[0_0_8px_rgba(245,158,11,0.4)]';
  return 'stroke-slate-400';
});
</script>
