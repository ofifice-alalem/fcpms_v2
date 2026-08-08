<template>
  <div class="h-screen w-full transition-colors duration-500 flex flex-col md:flex-row font-sans relative overflow-hidden bg-slate-50 dark:bg-[#090d16] text-slate-900 dark:text-white dir-rtl">
    
    <!-- Background Glow Orbs (Desktop only for max mobile performance) -->
    <div class="hidden md:block absolute top-0 left-0 w-96 h-96 bg-primary/15 rounded-full blur-3xl pointer-events-none"></div>
    <div class="hidden md:block absolute bottom-0 right-0 w-96 h-96 bg-emerald-600/15 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Mobile Sticky Top Header (Fixed/Sticky on Mobile) -->
    <header class="sticky top-0 z-40 md:hidden w-full bg-white/90 dark:bg-[#090d16]/90 backdrop-blur-xl border-b border-black/10 dark:border-white/10 px-4 py-3 flex items-center justify-between shadow-xs shrink-0">
      <!-- Right Side: Drawer Toggle Button & Brand -->
      <div class="flex items-center gap-3">
        <button
          @click="isMobileDrawerOpen = true"
          class="w-10 h-10 rounded-2xl bg-black/5 dark:bg-white/10 hover:bg-black/10 dark:hover:bg-white/20 text-slate-800 dark:text-white flex items-center justify-center transition-all cursor-pointer border border-black/5 dark:border-white/10 active:scale-95"
          aria-label="فتح القائمة"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>

        <div>
          <h2 class="font-black text-sm text-slate-900 dark:text-white leading-none">FCPMS</h2>
          <span class="text-[9px] font-bold text-emerald-500 block mt-0.5">البوابة الميدانية</span>
        </div>
      </div>

      <!-- Center / Left: Retroactive Indicator, Progress & Quick Logout Button -->
      <div class="flex items-center gap-2">
        <div v-if="$page.props.isHistorical" class="flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-500/20 border border-amber-500/40 text-amber-600 dark:text-amber-300 font-mono text-[10px] font-black animate-pulse">
          <span>📅 {{ formatDateOnly($page.props.selectedDate) }}</span>
        </div>

        <div v-else class="flex items-center gap-1.5 bg-emerald-500/10 dark:bg-emerald-500/15 border border-emerald-500/20 px-2 py-1 rounded-2xl">
          <SpatialCircularProgress
            :percentage="dailyCompletionPercentage"
            :size="30"
            :stroke-width="3"
          >
            <span class="text-[8px] font-black font-mono text-slate-900 dark:text-white">{{ dailyCompletionPercentage.toFixed(0) }}%</span>
          </SpatialCircularProgress>
        </div>

        <!-- Quick Logout Button on Mobile Header -->
        <button
          @click="logout"
          title="تسجيل الخروج"
          class="w-9 h-9 rounded-xl bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white border border-red-500/20 flex items-center justify-center transition-all cursor-pointer active:scale-95"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
        </button>
      </div>
    </header>

    <!-- Desktop Sidebar Navigation (Full Height) -->
    <aside class="hidden md:flex w-64 h-full bg-white/80 dark:bg-[#090d16]/80 backdrop-blur-2xl border-l border-black/10 dark:border-white/12 p-6 flex-col justify-between shrink-0 z-30 transition-all">
      <div class="space-y-8">
        <!-- Logo & Title -->
        <div class="flex items-center justify-between">
          <div>
            <h2 class="font-black text-xl text-slate-900 dark:text-white leading-none tracking-wide">FCPMS</h2>
            <span class="text-[11px] font-bold text-emerald-500 block mt-1">البوابة الميدانية للاستشاري</span>
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
            :href="route('consultant.history.index')"
            :class="[
              'flex items-center gap-3 px-4 py-3 rounded-[16px] font-bold text-sm transition-all cursor-pointer',
              $page.component === 'Consultant/History/Index' ? 'bg-primary text-white shadow-md scale-102' : 'text-slate-600 dark:text-white/70 hover:bg-black/5 dark:hover:bg-white/5 hover:text-slate-900 dark:hover:text-white'
            ]"
          >
            <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span>سجل الأيام السابقة</span>
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
            :href="route('consultant.reports.index')"
            :class="[
              'flex items-center gap-3 px-4 py-3 rounded-[16px] font-bold text-sm transition-all cursor-pointer',
              $page.component === 'Consultant/Reports/Index' ? 'bg-primary text-white shadow-md scale-102' : 'text-slate-600 dark:text-white/70 hover:bg-black/5 dark:hover:bg-white/5 hover:text-slate-900 dark:hover:text-white'
            ]"
          >
            <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span>تقاريري وسجلاتي</span>
          </Link>


        </nav>
      </div>

      <!-- Bottom User Controls -->
      <div class="pt-5 border-t border-black/10 dark:border-white/10 space-y-3">
        <div class="p-3 rounded-2xl bg-black/5 dark:bg-white/5 border border-black/5 dark:border-white/10 flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-500 border border-emerald-500/30 flex items-center justify-center font-black text-sm shrink-0">
            {{ ($page.props.auth?.user?.name || 'استشاري').charAt(0) }}
          </div>
          <div class="space-y-0.5 min-w-0 flex-1">
            <span class="font-black text-xs text-slate-900 dark:text-white block truncate">{{ $page.props.auth?.user?.name || 'الاستشاري الميداني' }}</span>
            <span class="text-[10px] font-bold text-emerald-500 block truncate">استشاري ميداني 🟢</span>
          </div>
        </div>

        <div class="flex items-center gap-2">
          <button
            @click="toggleTheme"
            class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl bg-black/5 dark:bg-white/10 hover:bg-black/10 dark:hover:bg-white/20 text-slate-900 dark:text-white font-bold text-xs transition-all cursor-pointer border border-black/5 dark:border-white/10"
          >
            <span v-if="isDark">☀️ الفاتح</span>
            <span v-else>🌙 الداكن</span>
          </button>

          <button
            @click="logout"
            class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl bg-red-500/10 hover:bg-red-600 text-red-500 hover:text-white border border-red-500/20 text-xs font-black transition-all cursor-pointer shadow-sm active:scale-95"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <span>خروج</span>
          </button>
        </div>
      </div>
    </aside>

    <!-- Mobile Navigation Drawer -->
    <SpatialDrawer
      v-model:is-open="isMobileDrawerOpen"
      title="القائمة الرئيسية"
      width="w-[300px]"
    >
      <div class="space-y-6 flex flex-col h-full justify-between dir-rtl">
        <div class="space-y-4">
          <!-- Profile Badge in Drawer -->
          <div class="p-3.5 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center font-black text-base shrink-0 shadow-md shadow-emerald-500/30">
              {{ ($page.props.auth?.user?.name || 'استشاري').charAt(0) }}
            </div>
            <div class="space-y-0.5 min-w-0 flex-1">
              <span class="font-black text-xs text-slate-900 dark:text-white block truncate">{{ $page.props.auth?.user?.name || 'الاستشاري الميداني' }}</span>
              <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 block truncate">استشاري ميداني نشط 🟢</span>
            </div>
          </div>

          <nav class="space-y-2 pt-2">
            <Link
              :href="route('consultant.visits.index')"
              @click="isMobileDrawerOpen = false"
              :class="[
                'flex items-center gap-3 px-4 py-3.5 rounded-[16px] font-bold text-sm transition-all cursor-pointer',
                $page.component === 'Consultant/DailyVisits/Index' ? 'bg-primary text-white shadow-md' : 'text-slate-700 dark:text-white/80 hover:bg-black/5 dark:hover:bg-white/10'
              ]"
            >
              <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
              <span>سجل الزيارات اليومية</span>
            </Link>

            <Link
              :href="route('consultant.history.index')"
              @click="isMobileDrawerOpen = false"
              :class="[
                'flex items-center gap-3 px-4 py-3.5 rounded-[16px] font-bold text-sm transition-all cursor-pointer',
                $page.component === 'Consultant/History/Index' ? 'bg-primary text-white shadow-md' : 'text-slate-700 dark:text-white/80 hover:bg-black/5 dark:hover:bg-white/10'
              ]"
            >
              <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              <span>سجل الأيام السابقة</span>
            </Link>

            <Link
              :href="route('consultant.site-visits.create')"
              @click="isMobileDrawerOpen = false"
              :class="[
                'flex items-center gap-3 px-4 py-3.5 rounded-[16px] font-bold text-sm transition-all cursor-pointer',
                $page.component === 'Consultant/DailyVisits/Execute' && !$page.props.visit ? 'bg-primary text-white shadow-md' : 'text-slate-700 dark:text-white/80 hover:bg-black/5 dark:hover:bg-white/10'
              ]"
            >
              <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
              <span>بدء زيارة موقع جديد</span>
            </Link>

            <Link
              :href="route('consultant.reports.index')"
              @click="isMobileDrawerOpen = false"
              :class="[
                'flex items-center gap-3 px-4 py-3.5 rounded-[16px] font-bold text-sm transition-all cursor-pointer',
                $page.component === 'Consultant/Reports/Index' ? 'bg-primary text-white shadow-md' : 'text-slate-700 dark:text-white/80 hover:bg-black/5 dark:hover:bg-white/10'
              ]"
            >
              <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
              <span>تقاريري وسجلاتي</span>
            </Link>


          </nav>
        </div>

        <div class="pt-6 border-t border-black/10 dark:border-white/10 space-y-3">
          <div class="flex items-center gap-2">
            <button
              @click="toggleTheme"
              class="flex-1 flex items-center justify-center gap-1.5 px-3 py-3 rounded-xl bg-black/5 dark:bg-white/10 hover:bg-black/10 text-slate-900 dark:text-white font-bold text-xs transition-all cursor-pointer border border-black/5 dark:border-white/10"
            >
              <span v-if="isDark">☀️ الفاتح</span>
              <span v-else>🌙 الداكن</span>
            </button>

            <button
              @click="logout"
              class="flex-1 flex items-center justify-center gap-1.5 px-3 py-3 rounded-xl bg-red-500/15 hover:bg-red-600 text-red-500 hover:text-white text-xs font-black transition-all cursor-pointer border border-red-500/30 active:scale-95 shadow-sm"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
              </svg>
              <span>تسجيل الخروج 🚪</span>
            </button>
          </div>
        </div>
      </div>
    </SpatialDrawer>

    <!-- Main Content Area -->
    <main class="flex-1 h-full overflow-y-auto custom-scroll p-4 md:p-8 max-w-7xl mx-auto w-full z-10 space-y-6 pb-24 md:pb-8">
      
      <!-- Retroactive Historical Mode Amber Banner (Enhanced Spatial UI Design) -->
      <div
        v-if="$page.props.isHistorical"
        class="relative p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-amber-500/20 via-amber-500/10 to-amber-500/20 border border-amber-500/40 backdrop-blur-xl flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-xl shadow-amber-500/5 animate-fade-in"
      >
        <div class="flex items-start sm:items-center gap-3.5">
          <!-- Pulsing Status Indicator -->
          <div class="relative flex items-center justify-center shrink-0 mt-0.5 sm:mt-0">
            <span class="animate-ping absolute inline-flex h-4 w-4 rounded-full bg-amber-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-amber-500 shadow-sm"></span>
          </div>

          <div class="space-y-1">
            <div class="flex items-center gap-2 flex-wrap">
              <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black bg-amber-500/20 text-amber-900 dark:text-amber-300 border border-amber-500/30">
                ⚠️ وضع العمل التاريخي والاستدراك
              </span>
              <span class="text-xs font-mono font-black text-amber-900 dark:text-amber-200 dir-ltr bg-amber-500/15 px-2.5 py-0.5 rounded-lg border border-amber-500/30 shadow-xs">
                {{ formattedHistoricalDate }}
              </span>
            </div>
            <p class="text-xs font-bold text-amber-800 dark:text-amber-200/90 leading-relaxed">
              تنبيه: أنت تقوم بتصفح وتعديل البيانات بأثر رجعي لهذا اليوم. جميع الإجراءات المعتمدة ستُسجل وتُنسب لهذا التاريخ.
            </p>
          </div>
        </div>

        <Link
          :href="route('consultant.visits.index')"
          class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 active:scale-95 text-slate-950 font-black text-xs transition-all shrink-0 text-center shadow-lg shadow-amber-500/20 flex items-center justify-center gap-1.5 cursor-pointer"
        >
          <span>العودة لليوم الحالي 📍</span>
        </Link>
      </div>

      <slot />
    </main>

    <!-- Mobile Native App Bottom Navigation Bar -->
    <SpatialMobileBottomNav>
      <slot name="mobile-bottom-nav">
      <!-- Item 1: Start New Visit (Right in RTL) -->
      <Link
        :href="$page.props.isHistorical && $page.props.selectedDate ? route('consultant.site-visits.create', { date: formatDateOnly($page.props.selectedDate) }) : route('consultant.site-visits.create')"
        :class="[
          'relative flex-1 flex flex-col items-center justify-center py-1 transition-all duration-150 active:scale-90 cursor-pointer',
          $page.component === 'Consultant/DailyVisits/Execute' && !$page.props.visit
            ? 'text-emerald-500 dark:text-emerald-400 font-black'
            : 'text-slate-400 dark:text-white/40 hover:text-slate-700 dark:hover:text-white'
        ]"
      >
        <!-- Active Top Indicator Pill -->
        <div v-if="$page.component === 'Consultant/DailyVisits/Execute' && !$page.props.visit" class="absolute -top-1.5 w-7 h-1 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)]"></div>

        <div class="relative">
          <svg class="w-6 h-6 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/>
          </svg>
        </div>
        <span class="text-[10px] font-bold tracking-tight mt-1 leading-none">زيارة جديدة</span>
      </Link>

      <!-- Item 2: Visits History (CENTER) -->
      <Link
        :href="$page.props.isHistorical && $page.props.selectedDate ? route('consultant.visits.index', { date: formatDateOnly($page.props.selectedDate) }) : route('consultant.visits.index')"
        :class="[
          'relative flex-1 flex flex-col items-center justify-center py-1 transition-all duration-150 active:scale-90 cursor-pointer',
          $page.component === 'Consultant/DailyVisits/Index'
            ? 'text-emerald-500 dark:text-emerald-400 font-black'
            : 'text-slate-400 dark:text-white/40 hover:text-slate-700 dark:hover:text-white'
        ]"
      >
        <!-- Active Top Indicator Pill -->
        <div v-if="$page.component === 'Consultant/DailyVisits/Index'" class="absolute -top-1.5 w-7 h-1 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)]"></div>

        <div class="relative">
          <svg class="w-6 h-6 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
          </svg>
        </div>
        <span class="text-[10px] font-bold tracking-tight mt-1 leading-none">سجل اليوم</span>
      </Link>

      <!-- Item 3: History Records (Left in RTL) -->
      <Link
        :href="route('consultant.history.index')"
        :class="[
          'relative flex-1 flex flex-col items-center justify-center py-1 transition-all duration-150 active:scale-90 cursor-pointer',
          $page.component === 'Consultant/History/Index'
            ? 'text-emerald-500 dark:text-emerald-400 font-black'
            : 'text-slate-400 dark:text-white/40 hover:text-slate-700 dark:hover:text-white'
        ]"
      >
        <!-- Active Top Indicator Pill -->
        <div v-if="$page.component === 'Consultant/History/Index'" class="absolute -top-1.5 w-7 h-1 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)]"></div>

        <div class="relative">
          <svg class="w-6 h-6 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
        </div>
        <span class="text-[10px] font-bold tracking-tight mt-1 leading-none">الأيام السابقة</span>
      </Link>
      </slot>
    </SpatialMobileBottomNav>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import SpatialDrawer from '@/Components/Spatial/SpatialDrawer.vue';
import SpatialCircularProgress from '@/Components/Spatial/SpatialCircularProgress.vue';
import SpatialMobileBottomNav from '@/Components/Spatial/SpatialMobileBottomNav.vue';

defineProps({
  title: String,
});

const page = usePage();
const isDark = ref(true);
const isMobileDrawerOpen = ref(false);

const formatDateOnly = (dateStr) => {
  if (!dateStr) return '';
  return String(dateStr).split('T')[0];
};

const formattedHistoricalDate = computed(() => {
  const dateVal = page.props.selectedDate;
  if (!dateVal) return '';
  const dateStr = String(dateVal).split('T')[0];
  const parts = dateStr.split('-');
  if (parts.length === 3) {
    return `${parts[2]} - ${parts[1]} - ${parts[0]}`;
  }
  return dateStr;
});

const dailyCompletionPercentage = computed(() => {
  const record = page.props.dailyRecord || page.props.visit?.daily_record || page.props.activeVisit?.dailyRecord || page.props.visit?.dailyRecord;
  if (record && record.completion_percentage !== undefined) {
    return Math.min(100, Math.max(0, Number(record.completion_percentage)));
  }
  if (page.props.completionPercentage !== undefined) {
    return Math.min(100, Math.max(0, Number(page.props.completionPercentage)));
  }
  return 0;
});

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

const logout = () => {
  router.post(route('logout'));
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
