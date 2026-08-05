<template>
  <SpatialModal
    :is-open="isOpen"
    title="إلغاء الزيارة الميدانية المعلقة"
    max-width="md"
    @close="$emit('close')"
  >
    <div class="space-y-4 text-right py-2 dir-rtl">
      <div class="w-12 h-12 rounded-2xl bg-red-500/15 border border-red-500/20 text-red-500 flex items-center justify-center mx-auto">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
        </svg>
      </div>

      <div class="text-center space-y-1">
        <h3 class="text-base font-black text-slate-900 dark:text-white">
          هل أنت تأكد من إلغاء أو مسح هذه الزيارة الميدانية؟
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
