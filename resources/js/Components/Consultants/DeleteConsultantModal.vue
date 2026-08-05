<template>
  <SpatialModal
    :isOpen="isOpen"
    title="حذف سجل الاستشاري"
    maxWidth="md"
    @close="$emit('close')"
  >
    <div class="space-y-5">
      <div class="flex items-center gap-4 p-4 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-400">
        <div class="w-12 h-12 rounded-xl bg-red-500/20 flex items-center justify-center shrink-0 text-xl">
          ⚠️
        </div>
        <div class="space-y-1">
          <h4 class="font-black text-sm text-red-600 dark:text-red-400">تأكيد الحذف الأرشيفي</h4>
          <p class="text-xs font-bold text-slate-600 dark:text-white/70 leading-relaxed">
            هل أنت متأكد من نقل سجل الاستشاري <span class="font-black text-slate-900 dark:text-white">[{{ consultantName }}]</span> إلى الأرشيف؟
          </p>
        </div>
      </div>

      <p class="text-xs text-slate-500 dark:text-white/50 leading-relaxed">
        سيتم تعطيل حساب المستخدم الموازي ونقل السجل إلى الحاوية الأرشيفية عبر SoftDelete. لن يتم إتلاف أو حذف أي زيارات أو تقارير ميدانية تاريخية سابقة مسجلة باسم هذا الاستشاري.
      </p>

      <div class="pt-3 border-t border-slate-200 dark:border-white/10 flex items-center justify-end gap-3">
        <SpatialButton variant="ghost" class="px-5 h-12" @click="$emit('close')">
          إلغاء
        </SpatialButton>
        <SpatialButton variant="danger" class="px-6 h-12 font-black" :loading="loading" @click="$emit('confirm')">
          حذف ونقل للأرشيف
        </SpatialButton>
      </div>
    </div>
  </SpatialModal>
</template>

<script setup>
import SpatialModal from '@/Components/Spatial/SpatialModal.vue';
import SpatialButton from '@/Components/Spatial/SpatialButton.vue';

defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  consultantName: {
    type: String,
    default: '',
  },
  loading: {
    type: Boolean,
    default: false,
  },
});

defineEmits(['close', 'confirm']);
</script>
