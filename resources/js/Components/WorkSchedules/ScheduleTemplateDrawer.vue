<template>
  <SpatialDrawer
    :is-open="isOpen"
    :title="isEdit ? `تعديل قالب الدوام: ${form.name}` : 'إضافة قالب دوام جديد'"
    width="w-full sm:w-[500px]"
    @close="handleClose"
  >
    <form @submit.prevent="handleSubmit" class="space-y-6 py-2">
      <!-- Name Input -->
      <SpatialInput
        v-model="form.name"
        label="اسم قالب الدوام *"
        placeholder="مثال: دوام كامل - 8 ساعات"
        :error="errors.name"
        required
      />

      <!-- Description Textarea -->
      <div class="space-y-1.5">
        <label class="block text-xs font-black text-slate-700 dark:text-white/90">وصف القالب</label>
        <textarea
          v-model="form.description"
          rows="3"
          class="spatial-input w-full p-3.5 rounded-2xl text-xs font-bold resize-none bg-transparent"
          placeholder="أدخل توضيح تفصيلي لساعات وضوابط القالب..."
        ></textarea>
      </div>

      <!-- Is Default Switch Container with Spatial UI styling -->
      <div
        @click="form.is_default = !form.is_default"
        :class="[
          'p-4 rounded-2xl border flex items-center justify-between transition-all cursor-pointer select-none',
          form.is_default
            ? 'bg-primary/10 border-primary/30 text-slate-900 dark:text-white shadow-sm'
            : 'bg-black/5 dark:bg-white/5 border-black/10 dark:border-white/10 text-slate-700 dark:text-white/80'
        ]"
      >
        <div class="flex items-center gap-3">
          <SpatialCheckbox v-model="form.is_default" />
          <div>
            <label class="text-xs font-black text-slate-900 dark:text-white block cursor-pointer">
              تعيين كقالب افتراضي للنظام (BR-007)
            </label>
            <span class="text-[11px] font-bold text-slate-500 dark:text-white/50 block mt-0.5">
              عند التفعيل يتم إلغاء الافتراضية تلقائياً عن القوالب الأخرى.
            </span>
          </div>
        </div>

        <span
          :class="[
            'px-2.5 py-1 rounded-xl text-[10px] font-black border font-mono',
            form.is_default
              ? 'bg-primary text-white border-primary shadow-sm'
              : 'bg-slate-200 dark:bg-white/10 text-slate-500 dark:text-white/50 border-slate-300 dark:border-white/10'
          ]"
        >
          {{ form.is_default ? 'افتراضي' : 'مخصص' }}
        </span>
      </div>

      <!-- Days Matrix Selection -->
      <div class="space-y-2">
        <label class="block text-xs font-black text-slate-700 dark:text-white/90">
          تخصيص أيام العمل الرسمية (BR-008):
        </label>
        <div class="space-y-2">
          <div
            v-for="(day, index) in daysOfWeek"
            :key="day.value"
            :class="[
              'p-3 rounded-2xl border flex items-center justify-between transition-all cursor-pointer select-none',
              form.days[index].is_working_day
                ? 'bg-emerald-500/10 border-emerald-500/30 text-slate-900 dark:text-white'
                : 'bg-black/5 dark:bg-white/5 border-black/10 dark:border-white/10 text-slate-400 dark:text-white/40'
            ]"
            @click="toggleDay(index)"
          >
            <div class="flex items-center gap-3">
              <div
                :class="[
                  'w-7 h-7 rounded-xl flex items-center justify-center font-black text-xs',
                  form.days[index].is_working_day ? 'bg-emerald-500 text-white shadow-md' : 'bg-black/10 dark:bg-white/10 text-slate-400'
                ]"
              >
                {{ index + 1 }}
              </div>
              <span class="font-bold text-xs">{{ day.name }}</span>
            </div>

            <div class="flex items-center gap-2">
              <span
                :class="[
                  'px-3 py-1 rounded-xl text-[11px] font-black border',
                  form.days[index].is_working_day
                    ? 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border-emerald-500/30'
                    : 'bg-slate-500/10 text-slate-400 border-slate-500/20'
                ]"
              >
                {{ form.days[index].is_working_day ? 'يوم عمل رسمـي' : 'عطلة أسبوعية (راحة)' }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="pt-4 flex items-center gap-3">
        <SpatialButton
          type="submit"
          variant="primary"
          class="w-full h-14 font-black"
          :loading="loading"
        >
          {{ isEdit ? 'حفظ التغييرات' : 'حفظ قالب الدوام' }}
        </SpatialButton>
        <SpatialButton
          type="button"
          variant="secondary"
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
import { reactive, watch } from 'vue';
import SpatialDrawer from '@/Components/Spatial/SpatialDrawer.vue';
import SpatialInput from '@/Components/Spatial/SpatialInput.vue';
import SpatialButton from '@/Components/Spatial/SpatialButton.vue';
import SpatialCheckbox from '@/Components/Spatial/SpatialCheckbox.vue';

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  isEdit: {
    type: Boolean,
    default: false,
  },
  templateData: {
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

const daysOfWeek = [
  { value: 0, name: 'الأحـد' },
  { value: 1, name: 'الإثنين' },
  { value: 2, name: 'الثلاثاء' },
  { value: 3, name: 'الأربعاء' },
  { value: 4, name: 'الخميس' },
  { value: 5, name: 'الجمعة' },
  { value: 6, name: 'السبت' },
];

const form = reactive({
  id: null,
  name: '',
  description: '',
  is_default: false,
  days: daysOfWeek.map(d => ({ day_of_week: d.value, is_working_day: d.value >= 0 && d.value <= 4 })),
});

watch(() => props.templateData, (newVal) => {
  if (newVal) {
    form.id = newVal.id;
    form.name = newVal.name || '';
    form.description = newVal.description || '';
    form.is_default = !!newVal.is_default;
    
    // Map days if present
    form.days = daysOfWeek.map(d => {
      const existing = newVal.days?.find(item => item.day_of_week === d.value);
      return {
        day_of_week: d.value,
        is_working_day: existing ? !!existing.is_working_day : (d.value >= 0 && d.value <= 4)
      };
    });
  } else {
    resetForm();
  }
}, { immediate: true });

function resetForm() {
  form.id = null;
  form.name = '';
  form.description = '';
  form.is_default = false;
  form.days = daysOfWeek.map(d => ({ day_of_week: d.value, is_working_day: d.value >= 0 && d.value <= 4 }));
}

function toggleDay(index) {
  form.days[index].is_working_day = !form.days[index].is_working_day;
}

function handleClose() {
  emit('close');
}

function handleSubmit() {
  emit('submit', { ...form });
}
</script>
