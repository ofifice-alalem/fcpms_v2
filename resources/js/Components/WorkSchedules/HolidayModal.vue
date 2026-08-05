<template>
  <SpatialModal
    :show="isOpen"
    :title="isEdit ? 'تعديل بيانات العطلة الرسمية' : 'تسجيل عطلة رسمية جديدة (نطاق تواريخ)'"
    @close="handleClose"
  >
    <form @submit.prevent="handleSubmit" class="space-y-5 text-right">
      <SpatialInput
        v-model="form.name"
        label="اسم المناسبة / العطلة الرسمية *"
        placeholder="مثال: عيد الفطر المبارك، رأس السنة الهجرية"
        :error="errors.name"
        required
      />

      <!-- Date Range (Start Date & End Date) -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <SpatialInput
          v-model="form.start_date"
          type="date"
          label="تاريخ بدء العطلة *"
          :error="errors.start_date"
          required
        />

        <SpatialInput
          v-model="form.end_date"
          type="date"
          label="تاريخ نهاية العطلة *"
          :error="errors.end_date"
          required
        />
      </div>

      <div class="space-y-1.5">
        <label class="block text-xs font-black text-slate-700 dark:text-white/90">ملاحظات وقرارات رسمية</label>
        <textarea
          v-model="form.notes"
          rows="3"
          class="spatial-input w-full p-3.5 rounded-2xl text-xs font-bold resize-none bg-transparent"
          placeholder="أدخل أي قرارات إدارية مرتبطة بالعطلة..."
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
          {{ isEdit ? 'تحديث العطلة الرسمية' : 'حفظ العطلة الرسمية' }}
        </SpatialButton>
      </div>
    </form>
  </SpatialModal>
</template>

<script setup>
import { reactive, watch } from 'vue';
import SpatialModal from '@/Components/Spatial/SpatialModal.vue';
import SpatialInput from '@/Components/Spatial/SpatialInput.vue';
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
  holidayData: {
    type: Object,
    default: null,
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

const todayStr = new Date().toISOString().substring(0, 10);

const form = reactive({
  id: null,
  name: '',
  start_date: todayStr,
  end_date: todayStr,
  notes: '',
});

watch(() => props.isOpen, (newVal) => {
  if (newVal) {
    if (props.isEdit && props.holidayData) {
      form.id = props.holidayData.id;
      form.name = props.holidayData.name || '';
      form.start_date = props.holidayData.start_date ? String(props.holidayData.start_date).substring(0, 10) : (props.holidayData.holiday_date ? String(props.holidayData.holiday_date).substring(0, 10) : todayStr);
      form.end_date = props.holidayData.end_date ? String(props.holidayData.end_date).substring(0, 10) : form.start_date;
      form.notes = props.holidayData.notes || '';
    } else {
      form.id = null;
      form.name = '';
      form.start_date = todayStr;
      form.end_date = todayStr;
      form.notes = '';
    }
  }
});

watch(() => form.start_date, (newStart) => {
  if (newStart && (!form.end_date || form.end_date < newStart)) {
    form.end_date = newStart;
  }
});

function handleClose() {
  emit('close');
}

function handleSubmit() {
  emit('submit', { ...form });
}
</script>
