<template>
  <div class="min-h-screen transition-colors duration-500 p-6 lg:p-10 space-y-8 custom-scroll font-sans relative">
    
    <!-- Header Navigation -->
    <header class="sticky top-4 z-40 backdrop-blur-2xl bg-white/85 dark:bg-[#090d16]/85 border border-black/10 dark:border-white/12 px-6 py-4 rounded-2xl flex items-center justify-between shadow-2xl transition-all">
      <div class="flex items-center gap-3">
        <div class="w-3.5 h-3.5 rounded-full bg-emerald-500 animate-ping"></div>
        <h1 class="text-xl font-black text-slate-900 dark:text-white flex items-center gap-2">
          FCPMS <span class="text-xs px-3 py-1 rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-black shadow-md">لوحة التحكم الرئيسية</span>
        </h1>
      </div>

      <div class="flex items-center gap-4">
        <button
          @click="toggleTheme"
          class="flex items-center gap-2 px-4 py-2 rounded-2xl bg-black/5 dark:bg-white/10 hover:bg-black/10 dark:hover:bg-white/20 border border-black/10 dark:border-white/15 text-slate-900 dark:text-white font-bold text-xs transition-all cursor-pointer shadow-md active:scale-95"
        >
          <span v-if="isDark">☀️ الفاتح</span>
          <span v-else>🌙 الداكن</span>
        </button>

        <button
          @click="logout"
          class="flex items-center gap-2 px-4 py-2 rounded-2xl bg-red-500/15 hover:bg-red-500 text-red-500 hover:text-white border border-red-500/30 text-xs font-bold transition-all cursor-pointer shadow-md active:scale-95"
        >
          <span>تسجيل الخروج 🚪</span>
        </button>
      </div>
    </header>

    <!-- Main Body Container -->
    <main class="max-w-5xl mx-auto space-y-8">
      
      <!-- Welcome Banner -->
      <SpatialCard padding="p-8 relative overflow-hidden">
        <div class="absolute -left-10 -bottom-10 w-72 h-72 bg-primary/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="space-y-4 relative z-10">
          <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 font-black text-xs">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span> تم تسجيل الدخول بنجاح
          </div>
          <h2 class="text-3xl font-black text-slate-900 dark:text-white">
            مرحباً بك، {{ auth.user.name }} 👋
          </h2>
          <p class="text-sm font-bold text-slate-500 dark:text-white/60">
            أهلاً بك في نظام متابعة وتقييم أداء الاستشاريين الميدانيين.
          </p>
        </div>
      </SpatialCard>

      <!-- Account Info Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <SpatialCard class="p-6 space-y-3">
          <span class="text-xs font-bold text-slate-500 dark:text-white/50 uppercase block">اسم المستخدم</span>
          <h3 class="text-xl font-black text-slate-900 dark:text-white font-mono">{{ auth.user.username }}</h3>
        </SpatialCard>

        <SpatialCard class="p-6 space-y-3">
          <span class="text-xs font-bold text-slate-500 dark:text-white/50 uppercase block">البريد الإلكتروني</span>
          <h3 class="text-xl font-black text-slate-900 dark:text-white">{{ auth.user.email }}</h3>
        </SpatialCard>

        <SpatialCard class="p-6 space-y-3">
          <span class="text-xs font-bold text-slate-500 dark:text-white/50 uppercase block">حالة الحساب</span>
          <div>
            <SpatialStatusPill type="completed" pulse>حساب نشط (Active)</SpatialStatusPill>
          </div>
        </SpatialCard>
      </div>

      <!-- Quick Navigation Link to Design System Catalog -->
      <SpatialCard class="p-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
          <h4 class="font-black text-lg text-slate-900 dark:text-white">معاينة كتالوج مكونات Spatial UI v3.0</h4>
          <p class="text-xs font-bold text-slate-500 dark:text-white/60 mt-1">تصفح كافة العناصر، الألوان، النماذج، والتبويبات</p>
        </div>
        <Link href="/design-system" class="spatial-button text-sm px-6 py-3 shrink-0">
          فتح الكتالوج ↗
        </Link>
      </SpatialCard>

    </main>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import SpatialCard from '@/Components/Spatial/SpatialCard.vue';
import SpatialStatusPill from '@/Components/Spatial/SpatialStatusPill.vue';

defineProps({
  auth: Object,
});

const isDark = ref(true);

function toggleTheme() {
  isDark.value = !isDark.value;
  if (isDark.value) {
    document.documentElement.classList.add('dark');
    document.body.classList.remove('body-bg-light');
    document.body.classList.add('body-bg-dark');
  } else {
    document.documentElement.classList.remove('dark');
    document.body.classList.remove('body-bg-dark');
    document.body.classList.add('body-bg-light');
  }
}

onMounted(() => {
  document.documentElement.classList.add('dark');
  document.body.classList.add('body-bg-dark');
});

function logout() {
  router.post(route('logout'));
}
</script>
