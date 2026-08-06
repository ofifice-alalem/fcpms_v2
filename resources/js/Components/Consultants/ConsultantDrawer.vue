<template>
  <SpatialDrawer
    :is-open="isOpen"
    :title="isEdit ? `تعديل بيانات الاستشاري: ${form.full_name}` : 'إضافة استشاري ميداني جديد'"
    width="w-full sm:w-[480px]"
    @close="handleClose"
  >
    <form @submit.prevent="handleSubmit" class="space-y-5 py-2">
      <!-- Frozen Employee Number (in Edit Mode) -->
      <div v-if="isEdit" class="p-3.5 rounded-2xl bg-blue-500/10 border border-blue-500/20 text-xs font-bold space-y-1">
        <div class="text-blue-600 dark:text-blue-400 font-mono">الرقم الوظيفي المرجعي (BR-003):</div>
        <div class="text-slate-900 dark:text-white font-black text-sm font-mono">{{ form.employee_number }}</div>
        <div class="text-[11px] text-slate-500 dark:text-white/50">الرقم الوظيفي مجمّد ولا يمكن تعديله برمجياً للحفاظ على سلامة المستندات.</div>
      </div>

      <!-- Full Name -->
      <SpatialInput
        v-model="form.full_name"
        label="الاسم الكامل للاستشاري *"
        placeholder="أدخل الاسم الرباعي"
        :error="errors.full_name"
        required
      />

      <!-- Email -->
      <SpatialInput
        v-model="form.email"
        type="email"
        label="البريد الإلكتروني لحساب المستخدم *"
        placeholder="ahmed.salem@fcpms.ly"
        :error="errors.email"
        required
      />

      <!-- Username -->
      <SpatialInput
        v-model="form.username"
        label="اسم المستخدم للدخول (Username)"
        placeholder="أدخل اسم المستخدم (اتركه فارغاً للتوليد التلقائي)"
        :error="errors.username"
      />

      <!-- Password -->
      <SpatialInput
        v-model="form.password"
        type="password"
        :label="isEdit ? 'كلمة المرور الجديدة' : 'كلمة المرور لتسجيل الدخول'"
        :placeholder="isEdit ? 'اتركه فارغاً لعدم تغيير كلمة المرور الحالية' : 'اتركه فارغاً للاعتماد على كلمة المرور الافتراضية Password123!'"
        :error="errors.password"
      />

      <!-- Phone Number -->
      <SpatialInput
        v-model="form.phone"
        label="رقم الهاتف"
        placeholder="091-234-5678"
        :error="errors.phone"
      />

      <!-- Specialization -->
      <SpatialInput
        v-model="form.specialization"
        label="التخصص الميداني"
        placeholder="مثال: هندسة مدنية، شبكات، صيانة"
        :error="errors.specialization"
      />

      <!-- Hire Date -->
      <SpatialInput
        v-model="form.hire_date"
        type="date"
        label="تاريخ التعيين"
        :error="errors.hire_date"
      />

      <!-- Work Schedule Template Dropdown -->
      <SpatialDropdown
        v-model="form.work_schedule_template_id"
        label="قالب دوام العمل"
        placeholder="اختر قالب الدوام"
        :options="scheduleOptions"
        :error="errors.work_schedule_template_id"
      />

      <!-- Employment Status Dropdown -->
      <SpatialDropdown
        v-model="form.employment_status"
        label="حالة التوظيف التشغيلية"
        placeholder="اختر حالة التوظيف"
        :options="statusOptions"
        :error="errors.employment_status"
      />

      <!-- Notes -->
      <div class="space-y-1.5">
        <label class="block text-xs font-black text-slate-700 dark:text-white/90">ملاحظات إضافية</label>
        <textarea
          v-model="form.notes"
          rows="3"
          class="spatial-input w-full p-3 rounded-2xl text-xs font-bold resize-none"
          placeholder="أدخل أي ملاحظات تشغيلية حول الاستشاري..."
        ></textarea>
      </div>

      <!-- Actions -->
      <div class="pt-4 flex items-center gap-3">
        <SpatialButton
          type="submit"
          variant="primary"
          class="w-full h-14 font-black"
          :loading="loading"
        >
          {{ isEdit ? 'حفظ التغييرات' : 'حفظ الاستشاري الجديد' }}
        </SpatialButton>
        <SpatialButton
          type="button"
          variant="ghost"
          class="w-auto h-14 px-6"
          @click="handleClose"
        >
          إلغاء
        </SpatialButton>
      </div>
    </form>
  </SpatialDrawer>
</template>

<script setup>
import { reactive, watch, computed } from 'vue';
import SpatialDrawer from '@/Components/Spatial/SpatialDrawer.vue';
import SpatialInput from '@/Components/Spatial/SpatialInput.vue';
import SpatialDropdown from '@/Components/Spatial/SpatialDropdown.vue';
import SpatialButton from '@/Components/Spatial/SpatialButton.vue';

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  isEdit: {
    type: Boolean,
    default: false,
  },
  consultantData: {
    type: Object,
    default: null,
  },
  workScheduleTemplates: {
    type: Array,
    default: () => [],
  },
  errors: {
    type: Object,
    default: () => ({}),
  },
  loading: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['close', 'submit']);

const form = reactive({
  id: null,
  employee_number: '',
  full_name: '',
  email: '',
  username: '',
  password: '',
  phone: '',
  specialization: '',
  hire_date: '',
  work_schedule_template_id: null,
  employment_status: 'active',
  notes: '',
});

const statusOptions = [
  { label: 'نشط (Active)', value: 'active' },
  { label: 'موقوف مؤقتاً (Suspended)', value: 'suspended' },
  { label: 'في إجازة (Vacation)', value: 'vacation' },
];

const scheduleOptions = computed(() => {
  return props.workScheduleTemplates.map(t => ({
    label: t.name,
    value: t.id,
  }));
});

watch(() => props.consultantData, (newVal) => {
  if (newVal) {
    form.id = newVal.id;
    form.employee_number = newVal.employee_number || '';
    form.full_name = newVal.full_name || '';
    form.email = newVal.user?.email || newVal.email || '';
    form.username = newVal.user?.username || newVal.username || '';
    form.password = '';
    form.phone = newVal.phone || '';
    form.specialization = newVal.specialization || '';
    form.hire_date = newVal.hire_date ? newVal.hire_date.substring(0, 10) : '';
    form.work_schedule_template_id = newVal.work_schedule_template_id || null;
    form.employment_status = newVal.employment_status?.value || newVal.employment_status || 'active';
    form.notes = newVal.notes || '';
  } else {
    resetForm();
  }
}, { immediate: true });

function resetForm() {
  form.id = null;
  form.employee_number = '';
  form.full_name = '';
  form.email = '';
  form.username = '';
  form.password = '';
  form.phone = '';
  form.specialization = '';
  form.hire_date = new Date().toISOString().substring(0, 10);
  form.work_schedule_template_id = null;
  form.employment_status = 'active';
  form.notes = '';
}

function handleClose() {
  emit('close');
}

function handleSubmit() {
  emit('submit', { ...form });
}
</script>
