<template>
  <Teleport to="body">
    <div
      v-if="isModalOpen"
      class="fixed inset-0 z-[1000] flex items-center justify-center p-3 sm:p-4 overflow-hidden"
    >
      <!-- Backdrop -->
      <div
        @click="closeOnBackdrop && closeModal()"
        class="absolute inset-0 bg-black/60 backdrop-blur-sm"
      ></div>

      <!-- Modal Card -->
      <div
        class="relative w-full max-h-[90vh] spatial-modal-card p-4 sm:p-6 flex flex-col gap-4 animate-spatial-in shadow-2xl"
        :class="maxWidthClass"
      >
        <!-- Header (Fixed at Top) -->
        <div class="flex items-center justify-between border-b border-black/10 dark:border-white/10 pb-3 shrink-0">
          <h3 class="font-black text-base sm:text-lg text-slate-900 dark:text-white">{{ title }}</h3>
          <button
            @click="closeModal()"
            class="w-8 h-8 rounded-full bg-black/5 dark:bg-white/10 hover:bg-black/10 dark:hover:bg-white/20 text-slate-700 dark:text-white flex items-center justify-center transition-all text-xs cursor-pointer"
          >
            ✕
          </button>
        </div>

        <!-- Content Body (Scrollable) -->
        <div class="space-y-4 overflow-y-auto max-h-[70vh] custom-scroll pr-1 flex-1">
          <slot />
        </div>

        <!-- Footer (Fixed at Bottom) -->
        <div v-if="$slots.footer" class="flex gap-3 pt-3 border-t border-black/5 dark:border-white/10 shrink-0">
          <slot name="footer" />
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  show: Boolean,
  isOpen: Boolean,
  modelValue: Boolean,
  title: String,
  maxWidth: {
    type: String,
    default: 'max-w-md',
  },
  closeOnBackdrop: {
    type: Boolean,
    default: true,
  },
});

const emit = defineEmits(['close', 'update:show', 'update:isOpen', 'update:modelValue']);

const isModalOpen = computed(() => props.show || props.isOpen || props.modelValue);

const maxWidthClass = computed(() => {
  const map = {
    sm: 'max-w-sm',
    md: 'max-w-md',
    lg: 'max-w-lg',
    xl: 'max-w-xl',
    '2xl': 'max-w-2xl',
  };
  return map[props.maxWidth] || props.maxWidth;
});

function closeModal() {
  emit('close');
  emit('update:show', false);
  emit('update:isOpen', false);
  emit('update:modelValue', false);
}
</script>
