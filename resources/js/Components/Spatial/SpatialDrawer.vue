<template>
  <Teleport to="body">
    <div
      v-if="show"
      @click="$emit('close')"
      class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[9998]"
    ></div>
    <div
      class="fixed top-0 right-0 h-full w-[480px] max-w-[90vw] bg-gradient-to-br from-[#181824] to-[#0f111a] border-l border-white/12 z-[9999] p-6 shadow-2xl flex flex-col transition-transform duration-350"
      :class="show ? 'translate-x-0' : 'translate-x-full'"
      v-if="show"
    >
      <div class="flex items-center justify-between border-b border-white/10 pb-4 mb-6">
        <h3 class="text-xl font-black flex items-center gap-2 text-white">
          <span class="w-2.5 h-2.5 rounded-full bg-primary"></span>
          {{ title }}
        </h3>
        <button
          @click="$emit('close')"
          class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 transition-all flex items-center justify-center text-white"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
      <div class="flex-1 overflow-y-auto custom-scroll space-y-5">
        <slot />
      </div>
    </div>
  </Teleport>
</template>

<script setup>
defineProps({
  show: Boolean,
  title: String,
});

defineEmits(['close']);
</script>
