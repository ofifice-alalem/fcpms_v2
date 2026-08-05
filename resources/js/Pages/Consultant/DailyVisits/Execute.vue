<template>
  <ConsultantLayout :title="pageTitle">
    <div class="max-w-5xl mx-auto space-y-6">
      
      <!-- Toast Feedback Component -->
      <SpatialToast ref="toastRef" />

      <!-- Page Header Bar -->
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-6 rounded-3xl bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl border border-black/10 dark:border-white/10 shadow-xl">
        <div class="space-y-1">
          <div class="flex items-center gap-2">
            <SpatialButton variant="ghost" size="sm" @click="goBack">
              <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
              </svg>
              <span>العودة لسجل اليوم</span>
            </SpatialButton>

            <SpatialStatusPill
              v-if="visit"
              :type="visit.status === 'completed' ? 'completed' : 'pending'"
              :pulse="visit.status !== 'completed'"
            >
              {{ visit.status === 'completed' ? 'مكتملة' : 'قيد التنفيذ' }}
            </SpatialStatusPill>
          </div>

          <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight mt-1">
            {{ pageTitle }}
          </h1>
          <p v-if="visit && visit.site" class="text-xs text-slate-500 dark:text-white/60 font-bold">
            كود الموقع: <span class="font-mono text-primary font-bold dir-ltr inline-block">{{ visit.site.code }}</span>
          </p>
        </div>

        <div v-if="visit && visit.site" class="px-4 py-2 rounded-2xl bg-black/5 dark:bg-white/5 border border-black/5 dark:border-white/10 text-left">
          <span class="text-[10px] text-slate-500 dark:text-white/50 block">الموقع الميداني</span>
          <span class="text-sm font-black text-slate-900 dark:text-white">{{ visit.site.name }}</span>
        </div>
      </div>

      <!-- MODE A: OPEN NEW SITE VISIT FORM -->
      <SpatialCard v-if="!visit" padding="p-6" class="space-y-6">
        <div class="space-y-1 border-b border-black/10 dark:border-white/10 pb-4">
          <h3 class="text-lg font-black text-slate-900 dark:text-white flex items-center gap-2">
            <span>اختيار الموقع الميداني للزيارة</span>
          </h3>
          <p class="text-xs text-slate-500 dark:text-white/60 font-bold">
            اختر الموقع من القائمة المنسدلة لبدء الزيارة وجلب المهام اليومية الدورية المسندة فوراً.
          </p>
        </div>

        <div class="space-y-4">
          <div class="space-y-1.5">
            <label class="text-xs font-black text-slate-700 dark:text-white/80">الموقع الميداني:</label>
            <SpatialDropdown
              v-model="selectedSiteId"
              placeholder="ابحث واختر الموقع الميداني..."
              :options="formattedSiteOptions"
              :searchable="true"
            />
          </div>

          <div class="space-y-1.5">
            <label class="text-xs font-black text-slate-700 dark:text-white/80">ملاحظات افتتاح الزيارة (اختياري):</label>
            <SpatialInput
              v-model="notes"
              placeholder="أدخل أي ملاحظات قبل افتتاح الزيارة..."
            />
          </div>
        </div>

        <div class="flex justify-end pt-4 border-t border-black/10 dark:border-white/10">
          <SpatialButton
            variant="primary"
            size="lg"
            :disabled="!selectedSiteId || isSubmitting"
            @click="handleOpenSiteVisit"
          >
            <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            <span>افتتاح زيارة الموقع وبدء المهام</span>
          </SpatialButton>
        </div>
      </SpatialCard>

      <!-- MODE B: EXECUTE & FILL TASKS FORM -->
      <div v-else class="space-y-6">

        <!-- SECTION 1: DAILY TASKS -->
        <div class="space-y-4">
          <div class="flex items-center justify-between px-1">
            <h3 class="text-sm font-black text-blue-600 dark:text-blue-400 uppercase tracking-wider flex items-center gap-1.5">
              <span>📅 المهام اليومية الدورية المعتمدة ({{ activeDailyTasks.length }})</span>
            </h3>
            <span class="text-xs font-mono text-slate-500 dark:text-white/50">تظهر تلقائياً فور افتتاح الزيارة</span>
          </div>

          <div v-if="activeDailyTasks.length > 0" class="space-y-4">
            <SpatialCard
              v-for="resp in activeDailyTasks"
              :key="resp.id"
              padding="p-6"
              class="space-y-4 shadow-xl"
            >
              <div class="space-y-1 border-b border-slate-200/60 dark:border-white/10 pb-3">
                <SpatialStatusPill type="completed">
                  مهمة يومية
                </SpatialStatusPill>
                <h4 class="text-base font-black text-slate-900 dark:text-white mt-1">
                  {{ resp.taskDefinition ? resp.taskDefinition.title : 'مهمة' }}
                </h4>
                <p v-if="resp.taskDefinition && resp.taskDefinition.description" class="text-xs text-slate-500 dark:text-white/60 font-bold">
                  {{ resp.taskDefinition.description }}
                </p>
              </div>

              <!-- Component Fields -->
              <div class="space-y-4">
                <div
                  v-for="comp in getVisibleComponentsForTask(resp)"
                  :key="comp.id"
                  class="p-4 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200/60 dark:border-white/10 space-y-2"
                >
                  <label class="text-xs font-black text-slate-900 dark:text-white flex items-center gap-1">
                    <span>{{ comp.label }}</span>
                    <span v-if="comp.is_required" class="text-red-500">*</span>
                  </label>

                  <div v-if="comp.component_type === 'text'">
                    <SpatialInput
                      v-model="formValues[getTaskKey(resp.task_definition_id, comp.id)]"
                      :placeholder="comp.placeholder || 'أدخل النص...'"
                    />
                  </div>

                  <div v-else-if="comp.component_type === 'number'">
                    <SpatialInput
                      v-model="formValues[getTaskKey(resp.task_definition_id, comp.id)]"
                      type="number"
                      :placeholder="comp.placeholder || 'أدخل رقم...'"
                    />
                  </div>

                  <div v-else-if="comp.component_type === 'select' || comp.component_type === 'choice'">
                    <SpatialDropdown
                      v-model="formValues[getTaskKey(resp.task_definition_id, comp.id)]"
                      placeholder="اختر الخيار المناسب..."
                      :options="getComponentOptions(comp)"
                    />
                  </div>

                  <div v-else-if="comp.component_type === 'checkbox'" class="space-y-2 pt-1">
                    <div
                      v-for="opt in getComponentOptions(comp)"
                      :key="opt.value"
                      class="flex items-center gap-2 cursor-pointer p-2.5 rounded-xl bg-white dark:bg-white/5 hover:bg-slate-100 dark:hover:bg-white/10 border border-slate-200/60 dark:border-white/5 transition-colors"
                      @click="toggleCheckbox(resp.task_definition_id, comp.id, opt.value)"
                    >
                      <SpatialCheckbox :model-value="isCheckboxChecked(resp.task_definition_id, comp.id, opt.value)" />
                      <span class="text-xs font-bold text-slate-800 dark:text-white/90">{{ opt.label }}</span>
                    </div>
                  </div>

                  <div v-else-if="comp.component_type === 'image_upload' || comp.component_type === 'image'">
                    <SpatialImageUpload />
                  </div>
                </div>
              </div>
            </SpatialCard>
          </div>

          <div v-else class="p-6 text-center text-xs font-bold text-slate-400 dark:text-white/40 border border-dashed border-slate-300 dark:border-white/10 rounded-3xl bg-slate-100/50 dark:bg-white/5">
            لا توجد مهام يومية مسندة لهذا الموقع.
          </div>
        </div>

        <!-- SECTION 2: TRIGGERED ON-DEMAND TASKS (RENDERED DIRECTLY ABOVE ON-DEMAND DROPDOWN) -->
        <div class="space-y-4 pt-4 border-t border-slate-200/60 dark:border-white/10">
          <div v-if="activeOnDemandTasks.length > 0" class="space-y-4">
            <div class="px-1 border-b border-amber-500/20 pb-2">
              <h3 class="text-sm font-black text-amber-600 dark:text-amber-400 uppercase tracking-wider flex items-center gap-1.5">
                <span>⚡ المهام المضافة عند الحاجة ({{ activeOnDemandTasks.length }})</span>
              </h3>
            </div>

            <SpatialCard
              v-for="resp in activeOnDemandTasks"
              :key="resp.id"
              padding="p-6"
              class="border-amber-500/30 space-y-4 shadow-xl"
            >
              <div class="space-y-1 border-b border-amber-500/20 pb-3">
                <SpatialStatusPill type="pending">
                  مهمة عند الحاجة
                </SpatialStatusPill>
                <h4 class="text-base font-black text-slate-900 dark:text-white mt-1">
                  {{ resp.taskDefinition ? resp.taskDefinition.title : 'مهمة عند الحاجة' }}
                </h4>
              </div>

              <div class="space-y-4">
                <div
                  v-for="comp in getVisibleComponentsForTask(resp)"
                  :key="comp.id"
                  class="p-4 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200/60 dark:border-white/10 space-y-2"
                >
                  <label class="text-xs font-black text-slate-900 dark:text-white block">{{ comp.label }}</label>
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
            </SpatialCard>
          </div>

          <!-- ON-DEMAND TASK DROPDOWN TRIGGER (LOCATED AT THE BOTTOM OF THE TASK LIST) -->
          <SpatialCard padding="p-6" class="border-amber-500/30 bg-amber-500/5 space-y-3 shadow-xl">
            <label class="text-xs font-black text-amber-700 dark:text-amber-300 block">إضافة مهمة حسب الحاجة لهذا الموقع:</label>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-center">
              <div class="sm:col-span-3">
                <SpatialDropdown
                  v-model="selectedOnDemandTaskId"
                  placeholder="اختر مهمة عند الحاجة لإضافتها..."
                  :options="formattedOnDemandOptions"
                  :searchable="true"
                />
              </div>
              <SpatialButton
                variant="amber"
                size="md"
                class="w-full justify-center h-[52px]"
                :disabled="!selectedOnDemandTaskId || isSubmitting"
                @click="handleTriggerOnDemand"
              >
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>إضافة المهمة</span>
              </SpatialButton>
            </div>
          </SpatialCard>
        </div>

        <!-- STICKY ACTION BAR -->
        <div class="p-6 rounded-3xl bg-white/90 dark:bg-slate-800/90 backdrop-blur-xl border border-black/10 dark:border-white/10 flex items-center justify-between gap-4 sticky bottom-6 shadow-2xl z-40">
          <SpatialButton variant="ghost" @click="goBack">
            العودة لسجل اليوم
          </SpatialButton>

          <div class="flex items-center gap-3">
            <SpatialButton variant="secondary" :disabled="isSubmitting" @click="handleSaveResponses(false)">
              <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
              </svg>
              <span>حفظ مسودة</span>
            </SpatialButton>

            <SpatialButton variant="primary" size="lg" :disabled="isSubmitting" @click="handleSaveResponses(true)">
              <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              <span>اعتماد وإنهاء الزيارة</span>
            </SpatialButton>
          </div>
        </div>

      </div>

    </div>
  </ConsultantLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import ConsultantLayout from '@/Layouts/ConsultantLayout.vue';
import SpatialCard from '@/Components/Spatial/SpatialCard.vue';
import SpatialButton from '@/Components/Spatial/SpatialButton.vue';
import SpatialInput from '@/Components/Spatial/SpatialInput.vue';
import SpatialDropdown from '@/Components/Spatial/SpatialDropdown.vue';
import SpatialCheckbox from '@/Components/Spatial/SpatialCheckbox.vue';
import SpatialImageUpload from '@/Components/Spatial/SpatialImageUpload.vue';
import SpatialStatusPill from '@/Components/Spatial/SpatialStatusPill.vue';
import SpatialToast from '@/Components/Spatial/SpatialToast.vue';

const props = defineProps({
  consultant: { type: Object, required: true },
  dailyRecord: { type: Object, required: true },
  visit: { type: Object, default: null },
  availableSites: { type: Array, default: () => [] },
  availableOnDemandTasks: { type: Array, default: () => [] },
});

const selectedSiteId = ref(null);
const notes = ref('');
const selectedOnDemandTaskId = ref(null);
const isSubmitting = ref(false);
const formValues = ref({});

const pageTitle = computed(() => {
  if (!props.visit) return 'افتتاح وتوجيه زيارة موقع جديد';
  return `نموذج إدخال مهام الزيارة - ${props.visit.site ? props.visit.site.name : ''}`;
});

const formattedSiteOptions = computed(() => {
  return props.availableSites.map((s) => ({
    label: `${s.name} (${s.code})`,
    value: s.id,
  }));
});

const formattedOnDemandOptions = computed(() => {
  return props.availableOnDemandTasks.map((t) => ({
    label: `${t.title}`,
    value: t.id,
  }));
});

const activeDailyTasks = computed(() => {
  if (!props.visit || !props.visit.task_responses) return [];
  return props.visit.task_responses.filter(
    (r) => r.task_definition && r.task_definition.task_type === 'daily'
  );
});

const activeOnDemandTasks = computed(() => {
  if (!props.visit || !props.visit.task_responses) return [];
  return props.visit.task_responses.filter(
    (r) => r.task_definition && r.task_definition.task_type === 'on_demand'
  );
});

watch(
  () => props.visit,
  (newVisit) => {
    if (!newVisit || !newVisit.task_responses) {
      formValues.value = {};
      return;
    }
    const initial = {};
    newVisit.task_responses.forEach((resp) => {
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
  return resp.taskDefinition.components.filter((comp) => {
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

const goBack = () => {
  router.get('/consultant/daily-visits');
};

const handleOpenSiteVisit = () => {
  if (!selectedSiteId.value) return;
  isSubmitting.value = true;
  router.post('/consultant/site-visits', {
    site_id: selectedSiteId.value,
    notes: notes.value,
  }, {
    onFinish: () => (isSubmitting.value = false),
  });
};

const handleTriggerOnDemand = () => {
  if (!selectedOnDemandTaskId.value || !props.visit) return;
  isSubmitting.value = true;
  router.post(`/consultant/site-visits/${props.visit.id}/trigger-on-demand`, {
    task_definition_id: selectedOnDemandTaskId.value,
  }, {
    onSuccess: () => (selectedOnDemandTaskId.value = null),
    onFinish: () => (isSubmitting.value = false),
  });
};

const handleSaveResponses = (completeVisit = false) => {
  if (!props.visit) return;
  isSubmitting.value = true;
  const responses = (props.visit.task_responses || []).map((resp) => {
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

  router.post(`/consultant/site-visits/${props.visit.id}/save-responses`, {
    responses,
    complete_visit: completeVisit,
  }, {
    onSuccess: () => {
      if (completeVisit) {
        router.get('/consultant/daily-visits');
      }
    },
    onFinish: () => (isSubmitting.value = false),
  });
};
</script>
