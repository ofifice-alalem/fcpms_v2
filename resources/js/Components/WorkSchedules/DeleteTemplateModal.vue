<template>
  <SpatialModal
    :show="isOpen"
    title="تأكيد حذف قالب الدوام"
    @close="handleClose"
  >
    <div v-if="template" class="space-y-5 text-right">
      <p class="text-sm font-bold text-slate-700 dark:text-white/90 leading-relaxed">
        هل أنت تأكد من رغبتك في حذف قالب الدوام <span class="font-black text-red-500">{{ template.name }}</span>؟
      </p>

      <div class="p-4 rounded-2xl bg-amber-500/15 border border-amber-500/30 text-amber-600 dark:text-amber-400 text-xs font-bold leading-relaxed">
        ⚠️ تنبيه (BR-006): يشترط ألا يكون هذا القالب مسنداً حالياً لأي استشاريين نشطين في النظام قبل إتمام عملية الحذف.
      </div>

      <div class="pt-2 flex items-center justify-end gap-3">
        <SpatialButton
          type="button"
          variant="secondary"
          @click="handleClose"
        >
          إلغاء
        </SpatialButton>
        <SpatialButton
          type="button"
          variant="danger"
          :loading="loading"
          @click="handleConfirm"
        >
          تأكيد الحذف
        </SpatialButton>
      </div>
    </div>
  </SpatialModal>
</template>

<script setup>
import SpatialModal from '@/Components/Spatial/SpatialModal.vue';
import SpatialButton from '@/Components/Spatial/SpatialButton.vue';

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  template: {
    type: Object,
    default: null,
  },
  loading: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['close', 'confirm']);

function handleClose() {
  emit('close');
}

function handleConfirm() {
  emit('confirm');
}
</script>
