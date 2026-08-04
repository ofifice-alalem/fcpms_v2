<template>
  <Teleport to="body">
    <div id="toastContainer" class="fixed top-5 left-5 z-[99999] flex flex-col gap-2.5 max-w-[380px] w-full pointer-events-none">
      <div
        v-for="toast in toasts"
        :key="toast.id"
        :class="['toast-item pointer-events-auto', toast.type]"
      >
        <span v-html="getIcon(toast.type)"></span>
        <span class="flex-1 font-bold text-sm text-white">{{ toast.message }}</span>
        <button @click="removeToast(toast.id)" class="close-toast">✕</button>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref } from 'vue';

const toasts = ref([]);
let nextId = 1;

function addToast(type, message, duration = 3500) {
  const id = nextId++;
  toasts.value.push({ id, type, message });
  setTimeout(() => {
    removeToast(id);
  }, duration);
}

function removeToast(id) {
  toasts.value = toasts.value.filter(t => t.id !== id);
}

function getIcon(type) {
  const icons = {
    success: '<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>',
    error: '<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>',
    warning: '<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>',
    info: '<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
  };
  return icons[type] || '';
}

defineExpose({ addToast });
</script>
