<template>
  <SpatialModal
    :show="isOpen"
    :title="isEdit ? 'تعديل بيانات إجازة الاستشاري' : 'تسجيل إجازة استشاري ميداني'"
    @close="handleClose"
  >
    <form @submit.prevent="handleSubmit" class="space-y-5 text-right">
      <!-- Consultant Selection Dropdown -->
      <SpatialDropdown
        v-model="form.consultant_id"
        label="اختر الاستشاري الميداني *"
        placeholder="اختر استشاري..."
        :options="consultantOptions"
        :error="errors.consultant_id"
        required
      />

      <!-- Dates Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <SpatialInput
          v-model="form.start_date"
          type="date"
          label="تاريخ بدء الإجازة *"
          :error="errors.start_date"
          required
        />

        <SpatialInput
          v-model="form.end_date"
          type="date"
          label="تاريخ نهاية الإجازة *"
          :error="errors.end_date"
          required
        />
      </div>

      <!-- Reason -->
      <SpatialInput
        v-model="form.reason"
        label="سبب الإجازة"
        placeholder="مثال: إجازة سنوية، إجازة مرضية، طارئ عائلي"
        :error="errors.reason"
      />

      <!-- BR-015 Info Banner -->
      <div class="p-3.5 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-600 dark:text-amber-400 text-xs font-bold leading-relaxed flex items-start gap-2">
        <span class="text-base shrink-0">⚠️</span>
        <span>تنبيه (BR-015): سيتم تحديث الحالة التشغيلية للاستشاري تلقائياً إلى <strong>"في إجازة"</strong> خلال هذه الفترة لحماية نسب أداء الميدان.</span>
      </div>

      <!-- Notes -->
      <div class="space-y-1.5">
        <label class="block text-xs font-black text-slate-700 dark:text-white/90">ملاحظات الإدارة</label>
        <textarea
          v-model="form.notes"
          rows="2"
          class="spatial-input w-full p-3 rounded-2xl text-xs font-bold resize-none bg-transparent"
          placeholder="أدخل أي ملاحظات إضافية..."
        ></textarea>
      </div>

      <div class="pt-4 flex items-center justify-end gap-3">
        <SpatialButton
          type="button"
          variant="secondary"
          @click="handleClose"
        >
          إلغاء
        </SpatialButton>
        <SpatialButton
          type="submit"
          variant="primary"
          :loading="loading"
        >
          {{ isEdit ? 'تحديث الإجازة' : 'تسجيل الإجازة وتحديث الحالة' }}
        </SpatialButton>
      </div>
    </form>
  </SpatialModal>
</template>

<script setup>
import { reactive, watch, computed } from 'vue';
import SpatialModal from '@/Components/Spatial/SpatialModal.vue';
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
  leaveData: {
    type: Object,
    default: null,
  },
  activeConsultants: {
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

const consultantOptions = computed(() => {
  return props.activeConsultants.map(c => ({
    label: `${c.full_name} (${c.employee_number})`,
    value: c.id,
  }));
});

function getLocalDateString() {
  const d = new Date();
  return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
}

const todayStr = getLocalDateString();

const form = reactive({
  id: null,
  consultant_id: null,
  start_date: todayStr,
  end_date: todayStr,
  reason: '',
  notes: '',
});

watch(() => props.isOpen, (newVal) => {
  if (newVal) {
    if (props.isEdit && props.leaveData) {
      form.id = props.leaveData.id;
      form.consultant_id = props.leaveData.consultant_id || (props.activeConsultants.length > 0 ? props.activeConsultants[0].id : null);
      form.start_date = props.leaveData.start_date ? String(props.leaveData.start_date).split('T')[0] : todayStr;
      form.end_date = props.leaveData.end_date ? String(props.leaveData.end_date).split('T')[0] : todayStr;
      form.reason = props.leaveData.reason || '';
      form.notes = props.leaveData.notes || '';
    } else {
      form.id = null;
      form.consultant_id = props.activeConsultants.length > 0 ? props.activeConsultants[0].id : null;
      form.start_date = todayStr;
      form.end_date = todayStr;
      form.reason = '';
      form.notes = '';
    }
  }
});

function handleClose() {
  emit('close');
}

function handleSubmit() {
  emit('submit', { ...form });
}
</script>
