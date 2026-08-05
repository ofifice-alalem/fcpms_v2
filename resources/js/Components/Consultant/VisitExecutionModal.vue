<template>
  <SpatialModal
    :is-open="isOpen"
    :title="modalTitle"
    max-width="4xl"
    @close="$emit('close')"
  >
    <div class="space-y-6 text-right dir-rtl">
      
      <!-- STEP 1: SELECT SITE IF NO VISIT IS ACTIVE -->
      <div v-if="!visit" class="space-y-4 p-6 rounded-3xl bg-slate-800/60 border border-white/10">
        <div class="space-y-1">
          <h3 class="text-base font-black text-white">📍 اختر الموقع الميداني للزيارة</h3>
          <p class="text-xs text-white/60 font-bold">
            اختر الموقع من القائمة المنسدلة لبدء الزيارة وجلب المهام اليومية الدورية المسندة فوراً.
          </p>
        </div>

        <div class="space-y-4 pt-2">
          <div class="space-y-1.5">
            <label class="text-xs font-black text-white/80">الموقع الميداني:</label>
            <SpatialDropdown
              v-model="localSiteId"
              placeholder="ابحث واختر الموقع الميداني..."
              :options="formattedSiteOptions"
              :searchable="true"
            />
          </div>

          <div class="space-y-1.5">
            <label class="text-xs font-black text-white/80">ملاحظات افتتاح الزيارة (اختياري):</label>
            <SpatialInput
              v-model="localNotes"
              placeholder="أي ملاحظات قبل البدء..."
            />
          </div>
        </div>

        <div class="flex justify-end pt-3">
          <SpatialButton
            variant="primary"
            size="md"
            :disabled="!localSiteId || loading"
            @click="submitOpenVisit"
          >
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            <span>فتح زيارة الموقع وبدء المهام</span>
          </SpatialButton>
        </div>
      </div>

      <!-- STEP 2: ACTIVE VISIT TASKS FORM -->
      <div v-else class="space-y-6">

        <!-- Visit Info Header -->
        <div class="p-4 rounded-2xl bg-gradient-to-r from-blue-900/40 via-indigo-900/40 to-slate-800 border border-blue-500/30 flex items-center justify-between">
          <div class="space-y-0.5">
            <span class="text-[11px] font-black text-emerald-400">📍 {{ visit.site ? visit.site.name : 'الموقع' }}</span>
            <h3 class="text-sm font-black text-white">
              كود الموقع: <span class="font-mono text-primary dir-ltr inline-block">{{ visit.site ? visit.site.code : '' }}</span>
            </h3>
          </div>

          <SpatialStatusPill
            :type="visit.status === 'completed' ? 'completed' : 'pending'"
            :pulse="visit.status !== 'completed'"
          >
            {{ visit.status === 'completed' ? 'مكتملة' : 'قيد التنفيذ' }}
          </SpatialStatusPill>
        </div>

        <!-- SECTION A: DAILY TASKS -->
        <div class="space-y-4">
          <div class="flex items-center justify-between border-b border-white/10 pb-2">
            <h4 class="text-xs font-black text-blue-400 uppercase tracking-wider flex items-center gap-1.5">
              <span>📅 المهام اليومية الدورية المعتمدة ({{ activeDailyTasks.length }})</span>
            </h4>
          </div>

          <div v-if="activeDailyTasks.length > 0" class="space-y-4">
            <div
              v-for="resp in activeDailyTasks"
              :key="resp.id"
              class="p-5 rounded-2xl bg-slate-800/80 border border-white/10 space-y-4"
            >
              <div class="space-y-1">
                <SpatialStatusPill type="completed">
                  مهمة يومية
                </SpatialStatusPill>
                <h5 class="text-sm font-black text-white mt-1">
                  {{ getTaskDef(resp)?.title || 'مهمة' }}
                </h5>
              </div>

              <!-- Component Fields -->
              <div class="space-y-3 pt-2 border-t border-white/5">
                <div
                  v-for="comp in getVisibleComponentsForTask(resp)"
                  :key="comp.id"
                  class="p-3.5 rounded-xl bg-white/5 border border-white/10 space-y-1.5"
                >
                  <label class="text-xs font-black text-white flex items-center gap-1">
                    <span>{{ comp.label }}</span>
                    <span v-if="comp.is_required" class="text-red-400">*</span>
                  </label>

                  <div v-if="comp.component_type === 'text'">
                    <SpatialInput v-model="formValues[getTaskKey(resp.task_definition_id, comp.id)]" :placeholder="comp.placeholder || 'أدخل إجابة...'" />
                  </div>

                  <div v-else-if="comp.component_type === 'number'">
                    <SpatialInput v-model="formValues[getTaskKey(resp.task_definition_id, comp.id)]" type="number" :placeholder="comp.placeholder || 'أدخل رقم...'" />
                  </div>

                  <div v-else-if="comp.component_type === 'select' || comp.component_type === 'choice'">
                    <SpatialDropdown v-model="formValues[getTaskKey(resp.task_definition_id, comp.id)]" placeholder="اختر الخيار..." :options="getComponentOptions(comp)" />
                  </div>

                  <div v-else-if="comp.component_type === 'checkbox'" class="space-y-1.5 pt-1">
                    <div
                      v-for="opt in getComponentOptions(comp)"
                      :key="opt.value"
                      class="flex items-center gap-2 cursor-pointer p-2 rounded-lg bg-white/5 hover:bg-white/10"
                      @click="toggleCheckbox(resp.task_definition_id, comp.id, opt.value)"
                    >
                      <SpatialCheckbox :model-value="isCheckboxChecked(resp.task_definition_id, comp.id, opt.value)" />
                      <span class="text-xs font-bold text-white/90">{{ opt.label }}</span>
                    </div>
                  </div>

                  <div v-else-if="comp.component_type === 'image_upload' || comp.component_type === 'image'">
                    <SpatialImageUpload />
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div v-else class="p-4 text-center text-xs font-bold text-white/40 border border-dashed border-white/10 rounded-2xl">
            لا توجد مهام يومية مسندة لهذا الموقع.
          </div>
        </div>

        <!-- SECTION B: TRIGGERED ON-DEMAND TASKS (DISPLAYED ABOVE ON-DEMAND DROPDOWN) -->
        <div class="space-y-4 pt-2">
          <div v-if="activeOnDemandTasks.length > 0" class="space-y-4">
            <div class="border-b border-amber-500/20 pb-2">
              <h4 class="text-xs font-black text-amber-400 uppercase tracking-wider flex items-center gap-1.5">
                <span>⚡ المهام المضافة عند الحاجة ({{ activeOnDemandTasks.length }})</span>
              </h4>
            </div>

            <div
              v-for="resp in activeOnDemandTasks"
              :key="resp.id"
              class="p-5 rounded-2xl bg-amber-950/30 border border-amber-500/30 space-y-4"
            >
              <div class="space-y-1">
                <SpatialStatusPill type="pending">
                  مهمة إضافية عند الحاجة
                </SpatialStatusPill>
                <h5 class="text-sm font-black text-white mt-1">
                  {{ getTaskDef(resp)?.title || 'مهمة عند الحاجة' }}
                </h5>
              </div>

              <div class="space-y-3 pt-2 border-t border-amber-500/20">
                <div
                  v-for="comp in getVisibleComponentsForTask(resp)"
                  :key="comp.id"
                  class="p-3.5 rounded-xl bg-white/5 border border-white/10 space-y-1.5"
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

          <!-- ON-DEMAND TASK DROPDOWN TRIGGER (LOCATED AT THE BOTTOM OF THE TASK LIST) -->
          <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 space-y-3">
            <label class="text-xs font-black text-amber-300 block">إضافة مهمة حسب الحاجة لهذا الموقع:</label>
            <div class="flex flex-col sm:flex-row gap-2 items-center">
              <div class="w-full">
                <SpatialDropdown
                  v-model="localOnDemandTaskId"
                  placeholder="اختر مهمة عند الحاجة لإضافتها..."
                  :options="formattedOnDemandOptions"
                  :searchable="true"
                />
              </div>
              <SpatialButton
                variant="amber"
                size="md"
                class="shrink-0 w-full sm:w-auto"
                :disabled="!localOnDemandTaskId || loading"
                @click="submitTriggerOnDemand"
              >
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>إضافة المهمة</span>
              </SpatialButton>
            </div>
          </div>
        </div>

      </div>

    </div>

    <!-- FOOTER ACTIONS -->
    <template #footer>
      <div v-if="visit" class="flex flex-col sm:flex-row items-center justify-between gap-3 w-full">
        <SpatialButton variant="ghost" @click="$emit('close')">
          إغلاق
        </SpatialButton>

        <div class="flex items-center gap-2">
          <SpatialButton variant="secondary" :disabled="loading" @click="submitSave(false)">
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
            </svg>
            <span>حفظ مسودة</span>
          </SpatialButton>

          <SpatialButton variant="primary" :disabled="loading" @click="submitSave(true)">
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>اعتماد وإنهاء الزيارة</span>
          </SpatialButton>
        </div>
      </div>

      <div v-else class="flex justify-end">
        <SpatialButton variant="ghost" @click="$emit('close')">
          إغلاق
        </SpatialButton>
      </div>
    </template>
  </SpatialModal>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import SpatialModal from '@/Components/Spatial/SpatialModal.vue';
import SpatialButton from '@/Components/Spatial/SpatialButton.vue';
import SpatialInput from '@/Components/Spatial/SpatialInput.vue';
import SpatialDropdown from '@/Components/Spatial/SpatialDropdown.vue';
import SpatialCheckbox from '@/Components/Spatial/SpatialCheckbox.vue';
import SpatialImageUpload from '@/Components/Spatial/SpatialImageUpload.vue';
import SpatialStatusPill from '@/Components/Spatial/SpatialStatusPill.vue';

const props = defineProps({
  isOpen: { type: Boolean, default: false },
  visit: { type: Object, default: null },
  availableSites: { type: Array, default: () => [] },
  availableOnDemandTasks: { type: Array, default: () => [] },
  loading: { type: Boolean, default: false },
});

const emit = defineEmits(['close', 'open-visit', 'trigger-ondemand', 'save-responses']);

const localSiteId = ref(null);
const localNotes = ref('');
const localOnDemandTaskId = ref(null);
const formValues = ref({});

const modalTitle = computed(() => {
  if (!props.visit) return 'بدء زيارة موقع جديد';
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

const getTaskDef = (resp) => {
  return resp?.task_definition || resp?.taskDefinition || null;
};

const activeDailyTasks = computed(() => {
  if (!props.visit || !props.visit.task_responses) return [];
  return props.visit.task_responses.filter(
    (r) => getTaskDef(r)?.task_type === 'daily'
  );
});

const activeOnDemandTasks = computed(() => {
  if (!props.visit || !props.visit.task_responses) return [];
  return props.visit.task_responses.filter(
    (r) => getTaskDef(r)?.task_type === 'on_demand'
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
  const taskDef = getTaskDef(resp);
  if (!taskDef || !taskDef.components) return [];
  return taskDef.components.filter((comp) => {
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

const submitOpenVisit = () => {
  if (!localSiteId.value) return;
  emit('open-visit', { site_id: localSiteId.value, notes: localNotes.value });
};

const submitTriggerOnDemand = () => {
  if (!localOnDemandTaskId.value || !props.visit) return;
  emit('trigger-ondemand', { visit_id: props.visit.id, task_definition_id: localOnDemandTaskId.value });
  localOnDemandTaskId.value = null;
};

const submitSave = (completeVisit = false) => {
  if (!props.visit) return;
  const responses = (props.visit.task_responses || []).map((resp) => {
    const valuesObj = {};
    const taskDef = getTaskDef(resp);
    if (taskDef && taskDef.components) {
      taskDef.components.forEach((comp) => {
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

  emit('save-responses', {
    visit_id: props.visit.id,
    responses,
    complete_visit: completeVisit,
  });
};
</script>
