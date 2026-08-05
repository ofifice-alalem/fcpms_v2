<template>
  <SpatialDrawer
    :is-open="isOpen"
    :title="isEditing ? 'تعديل بنية وتكليفات المهمة الميدانية' : 'بناء مهمة ميدانية وتصاميم الاستبيان'"
    width-class="w-full sm:w-[750px] lg:w-[960px]"
    @close="handleClose"
  >
    <form @submit.prevent="handleSubmit" class="space-y-8">
      <!-- 1. Basic Information Card -->
      <div class="space-y-4">
        <h3 class="text-xs font-black text-slate-500 dark:text-white/50 uppercase tracking-wider flex items-center gap-2">
          <span>1️⃣</span>
          <span>البيانات الأساسية ونوع المهمة التشغيلية (BR-027)</span>
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="space-y-1.5 md:col-span-2">
            <label class="text-xs font-black text-slate-900 dark:text-white block">عنوان المهمة الميدانية</label>
            <SpatialInput
              v-model="form.title"
              placeholder="مثال: تفقد أجهزة السلامة ومطابقة المخزون"
              :error="errors.title"
            />
          </div>

          <div class="space-y-1.5 md:col-span-2">
            <label class="text-xs font-black text-slate-900 dark:text-white block">وصف المهمة ورسالة الاستشاري (اختياري)</label>
            <textarea
              v-model="form.description"
              rows="2"
              placeholder="اكتب توجيهات واضحة للاستشاري عند تنفيذ المهمة..."
              class="w-full p-3 rounded-xl bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 text-slate-900 dark:text-white text-xs font-bold focus:ring-2 focus:ring-primary outline-none transition-all resize-none"
            ></textarea>
          </div>

          <div>
            <SpatialDropdown
              v-model="form.task_type"
              label="نوع وتكرار المهمة"
              :options="taskTypeOptions"
            />
          </div>

          <div class="flex flex-col justify-end">
            <div
              @click="form.is_active = !form.is_active"
              :class="[
                'h-[52px] px-4 rounded-xl border flex items-center justify-between cursor-pointer select-none transition-all',
                form.is_active
                  ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-600 dark:text-emerald-400'
                  : 'bg-black/5 dark:bg-white/5 border-black/10 dark:border-white/10 text-slate-500 dark:text-white/50'
              ]"
            >
              <span class="text-xs font-black">حالة التفعيل التشغيلي</span>
              <SpatialCheckbox v-model="form.is_active" />
            </div>
          </div>
        </div>
      </div>

      <div class="h-px bg-black/10 dark:bg-white/10"></div>

      <!-- 2. Dynamic Component Builder Container -->
      <div class="space-y-4">
        <div class="flex items-center justify-between">
          <h3 class="text-xs font-black text-slate-500 dark:text-white/50 uppercase tracking-wider flex items-center gap-2">
            <span>2️⃣</span>
            <span>تصميم حقول الاستبيان الميداني (Dynamic Form Builder)</span>
          </h3>

          <SpatialButton type="button" variant="secondary" size="sm" @click="addComponent">
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            إضافة حقل جديد
          </SpatialButton>
        </div>

        <!-- Components List -->
        <div class="space-y-4">
          <div
            v-for="(comp, index) in form.components"
            :key="comp.temp_id || comp.id"
            class="p-4 rounded-2xl bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 space-y-4 relative group"
          >
            <!-- Card Header Actions -->
            <div class="flex items-center justify-between pb-3 border-b border-black/5 dark:border-white/5">
              <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-primary/10 text-primary text-xs font-black flex items-center justify-center font-mono">
                  #{{ index + 1 }}
                </span>
                <span class="text-xs font-black text-slate-900 dark:text-white">
                  {{ comp.label || 'حقل جديد غير معنون' }}
                </span>
              </div>

              <div class="flex items-center gap-1.5">
                <button
                  type="button"
                  @click="moveComponent(index, -1)"
                  :disabled="index === 0"
                  class="p-1.5 rounded-lg hover:bg-black/10 dark:hover:bg-white/10 text-slate-500 dark:text-white/60 disabled:opacity-30 cursor-pointer"
                >
                  ↑
                </button>
                <button
                  type="button"
                  @click="moveComponent(index, 1)"
                  :disabled="index === form.components.length - 1"
                  class="p-1.5 rounded-lg hover:bg-black/10 dark:hover:bg-white/10 text-slate-500 dark:text-white/60 disabled:opacity-30 cursor-pointer"
                >
                  ↓
                </button>
                <button
                  type="button"
                  @click="removeComponent(index)"
                  class="p-1.5 rounded-lg hover:bg-red-500/10 text-red-500 cursor-pointer"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                </button>
              </div>
            </div>

            <!-- Field Setup Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
              <div class="md:col-span-2">
                <SpatialInput
                  v-model="comp.label"
                  label="عنوان السؤال / الحقل"
                  placeholder="مثال: هل طفايات الحريق صالحة؟"
                />
              </div>

              <div>
                <SpatialDropdown
                  v-model="comp.component_type"
                  label="نوع عنصر الإدخال (BR-028)"
                  :options="componentTypeOptions"
                />
              </div>

              <div class="md:col-span-2">
                <SpatialInput
                  v-model="comp.placeholder"
                  label="ملاحظات مساعدة / Placeholder"
                  placeholder="مثال: أدخل القيمة المسجلة بالعداد..."
                />
              </div>

              <div>
                <div
                  @click="comp.is_required = !comp.is_required"
                  :class="[
                    'w-full h-[52px] px-3 rounded-xl border flex items-center justify-between cursor-pointer select-none transition-all',
                    comp.is_required
                      ? 'bg-amber-500/10 border-amber-500/30 text-amber-600 dark:text-amber-400'
                      : 'bg-black/5 dark:bg-white/5 border-black/10 dark:border-white/10 text-slate-500 dark:text-white/50'
                  ]"
                >
                  <span class="text-xs font-black">حقل إلزامي *</span>
                  <SpatialCheckbox v-model="comp.is_required" />
                </div>
              </div>
            </div>

            <!-- Dynamic Options Manager (Select / Checkbox) -->
            <div v-if="['select', 'checkbox'].includes(comp.component_type)" class="p-3 rounded-xl bg-black/5 dark:bg-white/5 border border-black/5 dark:border-white/5 space-y-2">
              <div class="flex items-center justify-between">
                <label class="text-[11px] font-black text-slate-700 dark:text-white/80">خيارات القائمة المنسدلة / الاختيار</label>
                <button
                  type="button"
                  @click="addOption(comp)"
                  class="text-[11px] font-black text-primary hover:underline cursor-pointer"
                >
                  + إضافة خيار
                </button>
              </div>

              <div class="space-y-2">
                <div
                  v-for="(opt, optIdx) in comp.options"
                  :key="optIdx"
                  class="flex items-center gap-2"
                >
                  <SpatialInput
                    v-model="opt.label"
                    placeholder="نص الخيار (مثال: مطابق)"
                    class="flex-1"
                  />
                  <SpatialInput
                    v-model="opt.value"
                    placeholder="القيمة المخزنة (yes)"
                    class="w-32 font-mono text-xs"
                  />
                  <button
                    type="button"
                    @click="removeOption(comp, optIdx)"
                    class="p-2 text-red-500 hover:bg-red-500/10 rounded-lg cursor-pointer"
                  >
                    ×
                  </button>
                </div>
              </div>
            </div>

            <!-- Smart Adaptive Form Conditional Logic (BR-030) -->
            <div class="p-3 rounded-xl bg-purple-500/5 border border-purple-500/20 space-y-3">
              <div class="flex items-center justify-between">
                <span class="text-xs font-black text-purple-600 dark:text-purple-400 flex items-center gap-1.5">
                  <span>🔗</span>
                  <span>الربط الشرطي الذكي (Smart Adaptive Rules)</span>
                </span>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-3 items-end">
                <div>
                  <SpatialDropdown
                    v-model="comp.conditional_parent_temp_id"
                    label="يظهر فقط إذا كانت إجابة الحقل التالي:"
                    placeholder="بدون شرط (يظهر دائماً)"
                    :options="getParentOptions(index)"
                  />
                </div>

                <div v-if="comp.conditional_parent_temp_id">
                  <SpatialDropdown
                    v-if="getTriggerOptions(comp.conditional_parent_temp_id).length > 0"
                    v-model="comp.conditional_value"
                    label="القيمة المشروطة المطلوبة لإظهاره:"
                    placeholder="اختر قيمة التفعيل..."
                    :options="getTriggerOptions(comp.conditional_parent_temp_id)"
                  />
                  <SpatialInput
                    v-else
                    v-model="comp.conditional_value"
                    label="القيمة المشروطة المطلوبة لإظهاره:"
                    placeholder="مثال: no أو غير مطابق"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="h-px bg-black/10 dark:bg-white/10"></div>

      <!-- 3. Assignments Matrix (BR-029) -->
      <div class="space-y-4">
        <h3 class="text-xs font-black text-slate-500 dark:text-white/50 uppercase tracking-wider flex items-center gap-2">
          <span>3️⃣</span>
          <span>مصفوفة التكليفات الميدانية للمواضع والاستشاريين (BR-029)</span>
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Sites Assignment -->
          <div class="p-4 rounded-2xl bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 space-y-3">
            <div class="flex items-center justify-between">
              <label class="text-xs font-black text-slate-900 dark:text-white">المواقع الميدانية المسندة</label>
              <span class="text-[10px] font-bold text-slate-500 dark:text-white/50">
                (تركها فارغة = عامة لكافة المواقع)
              </span>
            </div>

            <div class="max-h-48 overflow-y-auto space-y-1.5 pr-1 custom-scroll">
              <div
                v-for="site in sites"
                :key="site.id"
                @click="toggleSiteAssignment(site.id)"
                :class="[
                  'p-2.5 rounded-xl border flex items-center justify-between text-xs font-bold cursor-pointer transition-all select-none',
                  form.site_ids.includes(site.id)
                    ? 'bg-primary/10 border-primary/30 text-slate-900 dark:text-white'
                    : 'bg-black/5 dark:bg-white/5 border-transparent text-slate-600 dark:text-white/70'
                ]"
              >
                <span>{{ site.name }} ({{ site.code }})</span>
                <SpatialCheckbox :model-value="form.site_ids.includes(site.id)" />
              </div>
            </div>
          </div>

          <!-- Consultants Assignment -->
          <div class="p-4 rounded-2xl bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 space-y-3">
            <div class="flex items-center justify-between">
              <label class="text-xs font-black text-slate-900 dark:text-white">الاستشاريين المسندين</label>
              <span class="text-[10px] font-bold text-slate-500 dark:text-white/50">
                (تركها فارغة = متاحة للجميع)
              </span>
            </div>

            <div class="max-h-48 overflow-y-auto space-y-1.5 pr-1 custom-scroll">
              <div
                v-for="consultant in consultants"
                :key="consultant.id"
                @click="toggleConsultantAssignment(consultant.id)"
                :class="[
                  'p-2.5 rounded-xl border flex items-center justify-between text-xs font-bold cursor-pointer transition-all select-none',
                  form.consultant_ids.includes(consultant.id)
                    ? 'bg-primary/10 border-primary/30 text-slate-900 dark:text-white'
                    : 'bg-black/5 dark:bg-white/5 border-transparent text-slate-600 dark:text-white/70'
                ]"
              >
                <span>{{ consultant.full_name }} ({{ consultant.employee_number }})</span>
                <SpatialCheckbox :model-value="form.consultant_ids.includes(consultant.id)" />
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex items-center justify-end gap-3 pt-4 border-t border-black/10 dark:border-white/10">
        <SpatialButton type="button" variant="ghost" @click="handleClose">
          إلغاء
        </SpatialButton>

        <SpatialButton type="submit" variant="primary" :loading="form.processing">
          {{ isEditing ? 'حفظ التعديلات' : 'حفظ وبناء المهمة الميدانية' }}
        </SpatialButton>
      </div>
    </form>
  </SpatialDrawer>
</template>

<script setup>
import { reactive, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import SpatialDrawer from '@/Components/Spatial/SpatialDrawer.vue';
import SpatialInput from '@/Components/Spatial/SpatialInput.vue';
import SpatialDropdown from '@/Components/Spatial/SpatialDropdown.vue';
import SpatialButton from '@/Components/Spatial/SpatialButton.vue';
import SpatialCheckbox from '@/Components/Spatial/SpatialCheckbox.vue';

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  taskToEdit: {
    type: Object,
    default: null,
  },
  sites: {
    type: Array,
    default: () => [],
  },
  consultants: {
    type: Array,
    default: () => [],
  },
});

const emit = defineEmits(['close', 'success']);

const isEditing = computed(() => !!props.taskToEdit);

const taskTypeOptions = [
  { label: '📅 مهمة يومية دورية (Daily)', value: 'daily' },
  { label: '⚡ مهمة عند الطلب (On-Demand)', value: 'on_demand' },
];

const componentTypeOptions = [
  { label: '✏️ نص عادي (Text)', value: 'text' },
  { label: '🔢 رقم (Number)', value: 'number' },
  { label: '📋 قائمة اختيار (Select)', value: 'select' },
  { label: '☑️ مربع اختيار (Checkbox)', value: 'checkbox' },
  { label: '📸 منطقة رفع الإثبات الحية (Image Upload)', value: 'image_upload' },
  { label: '📅 تاريخ (Date)', value: 'date' },
];

const form = useForm({
  title: '',
  description: '',
  task_type: 'daily',
  is_active: true,
  display_order: 0,
  components: [],
  site_ids: [],
  consultant_ids: [],
});

const errors = reactive({});

let nextTempId = 1;

watch(
  () => props.taskToEdit,
  (task) => {
    if (task) {
      form.title = task.title || '';
      form.description = task.description || '';
      form.task_type = task.task_type || 'daily';
      form.is_active = task.is_active !== undefined ? task.is_active : true;
      form.display_order = task.display_order || 0;

      if (task.components && task.components.length > 0) {
        form.components = task.components.map((c) => ({
          id: c.id,
          temp_id: c.id,
          label: c.label || '',
          component_type: c.component_type || 'text',
          placeholder: c.placeholder || '',
          is_required: !!c.is_required,
          conditional_parent_temp_id: c.conditional_parent_id || c.visibility_component_id || null,
          conditional_value: c.conditional_value || null,
          options: c.options
            ? c.options.map((opt) => ({
                label: opt.option_label || opt.label || '',
                value: opt.option_value || opt.value || '',
              }))
            : [],
        }));
      } else {
        form.components = [];
      }

      form.site_ids = task.site_assignments ? task.site_assignments.map((sa) => sa.site_id) : [];
      form.consultant_ids = task.consultant_assignments ? task.consultant_assignments.map((ca) => ca.consultant_id) : [];
    } else {
      resetForm();
    }
  },
  { immediate: true }
);

function resetForm() {
  form.title = '';
  form.description = '';
  form.task_type = 'daily';
  form.is_active = true;
  form.display_order = 0;
  form.components = [];
  form.site_ids = [];
  form.consultant_ids = [];
  addComponent();
}

function addComponent() {
  const tempId = `temp_${nextTempId++}`;
  form.components.push({
    temp_id: tempId,
    label: '',
    component_type: 'text',
    placeholder: '',
    is_required: true,
    conditional_parent_temp_id: null,
    conditional_value: null,
    options: [
      { label: 'مطابق', value: 'yes' },
      { label: 'غير مطابق', value: 'no' },
    ],
  });
}

function removeComponent(index) {
  form.components.splice(index, 1);
}

function moveComponent(index, direction) {
  const targetIndex = index + direction;
  if (targetIndex < 0 || targetIndex >= form.components.length) return;
  const temp = form.components[index];
  form.components[index] = form.components[targetIndex];
  form.components[targetIndex] = temp;
}

function addOption(comp) {
  if (!comp.options) comp.options = [];
  comp.options.push({ label: '', value: '' });
}

function removeOption(comp, optIdx) {
  comp.options.splice(optIdx, 1);
}

function getParentOptions(currentIndex) {
  const parents = form.components.slice(0, currentIndex);
  const options = [{ label: 'بدون شرط (يظهر دائماً)', value: null }];
  parents.forEach((p) => {
    options.push({
      label: `#${p.label || 'حقل غير معنون'}`,
      value: p.temp_id || p.id,
    });
  });
  return options;
}

function getTriggerOptions(parentTempId) {
  if (!parentTempId) return [];
  const parentComp = form.components.find(
    (c) => (c.temp_id || c.id) === parentTempId
  );
  if (!parentComp || !parentComp.options || parentComp.options.length === 0) return [];

  return parentComp.options
    .filter((opt) => opt.value || opt.label)
    .map((opt) => ({
      label: opt.label || opt.value,
      value: opt.value || opt.label,
    }));
}

function toggleSiteAssignment(siteId) {
  const index = form.site_ids.indexOf(siteId);
  if (index > -1) {
    form.site_ids.splice(index, 1);
  } else {
    form.site_ids.push(siteId);
  }
}

function toggleConsultantAssignment(consultantId) {
  const index = form.consultant_ids.indexOf(consultantId);
  if (index > -1) {
    form.consultant_ids.splice(index, 1);
  } else {
    form.consultant_ids.push(consultantId);
  }
}

function handleClose() {
  emit('close');
}

function handleSubmit() {
  if (isEditing.value) {
    form.put(route('admin.tasks.update', props.taskToEdit.id), {
      onSuccess: () => {
        emit('success');
        handleClose();
      },
    });
  } else {
    form.post(route('admin.tasks.store'), {
      onSuccess: () => {
        emit('success');
        handleClose();
      },
    });
  }
}
</script>
