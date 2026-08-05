<template>
  <Teleport to="body">
    <div
      v-if="isOpen && task"
      class="fixed inset-0 z-[1000] flex items-center justify-center p-4 sm:p-6"
    >
      <!-- Dark Backdrop Overlay -->
      <div
        @click="$emit('close')"
        class="absolute inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity animate-fade-in"
      ></div>

      <!-- Floating Close Button -->
      <button
        @click="$emit('close')"
        class="absolute top-6 left-6 z-50 w-11 h-11 rounded-2xl bg-white/10 hover:bg-white/20 text-white border border-white/20 flex items-center justify-center transition-all shadow-2xl hover:scale-105 cursor-pointer"
        title="إغلاق المعاينة"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>

      <!-- STANDALONE SMARTPHONE DEVICE MOCKUP FRAME -->
      <div class="relative z-10 w-[380px] sm:w-[410px] h-[800px] max-h-[90vh] rounded-[52px] border-[12px] border-slate-900 dark:border-[#131b2c] bg-slate-950 shadow-2xl ring-1 ring-white/20 flex flex-col overflow-hidden animate-spatial-in select-none">
        
        <!-- Device Notch / Dynamic Island -->
        <div class="bg-slate-900 dark:bg-[#131b2c] pt-2 pb-1 px-6 flex items-center justify-between text-white shrink-0 z-30">
          <span class="text-[11px] font-mono font-black tracking-tight text-white/90">09:41</span>
          
          <!-- Camera Notch -->
          <div class="w-24 h-4 bg-black rounded-full flex items-center justify-end px-2 gap-1.5 shadow-inner">
            <span class="w-2 h-2 rounded-full bg-blue-900/60 ring-1 ring-blue-500/40"></span>
            <span class="w-1.5 h-1.5 rounded-full bg-green-500/80 animate-pulse"></span>
          </div>

          <div class="flex items-center gap-1.5 text-[10px] text-white/80">
            <span>5G</span>
            <span>📶</span>
            <span>🔋</span>
          </div>
        </div>

        <!-- Mobile App Top Bar -->
        <div class="p-4 bg-white/90 dark:bg-[#0c121e]/90 backdrop-blur-xl border-b border-slate-200/80 dark:border-white/10 flex items-center justify-between shrink-0 z-20">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-xl bg-primary/20 flex items-center justify-center text-primary font-black text-sm">
              📋
            </div>
            <div>
              <h3 class="text-xs font-black text-slate-900 dark:text-white line-clamp-1">
                {{ task.title }}
              </h3>
              <div class="flex items-center gap-1.5 text-[10px] text-emerald-600 dark:text-emerald-400 font-bold">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>تطبيق الاستشاري الميداني</span>
              </div>
            </div>
          </div>

          <span
            :class="[
              'px-2 py-0.5 rounded-md text-[10px] font-black shrink-0',
              task.task_type === 'daily'
                ? 'bg-blue-500/15 text-blue-600 dark:text-blue-400 border border-blue-500/20'
                : 'bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-500/20'
            ]"
          >
            {{ task.task_type === 'daily' ? '📅 يومية' : '⚡ عند الطلب' }}
          </span>
        </div>

        <!-- Mobile Screen Scrollable Form Content -->
        <div class="flex-1 overflow-y-auto custom-scroll p-4 space-y-4 bg-slate-50 dark:bg-[#080d16] text-right">
          
          <!-- Task Description Banner -->
          <div v-if="task.description" class="p-3 rounded-2xl bg-white dark:bg-white/5 border border-slate-200/70 dark:border-white/10 text-xs text-slate-600 dark:text-white/70 leading-relaxed font-bold">
            💡 {{ task.description }}
          </div>

          <div class="flex items-center justify-between text-[11px] font-black text-slate-500 dark:text-white/50 px-1">
            <span>عناصر الاستبيان الميداني</span>
            <span class="font-mono text-primary">{{ visibleComponents.length }} حقول تظهر</span>
          </div>

          <!-- Dynamic Adaptive Fields -->
          <div
            v-for="(comp, index) in visibleComponents"
            :key="comp.id || comp.temp_id"
            class="p-4 rounded-2xl bg-white dark:bg-white/5 border border-slate-200/80 dark:border-white/10 shadow-sm transition-all duration-300 space-y-2.5"
          >
            <div class="flex items-center justify-between">
              <label class="text-xs font-black text-slate-900 dark:text-white flex items-center gap-1">
                <span>{{ index + 1 }}. {{ comp.label }}</span>
                <span v-if="comp.is_required" class="text-red-500 font-mono">*</span>
              </label>

              <span v-if="comp.conditional_parent_id || comp.conditional_parent_temp_id" class="px-2 py-0.5 rounded-md bg-purple-500/15 text-purple-600 dark:text-purple-300 text-[9px] font-bold border border-purple-500/20">
                🔗 حقل شرطي
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
                placeholder="اختر الإجابة..."
                :options="getFormattedOptions(comp)"
              />
            </div>

            <!-- 5. Checkbox -->
            <div v-else-if="comp.component_type === 'checkbox'" class="space-y-2 pt-1">
              <div
                v-for="opt in getOptions(comp)"
                :key="opt.value || opt.option_value || opt"
                class="flex items-center gap-2 cursor-pointer p-1.5 rounded-xl bg-slate-50 dark:bg-white/5 hover:bg-slate-100 dark:hover:bg-white/10 transition-colors"
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

            <!-- 6. Image Upload Dropzone -->
            <div v-else-if="comp.component_type === 'image_upload' || comp.component_type === 'image'">
              <SpatialImageUpload />
            </div>
          </div>

          <div v-if="visibleComponents.length === 0" class="p-8 text-center rounded-2xl border border-dashed border-slate-300 dark:border-white/10 text-slate-400 dark:text-white/40 text-xs font-bold space-y-1">
            <p>لا توجد مكونات تظهر حالياً</p>
            <p class="text-[10px] text-slate-400">اختر القيم الشرطية المطلوبة لإظهار الحقول المربوطة.</p>
          </div>

        </div>

        <!-- Mobile Footer Action Bar inside Device -->
        <div class="p-3 bg-white dark:bg-[#0c121e] border-t border-slate-200/80 dark:border-white/10 shrink-0 z-20 space-y-2">
          <SpatialButton variant="primary" size="md" class="w-full justify-center">
            🚀 إرسال التقرير الميداني (معاينة)
          </SpatialButton>

          <!-- Smartphone Home Bar Handle -->
          <div class="w-32 h-1 bg-slate-300 dark:bg-white/20 rounded-full mx-auto mt-1"></div>
        </div>

      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
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
