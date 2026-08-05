<template>
  <div class="min-h-screen bg-slate-900 text-slate-100 p-4 sm:p-6 lg:p-8 dir-rtl text-right font-sans">
    
    <!-- MAIN CONTAINER -->
    <div class="max-w-6xl mx-auto space-y-6">

      <!-- TOP HEADER BAR & BREADCRUMB -->
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-6 rounded-3xl bg-slate-800/80 backdrop-blur-xl border border-white/10 shadow-2xl">
        <div class="space-y-1">
          <div class="flex items-center gap-2">
            <span class="px-3 py-1 rounded-full text-xs font-black bg-primary/20 text-primary border border-primary/30">
              ⚡ البوابة الميدانية للاستشاري
            </span>
            <span class="text-xs text-white/50 font-mono">{{ todayDateFormatted }}</span>
          </div>
          <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight">
            تنفيذ الزيارات اليومية الميدانية
          </h1>
          <p class="text-xs text-white/60 font-bold">
            تسجيل الحضور اليومي، فتح زيارات المواقع، واستكمال النماذج الميدانية والإثباتات الحية.
          </p>
        </div>

        <!-- START DAY ACTION BUTTON & STATUS -->
        <div class="flex items-center gap-3">
          <div v-if="dailyRecord && dailyRecord.check_in_time" class="text-left">
            <span class="text-[11px] text-emerald-400 font-black block">🟢 اليوم العملي قيد التنفيذ</span>
            <span class="text-[10px] text-white/50 font-mono">بدء: {{ formatTime(dailyRecord.check_in_time) }}</span>
          </div>

          <SpatialButton
            v-else
            variant="primary"
            size="lg"
            :disabled="isSubmitting"
            @click="handleStartDay"
          >
            🚀 بدء اليوم العملي
          </SpatialButton>
        </div>
      </div>

      <!-- CONSULTANT & DAILY STATS CARDS -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Card 1: Consultant Info -->
        <SpatialCard padding="p-5" class="space-y-2">
          <div class="flex items-center justify-between">
            <span class="text-xs font-black text-white/50">الاستشاري الميداني</span>
            <span class="text-xs font-mono text-primary font-bold">{{ consultant.employee_number }}</span>
          </div>
          <h3 class="text-base font-black text-white">{{ consultant.full_name }}</h3>
          <p class="text-xs text-white/60 font-bold">{{ consultant.specialization || 'مفتش ميداني' }}</p>
        </SpatialCard>

        <!-- Card 2: Daily Progress -->
        <SpatialCard padding="p-5" class="space-y-2">
          <div class="flex items-center justify-between">
            <span class="text-xs font-black text-white/50">نسبة إنجاز مهام اليوم</span>
            <span class="text-xs font-mono text-emerald-400 font-bold">{{ dailyRecord.completion_percentage || 0 }}%</span>
          </div>
          <SpatialProgressBar :value="dailyRecord.completion_percentage || 0" />
          <div class="flex justify-between text-[11px] text-white/60 font-bold pt-1">
            <span>المهام المنجزة: {{ dailyRecord.completed_daily_tasks || 0 }}</span>
            <span>الإجمالي: {{ dailyRecord.required_daily_tasks || 0 }}</span>
          </div>
        </SpatialCard>

        <!-- Card 3: Visited Sites Count -->
        <SpatialCard padding="p-5" class="space-y-2">
          <div class="flex items-center justify-between">
            <span class="text-xs font-black text-white/50">المواقع المزارة اليوم</span>
            <span class="text-xs font-mono text-amber-400 font-bold">BR-023</span>
          </div>
          <h3 class="text-xl font-black text-white">
            {{ dailyRecord.site_visits ? dailyRecord.site_visits.length : 0 }} موقع
          </h3>
          <p class="text-xs text-white/60 font-bold">مسجلة في سجل اليوم الروزنامي الحالي</p>
        </SpatialCard>
      </div>

      <!-- SITE SELECTION BAR & OPEN VISIT (BR-023 & Dropdown) -->
      <SpatialCard padding="p-6" class="space-y-4 relative z-30">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 border-b border-white/10 pb-3">
          <div>
            <h3 class="text-base font-black text-white flex items-center gap-2">
              📍 اختيار الموقع الميداني وفتح زيارة جديدة
            </h3>
            <p class="text-xs text-white/60 font-bold">
              اختر الموقع من القائمة المنسدلة لإضافته لسجلك اليومي وعرض المهام المنسدلة فوراً.
            </p>
          </div>

          <span v-if="activeVisit" class="px-3 py-1 rounded-full text-xs font-black bg-amber-500/15 text-amber-400 border border-amber-500/20">
            ⏳ توجد زيارة جارية حالياً
          </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
          <div class="sm:col-span-3 space-y-1.5">
            <label class="text-xs font-black text-white/80">الموقع الميداني المتاح:</label>
            <SpatialDropdown
              v-model="selectedSiteId"
              placeholder="ابحث واختر الموقع الميداني..."
              :options="formattedSiteOptions"
              :searchable="true"
            />
          </div>

          <div>
            <SpatialButton
              variant="primary"
              size="md"
              class="w-full justify-center h-[54px]"
              :disabled="!selectedSiteId || isSubmitting"
              @click="handleOpenSiteVisit"
            >
              ➕ فتح زيارة الموقع
            </SpatialButton>
          </div>
        </div>
      </SpatialCard>

      <!-- ACTIVE SITE VISIT EXECUTION PORTAL (BR-019: Daily vs On-Demand Separation) -->
      <div v-if="currentActiveVisit" class="space-y-6">
        
        <!-- ACTIVE VISIT BANNER -->
        <div class="p-6 rounded-3xl bg-gradient-to-r from-blue-900/40 via-indigo-900/40 to-slate-900 backdrop-blur-xl border border-blue-500/30 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
          <div class="space-y-1">
            <div class="flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
              <span class="text-xs font-black text-emerald-400">زيارة قائمة (قيد التنفيذ)</span>
              <span class="text-xs text-white/50 font-mono">#{{ currentActiveVisit.id }}</span>
            </div>
            <h2 class="text-xl font-black text-white">
              📍 {{ currentActiveVisit.site ? currentActiveVisit.site.name : 'الموقع الميداني' }}
            </h2>
            <p class="text-xs text-white/60 font-bold">
              كود: {{ currentActiveVisit.site ? currentActiveVisit.site.code : '' }}
            </p>
          </div>

          <div class="flex items-center gap-2">
            <SpatialButton
              variant="secondary"
              size="sm"
              @click="openDetailsModal(currentActiveVisit)"
            >
              👁️ معاينة
            </SpatialButton>
            <SpatialButton
              variant="danger"
              size="sm"
              @click="confirmCancelVisit(currentActiveVisit)"
            >
              🗑️ إلغاء الزيارة المعلقة
            </SpatialButton>
          </div>
        </div>

        <!-- SECTION A: DAILY TASKS (AUTOMATICALLY LOADED BR-019) -->
        <div class="space-y-4">
          <div class="flex items-center justify-between px-1">
            <h3 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2">
              📅 المهام اليومية الدورية الإجبارية (تظهر فوراً عند فتح الزيارة)
            </h3>
            <span class="text-xs font-mono text-blue-400 font-bold">
              {{ activeDailyTasks.length }} مهام يومية
            </span>
          </div>

          <div v-if="activeDailyTasks.length > 0" class="space-y-4">
            <div
              v-for="resp in activeDailyTasks"
              :key="resp.id"
              class="p-6 rounded-3xl bg-slate-800/80 backdrop-blur-xl border border-white/10 space-y-4"
            >
              <div class="flex items-center justify-between border-b border-white/10 pb-3">
                <div class="space-y-0.5">
                  <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-blue-500/15 text-blue-400 border border-blue-500/20">
                    📅 مهمة يومية دورية
                  </span>
                  <h4 class="text-base font-black text-white mt-1">
                    {{ resp.taskDefinition ? resp.taskDefinition.title : 'مهمة ميدانية' }}
                  </h4>
                  <p v-if="resp.taskDefinition && resp.taskDefinition.description" class="text-xs text-white/60 font-bold">
                    {{ resp.taskDefinition.description }}
                  </p>
                </div>
              </div>

              <!-- SMART ADAPTIVE FORM COMPONENTS -->
              <div class="space-y-4">
                <div
                  v-for="comp in getVisibleComponentsForTask(resp)"
                  :key="comp.id"
                  class="p-4 rounded-2xl bg-white/5 border border-white/10 space-y-2"
                >
                  <div class="flex items-center justify-between">
                    <label class="text-xs font-black text-white flex items-center gap-1.5">
                      <span>{{ comp.label }}</span>
                      <span v-if="comp.is_required" class="text-red-400 font-mono">*</span>
                    </label>

                    <span v-if="comp.conditional_parent_id" class="px-2 py-0.5 rounded-md bg-purple-500/15 text-purple-400 text-[10px] font-bold">
                      🔗 حقل شرطي ذكي
                    </span>
                  </div>

                  <!-- Text Input -->
                  <div v-if="comp.component_type === 'text'">
                    <SpatialInput
                      v-model="formValues[getTaskKey(resp.task_definition_id, comp.id)]"
                      :placeholder="comp.placeholder || 'أدخل النص...'"
                    />
                  </div>

                  <!-- Number Input -->
                  <div v-else-if="comp.component_type === 'number'">
                    <SpatialInput
                      v-model="formValues[getTaskKey(resp.task_definition_id, comp.id)]"
                      type="number"
                      :placeholder="comp.placeholder || 'أدخل رقم...'"
                    />
                  </div>

                  <!-- Dropdown Input -->
                  <div v-else-if="comp.component_type === 'select' || comp.component_type === 'choice'">
                    <SpatialDropdown
                      v-model="formValues[getTaskKey(resp.task_definition_id, comp.id)]"
                      placeholder="اختر الخيار المناسب..."
                      :options="getComponentOptions(comp)"
                    />
                  </div>

                  <!-- Checkbox Input -->
                  <div v-else-if="comp.component_type === 'checkbox'" class="space-y-2 pt-1">
                    <div
                      v-for="opt in getComponentOptions(comp)"
                      :key="opt.value"
                      class="flex items-center gap-2 cursor-pointer p-2 rounded-xl bg-white/5 hover:bg-white/10 transition-colors"
                      @click="toggleCheckbox(resp.task_definition_id, comp.id, opt.value)"
                    >
                      <SpatialCheckbox
                        :model-value="isCheckboxChecked(resp.task_definition_id, comp.id, opt.value)"
                      />
                      <span class="text-xs font-bold text-white/90">{{ opt.label }}</span>
                    </div>
                  </div>

                  <!-- Image Upload Dropzone (BR-028) -->
                  <div v-else-if="comp.component_type === 'image_upload' || comp.component_type === 'image'">
                    <SpatialImageUpload />
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div v-else class="p-6 text-center rounded-3xl border border-dashed border-white/10 text-white/50 text-xs font-bold">
            لا توجد مهام يومية دورية مرتبطة بهذا الموقع حالياً.
          </div>
        </div>

        <!-- SECTION B: ON-DEMAND TASKS (TRIGGERED VIA DROPDOWN BR-019) -->
        <div class="space-y-4 pt-4 border-t border-white/10">
          <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 px-1">
            <div>
              <h3 class="text-sm font-black text-amber-400 uppercase tracking-wider flex items-center gap-2">
                ⚡ المهام عند الحاجة (تُضاف حتمياً عبر القائمة المنسدلة المخصصة)
              </h3>
              <p class="text-xs text-white/60 font-bold">
                تطبيق قاعدة الفصل التشغيلي (BR-019): اختر مهمة طارئة أو عند الحاجة لإضافتها لهذه الزيارة.
              </p>
            </div>
          </div>

          <!-- ON-DEMAND TRIGGER DROPDOWN -->
          <div class="p-5 rounded-3xl bg-amber-500/10 border border-amber-500/20 grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
            <div class="sm:col-span-3 space-y-1.5">
              <label class="text-xs font-black text-amber-300">اختر مهمة عند الحاجة لإضافتها للزيارة الحالية:</label>
              <SpatialDropdown
                v-model="selectedOnDemandTaskId"
                placeholder="⚡ اختر مهمة طارئة / عند الحاجة..."
                :options="formattedOnDemandOptions"
                :searchable="true"
              />
            </div>

            <div>
              <SpatialButton
                variant="amber"
                size="md"
                class="w-full justify-center h-[54px]"
                :disabled="!selectedOnDemandTaskId || isSubmitting"
                @click="handleTriggerOnDemand"
              >
                ➕ إضافة المهمة
              </SpatialButton>
            </div>
          </div>

          <!-- TRIGGERED ON-DEMAND TASKS LIST -->
          <div v-if="activeOnDemandTasks.length > 0" class="space-y-4">
            <div
              v-for="resp in activeOnDemandTasks"
              :key="resp.id"
              class="p-6 rounded-3xl bg-amber-950/20 backdrop-blur-xl border border-amber-500/30 space-y-4"
            >
              <div class="flex items-center justify-between border-b border-amber-500/20 pb-3">
                <div class="space-y-0.5">
                  <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-500/20 text-amber-300 border border-amber-500/30">
                    ⚡ مهمة عند الحاجة (مُفعلة)
                  </span>
                  <h4 class="text-base font-black text-white mt-1">
                    {{ resp.taskDefinition ? resp.taskDefinition.title : 'مهمة عند الحاجة' }}
                  </h4>
                </div>
              </div>

              <!-- Component inputs for on-demand task -->
              <div class="space-y-4">
                <div
                  v-for="comp in getVisibleComponentsForTask(resp)"
                  :key="comp.id"
                  class="p-4 rounded-2xl bg-white/5 border border-white/10 space-y-2"
                >
                  <label class="text-xs font-black text-white block">{{ comp.label }}</label>
                  <div v-if="comp.component_type === 'text'">
                    <SpatialInput v-model="formValues[getTaskKey(resp.task_definition_id, comp.id)]" />
                  </div>
                  <div v-else-if="comp.component_type === 'number'">
                    <SpatialInput v-model="formValues[getTaskKey(resp.task_definition_id, comp.id)]" type="number" />
                  </div>
                  <div v-else-if="comp.component_type === 'image_upload' || comp.component_type === 'image'">
                    <SpatialImageUpload />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- FORM ACTION BAR -->
        <div class="p-6 rounded-3xl bg-slate-800/90 backdrop-blur-xl border border-white/10 flex items-center justify-between gap-4 sticky bottom-6 shadow-2xl z-40">
          <SpatialButton variant="secondary" :disabled="isSubmitting" @click="handleSaveResponses(false)">
            💾 حفظ مسودة الإجابات
          </SpatialButton>

          <SpatialButton variant="primary" size="lg" :disabled="isSubmitting" @click="handleSaveResponses(true)">
            ✅ إنهاء واعتمد زيارة الموقع
          </SpatialButton>
        </div>

      </div>

      <!-- VISITED SITES HISTORY TIMELINE -->
      <SpatialCard padding="p-6" class="space-y-4">
        <h3 class="text-base font-black text-white border-b border-white/10 pb-3">
          📜 سجل زيارات اليوم الفعال (Timeline)
        </h3>

        <div v-if="dailyRecord.site_visits && dailyRecord.site_visits.length > 0" class="space-y-3">
          <div
            v-for="visit in dailyRecord.site_visits"
            :key="visit.id"
            class="p-4 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-between gap-4"
          >
            <div>
              <h4 class="text-sm font-black text-white">
                📍 {{ visit.site ? visit.site.name : 'موقع ميداني' }}
              </h4>
              <p class="text-xs text-white/50 font-mono">
                بدء الزيارة: {{ formatTime(visit.visit_started_at) }}
              </p>
            </div>

            <div class="flex items-center gap-3">
              <span
                :class="[
                  'px-3 py-1 rounded-full text-xs font-black',
                  visit.status === 'completed'
                    ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/20'
                    : 'bg-amber-500/15 text-amber-400 border border-amber-500/20'
                ]"
              >
                {{ visit.status === 'completed' ? '✓ مكتملة' : '⏳ قيد التنفيذ' }}
              </span>

              <SpatialButton variant="ghost" size="sm" @click="openDetailsModal(visit)">
                👁️ التفاصيل
              </SpatialButton>
            </div>
          </div>
        </div>

        <div v-else class="p-8 text-center text-xs font-bold text-white/40">
          لم يتم فتح زيارات مواقع بعد في هذا اليوم.
        </div>
      </SpatialCard>

    </div>

    <!-- REUSABLE MODALS -->
    <VisitDetailModal
      :is-open="showDetailModal"
      :visit="selectedModalVisit"
      @close="showDetailModal = false"
    />

    <CancelVisitModal
      :is-open="showCancelModal"
      :loading="isSubmitting"
      @close="showCancelModal = false"
      @confirm="handleCancelVisit"
    />

  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import SpatialCard from '@/Components/Spatial/SpatialCard.vue';
import SpatialButton from '@/Components/Spatial/SpatialButton.vue';
import SpatialInput from '@/Components/Spatial/SpatialInput.vue';
import SpatialDropdown from '@/Components/Spatial/SpatialDropdown.vue';
import SpatialCheckbox from '@/Components/Spatial/SpatialCheckbox.vue';
import SpatialImageUpload from '@/Components/Spatial/SpatialImageUpload.vue';
import SpatialProgressBar from '@/Components/Spatial/SpatialProgressBar.vue';
import VisitDetailModal from '@/Components/Consultant/VisitDetailModal.vue';
import CancelVisitModal from '@/Components/Consultant/CancelVisitModal.vue';

const props = defineProps({
  consultant: { type: Object, required: true },
  dailyRecord: { type: Object, required: true },
  availableSites: { type: Array, default: () => [] },
  activeVisit: { type: Object, default: null },
  availableOnDemandTasks: { type: Array, default: () => [] },
});

const selectedSiteId = ref(null);
const selectedOnDemandTaskId = ref(null);
const isSubmitting = ref(false);
const formValues = ref({});

// Modals state
const showDetailModal = ref(false);
const selectedModalVisit = ref(null);
const showCancelModal = ref(false);
const visitToCancel = ref(null);

const currentActiveVisit = computed(() => props.activeVisit);

const todayDateFormatted = computed(() => {
  return new Date().toLocaleDateString('ar-LY', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
});

const formattedSiteOptions = computed(() => {
  return props.availableSites.map((site) => ({
    label: `${site.name} (${site.code})`,
    value: site.id,
  }));
});

const formattedOnDemandOptions = computed(() => {
  return props.availableOnDemandTasks.map((t) => ({
    label: `⚡ ${t.title}`,
    value: t.id,
  }));
});

const activeDailyTasks = computed(() => {
  if (!currentActiveVisit.value || !currentActiveVisit.value.task_responses) return [];
  return currentActiveVisit.value.task_responses.filter(
    (r) => r.task_definition && r.task_definition.task_type === 'daily'
  );
});

const activeOnDemandTasks = computed(() => {
  if (!currentActiveVisit.value || !currentActiveVisit.value.task_responses) return [];
  return currentActiveVisit.value.task_responses.filter(
    (r) => r.task_definition && r.task_definition.task_type === 'on_demand'
  );
});

// Watch active visit to preload existing form response values
watch(
  () => props.activeVisit,
  (visit) => {
    if (!visit || !visit.task_responses) return;
    const initial = {};
    visit.task_responses.forEach((resp) => {
      if (resp.values) {
        resp.values.forEach((v) => {
          const key = getTaskKey(resp.task_definition_id, v.task_component_id);
          try {
            initial[key] = JSON.parse(v.value);
          } catch {
            initial[key] = v.value;
          }
        });
      }
    });
    formValues.value = initial;
  },
  { immediate: true }
);

const getTaskKey = (taskId, compId) => `t_${taskId}_c_${compId}`;

const getComponentOptions = (comp) => {
  if (!comp.options) return [];
  return comp.options.map((opt) => ({
    label: typeof opt === 'string' ? opt : opt.label || opt.option_label || opt.value,
    value: typeof opt === 'string' ? opt : opt.value || opt.option_value,
  }));
};

const getVisibleComponentsForTask = (resp) => {
  if (!resp.taskDefinition || !resp.taskDefinition.components) return [];
  const comps = resp.taskDefinition.components;
  return comps.filter((comp) => {
    if (!comp.conditional_parent_id) return true;
    const parentKey = getTaskKey(resp.task_definition_id, comp.conditional_parent_id);
    const parentVal = formValues.value[parentKey];
    if (!parentVal) return false;
    return String(parentVal) === String(comp.conditional_value);
  });
};

const isCheckboxChecked = (taskId, compId, val) => {
  const current = formValues.value[getTaskKey(taskId, compId)];
  return Array.isArray(current) ? current.includes(val) : false;
};

const toggleCheckbox = (taskId, compId, val) => {
  const key = getTaskKey(taskId, compId);
  if (!formValues.value[key] || !Array.isArray(formValues.value[key])) {
    formValues.value[key] = [val];
  } else {
    const idx = formValues.value[key].indexOf(val);
    if (idx > -1) formValues.value[key].splice(idx, 1);
    else formValues.value[key].push(val);
  }
};

const formatTime = (isoString) => {
  if (!isoString) return '';
  return new Date(isoString).toLocaleTimeString('ar-LY', { hour: '2-digit', minute: '2-digit' });
};

// Handlers
const handleStartDay = () => {
  isSubmitting.value = true;
  router.post('/consultant/daily-visits/start-day', {}, {
    onFinish: () => (isSubmitting.value = false),
  });
};

const handleOpenSiteVisit = () => {
  if (!selectedSiteId.value) return;
  isSubmitting.value = true;
  router.post('/consultant/site-visits', { site_id: selectedSiteId.value }, {
    onSuccess: () => (selectedSiteId.value = null),
    onFinish: () => (isSubmitting.value = false),
  });
};

const handleTriggerOnDemand = () => {
  if (!selectedOnDemandTaskId.value || !currentActiveVisit.value) return;
  isSubmitting.value = true;
  router.post(`/consultant/site-visits/${currentActiveVisit.value.id}/trigger-on-demand`, {
    task_definition_id: selectedOnDemandTaskId.value,
  }, {
    onSuccess: () => (selectedOnDemandTaskId.value = null),
    onFinish: () => (isSubmitting.value = false),
  });
};

const handleSaveResponses = (completeVisit = false) => {
  if (!currentActiveVisit.value) return;
  isSubmitting.value = true;

  const responses = (currentActiveVisit.value.task_responses || []).map((resp) => {
    const valuesObj = {};
    if (resp.taskDefinition && resp.taskDefinition.components) {
      resp.taskDefinition.components.forEach((comp) => {
        const key = getTaskKey(resp.task_definition_id, comp.id);
        if (formValues.value[key] !== undefined) {
          valuesObj[comp.id] = formValues.value[key];
        }
      });
    }

    return {
      task_definition_id: resp.task_definition_id,
      values: valuesObj,
      is_completed: completeVisit,
    };
  });

  router.post(`/consultant/site-visits/${currentActiveVisit.value.id}/save-responses`, {
    responses,
    complete_visit: completeVisit,
  }, {
    onFinish: () => (isSubmitting.value = false),
  });
};

const openDetailsModal = (visit) => {
  selectedModalVisit.value = visit;
  showDetailModal.value = true;
};

const confirmCancelVisit = (visit) => {
  visitToCancel.value = visit;
  showCancelModal.value = true;
};

const handleCancelVisit = () => {
  if (!visitToCancel.value) return;
  isSubmitting.value = true;
  router.delete(`/consultant/site-visits/${visitToCancel.value.id}`, {
    onSuccess: () => {
      showCancelModal.value = false;
      visitToCancel.value = null;
    },
    onFinish: () => (isSubmitting.value = false),
  });
};
</script>
