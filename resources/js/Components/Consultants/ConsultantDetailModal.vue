<template>
  <SpatialModal
    :isOpen="isOpen"
    title="تفاصيل ملف الاستشاري الميداني"
    maxWidth="2xl"
    @close="$emit('close')"
  >
    <div v-if="consultant" class="space-y-6">
      <!-- Header Profile Card -->
      <div class="flex items-center gap-4 p-4 rounded-2xl bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10">
        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white font-black text-xl flex items-center justify-center shadow-lg">
          {{ getInitials(consultant.full_name) }}
        </div>
        <div class="space-y-1">
          <h3 class="text-lg font-black text-slate-900 dark:text-white leading-tight">
            {{ consultant.full_name }}
          </h3>
          <div class="flex items-center gap-2">
            <span class="text-xs font-mono font-bold px-2.5 py-0.5 rounded-full bg-blue-500/15 text-blue-600 dark:text-blue-400 border border-blue-500/20">
              {{ consultant.employee_number }}
            </span>
            <SpatialStatusPill :status="consultant.employment_status" />
          </div>
        </div>
      </div>

      <!-- Vacation Details Card (Only shown if active leave exists) -->
      <div
        v-if="activeLeave"
        class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/25 space-y-3"
      >
        <div class="flex items-center justify-between flex-wrap gap-2">
          <div class="flex items-center gap-2 text-amber-600 dark:text-amber-400 font-black text-sm">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span>بيانات الإجازة الرسمية الحالية</span>
            <span v-if="activeLeave?.reason" class="text-xs font-bold text-slate-600 dark:text-white/60">({{ activeLeave.reason }})</span>
          </div>
          
          <span
            :class="[
              'px-3 py-1 rounded-xl font-mono font-black text-xs border shadow-sm',
              remainingDays > 0
                ? 'bg-amber-500/20 text-amber-700 dark:text-amber-300 border-amber-500/30'
                : 'bg-slate-500/20 text-slate-600 dark:text-slate-400 border-slate-500/30'
            ]"
          >
            {{ remainingDays > 0 ? `${remainingDays} يوم متبقي للإجازة` : 'انتهت فترة الإجازة' }}
          </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
          <div class="p-3 rounded-xl bg-white/70 dark:bg-black/20 border border-black/5 dark:border-white/5 space-y-1">
            <span class="text-[11px] font-bold text-slate-500 dark:text-white/50 block">تاريخ بدء الإجازة:</span>
            <span class="font-black font-mono text-slate-900 dark:text-white">{{ formatDate(activeLeave?.start_date) }}</span>
          </div>

          <div class="p-3 rounded-xl bg-white/70 dark:bg-black/20 border border-black/5 dark:border-white/5 space-y-1">
            <span class="text-[11px] font-bold text-slate-500 dark:text-white/50 block">تاريخ نهاية الإجازة:</span>
            <span class="font-black font-mono text-slate-900 dark:text-white">{{ formatDate(activeLeave?.end_date) }}</span>
          </div>

          <div class="p-3 rounded-xl bg-white/70 dark:bg-black/20 border border-black/5 dark:border-white/5 space-y-1">
            <span class="text-[11px] font-bold text-slate-500 dark:text-white/50 block">مدة الإجازة الإجمالية:</span>
            <span class="font-black text-slate-900 dark:text-white">{{ totalLeaveDays }} أيام</span>
          </div>
        </div>

        <div v-if="activeLeave?.notes" class="text-xs text-amber-800 dark:text-amber-300 font-bold pt-1">
          📝 ملاحظات الإجازة: {{ activeLeave.notes }}
        </div>
      </div>

      <!-- Work Schedule & Days Section -->
      <div class="p-4 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 space-y-3">
        <div class="flex items-center justify-between flex-wrap gap-2">
          <div class="flex items-center gap-2 text-slate-900 dark:text-white font-black text-xs">
            <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>أيام ونظام العمل المسند:</span>
            <span class="px-2.5 py-0.5 rounded-lg bg-blue-500/15 text-blue-600 dark:text-blue-400 font-mono text-xs font-black border border-blue-500/20">
              {{ templateName }}
            </span>
          </div>

          <span class="text-[11px] font-bold text-slate-500 dark:text-white/60">
            ({{ workingDaysCount }} أيام عمل أسبوعياً)
          </span>
        </div>

        <div class="grid grid-cols-7 gap-1.5 text-center text-xs">
          <div
            v-for="d in weekDaysConfig"
            :key="d.day"
            :class="[
              'p-2 rounded-xl flex flex-col items-center justify-center gap-1 transition-all',
              d.isWorking
                ? 'bg-blue-500/15 text-blue-700 dark:text-blue-300 border border-blue-500/25 font-black'
                : 'bg-slate-200/60 dark:bg-white/5 text-slate-400 dark:text-white/40 border border-slate-200/50 dark:border-white/5 font-medium'
            ]"
          >
            <span class="text-[11px] font-bold">{{ d.name }}</span>
            <span :class="['text-[10px] font-mono font-black px-1.5 py-0.5 rounded-md', d.isWorking ? 'bg-blue-600 text-white' : 'bg-slate-300 dark:bg-white/10 text-slate-600 dark:text-white/50']">
              {{ d.isWorking ? 'عمل' : 'راحة' }}
            </span>
          </div>
        </div>
      </div>

      <!-- Detail Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/5 space-y-1">
          <span class="font-bold text-slate-400 dark:text-white/40 block">البريد الإلكتروني:</span>
          <span class="font-black text-slate-800 dark:text-white font-mono">{{ consultant.user?.email || consultant.email || 'غير محدد' }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/5 space-y-1">
          <span class="font-bold text-slate-400 dark:text-white/40 block">رقم الهاتف:</span>
          <span class="font-black text-slate-800 dark:text-white">{{ consultant.phone || 'غير محدد' }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/5 space-y-1">
          <span class="font-bold text-slate-400 dark:text-white/40 block">التخصص الميداني:</span>
          <span class="font-black text-slate-800 dark:text-white">{{ consultant.specialization || 'عام' }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/5 space-y-1">
          <span class="font-bold text-slate-400 dark:text-white/40 block">تاريخ التعيين:</span>
          <span class="font-black text-slate-800 dark:text-white">{{ formatDate(consultant.hire_date) }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/5 space-y-1">
          <span class="font-bold text-slate-400 dark:text-white/40 block">قالب الدوام المسند:</span>
          <span class="font-black text-slate-800 dark:text-white">{{ templateName }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/5 space-y-1">
          <span class="font-bold text-slate-400 dark:text-white/40 block">تاريخ الإنشاء:</span>
          <span class="font-black text-slate-800 dark:text-white">{{ formatDate(consultant.created_at) }}</span>
        </div>
      </div>

      <!-- Notes section -->
      <div v-if="consultant.notes" class="p-4 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/5 space-y-1">
        <span class="font-bold text-slate-400 dark:text-white/40 block text-xs">ملاحظات تشغيلية:</span>
        <p class="text-xs text-slate-700 dark:text-white/80 font-medium leading-relaxed">
          {{ consultant.notes }}
        </p>
      </div>

      <!-- Action Footer -->
      <div class="pt-3 border-t border-slate-200 dark:border-white/10 flex justify-end">
        <SpatialButton variant="ghost" class="px-6 h-12" @click="$emit('close')">
          إغلاق
        </SpatialButton>
      </div>
    </div>
  </SpatialModal>
</template>

<script setup>
import { computed } from 'vue';
import SpatialModal from '@/Components/Spatial/SpatialModal.vue';
import SpatialStatusPill from '@/Components/Spatial/SpatialStatusPill.vue';
import SpatialButton from '@/Components/Spatial/SpatialButton.vue';

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  consultant: {
    type: Object,
    default: null,
  },
});

defineEmits(['close']);

function getInitials(name) {
  if (!name) return 'CS';
  const parts = name.split(' ');
  return parts.length >= 2 ? (parts[0][0] + parts[1][0]) : name.substring(0, 2);
}

function formatDate(dateStr) {
  if (!dateStr) return 'غير محدد';
  return String(dateStr).substring(0, 10);
}

const templateName = computed(() => {
  if (!props.consultant) return 'افتراضي';
  if (typeof props.consultant.work_schedule_template === 'object' && props.consultant.work_schedule_template !== null) {
    return props.consultant.work_schedule_template.name || 'افتراضي';
  }
  return props.consultant.work_schedule_template || 'دوام افتراضي';
});

const weekDaysConfig = computed(() => {
  const daysList = [
    { day: 0, name: 'الأحد' },
    { day: 1, name: 'الإثنين' },
    { day: 2, name: 'الثلاثاء' },
    { day: 3, name: 'الأربعاء' },
    { day: 4, name: 'الخميس' },
    { day: 5, name: 'الجمعة' },
    { day: 6, name: 'السبت' },
  ];

  const templateObj = props.consultant?.work_schedule_template;
  const templateDays = (typeof templateObj === 'object' && templateObj?.days) ? templateObj.days : null;

  return daysList.map(d => {
    let isWorking = d.day < 5; // Default: Sun-Thu working, Fri-Sat off
    if (templateDays && Array.isArray(templateDays)) {
      const match = templateDays.find(td => Number(td.day_of_week) === d.day);
      if (match !== undefined) {
        isWorking = Boolean(match.is_working_day);
      }
    }
    return {
      ...d,
      isWorking,
    };
  });
});

const workingDaysCount = computed(() => {
  return weekDaysConfig.value.filter(d => d.isWorking).length;
});

const activeLeave = computed(() => {
  if (!props.consultant || !props.consultant.leaves || props.consultant.leaves.length === 0) {
    return null;
  }
  const sorted = [...props.consultant.leaves].sort((a, b) => new Date(b.start_date) - new Date(a.start_date));
  return sorted[0];
});

const totalLeaveDays = computed(() => {
  if (!activeLeave.value?.start_date || !activeLeave.value?.end_date) return 0;
  const start = new Date(activeLeave.value.start_date);
  const end = new Date(activeLeave.value.end_date);
  const diffTime = Math.abs(end - start);
  return Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
});

const remainingDays = computed(() => {
  if (!activeLeave.value?.end_date) return 0;
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  const end = new Date(activeLeave.value.end_date);
  end.setHours(0, 0, 0, 0);
  const diffTime = end - today;
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  return diffDays > 0 ? diffDays : 0;
});
</script>
