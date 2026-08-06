<template>
  <SpatialModal
    :is-open="isOpen"
    title="تفاصيل وقائع زيارة الموقع الميدانية"
    max-width="2xl"
    @close="$emit('close')"
  >
    <div v-if="visit" class="space-y-6 text-right dir-rtl">
      <!-- Site & Time Overview Header -->
      <div class="p-4 rounded-2xl bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10 flex items-center justify-between">
        <div>
          <h3 class="text-base font-black text-slate-900 dark:text-white flex items-center gap-1.5">
            <span>{{ visit.site ? visit.site.name : 'الموقع الميداني' }}</span>
          </h3>
          <p v-if="visit.site" class="text-xs text-slate-500 dark:text-white/60 font-mono mt-0.5 dir-ltr text-right">
            كود الموقع: {{ visit.site.code }}
          </p>
        </div>

        <div class="text-left space-y-1">
          <SpatialStatusPill
            :type="visit.status === 'completed' ? 'completed' : 'pending'"
            :pulse="visit.status !== 'completed'"
          >
            {{ visit.status === 'completed' ? 'مكتملة' : 'قيد التنفيذ' }}
          </SpatialStatusPill>

          <span v-if="visit.visit_started_at" class="text-[11px] text-slate-500 dark:text-white/50 block font-mono dir-ltr">
            بدء: {{ formatTime(visit.visit_started_at) }}
          </span>
        </div>
      </div>

      <!-- Task Responses List -->
      <div class="space-y-4">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-white/10 pb-2">
          <h4 class="text-xs font-black text-slate-500 dark:text-white/50 uppercase tracking-wider">
            مهام وإجابات هذه الزيارة ({{ taskResponses.length }})
          </h4>
          <span class="text-[11px] font-bold text-slate-400 dark:text-white/40">
            سجل الاستبيانات والمدخلات
          </span>
        </div>

        <div
          v-for="resp in taskResponses"
          :key="resp.id"
          class="p-4 rounded-2xl bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 space-y-3 shadow-sm"
        >
          <div class="flex items-start justify-between gap-2">
            <div class="space-y-1">
              <span class="text-sm font-black text-slate-900 dark:text-white block">
                {{ getTaskDef(resp)?.title || 'مهمة ميدانية' }}
              </span>
            </div>

            <div class="flex flex-col items-end gap-1 shrink-0">
              <SpatialStatusPill
                :type="getTaskDef(resp)?.task_type === 'daily' ? 'completed' : 'pending'"
              >
                {{ getTaskDef(resp)?.task_type === 'daily' ? 'مهمة يومية' : 'عند الحاجة' }}
              </SpatialStatusPill>
              <span
                class="text-[10px] font-black px-2 py-0.5 rounded-full font-mono"
                :class="resp.values && resp.values.length > 0 ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20'"
              >
                {{ resp.values && resp.values.length > 0 ? 'تمت التعبئة ✓' : 'لم تُعبأ بعد ⏳' }}
              </span>
            </div>
          </div>

          <!-- Component Values -->
          <div v-if="resp.values && resp.values.length > 0" class="space-y-2 pt-2 border-t border-slate-100 dark:border-white/5">
            <div
              v-for="val in resp.values"
              :key="val.id"
              class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs p-3 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/5"
            >
              <span class="font-bold text-slate-600 dark:text-white/70">
                {{ getComponentLabel(val) }}:
              </span>
              <span
                :class="[
                  'inline-flex items-center px-3 py-1 rounded-xl font-black text-xs border transition-all self-start sm:self-auto',
                  getValueBadgeClass(val.value)
                ]"
              >
                {{ val.value || '-' }}
              </span>
            </div>
          </div>

          <!-- Unfilled State -->
          <div v-else class="text-right text-[11px] font-bold text-slate-400 dark:text-white/40 pt-1 border-t border-dashed border-slate-200 dark:border-white/5">
            لم يتم إدخال إجابات لهذا الاستبيان بعد.
          </div>

          <!-- Photo Attachments Gallery -->
          <div v-if="resp.attachments && resp.attachments.length > 0" class="space-y-1.5 pt-2">
            <span class="text-[11px] font-black text-slate-500 dark:text-white/50 block">
              إثباتات الصور المرفوعة ({{ resp.attachments.length }}):
            </span>
            <div class="grid grid-cols-3 gap-2">
              <a
                v-for="att in resp.attachments"
                :key="att.id"
                :href="att.file_path"
                target="_blank"
                class="block rounded-xl overflow-hidden border border-slate-200 dark:border-white/10 hover:opacity-95 transition-opacity"
              >
                <img :src="att.file_path" :alt="att.file_name" class="w-full h-20 object-cover" />
              </a>
            </div>
          </div>
        </div>

        <div v-if="taskResponses.length === 0" class="p-6 text-center text-xs font-bold text-slate-400 dark:text-white/40">
          لم يتم تسجيل إجابات بعد في هذه الزيارة.
        </div>
      </div>
    </div>

    <template #footer>
      <div class="flex justify-end">
        <SpatialButton variant="secondary" @click="$emit('close')">
          إغلاق التفاصيل
        </SpatialButton>
      </div>
    </template>
  </SpatialModal>
</template>

<script setup>
import { computed } from 'vue';
import SpatialModal from '@/Components/Spatial/SpatialModal.vue';
import SpatialButton from '@/Components/Spatial/SpatialButton.vue';
import SpatialStatusPill from '@/Components/Spatial/SpatialStatusPill.vue';

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  visit: {
    type: Object,
    default: null,
  },
});

defineEmits(['close']);

const taskResponses = computed(() => {
  return props.visit && props.visit.task_responses ? props.visit.task_responses : (props.visit && props.visit.taskResponses ? props.visit.taskResponses : []);
});

const getTaskDef = (resp) => {
  return resp?.task_definition || resp?.taskDefinition || null;
};

const getComponentLabel = (val) => {
  return val?.component?.label || val?.task_component?.label || 'العنصر';
};

const formatTime = (isoString) => {
  if (!isoString) return '';
  return new Date(isoString).toLocaleTimeString('ar-LY', { hour: '2-digit', minute: '2-digit' });
};

const getValueBadgeClass = (valStr) => {
  if (!valStr || valStr === '-') {
    return 'bg-slate-200/80 dark:bg-slate-700/80 text-slate-600 dark:text-slate-300 border-slate-300 dark:border-slate-600';
  }
  const str = String(valStr).toLowerCase();
  if (str.includes('ملتزم') || str.includes('مطابق') || str.includes('نعم') || str.includes('سليم') || str.includes('ممتاز')) {
    return 'bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border-emerald-500/30';
  }
  if (str.includes('غير') || str.includes('لا') || str.includes('مخالف') || str.includes('عطل') || str.includes('خطر')) {
    return 'bg-rose-500/15 text-rose-700 dark:text-rose-300 border-rose-500/30';
  }
  return 'bg-primary/15 text-primary dark:text-blue-300 border-primary/30';
};
</script>
