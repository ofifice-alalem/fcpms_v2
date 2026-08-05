<template>
  <SpatialModal
    :is-open="isOpen"
    title="⚠️ إلغاء الزيارة الميدانية المعلقة"
    max-width="md"
    @close="$emit('close')"
  >
    <div class="space-y-4 text-right py-2">
      <div class="w-12 h-12 rounded-2xl bg-red-500/15 border border-red-500/20 text-red-500 flex items-center justify-center text-xl mx-auto">
        🗑️
      </div>

      <div class="text-center space-y-1">
        <h3 class="text-base font-black text-slate-900 dark:text-white">
          هل أنت تأكد من إلغاء هذه الزيارة الميدانية؟
        </h3>
        <p class="text-xs text-slate-500 dark:text-white/60 leading-relaxed font-bold">
          سيتم مسح سجل الزيارة الجارية المسجلة بحالة (قيد التنفيذ) وإتاحة فتح موقع جديد. لا يمكن التراجع عن هذا الإجراء.
        </p>
      </div>
    </div>

    <template #footer>
      <div class="flex justify-end gap-2 w-full">
        <SpatialButton variant="ghost" @click="$emit('close')">
          تراجع
        </SpatialButton>
        <SpatialButton variant="danger" :disabled="loading" @click="$emit('confirm')">
          {{ loading ? 'جاري الإلغاء...' : 'تأكيد إغلاق وإلغاء الزيارة' }}
        </SpatialButton>
      </div>
    </template>
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
  loading: {
    type: Boolean,
    default: false,
  },
});

defineEmits(['close', 'confirm']);
</script>
