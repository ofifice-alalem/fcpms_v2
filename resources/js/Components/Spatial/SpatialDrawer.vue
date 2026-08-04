<template>
  <Teleport to="body">
    <div v-if="show" class="relative z-[9998]">
      <!-- Backdrop Overlay -->
      <transition
        enter-active-class="transition-opacity duration-300 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-300 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          @click="$emit('close')"
          class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[9998]"
        ></div>
      </transition>

      <!-- Drawer Content Sliding Panel -->
      <transition
        enter-active-class="transition-transform duration-350 ease-out"
        enter-from-class="translate-x-full"
        enter-to-class="translate-x-0"
        leave-active-class="transition-transform duration-300 ease-in"
        leave-from-class="translate-x-0"
        leave-to-class="translate-x-full"
      >
        <div
          class="fixed top-0 right-0 h-full w-[480px] max-w-[90vw] bg-white dark:bg-gradient-to-br dark:from-[#181824] dark:to-[#0f111a] border-l border-black/10 dark:border-white/12 z-[9999] p-6 shadow-2xl flex flex-col overflow-hidden"
        >
          <div class="flex items-center justify-between border-b border-black/10 dark:border-white/10 pb-4 mb-6">
            <h3 class="text-xl font-black flex items-center gap-2 text-slate-900 dark:text-white">
              <span class="w-2.5 h-2.5 rounded-full bg-primary"></span>
              {{ title }}
            </h3>
            <button
              @click="$emit('close')"
              class="w-9 h-9 rounded-full bg-black/5 dark:bg-white/10 hover:bg-black/10 dark:hover:bg-white/20 transition-all flex items-center justify-center text-slate-700 dark:text-white"
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
      </transition>
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
