<template>
  <SpatialCard padding="p-6" class="flex flex-col justify-between space-y-5 border-t-4 border-t-primary transition-all hover:scale-[1.01]">
    <!-- Card Header -->
    <div class="flex items-start justify-between gap-3">
      <div>
        <div class="flex items-center gap-2">
          <h3 class="text-base font-black text-slate-900 dark:text-white">{{ template.name }}</h3>
          <span
            v-if="template.is_default"
            class="px-2.5 py-0.5 rounded-lg bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 font-bold text-[11px] border border-emerald-500/20"
          >
            القالب الافتراضي
          </span>
        </div>
        <p class="text-xs font-bold text-slate-500 dark:text-white/50 mt-1 line-clamp-2">
          {{ template.description || 'لا يوجد وصف مخصص لهذا القالب.' }}
        </p>
      </div>
    </div>

    <!-- Working Days Grid Bar (Sun to Sat) -->
    <div class="space-y-1.5">
      <span class="text-[11px] font-bold text-slate-400 dark:text-white/50 block">أيام العمل الأسبوعية:</span>
      <div class="grid grid-cols-7 gap-1">
        <div
          v-for="day in daysOfWeek"
          :key="day.value"
          :class="[
            'p-2 rounded-xl text-center flex flex-col items-center justify-center border text-[11px] font-black transition-all',
            isWorkingDay(day.value)
              ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-600 dark:text-emerald-400 shadow-sm'
              : 'bg-black/5 dark:bg-white/5 border-black/10 dark:border-white/10 text-slate-400 dark:text-white/30'
          ]"
        >
          <span>{{ day.shortName }}</span>
          <span class="text-[9px] font-bold mt-0.5">
            {{ isWorkingDay(day.value) ? 'عمل' : 'راحة' }}
          </span>
        </div>
      </div>
    </div>

    <!-- Card Footer -->
    <div class="pt-3 border-t border-black/10 dark:border-white/10 flex items-center justify-between">
      <div class="text-xs font-bold text-slate-500 dark:text-white/60 flex items-center gap-1.5">
        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <span>{{ template.consultants_count || 0 }} استشاري مسند</span>
      </div>

      <div class="flex items-center gap-1.5">
        <SpatialIconButton variant="primary" title="عرض التفاصيل" @click="$emit('view', template)">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
          </svg>
        </SpatialIconButton>

        <SpatialIconButton variant="info" title="تعديل القالب" @click="$emit('edit', template)">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
          </svg>
        </SpatialIconButton>

        <SpatialIconButton variant="danger" title="حذف القالب" @click="$emit('delete', template)">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
          </svg>
        </SpatialIconButton>
      </div>
    </div>
  </SpatialCard>
</template>

<script setup>
import SpatialCard from '@/Components/Spatial/SpatialCard.vue';
import SpatialIconButton from '@/Components/Spatial/SpatialIconButton.vue';

const props = defineProps({
  template: {
    type: Object,
    required: true,
  },
});

defineEmits(['view', 'edit', 'delete']);

const daysOfWeek = [
  { value: 0, shortName: 'الأحد' },
  { value: 1, shortName: 'الإثنين' },
  { value: 2, shortName: 'الثلاثاء' },
  { value: 3, shortName: 'الأربعاء' },
  { value: 4, shortName: 'الخميس' },
  { value: 5, shortName: 'الجمعة' },
  { value: 6, shortName: 'السبت' },
];

function isWorkingDay(dayOfWeek) {
  if (!props.template.days) return false;
  const day = props.template.days.find(d => d.day_of_week === dayOfWeek);
  return day ? day.is_working_day : false;
}
</script>
