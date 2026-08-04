<template>
  <Teleport to="body">
    <div
      v-if="show"
      class="fixed inset-0 z-[1000] flex items-center justify-center p-4"
    >
      <div
        @click="closeOnBackdrop && $emit('close')"
        class="absolute inset-0 bg-black/60 backdrop-blur-sm"
      ></div>
      <div
        class="relative w-full spatial-modal-card p-6 flex flex-col gap-5 animate-spatial-in"
        :class="maxWidthClass"
      >
        <div class="flex items-center justify-between border-b border-white/10 pb-3">
          <h3 class="font-black text-lg text-white">{{ title }}</h3>
          <button
            @click="$emit('close')"
            class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all text-xs"
          >
            ✕
          </button>
        </div>
        <div class="space-y-4">
          <slot />
        </div>
        <div v-if="$slots.footer" class="flex gap-3 pt-2">
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

defineEmits(['close']);

const maxWidthClass = computed(() => props.maxWidth);
</script>
