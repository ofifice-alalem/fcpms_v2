<template>
  <nav class="fixed bottom-0 inset-x-0 z-50 md:hidden bg-white/95 dark:bg-[#090c13]/95 backdrop-blur-2xl border-t border-slate-200/80 dark:border-white/10 pt-1.5 pb-[max(0.5rem,env(safe-area-inset-bottom))] shadow-[0_-4px_25px_rgba(0,0,0,0.08)] dark:shadow-[0_-8px_30px_rgba(0,0,0,0.5)] select-none">
    <div class="flex items-center justify-around max-w-md mx-auto px-2">
      <slot />
    </div>
  </nav>
</template>

<script setup>
</script>
