<template>
  <SpatialModal
    :show="isOpen"
    title="تفاصيل قالب الدوام وساعات العمل"
    @close="handleClose"
  >
    <div v-if="template" class="space-y-6 text-right">
      <!-- Template Header Info -->
      <div class="flex items-center justify-between border-b border-black/10 dark:border-white/10 pb-4">
        <div>
          <div class="flex items-center gap-2">
            <h3 class="text-lg font-black text-slate-900 dark:text-white">{{ template.name }}</h3>
            <span
              v-if="template.is_default"
              class="px-2.5 py-0.5 rounded-lg bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 font-bold text-[11px] border border-emerald-500/20"
            >
              القالب الافتراضي
            </span>
          </div>
          <p class="text-xs font-bold text-slate-500 dark:text-white/60 mt-1">
            {{ template.description || 'لا يوجد وصف مخصص.' }}
          </p>
        </div>
      </div>

      <!-- Weekly Schedule Grid -->
      <div class="space-y-2">
        <h4 class="text-xs font-black text-slate-700 dark:text-white/80">جدول الأيام الأسبوعي المعتمد:</h4>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
          <div
            v-for="day in daysOfWeek"
            :key="day.value"
            :class="[
              'p-3 rounded-2xl border flex items-center justify-between font-black text-xs transition-all',
              isWorkingDay(day.value)
                ? 'bg-emerald-500/10 dark:bg-emerald-500/15 border-emerald-500/30 text-emerald-800 dark:text-emerald-300'
                : 'bg-slate-200/90 dark:bg-slate-800/90 border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300'
            ]"
          >
            <span class="font-black text-sm">{{ day.name }}</span>
            <span
              :class="[
                'px-2.5 py-1 rounded-xl text-[11px] font-black border',
                isWorkingDay(day.value)
                  ? 'bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border-emerald-500/30'
                  : 'bg-slate-400/20 text-slate-700 dark:text-slate-300 border-slate-300 dark:border-slate-600'
              ]"
            >
              {{ isWorkingDay(day.value) ? 'يوم عمل رسمـي' : 'عطلة أسبوعية (راحة)' }}
            </span>
          </div>
        </div>
      </div>

      <!-- Assigned Consultants List -->
      <div class="space-y-2 pt-2 border-t border-black/10 dark:border-white/10">
        <div class="flex items-center justify-between">
          <h4 class="text-xs font-black text-slate-700 dark:text-white/80">الاستشاريين المسندين بهذا القالب:</h4>
          <span class="text-xs font-mono font-black text-primary">
            {{ template.consultants ? template.consultants.length : 0 }} استشاري
          </span>
        </div>

        <div v-if="template.consultants && template.consultants.length > 0" class="max-h-48 overflow-y-auto space-y-1.5 custom-scroll">
          <div
            v-for="consultant in template.consultants"
            :key="consultant.id"
            class="p-2.5 rounded-xl bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 flex items-center justify-between text-xs"
          >
            <div class="font-black text-slate-900 dark:text-white">{{ consultant.full_name }}</div>
            <div class="font-mono text-slate-500 dark:text-white/50 text-[11px]">{{ consultant.employee_number }}</div>
          </div>
        </div>
        <div v-else class="text-xs text-slate-400 dark:text-white/40 font-bold p-4 text-center rounded-2xl bg-black/5 dark:bg-white/5">
          لا يوجد استشاريين مسندين لهذا القالب حالياً.
        </div>
      </div>
    </div>
  </SpatialModal>
</template>

<script setup>
import SpatialModal from '@/Components/Spatial/SpatialModal.vue';

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  template: {
    type: Object,
    default: null,
  },
});

const emit = defineEmits(['close']);

const daysOfWeek = [
  { value: 0, name: 'الأحد' },
  { value: 1, name: 'الإثنين' },
  { value: 2, name: 'الثلاثاء' },
  { value: 3, name: 'الأربعاء' },
  { value: 4, name: 'الخميس' },
  { value: 5, name: 'الجمعة' },
  { value: 6, name: 'السبت' },
];

function isWorkingDay(dayOfWeek) {
  if (!props.template || !props.template.days) return false;
  const day = props.template.days.find(d => d.day_of_week === dayOfWeek);
  return day ? day.is_working_day : false;
}

function handleClose() {
  emit('close');
}
</script>
