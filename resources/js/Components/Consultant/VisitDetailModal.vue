<template>
  <SpatialModal
    :is-open="isOpen"
    title="تفاصيل وقائع زيارة الموقع الميدانية"
    max-width="2xl"
    @close="$emit('close')"
  >
    <div v-if="visit" class="space-y-5 text-right dir-rtl">
      <!-- Site & Overview Header -->
      <div class="p-4 rounded-2xl bg-slate-100/90 dark:bg-slate-800/80 border border-slate-200/80 dark:border-white/10 flex items-center justify-between shadow-xs">
        <div class="space-y-0.5">
          <h3 class="text-base font-black text-slate-900 dark:text-white flex items-center gap-2">
            <span>{{ visit.site ? visit.site.name : 'الموقع الميداني' }}</span>
          </h3>
          <p v-if="visit.site" class="text-xs font-mono font-black text-slate-600 dark:text-white/60 dir-ltr text-right">
            كود: {{ visit.site.code }}
          </p>
        </div>

        <div class="flex items-center gap-3">
          <span v-if="visit.visit_started_at" class="text-xs font-mono font-black text-slate-700 dark:text-white/70 dir-ltr">
            ⏰ {{ formatTime(visit.visit_started_at) }}
          </span>
          <SpatialStatusPill
            :type="visitProgress >= 100 ? 'completed' : 'pending'"
            :pulse="visitProgress < 100"
          >
            {{ visitProgress >= 100 ? 'مكتملة' : 'قيد التنفيذ' }}
          </SpatialStatusPill>
        </div>
      </div>

      <!-- Navigation Tabs (4 Tabs Grid with Top-Centered Count Badges & Hidden Scrollbar) -->
      <div class="flex sm:grid sm:grid-cols-4 items-center gap-2 pt-3 pb-1 overflow-x-auto no-scrollbar" style="scrollbar-width: none; -ms-overflow-style: none;">
        <button
          type="button"
          @click="activeTab = 'completed_daily'"
          :class="[
            'flex-1 min-w-[85px] sm:min-w-0 relative pt-3.5 pb-2.5 px-1.5 rounded-xl text-xs font-black transition-all flex flex-col items-center justify-center gap-1 cursor-pointer select-none text-center',
            activeTab === 'completed_daily'
              ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30'
              : 'bg-slate-100 dark:bg-slate-900/80 text-slate-700 dark:text-white/80 hover:bg-white/60 dark:hover:bg-white/10 border border-slate-200/80 dark:border-white/10'
          ]"
        >
          <span
            :class="[
              'absolute -top-2.5 left-1/2 -translate-x-1/2 px-2 py-0.5 rounded-full text-[10px] font-mono font-black border shadow-xs transition-all',
              activeTab === 'completed_daily'
                ? 'bg-slate-900 text-emerald-400 border-emerald-400/60'
                : 'bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border-emerald-500/30'
            ]"
          >
            {{ completedDailyTasks.length }}
          </span>
          <span class="whitespace-nowrap">مهام تمت</span>
        </button>

        <button
          type="button"
          @click="activeTab = 'uncompleted_daily'"
          :class="[
            'flex-1 min-w-[85px] sm:min-w-0 relative pt-3.5 pb-2.5 px-1.5 rounded-xl text-xs font-black transition-all flex flex-col items-center justify-center gap-1 cursor-pointer select-none text-center',
            activeTab === 'uncompleted_daily'
              ? 'bg-amber-500 text-white shadow-md shadow-amber-500/30'
              : 'bg-slate-100 dark:bg-slate-900/80 text-slate-700 dark:text-white/80 hover:bg-white/60 dark:hover:bg-white/10 border border-slate-200/80 dark:border-white/10'
          ]"
        >
          <span
            :class="[
              'absolute -top-2.5 left-1/2 -translate-x-1/2 px-2 py-0.5 rounded-full text-[10px] font-mono font-black border shadow-xs transition-all',
              activeTab === 'uncompleted_daily'
                ? 'bg-slate-900 text-amber-400 border-amber-400/60'
                : 'bg-amber-500/20 text-amber-700 dark:text-amber-300 border-amber-500/30'
            ]"
          >
            {{ uncompletedDailyTasks.length }}
          </span>
          <span class="whitespace-nowrap">غير منجزة</span>
        </button>

        <button
          type="button"
          @click="activeTab = 'on_demand'"
          :class="[
            'flex-1 min-w-[85px] sm:min-w-0 relative pt-3.5 pb-2.5 px-1.5 rounded-xl text-xs font-black transition-all flex flex-col items-center justify-center gap-1 cursor-pointer select-none text-center',
            activeTab === 'on_demand'
              ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30'
              : 'bg-slate-100 dark:bg-slate-900/80 text-slate-700 dark:text-white/80 hover:bg-white/60 dark:hover:bg-white/10 border border-slate-200/80 dark:border-white/10'
          ]"
        >
          <span
            :class="[
              'absolute -top-2.5 left-1/2 -translate-x-1/2 px-2 py-0.5 rounded-full text-[10px] font-mono font-black border shadow-xs transition-all',
              activeTab === 'on_demand'
                ? 'bg-slate-900 text-blue-400 border-blue-400/60'
                : 'bg-blue-500/20 text-blue-700 dark:text-blue-300 border-blue-500/30'
            ]"
          >
            {{ onDemandTasks.length }}
          </span>
          <span class="whitespace-nowrap">إضافية</span>
        </button>

        <button
          type="button"
          @click="activeTab = 'draft_tasks'"
          :class="[
            'flex-1 min-w-[85px] sm:min-w-0 relative pt-3.5 pb-2.5 px-1.5 rounded-xl text-xs font-black transition-all flex flex-col items-center justify-center gap-1 cursor-pointer select-none text-center',
            activeTab === 'draft_tasks'
              ? 'bg-purple-600 text-white shadow-md shadow-purple-600/30'
              : 'bg-slate-100 dark:bg-slate-900/80 text-slate-700 dark:text-white/80 hover:bg-white/60 dark:hover:bg-white/10 border border-slate-200/80 dark:border-white/10'
          ]"
        >
          <span
            :class="[
              'absolute -top-2.5 left-1/2 -translate-x-1/2 px-2 py-0.5 rounded-full text-[10px] font-mono font-black border shadow-xs transition-all',
              activeTab === 'draft_tasks'
                ? 'bg-slate-900 text-purple-400 border-purple-400/60'
                : 'bg-purple-500/20 text-purple-700 dark:text-purple-300 border-purple-500/30'
            ]"
          >
            {{ draftTasks.length }}
          </span>
          <span class="whitespace-nowrap">مسودة</span>
        </button>
      </div>

      <!-- Task Responses Cards under Active Tab -->
      <div class="space-y-4">
        <div
          v-for="resp in currentTabTasks"
          :key="resp.id"
          class="p-5 sm:p-6 rounded-2xl bg-white dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 space-y-4 shadow-none transition-colors"
        >
          <!-- 1. Task Title at Top -->
          <div class="pb-1">
            <span class="text-base sm:text-lg font-black text-slate-900 dark:text-white leading-snug block">
              {{ getTaskDef(resp)?.title || 'مهمة ميدانية' }}
            </span>
          </div>

          <!-- 2. Question & Answer Component Values -->
          <div v-if="resp.values && resp.values.length > 0" class="space-y-3 pt-1">
            <div
              v-for="val in resp.values"
              :key="val.id"
              class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 p-3.5 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/5"
            >
              <span class="font-black text-slate-800 dark:text-white/90 text-xs sm:text-sm">
                {{ getComponentLabel(val) }}:
              </span>
              <span
                :class="[
                  'inline-flex items-center px-3.5 py-1.5 rounded-lg font-black text-xs sm:text-sm border transition-all self-start sm:self-auto',
                  getValueBadgeClass(val.value)
                ]"
              >
                {{ val.value || '-' }}
              </span>
            </div>
          </div>

          <!-- Unfilled State -->
          <div v-else class="p-3 text-right text-xs font-bold text-slate-400 dark:text-white/40 bg-slate-50 dark:bg-white/5 rounded-xl border border-dashed border-slate-200 dark:border-white/10">
            لم يتم إدخال إجابات لهذا الاستبيان بعد.
          </div>

          <!-- Photo Attachments Gallery -->
          <div v-if="resp.attachments && resp.attachments.length > 0" class="space-y-2.5 pt-3 border-t border-slate-100 dark:border-white/5">
            <span class="text-xs font-black text-slate-700 dark:text-white/70 block">
              📸 إثباتات الصور المرفوعة ({{ resp.attachments.length }}):
            </span>
            <div class="grid grid-cols-3 gap-3">
              <a
                v-for="att in resp.attachments"
                :key="att.id"
                :href="att.file_path"
                target="_blank"
                class="block rounded-xl overflow-hidden border border-slate-200 dark:border-white/10 hover:opacity-95 transition-all"
              >
                <img :src="att.file_path" :alt="att.file_name" class="w-full h-22 object-cover" />
              </a>
            </div>
          </div>

          <!-- 3. Status Badges & Action Row -->
          <div class="pt-3 border-t border-slate-100 dark:border-white/5 flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-2">
              <span
                class="text-[11px] font-black px-3 py-1 rounded-full border"
                :class="getTaskDef(resp)?.task_type === 'daily' ? 'bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-500/30' : 'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-500/30'"
              >
                {{ getTaskDef(resp)?.task_type === 'daily' ? 'مهمة يومية' : 'عند الحاجة ⚡' }}
              </span>

              <span
                class="text-[11px] font-black px-3 py-1 rounded-full font-mono border"
                :class="[
                  resp.status === 'submitted'
                    ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-500/30'
                    : (resp.values && resp.values.length > 0
                        ? 'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-500/30'
                        : 'bg-slate-100 dark:bg-white/10 text-slate-500 dark:text-white/50 border-slate-200 dark:border-white/10')
                ]"
              >
                {{
                  resp.status === 'submitted'
                    ? 'مكتملة ومستلمة ✓'
                    : (resp.values && resp.values.length > 0 ? 'مخزنة كمسودة 📝' : 'لم تُعبأ بعد ⏳')
                }}
              </span>
            </div>

            <!-- Convert Single Draft to Completed -->
            <button
              v-if="resp.status === 'draft' && resp.values && resp.values.length > 0"
              type="button"
              :disabled="isSubmitting"
              @click="submitSingleTask(resp.id)"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-black bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition-all cursor-pointer disabled:opacity-50"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
              </svg>
              <span>تحويل إلى منجزة ✓</span>
            </button>
          </div>
        </div>

        <!-- Convert All Drafts Action Button -->
        <div v-if="activeTab === 'draft_tasks' && draftTasks.length > 0" class="pt-4 flex justify-center">
          <button
            type="button"
            :disabled="isSubmitting"
            @click="submitAllDrafts"
            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl text-xs sm:text-sm font-black bg-emerald-600 hover:bg-emerald-700 text-white shadow-lg shadow-emerald-600/25 transition-all cursor-pointer disabled:opacity-50"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>جعل كل المسودة إلى منجزة ({{ draftTasks.length }}) ✨</span>
          </button>
        </div>

        <!-- Empty Tab States -->
        <div v-if="currentTabTasks.length === 0" class="p-8 text-center rounded-3xl bg-slate-100/60 dark:bg-white/5 border border-dashed border-slate-200 dark:border-white/10 text-slate-500 dark:text-white/40 space-y-1">
          <p v-if="activeTab === 'completed_daily'" class="text-sm font-black">لا توجد مهام يومية تعبئتها المكتملة بعد.</p>
          <p v-else-if="activeTab === 'uncompleted_daily'" class="text-sm font-black text-emerald-600 dark:text-emerald-400">ممتاز! تم تعبئة جميع المهام اليومية الدورية بنجاح 🎉</p>
          <p v-else-if="activeTab === 'draft_tasks'" class="text-sm font-black text-emerald-600 dark:text-emerald-400">لا توجد مسودات غير معتمدة، جميع المهام المعبأة معتمدة بنجاح ✨</p>
          <p v-else class="text-sm font-black">لم يتم إضافة أو تفعيل مهام إضافية (عند الحاجة) لهذه الزيارة.</p>
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
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
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

const isSubmitting = ref(false);
const activeTab = ref('completed_daily');

const submitSingleTask = (responseId) => {
  if (!props.visit || !responseId) return;
  isSubmitting.value = true;
  router.post(`/consultant/site-visits/${props.visit.id}/task-responses/${responseId}/submit`, {}, {
    preserveScroll: true,
    onFinish: () => (isSubmitting.value = false),
  });
};

const submitAllDrafts = () => {
  if (!props.visit) return;
  isSubmitting.value = true;
  router.post(`/consultant/site-visits/${props.visit.id}/submit-all-drafts`, {}, {
    preserveScroll: true,
    onFinish: () => (isSubmitting.value = false),
  });
};

const taskResponses = computed(() => {
  return props.visit && props.visit.task_responses ? props.visit.task_responses : (props.visit && props.visit.taskResponses ? props.visit.taskResponses : []);
});

const getTaskDef = (resp) => {
  return resp?.task_definition || resp?.taskDefinition || null;
};

const visitProgress = computed(() => {
  if (!taskResponses.value || taskResponses.value.length === 0) return 0;
  const completed = taskResponses.value.filter((r) => {
    const hasValues = r.values && r.values.some(v => v.value && String(v.value).trim() !== '' && String(v.value) !== '[]' && String(v.value) !== 'null');
    return r.status === 'submitted' || (r.completed_at && hasValues);
  }).length;
  return Math.round((completed / taskResponses.value.length) * 100);
});

const completedDailyTasks = computed(() => {
  return taskResponses.value.filter((resp) => {
    const taskDef = getTaskDef(resp);
    const type = taskDef?.task_type || 'daily';
    const hasValues = resp.values && resp.values.length > 0;
    return type === 'daily' && hasValues && resp.status === 'submitted';
  });
});

const draftTasks = computed(() => {
  return taskResponses.value.filter((resp) => {
    const hasValues = resp.values && resp.values.length > 0;
    return hasValues && resp.status === 'draft';
  });
});

const uncompletedDailyTasks = computed(() => {
  return taskResponses.value.filter((resp) => {
    const taskDef = getTaskDef(resp);
    const type = taskDef?.task_type || 'daily';
    const hasValues = resp.values && resp.values.length > 0;
    return type === 'daily' && !hasValues;
  });
});

const onDemandTasks = computed(() => {
  return taskResponses.value.filter((resp) => {
    const taskDef = getTaskDef(resp);
    return taskDef?.task_type === 'on_demand' || taskDef?.task_type?.value === 'on_demand';
  });
});

const currentTabTasks = computed(() => {
  if (activeTab.value === 'completed_daily') return completedDailyTasks.value;
  if (activeTab.value === 'uncompleted_daily') return uncompletedDailyTasks.value;
  if (activeTab.value === 'on_demand') return onDemandTasks.value;
  if (activeTab.value === 'draft_tasks') return draftTasks.value;
  return [];
});

const getComponentLabel = (val) => {
  return val?.component?.label || val?.task_component?.label || val?.taskComponent?.label || val?.label || 'العنصر';
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
  if (str.includes('ملتزم') || str.includes('مطابق') || str.includes('نعم') || str.includes('سليم') || str.includes('ممتاز') || str.includes('بكفاءة')) {
    return 'bg-emerald-500/15 text-emerald-800 dark:text-emerald-300 border-emerald-500/30';
  }
  if (str.includes('غير') || str.includes('لا') || str.includes('مخالف') || str.includes('عطل') || str.includes('خطر')) {
    return 'bg-rose-500/15 text-rose-800 dark:text-rose-300 border-rose-500/30';
  }
  return 'bg-blue-500/15 text-blue-800 dark:text-blue-300 border-blue-500/30';
};
</script>
