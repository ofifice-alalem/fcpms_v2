<template>
  <div class="min-h-screen transition-colors duration-500 flex flex-col items-center justify-center p-4 sm:p-6 custom-scroll font-sans relative overflow-hidden">
    
    <!-- Background Ambient Glow Orbs -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-primary/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-purple-600/20 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Theme Toggle Floating Pill Top Right -->
    <div class="absolute top-6 right-6 z-50">
      <button
        @click="toggleTheme"
        class="flex items-center gap-2 px-4 py-2 rounded-2xl bg-black/10 dark:bg-white/10 hover:bg-black/20 dark:hover:bg-white/20 border border-black/10 dark:border-white/15 text-slate-900 dark:text-white font-bold text-xs transition-all cursor-pointer shadow-md active:scale-95 backdrop-blur-md"
      >
        <span v-if="isDark" class="flex items-center gap-1.5">☀️ الوضع الفاتح</span>
        <span v-else class="flex items-center gap-1.5">🌙 الوضع الداكن</span>
      </button>
    </div>

    <!-- Page Content Container -->
    <div class="w-full max-w-md relative z-10">
      <slot />
    </div>

    <!-- Footer Copyright -->
    <footer class="mt-8 text-center text-xs font-bold text-slate-400 dark:text-white/40">
      نظام متابعة أداء الاستشاريين الميدانيين (FCPMS) &copy; {{ new Date().getFullYear() }}
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const isDark = ref(true);

function applyTheme(dark) {
  isDark.value = dark;
  localStorage.setItem('fcpms_theme', dark ? 'dark' : 'light');
  if (dark) {
    document.documentElement.classList.add('dark');
    document.body.classList.remove('body-bg-light');
    document.body.classList.add('body-bg-dark');
  } else {
    document.documentElement.classList.remove('dark');
    document.body.classList.remove('body-bg-dark');
    document.body.classList.add('body-bg-light');
  }
}

function toggleTheme() {
  applyTheme(!isDark.value);
}

onMounted(() => {
  const savedTheme = localStorage.getItem('fcpms_theme');
  if (savedTheme) {
    applyTheme(savedTheme === 'dark');
  } else {
    applyTheme(true);
  }
});
</script>
