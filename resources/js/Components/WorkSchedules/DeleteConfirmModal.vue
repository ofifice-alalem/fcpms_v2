<template>
  <SpatialModal
    :show="isOpen"
    :title="title"
    @close="handleClose"
  >
    <div class="space-y-5 text-right">
      <p class="text-sm font-bold text-slate-700 dark:text-white/90 leading-relaxed">
        {{ message }}
      </p>

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

defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  title: {
    type: String,
    default: 'تأكيد الحذف',
  },
  message: {
    type: String,
    default: 'هل أنت تأكد من رغبتك في إتمام عملية الحذف؟',
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
