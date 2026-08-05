<template>
  <div class="min-h-screen transition-colors duration-500 flex flex-col md:flex-row custom-scroll font-sans relative overflow-x-clip bg-slate-50 dark:bg-[#090d16] text-slate-900 dark:text-white">
    
    <!-- Background Glow Orbs -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-primary/15 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-emerald-600/15 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Sidebar Navigation -->
    <aside class="w-full md:w-64 backdrop-blur-2xl bg-white/80 dark:bg-[#090d16]/80 border-b md:border-b-0 md:border-l border-black/10 dark:border-white/12 p-6 flex flex-col justify-between shrink-0 z-30 transition-all">
      <div class="space-y-8">
        <!-- Logo & Title -->
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-[14px] bg-gradient-to-br from-emerald-500 to-teal-700 text-white font-black text-lg flex items-center justify-center shadow-lg">
              F
            </div>
            <div>
              <h2 class="font-black text-lg text-slate-900 dark:text-white leading-none">FCPMS</h2>
              <span class="text-[11px] font-bold text-emerald-500 block mt-1">البوابة الميدانية للاستشاري</span>
            </div>
          </div>
        </div>

        <!-- Nav Items List -->
        <nav class="space-y-2">
          <Link
            :href="route('consultant.visits.index')"
            :class="[
              'flex items-center gap-3 px-4 py-3 rounded-[16px] font-bold text-sm transition-all cursor-pointer',
              $page.component === 'Consultant/DailyVisits/Index' ? 'bg-primary text-white shadow-md scale-102' : 'text-slate-600 dark:text-white/70 hover:bg-black/5 dark:hover:bg-white/5 hover:text-slate-900 dark:hover:text-white'
            ]"
          >
            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span>سجل الزيارات اليومية</span>
          </Link>

          <Link
            :href="route('consultant.site-visits.create')"
            :class="[
              'flex items-center gap-3 px-4 py-3 rounded-[16px] font-bold text-sm transition-all cursor-pointer',
              $page.component === 'Consultant/DailyVisits/Execute' && !$page.props.visit ? 'bg-primary text-white shadow-md scale-102' : 'text-slate-600 dark:text-white/70 hover:bg-black/5 dark:hover:bg-white/5 hover:text-slate-900 dark:hover:text-white'
            ]"
          >
            <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>بدء زيارة موقع جديد</span>
          </Link>

          <Link
            href="/design-system"
            class="flex items-center gap-3 px-4 py-3 rounded-[16px] font-bold text-sm text-slate-600 dark:text-white/70 hover:bg-black/5 dark:hover:bg-white/5 hover:text-slate-900 dark:hover:text-white transition-all cursor-pointer"
          >
            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
            <span>كتالوج Spatial UI</span>
          </Link>
        </nav>
      </div>

      <!-- Bottom User Controls -->
      <div class="pt-6 border-t border-black/10 dark:border-white/10 space-y-3">
        <div class="flex items-center justify-between">
          <button
            @click="toggleTheme"
            class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-black/5 dark:bg-white/10 hover:bg-black/10 text-slate-900 dark:text-white font-bold text-xs transition-all cursor-pointer"
          >
            <span v-if="isDark">☀️ الفاتح</span>
            <span v-else>🌙 الداكن</span>
          </button>
        </div>

        <div class="p-3.5 rounded-[16px] bg-black/5 dark:bg-white/5 border border-black/5 dark:border-white/10 flex items-center justify-between">
          <div class="space-y-0.5">
            <span class="font-black text-xs text-slate-900 dark:text-white block">{{ $page.props.auth?.user?.name || 'الاستشاري الميداني' }}</span>
            <span class="text-[10px] font-bold text-slate-500 dark:text-white/60 block">{{ $page.props.auth?.user?.email || 'مفتش سلامة' }}</span>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 p-4 md:p-8 overflow-y-auto max-w-7xl mx-auto w-full z-10 space-y-6">
      <slot />
    </main>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';

defineProps({
  title: String,
});

const isDark = ref(true);

const toggleTheme = () => {
  isDark.value = !isDark.value;
  if (isDark.value) {
    document.documentElement.classList.add('dark');
    localStorage.setItem('theme', 'dark');
  } else {
    document.documentElement.classList.remove('dark');
    localStorage.setItem('theme', 'light');
  }
};

onMounted(() => {
  const savedTheme = localStorage.getItem('theme');
  if (savedTheme === 'light') {
    isDark.value = false;
    document.documentElement.classList.remove('dark');
  } else {
    isDark.value = true;
    document.documentElement.classList.add('dark');
  }
});
</script>
