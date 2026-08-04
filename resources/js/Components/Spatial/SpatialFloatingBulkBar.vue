<template>
  <div
    v-if="selectedCount > 0"
    class="fixed bottom-8 left-1/2 -translate-x-1/2 z-[90] backdrop-blur-2xl bg-slate-950/95 text-white border border-primary/40 shadow-2xl rounded-[26px] px-6 py-3.5 flex items-center gap-5 animate-spatial-in"
  >
    <div class="flex items-center gap-2.5">
      <span class="w-3 h-3 rounded-full bg-primary animate-ping"></span>
      <span class="text-xs font-black text-white px-3 py-1 rounded-full bg-primary/20 border border-primary/40">
        تم تحديد {{ selectedCount }} عناصر
      </span>
    </div>
    <div class="h-6 w-px bg-white/15"></div>
    <div class="flex items-center gap-2">
      <slot />
    </div>
    <div class="h-6 w-px bg-white/15"></div>
    <button
      @click="$emit('clear')"
      class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all text-xs font-bold"
      title="إلغاء التحديد"
    >
      ✕
    </button>
  </div>
</template>

<script setup>
defineProps({
  selectedCount: {
    type: Number,
    default: 0,
  },
});

defineEmits(['clear']);
</script>
