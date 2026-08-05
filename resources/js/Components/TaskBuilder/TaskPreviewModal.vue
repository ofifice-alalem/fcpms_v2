<template>
  <SpatialModal
    :is-open="isOpen"
    title="المعاينة الحية التفاعلية للمهمة الميدانية"
    max-width="2xl"
    @close="$emit('close')"
  >
    <div v-if="task" class="space-y-6">
      <!-- Task Header info -->
      <div class="p-4 rounded-2xl bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 flex items-center justify-between">
        <div>
          <div class="flex items-center gap-2">
            <span
              :class="[
                'px-2.5 py-0.5 rounded-full text-xs font-black',
                task.task_type === 'daily'
                  ? 'bg-blue-500/15 text-blue-600 dark:text-blue-400 border border-blue-500/20'
                  : 'bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-500/20'
              ]"
            >
              {{ task.task_type === 'daily' ? '📅 مهمة يومية دورية' : '⚡ مهمة عند الطلب' }}
            </span>
          </div>
          <h3 class="text-base font-black text-slate-900 dark:text-white mt-1">
            {{ task.title }}
          </h3>
          <p v-if="task.description" class="text-xs text-slate-500 dark:text-white/60 mt-0.5">
            {{ task.description }}
          </p>
        </div>

        <div class="text-right shrink-0">
          <span class="text-[11px] font-bold text-slate-500 dark:text-white/50 block">عدد المكونات</span>
          <span class="font-mono text-xs font-black text-slate-900 dark:text-white">
            {{ task.components ? task.components.length : 0 }} حقول
          </span>
        </div>
      </div>

      <!-- Live Interactive Form Simulation -->
      <div class="space-y-5">
        <h4 class="text-xs font-black text-slate-500 dark:text-white/50 uppercase tracking-wider">
          نموذج الاستبيان الميداني التفاعلي (Smart Adaptive Form)
        </h4>

        <div
          v-for="comp in visibleComponents"
          :key="comp.id || comp.temp_id"
          class="p-4 rounded-2xl bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 transition-all duration-300 space-y-2"
        >
          <div class="flex items-center justify-between">
            <label class="text-xs font-black text-slate-900 dark:text-white flex items-center gap-1.5">
              <span>{{ comp.label }}</span>
              <span v-if="comp.is_required" class="text-red-500 font-mono">*</span>
            </label>

            <span v-if="comp.conditional_parent_id || comp.conditional_parent_temp_id" class="px-2 py-0.5 rounded-md bg-purple-500/15 text-purple-600 dark:text-purple-400 text-[10px] font-bold">
              🔗 حقل مشروط ذكي
            </span>
          </div>

          <!-- Component Input Types -->
          <!-- 1. Text -->
          <div v-if="comp.component_type === 'text'">
            <SpatialInput
              v-model="formValues[getCompKey(comp)]"
              :placeholder="comp.placeholder || 'أدخل النص المطلوب...'"
            />
          </div>

          <!-- 2. Number -->
          <div v-else-if="comp.component_type === 'number'">
            <SpatialInput
              v-model="formValues[getCompKey(comp)]"
              type="number"
              :placeholder="comp.placeholder || 'أدخل القيمة الرقمية...'"
            />
          </div>

          <!-- 3. Date -->
          <div v-else-if="comp.component_type === 'date'">
            <SpatialInput
              v-model="formValues[getCompKey(comp)]"
              type="date"
            />
          </div>

          <!-- 4. Single Select / Choice -->
          <div v-else-if="comp.component_type === 'select' || comp.component_type === 'choice'">
            <SpatialDropdown
              v-model="formValues[getCompKey(comp)]"
              placeholder="اختر إجابة..."
              :options="getFormattedOptions(comp)"
            />
          </div>

          <!-- 5. Checkbox -->
          <div v-else-if="comp.component_type === 'checkbox'" class="space-y-2">
            <div
              v-for="opt in getOptions(comp)"
              :key="opt.value || opt.option_value || opt"
              class="flex items-center gap-2 cursor-pointer"
              @click="toggleCheckboxValue(getCompKey(comp), opt.value || opt.option_value || opt)"
            >
              <SpatialCheckbox
                :model-value="isCheckboxChecked(getCompKey(comp), opt.value || opt.option_value || opt)"
              />
              <span class="text-xs font-bold text-slate-700 dark:text-white/80">
                {{ opt.label || opt.option_label || opt }}
              </span>
            </div>
          </div>

          <!-- 6. Interactive Image Upload Dropzone (BR-028) -->
          <div v-else-if="comp.component_type === 'image_upload' || comp.component_type === 'image'">
            <SpatialImageUpload />
          </div>
        </div>

        <div v-if="visibleComponents.length === 0" class="p-8 text-center rounded-2xl border border-dashed border-black/10 dark:border-white/10 text-slate-500 dark:text-white/50 text-xs font-bold">
          لا توجد مكونات مصممة في هذه المهمة حالياً.
        </div>
      </div>
    </div>

    <template #footer>
      <div class="flex justify-end">
        <SpatialButton variant="ghost" @click="$emit('close')">
          إغلاق المعاينة
        </SpatialButton>
      </div>
    </template>
  </SpatialModal>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import SpatialModal from '@/Components/Spatial/SpatialModal.vue';
import SpatialInput from '@/Components/Spatial/SpatialInput.vue';
import SpatialDropdown from '@/Components/Spatial/SpatialDropdown.vue';
import SpatialCheckbox from '@/Components/Spatial/SpatialCheckbox.vue';
import SpatialImageUpload from '@/Components/Spatial/SpatialImageUpload.vue';
import SpatialButton from '@/Components/Spatial/SpatialButton.vue';

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  task: {
    type: Object,
    default: null,
  },
});

defineEmits(['close']);

const formValues = ref({});

watch(
  () => props.task,
  (newTask) => {
    formValues.value = {};
  },
  { immediate: true }
);

const getCompKey = (comp) => {
  return comp.id ? `comp_${comp.id}` : `temp_${comp.temp_id}`;
};

const getOptions = (comp) => {
  if (!comp.options) return [];
  return comp.options;
};

const getFormattedOptions = (comp) => {
  const opts = getOptions(comp);
  return opts.map((opt) => {
    if (typeof opt === 'string') {
      return { label: opt, value: opt };
    }
    return {
      label: opt.label || opt.option_label || opt.value,
      value: opt.value || opt.option_value,
    };
  });
};

const visibleComponents = computed(() => {
  if (!props.task || !props.task.components) return [];

  const comps = props.task.components;
  return comps.filter((comp) => {
    const parentId = comp.conditional_parent_id || comp.conditional_parent_temp_id;
    if (!parentId) return true;

    const parentComp = comps.find(
      (c) => c.id === parentId || c.temp_id === parentId
    );
    if (!parentComp) return true;

    const parentKey = getCompKey(parentComp);
    const parentVal = formValues.value[parentKey];
    const expectedVal = comp.conditional_value;

    if (!parentVal) return false;

    if (Array.isArray(parentVal)) {
      return parentVal.includes(expectedVal);
    }
    return String(parentVal) === String(expectedVal);
  });
});

const isCheckboxChecked = (compKey, val) => {
  const current = formValues.value[compKey];
  if (Array.isArray(current)) {
    return current.includes(val);
  }
  return false;
};

const toggleCheckboxValue = (compKey, val) => {
  if (!formValues.value[compKey] || !Array.isArray(formValues.value[compKey])) {
    formValues.value[compKey] = [val];
  } else {
    const index = formValues.value[compKey].indexOf(val);
    if (index > -1) {
      formValues.value[compKey].splice(index, 1);
    } else {
      formValues.value[compKey].push(val);
    }
  }
};
</script>
