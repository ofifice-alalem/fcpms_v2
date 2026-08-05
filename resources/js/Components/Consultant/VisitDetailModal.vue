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
        <h4 class="text-xs font-black text-slate-500 dark:text-white/50 uppercase tracking-wider">
          إجابات الاستبيانات والمهام المسجلة ({{ taskResponses.length }})
        </h4>

        <div
          v-for="resp in taskResponses"
          :key="resp.id"
          class="p-4 rounded-2xl bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 space-y-3"
        >
          <div class="flex items-center justify-between">
            <span class="text-sm font-black text-slate-900 dark:text-white">
              {{ resp.taskDefinition ? resp.taskDefinition.title : 'مهمة ميدانية' }}
            </span>

            <SpatialStatusPill
              :type="resp.taskDefinition && resp.taskDefinition.task_type === 'daily' ? 'completed' : 'pending'"
            >
              {{ resp.taskDefinition && resp.taskDefinition.task_type === 'daily' ? 'مهمة يومية' : 'عند الحاجة' }}
            </SpatialStatusPill>
          </div>

          <!-- Component Values -->
          <div v-if="resp.values && resp.values.length > 0" class="space-y-2 pt-1 border-t border-slate-100 dark:border-white/5">
            <div
              v-for="val in resp.values"
              :key="val.id"
              class="flex items-center justify-between text-xs py-1"
            >
              <span class="font-bold text-slate-500 dark:text-white/70">
                {{ val.component ? val.component.label : 'العنصر' }}:
              </span>
              <span class="font-black text-slate-900 dark:text-white">
                {{ val.value || '-' }}
              </span>
            </div>
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
  return props.visit && props.visit.task_responses ? props.visit.task_responses : [];
});

const formatTime = (isoString) => {
  if (!isoString) return '';
  return new Date(isoString).toLocaleTimeString('ar-LY', { hour: '2-digit', minute: '2-digit' });
};
</script>
