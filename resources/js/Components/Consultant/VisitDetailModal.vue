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

      <!-- 3 TABS NAVIGATION BAR (Top-Centered Count Badges) -->
      <div class="grid grid-cols-3 gap-2 p-1.5 rounded-2xl bg-slate-100 dark:bg-slate-800/90 border border-slate-200/80 dark:border-white/10 pt-4">
        <button
          type="button"
          @click="activeTab = 'completed_daily'"
          :class="[
            'relative py-3 px-2 rounded-xl text-xs font-black transition-all flex items-center justify-center gap-1 cursor-pointer select-none text-center',
            activeTab === 'completed_daily'
              ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/30'
              : 'text-slate-700 dark:text-white/80 hover:bg-white/60 dark:hover:bg-white/10'
          ]"
        >
          <span
            :class="[
              'absolute -top-3 left-1/2 -translate-x-1/2 px-2.5 py-0.5 rounded-full text-[11px] font-mono font-black border shadow-xs transition-all',
              activeTab === 'completed_daily'
                ? 'bg-slate-900 text-emerald-400 border-emerald-400/60'
                : 'bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border-emerald-500/30'
            ]"
          >
            {{ completedDailyTasks.length }}
          </span>
          <span>المهام التي تمت</span>
        </button>

        <button
          type="button"
          @click="activeTab = 'uncompleted_daily'"
          :class="[
            'relative py-3 px-2 rounded-xl text-xs font-black transition-all flex items-center justify-center gap-1 cursor-pointer select-none text-center',
            activeTab === 'uncompleted_daily'
              ? 'bg-amber-500 text-white shadow-lg shadow-amber-500/30'
              : 'text-slate-700 dark:text-white/80 hover:bg-white/60 dark:hover:bg-white/10'
          ]"
        >
          <span
            :class="[
              'absolute -top-3 left-1/2 -translate-x-1/2 px-2.5 py-0.5 rounded-full text-[11px] font-mono font-black border shadow-xs transition-all',
              activeTab === 'uncompleted_daily'
                ? 'bg-slate-900 text-amber-400 border-amber-400/60'
                : 'bg-amber-500/20 text-amber-700 dark:text-amber-300 border-amber-500/30'
            ]"
          >
            {{ uncompletedDailyTasks.length }}
          </span>
          <span>غير المنجزة</span>
        </button>

        <button
          type="button"
          @click="activeTab = 'on_demand'"
          :class="[
            'relative py-3 px-2 rounded-xl text-xs font-black transition-all flex items-center justify-center gap-1 cursor-pointer select-none text-center',
            activeTab === 'on_demand'
              ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30'
              : 'text-slate-700 dark:text-white/80 hover:bg-white/60 dark:hover:bg-white/10'
          ]"
        >
          <span
            :class="[
              'absolute -top-3 left-1/2 -translate-x-1/2 px-2.5 py-0.5 rounded-full text-[11px] font-mono font-black border shadow-xs transition-all',
              activeTab === 'on_demand'
                ? 'bg-slate-900 text-blue-400 border-blue-400/60'
                : 'bg-blue-500/20 text-blue-700 dark:text-blue-300 border-blue-500/30'
            ]"
          >
            {{ onDemandTasks.length }}
          </span>
          <span>المهام الإضافية</span>
        </button>
      </div>

      <!-- Task Responses Cards under Active Tab -->
      <div class="space-y-4">
        <div
          v-for="resp in currentTabTasks"
          :key="resp.id"
          class="p-5 rounded-3xl bg-white dark:bg-slate-800/90 border border-slate-200/80 dark:border-white/10 space-y-4 shadow-md hover:shadow-lg transition-all"
        >
          <!-- Task Header: Badges on Top Row for Mobile -->
          <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-2.5 pb-3 border-b border-slate-100 dark:border-white/10">
            <!-- Badges Row (Top on Mobile with space between) -->
            <div class="flex items-center justify-between w-full sm:w-auto sm:justify-start gap-2 order-1 sm:order-2 shrink-0">
              <span
                class="text-[11px] font-black px-2.5 py-0.5 rounded-full border"
                :class="getTaskDef(resp)?.task_type === 'daily' ? 'bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-500/30' : 'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-500/30'"
              >
                {{ getTaskDef(resp)?.task_type === 'daily' ? 'مهمة يومية' : 'عند الحاجة ⚡' }}
              </span>

              <span
                class="text-[11px] font-black px-2.5 py-0.5 rounded-full font-mono border"
                :class="resp.values && resp.values.length > 0 ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-500/30' : 'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-500/30'"
              >
                {{ resp.values && resp.values.length > 0 ? 'تمت التعبئة ✓' : 'لم تُعبأ بعد ⏳' }}
              </span>
            </div>

            <!-- Task Title (Under Badges on Mobile) -->
            <div class="space-y-1 order-2 sm:order-1">
              <span class="text-base font-black text-slate-900 dark:text-white leading-snug block">
                {{ getTaskDef(resp)?.title || 'مهمة ميدانية' }}
              </span>
            </div>
          </div>

          <!-- Component Question & Answer Rows -->
          <div v-if="resp.values && resp.values.length > 0" class="space-y-3">
            <div
              v-for="val in resp.values"
              :key="val.id"
              class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 p-3.5 rounded-2xl bg-slate-100/80 dark:bg-white/5 border border-slate-200/70 dark:border-white/10"
            >
              <span class="font-black text-slate-800 dark:text-white/90 text-xs sm:text-sm">
                {{ getComponentLabel(val) }}:
              </span>
              <span
                :class="[
                  'inline-flex items-center px-3.5 py-1.5 rounded-xl font-black text-xs sm:text-sm border transition-all self-start sm:self-auto shadow-2xs',
                  getValueBadgeClass(val.value)
                ]"
              >
                {{ val.value || '-' }}
              </span>
            </div>
          </div>

          <!-- Unfilled State -->
          <div v-else class="p-3 text-right text-xs font-bold text-slate-400 dark:text-white/40 bg-slate-50 dark:bg-white/5 rounded-2xl border border-dashed border-slate-200 dark:border-white/10">
            لم يتم إدخال إجابات لهذا الاستبيان بعد.
          </div>

          <!-- Photo Attachments Gallery -->
          <div v-if="resp.attachments && resp.attachments.length > 0" class="space-y-2 pt-2 border-t border-slate-100 dark:border-white/10">
            <span class="text-xs font-black text-slate-700 dark:text-white/70 block">
              📸 إثباتات الصور المرفوعة ({{ resp.attachments.length }}):
            </span>
            <div class="grid grid-cols-3 gap-2.5">
              <a
                v-for="att in resp.attachments"
                :key="att.id"
                :href="att.file_path"
                target="_blank"
                class="block rounded-2xl overflow-hidden border border-slate-200/80 dark:border-white/10 hover:opacity-95 transition-all shadow-xs hover:scale-102"
              >
                <img :src="att.file_path" :alt="att.file_name" class="w-full h-24 object-cover" />
              </a>
            </div>
          </div>
        </div>

        <!-- Empty Tab States -->
        <div v-if="currentTabTasks.length === 0" class="p-8 text-center rounded-3xl bg-slate-100/60 dark:bg-white/5 border border-dashed border-slate-200 dark:border-white/10 text-slate-500 dark:text-white/40 space-y-1">
          <p v-if="activeTab === 'completed_daily'" class="text-sm font-black">لا توجد مهام يومية تعبئتها المكتملة بعد.</p>
          <p v-else-if="activeTab === 'uncompleted_daily'" class="text-sm font-black text-emerald-600 dark:text-emerald-400">ممتاز! تم تعبئة جميع المهام اليومية الدورية بنجاح 🎉</p>
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

const activeTab = ref('completed_daily');

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
    return type === 'daily' && hasValues;
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
  return [];
});

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
  if (str.includes('ملتزم') || str.includes('مطابق') || str.includes('نعم') || str.includes('سليم') || str.includes('ممتاز') || str.includes('بكفاءة')) {
    return 'bg-emerald-500/15 text-emerald-800 dark:text-emerald-300 border-emerald-500/30';
  }
  if (str.includes('غير') || str.includes('لا') || str.includes('مخالف') || str.includes('عطل') || str.includes('خطر')) {
    return 'bg-rose-500/15 text-rose-800 dark:text-rose-300 border-rose-500/30';
  }
  return 'bg-blue-500/15 text-blue-800 dark:text-blue-300 border-blue-500/30';
};
</script>
