<template>
  <div class="relative p-7 rounded-3xl transition-all duration-300 space-y-7 border bg-white/80 dark:bg-slate-900/85 backdrop-blur-xl border-slate-200/80 dark:border-white/10 shadow-xl hover:shadow-2xl hover:border-emerald-500/50 hover:-translate-y-1 group overflow-hidden flex flex-col justify-between text-center items-center">
    
    <!-- Ambient Glass Radial Glow Orbs -->
    <div class="absolute -top-20 -right-20 w-56 h-56 rounded-full bg-emerald-500/15 blur-3xl pointer-events-none group-hover:bg-emerald-500/25 transition-all duration-500"></div>
    <div class="absolute -bottom-20 -left-20 w-56 h-56 rounded-full bg-indigo-500/15 blur-3xl pointer-events-none group-hover:bg-indigo-500/25 transition-all duration-500"></div>

    <div class="space-y-6 w-full relative z-10 flex flex-col items-center justify-center">
      
      <!-- Top Section: Template Title & Status Badge -->
      <div class="flex flex-col items-center justify-center space-y-2 w-full pb-4 border-b border-slate-100 dark:border-white/10">
        <div class="flex items-center justify-center gap-2 flex-wrap">
          <h3 class="text-lg font-black text-slate-900 dark:text-white leading-tight tracking-tight">
            {{ template.name }}
          </h3>
          <span
            v-if="template.is_default"
            class="px-2.5 py-0.5 rounded-full bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 font-black text-[10px] border border-emerald-500/30 shrink-0 flex items-center gap-1 shadow-xs"
          >
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
            افتراضي 🌟
          </span>
          <span
            v-else
            class="px-2.5 py-0.5 rounded-full bg-blue-500/10 text-blue-600 dark:text-blue-400 font-black text-[10px] border border-blue-500/20 shrink-0 shadow-xs"
          >
            مخصص 💼
          </span>
        </div>
        <p class="text-xs font-bold text-slate-500 dark:text-white/60 line-clamp-1 max-w-sm mx-auto">
          {{ template.description || 'لا يوجد وصف مخصص لهذا القالب.' }}
        </p>
      </div>

      <!-- Prominent SVG Circular Gauge Ring (150px Kept!) -->
      <div class="flex flex-col items-center justify-center space-y-2.5 py-1 relative z-10">
        <div class="relative drop-shadow-[0_0_24px_rgba(16,185,129,0.4)]">
          <SpatialCircularProgress
            :percentage="workingPercentage"
            :size="150"
            :stroke-width="12"
            color="emerald"
          >
            <div class="flex flex-col items-center justify-center leading-none">
              <span class="text-2xl sm:text-3xl font-black font-mono tracking-tight text-slate-900 dark:text-white">
                {{ workingDaysCount }} / 7
              </span>
              <span class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 mt-1 uppercase tracking-widest">
                أيام عمل
              </span>
            </div>
          </SpatialCircularProgress>
        </div>
        <span class="text-xs font-black text-slate-500 dark:text-white/60 uppercase tracking-widest pt-1">
          معدل أيام التشغيل الأسبوعية ({{ workingPercentage }}%)
        </span>
      </div>

      <!-- 50% Circular Day Badges (Starts with Saturday) -->
      <div class="space-y-2 w-full pt-1">
        <div class="flex items-center justify-center gap-2 sm:gap-2.5 flex-wrap w-full">
          <div
            v-for="day in daysOfWeek"
            :key="day.value"
            :title="`${day.shortName}: ${isWorkingDay(day.value) ? 'يوم عمل' : 'يوم راحة'}`"
            :class="[
              'w-13 h-13 sm:w-14 sm:h-14 flex flex-col items-center justify-center transition-all duration-300 select-none bg-transparent cursor-default',
              isWorkingDay(day.value)
                ? 'border-2 border-emerald-500 text-emerald-600 dark:text-emerald-400 shadow-[0_0_16px_rgba(16,185,129,0.4)] ring-4 ring-emerald-500/20 scale-105'
                : 'border border-slate-300 dark:border-white/15 text-slate-400 dark:text-white/30 opacity-50'
            ]"
            style="border-radius: 50%; background: transparent !important;"
          >
            <span class="font-bold text-[10px] sm:text-[11px] leading-tight">{{ day.shortName }}</span>
            <span
              :class="[
                'text-[8px] sm:text-[9px] font-black leading-none mt-0.5',
                isWorkingDay(day.value) ? 'text-emerald-500 dark:text-emerald-400' : 'text-slate-400 dark:text-white/30'
              ]"
            >
              {{ isWorkingDay(day.value) ? 'عمل' : 'راحة' }}
            </span>
          </div>
        </div>
      </div>

    </div>

    <!-- Card Footer with 50% Circular Bordered Action Buttons -->
    <div class="pt-4 border-t border-slate-100 dark:border-white/10 flex flex-col sm:flex-row items-center justify-between gap-3 w-full relative z-10 mt-2">
      <div class="flex items-center justify-center gap-2 bg-slate-100 dark:bg-white/5 px-4 py-2.5 rounded-full border border-slate-200/80 dark:border-white/10 text-xs font-bold text-slate-700 dark:text-white/80 shadow-xs">
        <svg class="w-4.5 h-4.5 text-indigo-500 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <span class="font-black text-slate-900 dark:text-white font-mono text-sm">{{ template.consultants_count || 0 }}</span>
        <span>استشاري</span>
      </div>

      <!-- Action Buttons with border-radius: 50% -->
      <div class="flex items-center justify-center gap-2.5">
        <!-- View Button -->
        <button
          type="button"
          title="عرض التفاصيل"
          @click="$emit('view', template)"
          class="w-11 h-11 border-2 border-indigo-500/70 text-indigo-600 dark:text-indigo-400 bg-transparent hover:bg-indigo-500/10 flex items-center justify-center transition-all duration-300 hover:scale-110 shadow-xs ring-2 ring-indigo-500/15"
          style="border-radius: 50%; background: transparent !important;"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
          </svg>
        </button>

        <!-- Edit Button (Green / Emerald) -->
        <button
          v-if="hasPerm('edit-schedules')"
          type="button"
          title="تعديل القالب"
          @click="$emit('edit', template)"
          class="w-11 h-11 border-2 border-emerald-500/70 text-emerald-600 dark:text-emerald-400 bg-transparent hover:bg-emerald-500/10 flex items-center justify-center transition-all duration-300 hover:scale-110 shadow-xs ring-2 ring-emerald-500/15"
          style="border-radius: 50%; background: transparent !important;"
        >
          <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
          </svg>
        </button>

        <!-- Delete Button -->
        <button
          v-if="hasPerm('delete-schedules')"
          type="button"
          title="حذف القالب"
          @click="$emit('delete', template)"
          class="w-11 h-11 border-2 border-rose-500/70 text-rose-600 dark:text-rose-400 bg-transparent hover:bg-rose-500/10 flex items-center justify-center transition-all duration-300 hover:scale-110 shadow-xs ring-2 ring-rose-500/15"
          style="border-radius: 50%; background: transparent !important;"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import SpatialCircularProgress from '@/Components/Spatial/SpatialCircularProgress.vue';

const props = defineProps({
  template: {
    type: Object,
    required: true,
  },
});

defineEmits(['view', 'edit', 'delete']);

const page = usePage();
const userPermissions = computed(() => page.props.auth?.user?.permissions || []);
const userRoles = computed(() => page.props.auth?.user?.roles || []);

const hasPerm = (perm) => {
  if (userRoles.value.includes('admin')) return true;
  return userPermissions.value.includes(perm);
};

const daysOfWeek = [
  { value: 6, shortName: 'السبت' },
  { value: 0, shortName: 'الأحد' },
  { value: 1, shortName: 'الإثنين' },
  { value: 2, shortName: 'الثلاثاء' },
  { value: 3, shortName: 'الأربعاء' },
  { value: 4, shortName: 'الخميس' },
  { value: 5, shortName: 'الجمعة' },
];

function isWorkingDay(dayOfWeek) {
  if (!props.template.days) return false;
  const day = props.template.days.find(d => d.day_of_week === dayOfWeek);
  return day ? day.is_working_day : false;
}

const workingDaysCount = computed(() => {
  if (!props.template.days) return 0;
  return props.template.days.filter(d => d.is_working_day).length;
});

const workingPercentage = computed(() => {
  return Math.round((workingDaysCount.value / 7) * 100);
});
</script>
