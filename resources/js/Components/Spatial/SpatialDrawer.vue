<template>
  <Teleport to="body">
    <!-- Backdrop Overlay Transition -->
    <Transition name="drawer-backdrop">
      <div
        v-if="show"
        @click="$emit('close')"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[9998]"
      ></div>
    </Transition>

    <!-- Drawer Panel Slide Transition -->
    <Transition name="drawer-panel">
      <div
        v-if="show"
        class="fixed top-0 right-0 h-full w-[480px] max-w-[90vw] bg-white dark:bg-gradient-to-br dark:from-[#181824] dark:to-[#0f111a] border-l border-black/10 dark:border-white/12 z-[9999] p-6 shadow-2xl flex flex-col overflow-hidden font-sans"
      >
        <div class="flex items-center justify-between border-b border-black/10 dark:border-white/10 pb-4 mb-6">
          <h3 class="text-xl font-black flex items-center gap-2 text-slate-900 dark:text-white">
            <span class="w-2.5 h-2.5 rounded-full bg-primary"></span>
            {{ title }}
          </h3>
          <button
            @click="$emit('close')"
            class="w-9 h-9 rounded-full bg-black/5 dark:bg-white/10 hover:bg-black/10 dark:hover:bg-white/20 transition-all flex items-center justify-center text-slate-700 dark:text-white cursor-pointer"
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
    </Transition>
  </Teleport>
</template>

<script setup>
defineProps({
  show: Boolean,
  title: String,
});

defineEmits(['close']);
</script>

<style scoped>
.drawer-backdrop-enter-active,
.drawer-backdrop-leave-active {
  transition: opacity 0.3s ease;
}
.drawer-backdrop-enter-from,
.drawer-backdrop-leave-to {
  opacity: 0;
}

.drawer-panel-enter-active,
.drawer-panel-leave-active {
  transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}
.drawer-panel-enter-from,
.drawer-panel-leave-to {
  transform: translateX(100%);
}
</style>
