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
      </div>

      <!-- MODE A: OPEN NEW SITE VISIT FORM -->
      <SpatialCard v-if="!visit" padding="p-6" class="space-y-6">
        <div class="space-y-1 border-b border-black/10 dark:border-white/10 pb-4">
          <h3 class="text-lg font-black text-slate-900 dark:text-white flex items-center gap-2">
            <span>اختيار الموقع الميداني للزيارة</span>
          </h3>
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
                  {{ getTaskDef(resp)?.title || 'مهمة' }}
                </h4>
                <p v-if="getTaskDef(resp)?.description" class="text-xs text-slate-500 dark:text-white/60 font-bold">
                  {{ getTaskDef(resp)?.description }}
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

                  <div v-else-if="comp.component_type === 'select' || comp.component_type === 'choice'" class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                    <div
                      v-for="opt in getComponentOptions(comp)"
                      :key="opt.value"
                      :class="[
                        'flex items-center justify-between p-3.5 rounded-2xl border-2 transition-all cursor-pointer select-none font-black text-xs',
                        formValues[getTaskKey(resp.task_definition_id, comp.id)] === opt.value
                          ? 'bg-primary/10 dark:bg-primary/20 border-primary text-primary shadow-xs'
                          : 'bg-white dark:bg-slate-800/80 border-slate-200 dark:border-slate-700/80 text-slate-700 dark:text-slate-200 hover:border-slate-300 dark:hover:border-slate-600'
                      ]"
                      @click="selectRadioOption(resp.task_definition_id, comp.id, opt.value)"
                    >
                      <span class="font-black text-xs sm:text-sm">{{ opt.label }}</span>
                      <div
                        :class="[
                          'w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all',
                          formValues[getTaskKey(resp.task_definition_id, comp.id)] === opt.value
                            ? 'border-primary bg-primary'
                            : 'border-slate-300 dark:border-slate-600 bg-transparent'
                        ]"
                      >
                        <div v-if="formValues[getTaskKey(resp.task_definition_id, comp.id)] === opt.value" class="w-2 h-2 rounded-full bg-white"></div>
                      </div>
                    </div>
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
                    <SpatialImageUpload
                      :model-value="formValues[getTaskKey(resp.task_definition_id, comp.id)]"
                      @file-selected="(file) => handleImageSelected(resp.task_definition_id, comp.id, file)"
                      @file-removed="() => handleImageRemoved(resp.task_definition_id, comp.id)"
                    />
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
        <div class="space-y-4 pt-4 border-t border-slate-200/60 dark:border-white/10" id="on-demand-tasks-section">
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
              <div class="space-y-2 border-b border-amber-500/20 pb-3">
                <div class="flex items-center justify-between">
                  <SpatialStatusPill type="pending">
                    مهمة عند الحاجة
                  </SpatialStatusPill>

                  <button
                    type="button"
                    :disabled="isSubmitting"
                    @click="promptRemoveOnDemand(resp.id)"
                    title="مسح المهمة"
                    style="border-radius: 50%;"
                    class="w-9 h-9 sm:w-auto sm:h-auto sm:px-3 sm:py-1.5 border border-red-500/40 text-red-600 dark:text-red-400 bg-red-500/10 hover:bg-red-600 hover:text-white flex items-center justify-center gap-1.5 transition-all text-xs font-bold shrink-0 disabled:opacity-50 sm:!rounded-xl"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span class="hidden sm:inline">مسح المهمة</span>
                  </button>
                </div>

                <h4 class="text-base font-black text-slate-900 dark:text-white block pt-1">
                  {{ getTaskDef(resp)?.title || 'مهمة عند الحاجة' }}
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
                  <div v-else-if="comp.component_type === 'select' || comp.component_type === 'choice'" class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                    <div
                      v-for="opt in getComponentOptions(comp)"
                      :key="opt.value"
                      :class="[
                        'flex items-center justify-between p-3.5 rounded-2xl border-2 transition-all cursor-pointer select-none font-black text-xs',
                        formValues[getTaskKey(resp.task_definition_id, comp.id)] === opt.value
                          ? 'bg-primary/10 dark:bg-primary/20 border-primary text-primary shadow-xs'
                          : 'bg-white dark:bg-slate-800/80 border-slate-200 dark:border-slate-700/80 text-slate-700 dark:text-slate-200 hover:border-slate-300 dark:hover:border-slate-600'
                      ]"
                      @click="selectRadioOption(resp.task_definition_id, comp.id, opt.value)"
                    >
                      <span class="font-black text-xs sm:text-sm">{{ opt.label }}</span>
                      <div
                        :class="[
                          'w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all',
                          formValues[getTaskKey(resp.task_definition_id, comp.id)] === opt.value
                            ? 'border-primary bg-primary'
                            : 'border-slate-300 dark:border-slate-600 bg-transparent'
                        ]"
                      >
                        <div v-if="formValues[getTaskKey(resp.task_definition_id, comp.id)] === opt.value" class="w-2 h-2 rounded-full bg-white"></div>
                      </div>
                    </div>
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
                    <SpatialImageUpload
                      :model-value="formValues[getTaskKey(resp.task_definition_id, comp.id)]"
                      @file-selected="(file) => handleImageSelected(resp.task_definition_id, comp.id, file)"
                      @file-removed="() => handleImageRemoved(resp.task_definition_id, comp.id)"
                    />
                  </div>
                </div>
              </div>
            </SpatialCard>
          </div>

          <!-- ON-DEMAND TASK DROPDOWN TRIGGER (LOCATED AT THE BOTTOM OF THE TASK LIST) -->
          <SpatialCard padding="p-6" class="border-amber-500/30 bg-amber-500/5 space-y-3 shadow-xl relative z-30">
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

        <!-- ACTION BAR (Natural flow at end of form) -->
        <div class="p-4 sm:p-6 rounded-2xl bg-white dark:bg-slate-800/90 border border-slate-200 dark:border-white/10 flex flex-col min-[471px]:flex-row items-stretch min-[471px]:items-center justify-between gap-3 shadow-lg mt-6">
          <SpatialButton variant="ghost" class="w-full min-[471px]:w-auto justify-center" @click="goBack">
            العودة لسجل اليوم
          </SpatialButton>

          <div class="flex flex-col min-[471px]:flex-row items-stretch min-[471px]:items-center gap-2.5 sm:gap-3 w-full min-[471px]:w-auto">
            <SpatialButton variant="secondary" class="w-full min-[471px]:w-auto justify-center" :disabled="isSubmitting" @click="handleSaveResponses(false)">
              <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
              </svg>
              <span>حفظ مسودة</span>
            </SpatialButton>

            <SpatialButton variant="primary" size="lg" class="w-full min-[471px]:w-auto justify-center" :disabled="isSubmitting" @click="handleSaveResponses(true)">
              <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              <span>اعتماد وإنهاء الزيارة</span>
            </SpatialButton>
          </div>
        </div>

      </div>

    </div>

    <DeleteOnDemandTaskModal
      :is-open="isDeleteModalOpen"
      :loading="isSubmitting"
      @close="isDeleteModalOpen = false"
      @confirm="confirmRemoveOnDemand"
    />

    <SpatialToast ref="toastRef" />

    <!-- Mobile Custom Execution Bottom Bar (Exact Native App Nav Bar Style) -->
    <template #mobile-bottom-nav v-if="visit">
      <!-- Item 1: Daily Tasks Progress Ring (Right in RTL) -->
      <div class="relative flex-1 flex flex-col items-center justify-center py-0.5 text-slate-700 dark:text-white/80 active:scale-95 transition-all cursor-pointer">
        <SpatialCircularProgress
          :percentage="dailyCompletionPercentage"
          :size="35"
          :stroke-width="3"
        >
          <span class="text-[9px] font-black font-mono text-slate-900 dark:text-white leading-none">
            {{ completedDailyTasksCount }}/{{ activeDailyTasks.length }}
          </span>
        </SpatialCircularProgress>
        <span class="text-[10px] font-bold tracking-tight mt-1 leading-none">اليومية</span>
      </div>

      <!-- Item 2: Submit Button (CENTER in RTL) - Keeps Top Indicator Pill -->
      <button
        type="button"
        :disabled="isSubmitting"
        @click="handleSaveResponses(true)"
        class="relative flex-1 flex flex-col items-center justify-center py-1 text-emerald-500 dark:text-emerald-400 font-black active:scale-90 transition-all duration-150 cursor-pointer select-none disabled:opacity-50"
      >
        <!-- Active Top Indicator Pill -->
        <div class="absolute -top-1.5 w-7 h-1 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)]"></div>

        <div class="relative">
          <svg class="w-6 h-6 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
          </svg>
        </div>
        <span class="text-[10px] font-black tracking-tight mt-1 leading-none">اعتماد</span>
      </button>

      <!-- Item 3: On-Demand Tasks Counter (Left in RTL) -->
      <div class="relative flex-1 flex flex-col items-center justify-center py-0.5 text-amber-500 active:scale-95 transition-all cursor-pointer">
        <div class="w-[35px] h-[35px] rounded-full border-2 border-amber-500/50 bg-amber-500/10 flex items-center justify-center shadow-2xs">
          <span class="text-[10px] font-black font-mono text-amber-600 dark:text-amber-400 leading-none">
            ⚡ {{ activeOnDemandTasks.length }}
          </span>
        </div>
        <span class="text-[10px] font-bold tracking-tight mt-1 leading-none text-amber-600 dark:text-amber-400">الإضافية</span>
      </div>
    </template>
  </ConsultantLayout>
</template>

<script setup>
import { ref, computed, watch, nextTick } from 'vue';
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
import SpatialCircularProgress from '@/Components/Spatial/SpatialCircularProgress.vue';
import DeleteOnDemandTaskModal from '@/Components/Consultant/DeleteOnDemandTaskModal.vue';

const props = defineProps({
  consultant: { type: Object, required: true },
  dailyRecord: { type: Object, required: true },
  visit: { type: Object, default: null },
  availableSites: { type: Array, default: () => [] },
  availableOnDemandTasks: { type: Array, default: () => [] },
});

const toastRef = ref(null);
const selectedSiteId = ref(null);
const notes = ref('');
const selectedOnDemandTaskId = ref(null);
const isSubmitting = ref(false);
const formValues = ref({});
const isDeleteModalOpen = ref(false);
const taskToDeleteId = ref(null);

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
  const existingTaskDefIds = (props.visit?.task_responses || [])
    .map(r => r.task_definition_id)
    .filter(Boolean);

  return (props.availableOnDemandTasks || [])
    .filter(t => !existingTaskDefIds.includes(t.id))
    .map((t) => ({
      label: `${t.title}`,
      value: t.id,
    }));
});

const getTaskKey = (taskId, compId) => `t_${taskId}_c_${compId}`;

const getTaskDef = (resp) => {
  return resp?.task_definition || resp?.taskDefinition || null;
};

const getComponentOptions = (comp) => {
  if (!comp.options) return [];
  return comp.options.map((opt) => ({
    label: typeof opt === 'string' ? opt : opt.label || opt.option_label || opt.value,
    value: typeof opt === 'string' ? opt : opt.value || opt.option_value,
  }));
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

const completedDailyTasksCount = computed(() => {
  if (!activeDailyTasks.value) return 0;
  return activeDailyTasks.value.filter((resp) => {
    const taskDef = getTaskDef(resp);
    if (!taskDef || !taskDef.components) return false;
    return taskDef.components.some((comp) => {
      const key = getTaskKey(resp.task_definition_id, comp.id);
      const val = formValues.value[key];
      if (Array.isArray(val)) return val.length > 0;
      return val !== undefined && val !== null && val !== '';
    });
  }).length;
});

const dailyCompletionPercentage = computed(() => {
  if (!activeDailyTasks.value || activeDailyTasks.value.length === 0) return 0;
  return Math.min(100, Math.max(0, Math.round((completedDailyTasksCount.value / activeDailyTasks.value.length) * 100)));
});

watch(
  () => props.visit,
  (newVisit) => {
    if (!newVisit || !newVisit.task_responses) {
      return;
    }
    const merged = { ...formValues.value };
    newVisit.task_responses.forEach((resp) => {
      if (resp.values) {
        resp.values.forEach((v) => {
          const key = getTaskKey(resp.task_definition_id, v.task_component_id);
          if (merged[key] === undefined || merged[key] === null || merged[key] === '') {
            try {
              merged[key] = JSON.parse(v.value);
            } catch {
              merged[key] = v.value;
            }
          }
        });
      }
    });
    formValues.value = merged;
  },
  { immediate: true }
);

const prepareResponsesPayload = (completeVisit = false) => {
  if (!props.visit) return [];
  return (props.visit.task_responses || []).map((resp) => {
    const valuesObj = {};
    const taskDef = getTaskDef(resp);
    let hasTaskValues = false;
    if (taskDef && taskDef.components) {
      taskDef.components.forEach((comp) => {
        const key = getTaskKey(resp.task_definition_id, comp.id);
        const val = formValues.value[key];
        if (val !== undefined && val !== null && val !== '') {
          if (Array.isArray(val) && val.length === 0) {
            // empty array
          } else {
            valuesObj[comp.id] = val;
            hasTaskValues = true;
          }
        }
      });
    }
    return {
      task_definition_id: resp.task_definition_id,
      values: valuesObj,
      is_completed: completeVisit && hasTaskValues,
    };
  });
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

const selectRadioOption = (taskId, compId, val) => {
  const key = getTaskKey(taskId, compId);
  formValues.value[key] = val;
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
  if (props.isHistorical && props.selectedDate) {
    router.get('/consultant/daily-visits', { date: props.selectedDate });
  } else {
    router.get('/consultant/daily-visits');
  }
};

const handleOpenSiteVisit = () => {
  if (!selectedSiteId.value) return;
  isSubmitting.value = true;

  let targetDate = props.selectedDate;
  if (!targetDate && props.dailyRecord?.work_date) {
    targetDate = String(props.dailyRecord.work_date).split('T')[0];
  }
  if (!targetDate && typeof window !== 'undefined') {
    const urlParams = new URLSearchParams(window.location.search);
    targetDate = urlParams.get('date');
  }

  const payload = {
    site_id: selectedSiteId.value,
    notes: notes.value,
  };
  if (targetDate) {
    payload.date = String(targetDate).split('T')[0];
  }

  router.post('/consultant/site-visits', payload, {
    onFinish: () => (isSubmitting.value = false),
  });
};

const handleTriggerOnDemand = () => {
  if (!selectedOnDemandTaskId.value || !props.visit) return;
  isSubmitting.value = true;

  // Auto-save existing filled daily tasks responses as draft first
  const responses = prepareResponsesPayload(false);

  router.post(`/consultant/site-visits/${props.visit.id}/save-responses`, {
    responses,
    complete_visit: false,
  }, {
    preserveScroll: true,
    onSuccess: () => {
      // Trigger the on-demand task after saving draft
      router.post(`/consultant/site-visits/${props.visit.id}/trigger-on-demand`, {
        task_definition_id: selectedOnDemandTaskId.value,
      }, {
        preserveScroll: true,
        onSuccess: () => {
          selectedOnDemandTaskId.value = null;
          toastRef.value?.addToast('success', 'تمت إضافة المهمة الإضافية وحفظ الخيارات السابقة بنجاح ⚡');
          nextTick(() => {
            const el = document.getElementById('on-demand-tasks-section');
            if (el) {
              el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
          });
        },
        onFinish: () => (isSubmitting.value = false),
      });
    },
    onError: () => (isSubmitting.value = false),
  });
};

const promptRemoveOnDemand = (responseId) => {
  taskToDeleteId.value = responseId;
  isDeleteModalOpen.value = true;
};

const confirmRemoveOnDemand = () => {
  if (!props.visit || !taskToDeleteId.value) return;
  isSubmitting.value = true;
  router.delete(`/consultant/site-visits/${props.visit.id}/on-demand/${taskToDeleteId.value}`, {
    preserveScroll: true,
    onSuccess: () => {
      isDeleteModalOpen.value = false;
      taskToDeleteId.value = null;
      toastRef.value?.addToast('warning', 'تم حذف المهمة الإضافية 🗑️');
    },
    onFinish: () => (isSubmitting.value = false),
  });
};

const imageFiles = ref({});

const handleImageSelected = (taskId, compId, file) => {
  const key = getTaskKey(taskId, compId);
  imageFiles.value[key] = file;
  formValues.value[key] = file;
};

const handleImageRemoved = (taskId, compId) => {
  const key = getTaskKey(taskId, compId);
  delete imageFiles.value[key];
  delete formValues.value[key];
};

const prepareAttachmentsPayload = () => {
  const attachmentsObj = {};
  if (!props.visit || !props.visit.task_responses) return attachmentsObj;

  props.visit.task_responses.forEach((resp) => {
    const taskDef = getTaskDef(resp);
    if (taskDef && taskDef.components) {
      taskDef.components.forEach((comp) => {
        const key = getTaskKey(resp.task_definition_id, comp.id);
        const file = imageFiles.value[key] || formValues.value[key];
        if (file instanceof File) {
          if (!attachmentsObj[resp.task_definition_id]) {
            attachmentsObj[resp.task_definition_id] = {};
          }
          attachmentsObj[resp.task_definition_id][comp.id] = file;
        }
      });
    }
  });

  return attachmentsObj;
};

const handleSaveResponses = (completeVisit = false) => {
  if (!props.visit) return;
  isSubmitting.value = true;
  const responses = prepareResponsesPayload(completeVisit);
  const attachments = prepareAttachmentsPayload();

  router.post(`/consultant/site-visits/${props.visit.id}/save-responses`, {
    responses,
    attachments,
    complete_visit: completeVisit,
  }, {
    preserveScroll: true,
    onSuccess: () => {
      if (!completeVisit) {
        toastRef.value?.addToast('success', 'تم حفظ الخيارات والمرفقات كمسودة بنجاح 💾');
      }
    },
    onFinish: () => (isSubmitting.value = false),
  });
};
</script>
