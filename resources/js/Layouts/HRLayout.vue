<template>
  <div class="h-screen w-full transition-colors duration-500 flex flex-col md:flex-row font-sans relative overflow-hidden bg-slate-50 dark:bg-[#090d16] text-slate-900 dark:text-white dir-rtl">
    
    <!-- Background Glow Orbs -->
    <div class="hidden md:block absolute -top-32 -left-32 w-96 h-96 bg-primary/15 rounded-full blur-3xl pointer-events-none"></div>
    <div class="hidden md:block absolute -bottom-32 -right-32 w-96 h-96 bg-purple-600/15 rounded-full blur-3xl pointer-events-none"></div>

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
          <span class="text-[9px] font-bold text-primary block mt-0.5">لوحة الإدارة والموارد البشرية</span>
        </div>
      </div>

      <!-- Left Side: User Pill & Quick Logout -->
      <div class="flex items-center gap-2">
        <div class="flex items-center gap-1.5 bg-primary/10 border border-primary/20 px-2.5 py-1.5 rounded-2xl">
          <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
          <span class="text-xs font-black text-slate-900 dark:text-white max-w-[100px] truncate">{{ $page.props.auth?.user?.name || 'مدير النظام' }}</span>
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

    <!-- Desktop Sidebar Navigation (Full Viewport Height) -->
    <aside class="hidden md:flex w-64 h-full bg-white/80 dark:bg-[#090d16]/80 backdrop-blur-2xl border-l border-black/10 dark:border-white/12 p-6 flex-col justify-between shrink-0 z-30 transition-all">
      <div class="space-y-8">
        <!-- Logo & Title -->
        <div class="flex items-center justify-between">
          <div>
            <h2 class="font-black text-xl text-slate-900 dark:text-white leading-none tracking-wide">FCPMS</h2>
            <span class="text-[11px] font-bold text-primary block mt-1">لوحة الإدارة والموارد البشرية</span>
          </div>
        </div>

        <!-- Nav Items List -->
        <nav class="space-y-2">
          <Link
            :href="route('dashboard')"
            :class="[
              'flex items-center gap-3 px-4 py-3 rounded-[16px] font-bold text-sm transition-all cursor-pointer',
              $page.component.includes('Dashboard') ? 'bg-primary text-white shadow-md scale-102' : 'text-slate-600 dark:text-white/70 hover:bg-black/5 dark:hover:bg-white/5 hover:text-slate-900 dark:hover:text-white'
            ]"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span>الرئيسية</span>
          </Link>

          <Link
            :href="route('admin.sites.index')"
            :class="[
              'flex items-center gap-3 px-4 py-3 rounded-[16px] font-bold text-sm transition-all cursor-pointer',
              $page.component.startsWith('Admin/Sites') ? 'bg-primary text-white shadow-md scale-102' : 'text-slate-600 dark:text-white/70 hover:bg-black/5 dark:hover:bg-white/5 hover:text-slate-900 dark:hover:text-white'
            ]"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m3 0h1m-1-4h.01M9 16h.01M9 12h.01M9 8h.01M15 16h.01M15 12h.01M15 8h.01"/></svg>
            <span>دليل المواقع الميدانية</span>
          </Link>

          <Link
            :href="route('admin.consultants.index')"
            :class="[
              'flex items-center gap-3 px-4 py-3 rounded-[16px] font-bold text-sm transition-all cursor-pointer',
              $page.component.startsWith('Admin/Consultants') ? 'bg-primary text-white shadow-md scale-102' : 'text-slate-600 dark:text-white/70 hover:bg-black/5 dark:hover:bg-white/5 hover:text-slate-900 dark:hover:text-white'
            ]"
          >
            <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <span>دليل الاستشاريين الميدانيين</span>
          </Link>

          <Link
            :href="route('admin.schedules.index')"
            :class="[
              'flex items-center gap-3 px-4 py-3 rounded-[16px] font-bold text-sm transition-all cursor-pointer',
              $page.component.startsWith('Admin/WorkSchedules') ? 'bg-primary text-white shadow-md scale-102' : 'text-slate-600 dark:text-white/70 hover:bg-black/5 dark:hover:bg-white/5 hover:text-slate-900 dark:hover:text-white'
            ]"
          >
            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span>جداول الدوام والإجازات</span>
          </Link>

          <Link
            :href="route('admin.tasks.index')"
            :class="[
              'flex items-center gap-3 px-4 py-3 rounded-[16px] font-bold text-sm transition-all cursor-pointer',
              $page.component.startsWith('Admin/TaskBuilder') ? 'bg-primary text-white shadow-md scale-102' : 'text-slate-600 dark:text-white/70 hover:bg-black/5 dark:hover:bg-white/5 hover:text-slate-900 dark:hover:text-white'
            ]"
          >
            <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            <span>منشئ المهام وتكليفات الميدان</span>
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
      <div class="pt-5 border-t border-black/10 dark:border-white/10 space-y-3">
        <div class="p-3 rounded-2xl bg-black/5 dark:bg-white/5 border border-black/5 dark:border-white/10 flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-primary/20 text-primary border border-primary/30 flex items-center justify-center font-black text-sm shrink-0">
            {{ ($page.props.auth?.user?.name || 'مدير').charAt(0) }}
          </div>
          <div class="space-y-0.5 min-w-0 flex-1">
            <span class="font-black text-xs text-slate-900 dark:text-white block truncate">{{ $page.props.auth?.user?.name || 'مدير النظام' }}</span>
            <span class="text-[10px] font-bold text-primary block truncate">إدارة الموارد البشرية 🛡️</span>
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
      title="لوحة التحكّم الإدارية"
      width="w-[300px]"
    >
      <div class="space-y-6 flex flex-col h-full justify-between dir-rtl">
        <div class="space-y-4">
          <!-- Profile Badge in Drawer -->
          <div class="p-3.5 rounded-2xl bg-primary/10 border border-primary/20 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-primary text-white flex items-center justify-center font-black text-base shrink-0 shadow-md shadow-primary/30">
              {{ ($page.props.auth?.user?.name || 'مدير').charAt(0) }}
            </div>
            <div class="space-y-0.5 min-w-0 flex-1">
              <span class="font-black text-xs text-slate-900 dark:text-white block truncate">{{ $page.props.auth?.user?.name || 'مدير النظام' }}</span>
              <span class="text-[10px] font-bold text-primary block truncate">إدارة الموارد البشرية 🛡️</span>
            </div>
          </div>

          <nav class="space-y-2 pt-2">
            <Link
              :href="route('dashboard')"
              @click="isMobileDrawerOpen = false"
              :class="[
                'flex items-center gap-3 px-4 py-3.5 rounded-[16px] font-bold text-sm transition-all cursor-pointer',
                $page.component.includes('Dashboard') ? 'bg-primary text-white shadow-md' : 'text-slate-700 dark:text-white/80 hover:bg-black/5 dark:hover:bg-white/10'
              ]"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
              <span>الرئيسية</span>
            </Link>

            <Link
              :href="route('admin.sites.index')"
              @click="isMobileDrawerOpen = false"
              :class="[
                'flex items-center gap-3 px-4 py-3.5 rounded-[16px] font-bold text-sm transition-all cursor-pointer',
                $page.component.startsWith('Admin/Sites') ? 'bg-primary text-white shadow-md' : 'text-slate-700 dark:text-white/80 hover:bg-black/5 dark:hover:bg-white/10'
              ]"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m3 0h1m-1-4h.01M9 16h.01M9 12h.01M9 8h.01M15 16h.01M15 12h.01M15 8h.01"/></svg>
              <span>دليل المواقع الميدانية</span>
            </Link>

            <Link
              :href="route('admin.consultants.index')"
              @click="isMobileDrawerOpen = false"
              :class="[
                'flex items-center gap-3 px-4 py-3.5 rounded-[16px] font-bold text-sm transition-all cursor-pointer',
                $page.component.startsWith('Admin/Consultants') ? 'bg-primary text-white shadow-md' : 'text-slate-700 dark:text-white/80 hover:bg-black/5 dark:hover:bg-white/10'
              ]"
            >
              <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
              <span>دليل الاستشاريين الميدانيين</span>
            </Link>

            <Link
              :href="route('admin.schedules.index')"
              @click="isMobileDrawerOpen = false"
              :class="[
                'flex items-center gap-3 px-4 py-3.5 rounded-[16px] font-bold text-sm transition-all cursor-pointer',
                $page.component.startsWith('Admin/WorkSchedules') ? 'bg-primary text-white shadow-md' : 'text-slate-700 dark:text-white/80 hover:bg-black/5 dark:hover:bg-white/10'
              ]"
            >
              <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              <span>جداول الدوام والإجازات</span>
            </Link>

            <Link
              :href="route('admin.tasks.index')"
              @click="isMobileDrawerOpen = false"
              :class="[
                'flex items-center gap-3 px-4 py-3.5 rounded-[16px] font-bold text-sm transition-all cursor-pointer',
                $page.component.startsWith('Admin/TaskBuilder') ? 'bg-primary text-white shadow-md' : 'text-slate-700 dark:text-white/80 hover:bg-black/5 dark:hover:bg-white/10'
              ]"
            >
              <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
              <span>منشئ المهام وتكليفات الميدان</span>
            </Link>

            <Link
              href="/design-system"
              @click="isMobileDrawerOpen = false"
              class="flex items-center gap-3 px-4 py-3.5 rounded-[16px] font-bold text-sm text-slate-700 dark:text-white/80 hover:bg-black/5 dark:hover:bg-white/10 transition-all cursor-pointer"
            >
              <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
              <span>كتالوج Spatial UI</span>
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

    <!-- Main Content Area with Custom Scroll -->
    <main class="flex-1 h-full overflow-y-auto custom-scroll p-4 sm:p-6 lg:p-10 space-y-8 z-10 pb-24 md:pb-8">
      <slot />
    </main>

    <!-- Mobile Native App Bottom Navigation Bar -->
    <SpatialMobileBottomNav>
      <Link
        :href="route('dashboard')"
        :class="[
          'relative flex-1 flex flex-col items-center justify-center py-1 transition-all duration-150 active:scale-90 cursor-pointer',
          $page.component.includes('Dashboard')
            ? 'text-primary dark:text-primary font-black'
            : 'text-slate-400 dark:text-white/40 hover:text-slate-700 dark:hover:text-white'
        ]"
      >
        <div v-if="$page.component.includes('Dashboard')" class="absolute -top-1.5 w-7 h-1 rounded-full bg-primary shadow-[0_0_8px_rgba(59,130,246,0.8)]"></div>
        <svg class="w-6 h-6 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        <span class="text-[10px] font-bold tracking-tight mt-1 leading-none">الرئيسية</span>
      </Link>

      <Link
        :href="route('admin.sites.index')"
        :class="[
          'relative flex-1 flex flex-col items-center justify-center py-1 transition-all duration-150 active:scale-90 cursor-pointer',
          $page.component.startsWith('Admin/Sites')
            ? 'text-primary dark:text-primary font-black'
            : 'text-slate-400 dark:text-white/40 hover:text-slate-700 dark:hover:text-white'
        ]"
      >
        <div v-if="$page.component.startsWith('Admin/Sites')" class="absolute -top-1.5 w-7 h-1 rounded-full bg-primary shadow-[0_0_8px_rgba(59,130,246,0.8)]"></div>
        <svg class="w-6 h-6 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m3 0h1m-1-4h.01M9 16h.01M9 12h.01M9 8h.01M15 16h.01M15 12h.01M15 8h.01"/></svg>
        <span class="text-[10px] font-bold tracking-tight mt-1 leading-none">المواقع</span>
      </Link>

      <Link
        :href="route('admin.consultants.index')"
        :class="[
          'relative flex-1 flex flex-col items-center justify-center py-1 transition-all duration-150 active:scale-90 cursor-pointer',
          $page.component.startsWith('Admin/Consultants')
            ? 'text-primary dark:text-primary font-black'
            : 'text-slate-400 dark:text-white/40 hover:text-slate-700 dark:hover:text-white'
        ]"
      >
        <div v-if="$page.component.startsWith('Admin/Consultants')" class="absolute -top-1.5 w-7 h-1 rounded-full bg-primary shadow-[0_0_8px_rgba(59,130,246,0.8)]"></div>
        <svg class="w-6 h-6 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        <span class="text-[10px] font-bold tracking-tight mt-1 leading-none">الاستشاريون</span>
      </Link>

      <Link
        :href="route('admin.schedules.index')"
        :class="[
          'relative flex-1 flex flex-col items-center justify-center py-1 transition-all duration-150 active:scale-90 cursor-pointer',
          $page.component.startsWith('Admin/WorkSchedules')
            ? 'text-primary dark:text-primary font-black'
            : 'text-slate-400 dark:text-white/40 hover:text-slate-700 dark:hover:text-white'
        ]"
      >
        <div v-if="$page.component.startsWith('Admin/WorkSchedules')" class="absolute -top-1.5 w-7 h-1 rounded-full bg-primary shadow-[0_0_8px_rgba(59,130,246,0.8)]"></div>
        <svg class="w-6 h-6 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        <span class="text-[10px] font-bold tracking-tight mt-1 leading-none">الدوام</span>
      </Link>

      <button
        @click="isMobileDrawerOpen = true"
        :class="[
          'relative flex-1 flex flex-col items-center justify-center py-1 transition-all duration-150 active:scale-90 cursor-pointer',
          isMobileDrawerOpen
            ? 'text-primary dark:text-primary font-black'
            : 'text-slate-400 dark:text-white/40 hover:text-slate-700 dark:hover:text-white'
        ]"
      >
        <div v-if="isMobileDrawerOpen" class="absolute -top-1.5 w-7 h-1 rounded-full bg-primary shadow-[0_0_8px_rgba(59,130,246,0.8)]"></div>
        <svg class="w-6 h-6 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
        <span class="text-[10px] font-bold tracking-tight mt-1 leading-none">القائمة</span>
      </button>
    </SpatialMobileBottomNav>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import SpatialDrawer from '@/Components/Spatial/SpatialDrawer.vue';
import SpatialMobileBottomNav from '@/Components/Spatial/SpatialMobileBottomNav.vue';

const isDark = ref(true);
const isMobileDrawerOpen = ref(false);

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

function logout() {
  router.post(route('logout'));
}
</script>
