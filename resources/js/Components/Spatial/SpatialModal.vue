<template>
  <Teleport to="body">
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-md">
      <div class="glass-card w-full max-w-lg p-6 border border-white/10 rounded-[30px] shadow-2xl relative">
        <div class="flex items-center justify-between mb-4 border-b border-white/10 pb-3">
          <h3 class="text-xl font-bold text-white">{{ title }}</h3>
          <button @click="$emit('close')" class="text-gray-400 hover:text-white transition">✕</button>
        </div>
        <div class="py-2">
          <slot />
        </div>
        <div class="flex justify-end gap-3 mt-6 pt-3 border-t border-white/10">
          <slot name="footer" />
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
defineProps({
  show: Boolean,
  title: String
});

defineEmits(['close']);
</script>
