<template>
  <HRLayout :title="`تقرير المواقع المزارة للاستشاري: ${consultant?.full_name || ''}`">
    <SpatialToast ref="toastRef" />

    <div class="space-y-6">
      <!-- Top Navigation & Consultant Banner -->
      <SpatialCard padding="p-6" class="relative overflow-hidden z-10">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
          <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500/20 to-emerald-500/20 border border-indigo-500/30 flex items-center justify-center text-indigo-500 shadow-inner">
              <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
            <div>
              <div class="flex items-center gap-2">
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">
                  تقرير المواقع المزارة للاستشاري: {{ consultant?.full_name }}
                </h1>
                <span class="px-2.5 py-0.5 rounded-full bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-mono text-xs font-black">
                  كود: {{ consultant?.employee_number }}
                </span>
              </div>
              <p class="text-xs font-bold text-slate-500 dark:text-white/60 mt-1">
                التخصص: {{ consultant?.specialization }} | الهاتف: {{ consultant?.phone || 'غير مدخل' }} | كشف المواقع التي قام بزيارتها والمهام المنفذة في كل موقع
              </p>
            </div>
          </div>

          <Link
            :href="route('admin.reports.consultants')"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-white/10 text-slate-700 dark:text-white hover:bg-slate-200 dark:hover:bg-white/20 transition-all font-black text-xs group"
          >
            <svg class="w-4 h-4 text-slate-500 group-hover:text-slate-700 dark:text-white/60 dark:group-hover:text-white transform group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
            <span>العودة لتقرير الاستشاريين</span>
          </Link>
        </div>
      </SpatialCard>

      <!-- Combined Spatial Summary Cards Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <!-- Card 1: Coverage & Visits Summary Card -->
        <SpatialCard padding="p-5" class="relative overflow-hidden space-y-4">
          <!-- Card Top Header -->
          <div class="pb-3 border-b border-slate-100 dark:border-white/10 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
              <div class="w-9 h-9 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-sm shrink-0">
                🏢
              </div>
              <div>
                <h3 class="text-base font-black text-slate-900 dark:text-white leading-tight">
                  إحصائيات التغطية والمواقع
                </h3>
                <span class="text-[11px] font-bold text-slate-400 dark:text-white/40 block">
                  ملخص المواقع المزارة والزيارات الميدانية
                </span>
              </div>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-mono font-black bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20 shrink-0">
              تغطية ميدانية 📍
            </span>
          </div>

          <!-- Card Body: Circular Gauge & Metrics Stack -->
          <div class="flex items-center justify-between gap-4 py-1">
            <!-- Circular Gauge: Visits Count -->
            <div class="flex flex-col items-center justify-center shrink-0 pr-1">
              <SpatialCircularProgress
                :percentage="100"
                :size="84"
                :strokeWidth="8"
              >
                <span class="text-2xl font-black font-mono text-indigo-600 dark:text-indigo-400">
                  {{ summary?.total_visits || 0 }}
                </span>
              </SpatialCircularProgress>
              <span class="text-xs font-black text-slate-700 dark:text-white/80 mt-2">إجمالي الزيارات 📌</span>
            </div>

            <!-- Right Metrics Stack -->
            <div class="flex-1 space-y-2.5">
              <!-- Metric 1: Visited Sites -->
              <div class="flex items-center justify-between p-3 rounded-xl bg-slate-100/90 dark:bg-white/5 border border-slate-200/80 dark:border-white/10 text-xs shadow-2xs">
                <span class="font-black text-slate-700 dark:text-white/80 flex items-center gap-1.5">
                  <span>🏢</span>
                  <span>عدد المواقع المزارة:</span>
                </span>
                <span class="font-mono font-black text-slate-900 dark:text-white text-sm">
                  {{ summary?.total_sites || 0 }} موقع
                </span>
              </div>

              <!-- Metric 2: Visits Count -->
              <div class="flex items-center justify-between p-3 rounded-xl bg-slate-100/90 dark:bg-white/5 border border-slate-200/80 dark:border-white/10 text-xs shadow-2xs">
                <span class="font-black text-slate-700 dark:text-white/80 flex items-center gap-1.5">
                  <span>📌</span>
                  <span>الزيارات الموثقة:</span>
                </span>
                <span class="font-mono font-black text-indigo-600 dark:text-indigo-400 text-sm">
                  {{ summary?.total_visits || 0 }} زيارة
                </span>
              </div>
            </div>
          </div>
        </SpatialCard>

        <!-- Card 2: Tasks Breakdown & Executions Summary Card -->
        <SpatialCard padding="p-5" class="relative overflow-hidden space-y-4">
          <!-- Card Top Header -->
          <div class="pb-3 border-b border-slate-100 dark:border-white/10 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
              <div class="w-9 h-9 rounded-xl bg-blue-500/10 border border-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-sm shrink-0">
                📋
              </div>
              <div>
                <h3 class="text-base font-black text-slate-900 dark:text-white leading-tight">
                  ملخص المهام المنفذة
                </h3>
                <span class="text-[11px] font-bold text-slate-400 dark:text-white/40 block">
                  مقارنة إنجاز المهام اليومية والمهام عند الحاجة
                </span>
              </div>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-mono font-black bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 shrink-0">
              إنجاز المهام ⚡
            </span>
          </div>

          <!-- Card Body: Two Side-by-Side Labeled Sections -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <!-- Section 1: Daily Tasks -->
            <div class="p-3 rounded-2xl bg-slate-50/80 dark:bg-white/5 border border-slate-200/60 dark:border-white/10 space-y-2.5">
              <div class="flex items-center justify-between pb-1 border-b border-slate-200/40 dark:border-white/5">
                <span class="text-xs font-black text-slate-800 dark:text-white flex items-center gap-1.5">
                  <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                  <span>📋 المهام اليومية</span>
                </span>
                <span class="text-[10px] font-black text-blue-600 dark:text-blue-400 font-mono bg-blue-500/10 px-2 py-0.5 rounded-md border border-blue-500/20">
                  قياسية
                </span>
              </div>

              <div class="flex items-center justify-center pt-1">
                <div class="flex flex-col items-center justify-center text-center">
                  <SpatialCircularProgress
                    :percentage="100"
                    :size="58"
                    :strokeWidth="5"
                  >
                    <span class="text-base font-black font-mono text-blue-600 dark:text-blue-400">
                      {{ summary?.daily_tasks_executions || 0 }}
                    </span>
                  </SpatialCircularProgress>
                  <span class="text-[10px] font-black text-slate-600 dark:text-white/70 mt-1.5 flex items-center gap-1 justify-center">
                    <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    <span>إجمالي التنفيذ</span>
                  </span>
                </div>
              </div>
            </div>

            <!-- Section 2: On-Demand Tasks -->
            <div class="p-3 rounded-2xl bg-slate-50/80 dark:bg-white/5 border border-slate-200/60 dark:border-white/10 space-y-2.5">
              <div class="flex items-center justify-between pb-1 border-b border-slate-200/40 dark:border-white/5">
                <span class="text-xs font-black text-slate-800 dark:text-white flex items-center gap-1.5">
                  <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                  <span>⚡ المهام الإضافية</span>
                </span>
                <span class="text-[10px] font-black text-amber-600 dark:text-amber-400 font-mono bg-amber-500/10 px-2 py-0.5 rounded-md border border-amber-500/20">
                  عند الحاجة
                </span>
              </div>

              <div class="flex items-center justify-center pt-1">
                <div class="flex flex-col items-center justify-center text-center">
                  <SpatialCircularProgress
                    :percentage="100"
                    :size="58"
                    :strokeWidth="5"
                  >
                    <span class="text-base font-black font-mono text-amber-600 dark:text-amber-400">
                      {{ summary?.on_demand_tasks_executions || 0 }}
                    </span>
                  </SpatialCircularProgress>
                  <span class="text-[10px] font-black text-slate-600 dark:text-white/70 mt-1.5 flex items-center gap-1 justify-center">
                    <svg class="w-3 h-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span>إجمالي التنفيذ</span>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </SpatialCard>
      </div>

      <!-- Date Filter Bar -->
      <SpatialCard padding="p-5" class="relative z-30">
        <form @submit.prevent="applyFilters" class="flex flex-col sm:flex-row items-end justify-between gap-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 w-full sm:w-auto">
            <SpatialInput
              type="date"
              v-model="filterForm.date_from"
              label="من تاريخ"
            />
            <SpatialInput
              type="date"
              v-model="filterForm.date_to"
              label="إلى تاريخ"
            />
          </div>

          <div class="flex items-center gap-2">
            <SpatialButton type="submit" variant="primary" size="md">
              <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
              </svg>
              <span>تصفية بالفترة</span>
            </SpatialButton>

            <SpatialButton
              v-if="filterForm.date_from || filterForm.date_to"
              type="button"
              variant="secondary"
              size="md"
              @click="resetFilters"
            >
              <span>إلغاء الفلترة</span>
            </SpatialButton>
          </div>
        </form>
      </SpatialCard>

      <!-- Sites Breakdown Table -->
      <SpatialCard padding="p-0 overflow-hidden" class="relative z-10">
        <div class="p-4 border-b border-slate-200 dark:border-white/10 flex items-center justify-between bg-slate-50/50 dark:bg-white/5">
          <h3 class="font-black text-sm text-slate-900 dark:text-white flex items-center gap-2">
            <span>🏢 قائمة المواقع التي زارها الاستشاري وتفاصيل المهام</span>
            <span class="px-2.5 py-0.5 rounded-full bg-primary/10 text-primary font-mono text-xs font-black">
              {{ sites ? sites.length : 0 }} موقع
            </span>
          </h3>
        </div>

        <div class="overflow-x-auto custom-scroll">
          <table class="w-full text-right border-collapse">
            <thead>
              <tr class="border-b border-slate-200 dark:border-white/10 text-xs font-black text-slate-600 dark:text-white/70 bg-slate-100/90 dark:bg-white/5">
                <th class="p-4">اسم الموقع</th>
                <th class="p-4">المدينة</th>
                <th class="p-4 text-center">عدد الزيارات الميدانية</th>
                <th class="p-4 text-center">المهام اليومية المنفذة</th>
                <th class="p-4 text-center">المهام الإضافية (عند الحاجة)</th>
                <th class="p-4 text-center">إجمالي المهام المنجزة للموقع</th>
                <th class="p-4 text-center w-36">تقرير الموقع</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-sm font-bold">
              <tr
                v-for="site in sites"
                :key="site.site_id"
                class="transition-colors hover:bg-slate-50/80 dark:hover:bg-white/5"
              >
                <td class="p-4">
                  <div class="font-black text-slate-900 dark:text-white">
                    {{ site.site_name }}
                  </div>
                  <div class="font-mono text-xs text-slate-400 mt-0.5">
                    كود: {{ site.site_code }}
                  </div>
                </td>
                <td class="p-4 text-xs font-bold text-slate-600 dark:text-white/70">
                  {{ site.site_city }}
                </td>
                <td class="p-4 text-center">
                  <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-mono text-xs font-black border border-indigo-500/20">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>{{ site.visits_count }} زيارة</span>
                  </span>
                </td>
                <td class="p-4 text-center">
                  <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 font-mono text-xs font-black border border-blue-500/20">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    <span>{{ site.daily_tasks_executions }} تنفيذ</span>
                  </span>
                </td>
                <td class="p-4 text-center">
                  <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 font-mono text-xs font-black border border-amber-500/20">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span>{{ site.on_demand_tasks_executions }} تنفيذ</span>
                  </span>
                </td>
                <td class="p-4 text-center">
                  <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-mono text-sm font-black border border-emerald-500/20">
                    <span>{{ site.total_tasks_count }} مهمة</span>
                  </span>
                </td>
                <td class="p-4 text-center">
                  <button
                    type="button"
                    @click="openSiteDrawer(site)"
                    class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-500/10 hover:bg-indigo-600 text-indigo-600 hover:text-white border border-indigo-500/30 text-xs font-black transition-all cursor-pointer shadow-2xs hover:shadow-md group"
                  >
                    <svg class="w-4 h-4 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <span>تفاصيل الموقع</span>
                  </button>
                </td>
              </tr>

              <tr v-if="!sites || sites.length === 0">
                <td colspan="7" class="p-12 text-center">
                  <SpatialEmptyState
                    title="لا توجد مواضع مزارة لهذا الاستشاري"
                    description="لم يتم تسجيل أي زيارات ميدانية لهذا الاستشاري خلال الفترة المحددة."
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </SpatialCard>
    </div>

    <!-- Spatial Drawer for Specific Site Task Breakdown -->
    <SpatialDrawer
      :isOpen="isDrawerOpen"
      :title="`تفاصيل تكرار مهام الموقع: ${selectedSite?.site_name || ''}`"
      width="max-w-2xl"
      @close="isDrawerOpen = false"
    >
      <div v-if="selectedSite" class="space-y-6">
        <!-- Top Summary Card inside Drawer matching SiteTaskBreakdown drawer header style -->
        <SpatialCard padding="p-5 overflow-hidden" class="relative z-10 space-y-4">
          <!-- Top Header: Title & Badges -->
          <div class="pb-3 border-b border-slate-100 dark:border-white/10 space-y-2">
            <h3 class="text-lg font-black text-slate-900 dark:text-white leading-tight">
              {{ selectedSite.site_name }}
            </h3>

            <div class="flex items-center justify-between gap-2">
              <div class="flex items-center gap-1.5 text-xs font-black text-indigo-600 dark:text-indigo-400 bg-indigo-500/10 px-2.5 py-0.5 rounded-lg border border-indigo-500/20">
                <span>📍</span>
                <span>{{ selectedSite.site_city }} (كود: {{ selectedSite.site_code }})</span>
              </div>

              <span class="px-3 py-1 rounded-full text-xs font-black bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30">
                الاستشاري: {{ consultant?.full_name }}
              </span>
            </div>
          </div>

          <!-- Center Body: Triple Circular Progress Rings -->
          <div class="flex items-center justify-around gap-2 sm:gap-4 py-3 bg-slate-50/80 dark:bg-white/5 p-4 rounded-2xl border border-slate-200/60 dark:border-white/10 shadow-inner">
            <!-- Ring 1: Visited Count -->
            <div class="flex flex-col items-center justify-center text-center">
              <SpatialCircularProgress
                :percentage="100"
                :size="80"
                :strokeWidth="8"
              >
                <span class="text-xl sm:text-2xl font-black font-mono text-purple-600 dark:text-purple-400">
                  {{ selectedSite.visits_count || 0 }}
                </span>
              </SpatialCircularProgress>
              <div class="flex items-center gap-1.5 mt-2">
                <div class="w-5 h-5 sm:w-6 sm:h-6 rounded-full bg-purple-500/10 border border-purple-500/20 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                  <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                </div>
                <span class="text-[11px] sm:text-xs font-black text-slate-800 dark:text-white">عدد زيارات الموقع</span>
              </div>
            </div>

            <!-- Divider Line 1 -->
            <div class="w-px h-16 sm:h-20 bg-slate-200 dark:bg-white/10"></div>

            <!-- Ring 2: Total Repetitions -->
            <div class="flex flex-col items-center justify-center text-center">
              <SpatialCircularProgress
                :percentage="100"
                :size="80"
                :strokeWidth="8"
              >
                <span class="text-xl sm:text-2xl font-black font-mono text-indigo-600 dark:text-indigo-400">
                  {{ selectedSite.daily_tasks_executions + selectedSite.on_demand_tasks_executions }}
                </span>
              </SpatialCircularProgress>
              <div class="flex items-center gap-1.5 mt-2">
                <div class="w-5 h-5 sm:w-6 sm:h-6 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                  <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                  </svg>
                </div>
                <span class="text-[11px] sm:text-xs font-black text-slate-800 dark:text-white">تكرار المهام الإجمالي</span>
              </div>
            </div>

            <!-- Divider Line 2 -->
            <div class="w-px h-16 sm:h-20 bg-slate-200 dark:bg-white/10"></div>

            <!-- Ring 3: Distinct Tasks Count -->
            <div class="flex flex-col items-center justify-center text-center">
              <SpatialCircularProgress
                :percentage="100"
                :size="80"
                :strokeWidth="8"
              >
                <span class="text-xl sm:text-2xl font-black font-mono text-emerald-600 dark:text-emerald-400">
                  {{ selectedSite.tasks_breakdown ? selectedSite.tasks_breakdown.length : 0 }}
                </span>
              </SpatialCircularProgress>
              <div class="flex items-center gap-1.5 mt-2">
                <div class="w-5 h-5 sm:w-6 sm:h-6 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                  <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                  </svg>
                </div>
                <span class="text-[11px] sm:text-xs font-black text-slate-800 dark:text-white">عدد المهام المنفذة</span>
              </div>
            </div>
          </div>
        </SpatialCard>

        <!-- Tasks Breakdown Table inside Drawer -->
        <div class="rounded-2xl border border-slate-200/80 dark:border-white/10 overflow-hidden">
          <div class="p-3.5 bg-slate-100/90 dark:bg-white/5 border-b border-slate-200 dark:border-white/10 flex items-center justify-between">
            <h4 class="font-black text-xs text-slate-900 dark:text-white flex items-center gap-2">
              <span>📋 المهام المنفذة وتكرارات الإنجاز في هذا الموقع</span>
            </h4>
            <span class="px-2.5 py-0.5 rounded-full bg-slate-200 dark:bg-white/10 text-slate-700 dark:text-white font-mono text-[11px] font-black">
              الاستشاري: {{ consultant?.full_name }}
            </span>
          </div>

          <div class="overflow-x-auto custom-scroll">
            <table class="w-full text-right border-collapse">
              <thead>
                <tr class="border-b border-slate-200 dark:border-white/10 text-[11px] font-black text-slate-500 dark:text-white/60 bg-slate-50 dark:bg-white/5">
                  <th class="p-3">اسم المهمة</th>
                  <th class="p-3">نوع المهمة</th>
                  <th class="p-3 text-center">تكرار الإنجاز والتنفيذ</th>
                  <th class="p-3 text-center">أحدث تاريخ تنفيذ</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-xs font-bold">
                <tr
                  v-for="task in selectedSite.tasks_breakdown"
                  :key="task.task_id"
                  class="hover:bg-slate-50/80 dark:hover:bg-white/5"
                >
                  <td class="p-3 font-black text-slate-900 dark:text-white">
                    {{ task.title }}
                  </td>
                  <td class="p-3">
                    <span
                      :class="[
                        'px-2.5 py-0.5 rounded-full text-[10px] font-black',
                        task.task_type === 'إضافية'
                          ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400'
                          : 'bg-blue-500/10 text-blue-600 dark:text-blue-400'
                      ]"
                    >
                      {{ task.task_type }}
                    </span>
                  </td>
                  <td class="p-3 text-center">
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-mono text-xs font-black border border-indigo-500/20">
                      <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                      </svg>
                      <span>{{ task.execution_count }} مرة</span>
                    </span>
                  </td>
                  <td class="p-3 text-center font-mono text-slate-500 dark:text-white/70">
                    {{ formatDate(task.last_executed_at) }}
                  </td>
                </tr>

                <tr v-if="!selectedSite.tasks_breakdown || selectedSite.tasks_breakdown.length === 0">
                  <td colspan="4" class="p-8 text-center text-slate-400">
                    لا توجد تفاصيل مهام منفذة مسجلة لهذا الموقع.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </SpatialDrawer>
  </HRLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import HRLayout from '@/Layouts/HRLayout.vue';
import SpatialToast from '@/Components/Spatial/SpatialToast.vue';
import SpatialCard from '@/Components/Spatial/SpatialCard.vue';
import SpatialButton from '@/Components/Spatial/SpatialButton.vue';
import SpatialInput from '@/Components/Spatial/SpatialInput.vue';
import SpatialCircularProgress from '@/Components/Spatial/SpatialCircularProgress.vue';
import SpatialEmptyState from '@/Components/Spatial/SpatialEmptyState.vue';
import SpatialDrawer from '@/Components/Spatial/SpatialDrawer.vue';

const props = defineProps({
  consultant: Object,
  summary: Object,
  sites: Array,
  filters: Object,
});

const toastRef = ref(null);
const isDrawerOpen = ref(false);
const selectedSite = ref(null);

const filterForm = reactive({
  date_from: props.filters?.date_from || '',
  date_to: props.filters?.date_to || '',
});

function openSiteDrawer(site) {
  selectedSite.value = site;
  isDrawerOpen.value = true;
}

function formatDate(dateStr) {
  if (!dateStr) return '-';
  if (typeof dateStr === 'string' && dateStr.includes('T')) {
    dateStr = dateStr.split('T')[0];
  }
  const parts = String(dateStr).split('-');
  if (parts.length === 3) {
    return `${parseInt(parts[2], 10)}-${parseInt(parts[1], 10)}-${parts[0]}`;
  }
  return dateStr;
}

function applyFilters() {
  router.get(
    route('admin.reports.consultant-sites', props.consultant.id),
    filterForm,
    { preserveState: true, preserveScroll: true }
  );
}

function resetFilters() {
  filterForm.date_from = '';
  filterForm.date_to = '';
  applyFilters();
}
</script>
