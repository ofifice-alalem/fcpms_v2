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
          <span class="font-black text-slate-800 dark:text-white">{{ consultant.hire_date || 'غير محدد' }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/5 space-y-1">
          <span class="font-bold text-slate-400 dark:text-white/40 block">قالب الدوام المسند:</span>
          <span class="font-black text-slate-800 dark:text-white">{{ consultant.work_schedule_template || consultant.work_schedule_template?.name || 'افتراضي' }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/5 space-y-1">
          <span class="font-bold text-slate-400 dark:text-white/40 block">تاريخ الإنشاء:</span>
          <span class="font-black text-slate-800 dark:text-white">{{ consultant.created_at || 'غير متوفر' }}</span>
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
import SpatialModal from '@/Components/Spatial/SpatialModal.vue';
import SpatialStatusPill from '@/Components/Spatial/SpatialStatusPill.vue';
import SpatialButton from '@/Components/Spatial/SpatialButton.vue';

defineProps({
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
</script>
