<template>
  <SpatialModal
    :is-open="isOpen"
    title="تأكيد أرشفة وحذف المهمة الميدانية"
    max-width="md"
    @close="$emit('close')"
  >
    <div v-if="task" class="space-y-4">
      <div class="p-4 rounded-2xl bg-red-500/10 border border-red-500/20 flex items-start gap-3">
        <div class="p-2 rounded-xl bg-red-500/20 text-red-500 shrink-0 mt-0.5">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
          </svg>
        </div>
        <div>
          <h4 class="text-xs font-black text-red-600 dark:text-red-400">
            تنبيه حماية سلامة سجلات الميدان (BR-031)
          </h4>
          <p class="text-xs font-bold text-slate-700 dark:text-white/80 mt-1">
            هل أنت متأكد من أرشفة وحذف المهمة <span class="font-black underline text-slate-900 dark:text-white">{{ task.title }}</span>؟
          </p>
          <p class="text-[11px] font-bold text-slate-500 dark:text-white/60 mt-1">
            سيتم الاحتفاظ بالإجابات والمرفقات السابقة نهائياً في قاعدة البيانات واستبعاد المهمة من التكليفات المستقبلية.
          </p>
        </div>
      </div>
    </div>

    <template #footer>
      <div class="flex justify-end gap-3">
        <SpatialButton variant="ghost" @click="$emit('close')">
          إلغاء
        </SpatialButton>
        <SpatialButton
          variant="danger"
          :loading="loading"
          @click="$emit('confirm')"
        >
          أرشفة وحذف المهمة
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
  task: {
    type: Object,
    default: null,
  },
  loading: {
    type: Boolean,
    default: false,
  },
});

defineEmits(['close', 'confirm']);
</script>
