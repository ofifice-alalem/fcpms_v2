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

      <!-- 3 TABS NAVIGATION BAR (MOBILE OPTIMIZED WITH TOP-CENTERED BADGES) -->
      <div class="grid grid-cols-3 gap-2 p-1.5 rounded-2xl bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10 pt-4">
        <button
          type="button"
          @click="activeTab = 'completed_daily'"
          :class="[
            'relative py-3 px-2 rounded-xl text-[11px] sm:text-xs font-black transition-all flex items-center justify-center gap-1 cursor-pointer select-none text-center',
            activeTab === 'completed_daily'
              ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/20'
              : 'text-slate-600 dark:text-white/70 hover:bg-white/50 dark:hover:bg-white/10'
          ]"
        >
          <span
            :class="[
              'absolute -top-3 left-1/2 -translate-x-1/2 px-2 py-0.5 rounded-full text-[10px] font-mono font-black border shadow-md transition-all',
              activeTab === 'completed_daily'
                ? 'bg-slate-900 text-emerald-400 border-emerald-400/50'
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
            'relative py-3 px-2 rounded-xl text-[11px] sm:text-xs font-black transition-all flex items-center justify-center gap-1 cursor-pointer select-none text-center',
            activeTab === 'uncompleted_daily'
              ? 'bg-amber-500 text-white shadow-lg shadow-amber-500/20'
              : 'text-slate-600 dark:text-white/70 hover:bg-white/50 dark:hover:bg-white/10'
          ]"
        >
          <span
            :class="[
              'absolute -top-3 left-1/2 -translate-x-1/2 px-2 py-0.5 rounded-full text-[10px] font-mono font-black border shadow-md transition-all',
              activeTab === 'uncompleted_daily'
                ? 'bg-slate-900 text-amber-400 border-amber-400/50'
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
            'relative py-3 px-2 rounded-xl text-[11px] sm:text-xs font-black transition-all flex items-center justify-center gap-1 cursor-pointer select-none text-center',
            activeTab === 'on_demand'
              ? 'bg-primary text-white shadow-lg shadow-primary/20'
              : 'text-slate-600 dark:text-white/70 hover:bg-white/50 dark:hover:bg-white/10'
          ]"
        >
          <span
            :class="[
              'absolute -top-3 left-1/2 -translate-x-1/2 px-2 py-0.5 rounded-full text-[10px] font-mono font-black border shadow-md transition-all',
              activeTab === 'on_demand'
                ? 'bg-slate-900 text-blue-400 border-blue-400/50'
                : 'bg-primary/20 text-primary dark:text-blue-300 border-primary/30'
            ]"
          >
            {{ onDemandTasks.length }}
          </span>
          <span>المهام الإضافية</span>
        </button>
      </div>

      <!-- Task Responses List under active tab -->
      <div class="space-y-4">
        <div
          v-for="resp in currentTabTasks"
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

        <!-- Empty Tab States -->
        <div v-if="currentTabTasks.length === 0" class="p-8 text-center rounded-2xl bg-slate-50 dark:bg-white/5 border border-dashed border-slate-200 dark:border-white/10 text-slate-500 dark:text-white/40 space-y-1">
          <p v-if="activeTab === 'completed_daily'" class="text-xs font-bold">لا توجد مهام يومية تعبئتها المكتملة بعد.</p>
          <p v-else-if="activeTab === 'uncompleted_daily'" class="text-xs font-bold">ممتاز! تم تعبئة جميع المهام اليومية الدورية بنجاح 🎉</p>
          <p v-else class="text-xs font-bold">لم يتم إضافة أو تفعيل مهام إضافية (عند الحاجة) لهذه الزيارة.</p>
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
    return taskDef?.task_type === 'on_demand';
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
  if (str.includes('ملتزم') || str.includes('مطابق') || str.includes('نعم') || str.includes('سليم') || str.includes('ممتاز')) {
    return 'bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border-emerald-500/30';
  }
  if (str.includes('غير') || str.includes('لا') || str.includes('مخالف') || str.includes('عطل') || str.includes('خطر')) {
    return 'bg-rose-500/15 text-rose-700 dark:text-rose-300 border-rose-500/30';
  }
  return 'bg-primary/15 text-primary dark:text-blue-300 border-primary/30';
};
</script>
