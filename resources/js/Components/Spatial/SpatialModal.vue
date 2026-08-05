<template>
  <Teleport to="body">
    <div
      v-if="isOpen"
      class="fixed inset-0 z-[1000] flex items-center justify-center p-4"
    >
      <div
        @click="closeOnBackdrop && closeModal()"
        class="absolute inset-0 bg-black/60 backdrop-blur-sm"
      ></div>
      <div
        class="relative w-full spatial-modal-card p-6 flex flex-col gap-5 animate-spatial-in"
        :class="maxWidthClass"
      >
        <div class="flex items-center justify-between border-b border-black/10 dark:border-white/10 pb-3">
          <h3 class="font-black text-lg text-slate-900 dark:text-white">{{ title }}</h3>
          <button
            @click="closeModal()"
            class="w-8 h-8 rounded-full bg-black/5 dark:bg-white/10 hover:bg-black/10 dark:hover:bg-white/20 text-slate-700 dark:text-white flex items-center justify-center transition-all text-xs cursor-pointer"
          >
            ✕
          </button>
        </div>
        <div class="space-y-4">
          <slot />
        </div>
        <div v-if="$slots.footer" class="flex gap-3 pt-2 border-t border-black/5 dark:border-white/10">
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

const emit = defineEmits(['close', 'update:show', 'update:modelValue']);

const isOpen = computed(() => props.show || props.modelValue);

const maxWidthClass = computed(() => props.maxWidth);

function closeModal() {
  emit('close');
  emit('update:show', false);
  emit('update:modelValue', false);
}
</script>
