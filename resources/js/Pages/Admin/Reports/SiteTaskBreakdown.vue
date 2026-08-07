<template>
  <HRLayout :title="`تقرير تكرار والمهام للموقع: ${siteData?.name || ''}`">
    <SpatialToast ref="toastRef" />

    <div class="space-y-6">
      <!-- Top Navigation & Site Banner -->
      <SpatialCard padding="p-6" class="relative overflow-hidden z-10">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
          <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary/20 to-blue-500/20 border border-primary/30 flex items-center justify-center text-primary shadow-inner">
              <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
              </svg>
            </div>
            <div>
              <div class="flex items-center gap-2">
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">
                  تحليل المهام وتكرار الإنجاز للموقع: {{ siteData?.name }}
                </h1>
                <span class="px-2.5 py-0.5 rounded-full bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-mono text-xs font-black">
                  كود: {{ siteData?.code }}
                </span>
              </div>
              <p class="text-xs font-bold text-slate-500 dark:text-white/60 mt-1">
                المدينة: {{ siteData?.city }} | تكرار كل مهمة وعدد وأسماء الاستشاريين الذين نفذوها على هذا الموقع
              </p>
            </div>
          </div>

          <Link
            :href="route('admin.reports.sites')"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-white/10 text-slate-700 dark:text-white hover:bg-slate-200 transition-all font-black text-xs"
          >
            <span>⬅️ العودة لتقارير الأماكن والمواقع</span>
          </Link>
        </div>
      </SpatialCard>

      <!-- Combined Spatial Summary Cards Grid (Matching Consultant Visited Site Card Design) -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <!-- Card 1: Coverage & Site Visits Summary Card -->
        <SpatialCard padding="p-5" class="relative overflow-hidden space-y-4">
          <!-- Card Top Header -->
          <div class="pb-3 border-b border-slate-100 dark:border-white/10 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
              <div class="w-9 h-9 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-sm shrink-0">
                📌
              </div>
              <div>
                <h3 class="text-base font-black text-slate-900 dark:text-white leading-tight">
                  إحصائيات التغطية والزيارات
                </h3>
                <span class="text-[11px] font-bold text-slate-400 dark:text-white/40 block">
                  ملخص تواجد الاستشاريين والزيارات للموقع
                </span>
              </div>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-mono font-black bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20 shrink-0">
              ميداني 📍
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
              <!-- Metric 1: Unique Consultants -->
              <div class="flex items-center justify-between p-3 rounded-xl bg-slate-100/90 dark:bg-white/5 border border-slate-200/80 dark:border-white/10 text-xs shadow-2xs">
                <span class="font-black text-slate-700 dark:text-white/80 flex items-center gap-1.5">
                  <span>👥</span>
                  <span>الاستشاريين الزائرين:</span>
                </span>
                <span class="font-mono font-black text-slate-900 dark:text-white text-sm">
                  {{ summary?.unique_consultants || 0 }} استشاري
                </span>
              </div>

              <!-- Metric 2: Visits Count -->
              <div class="flex items-center justify-between p-3 rounded-xl bg-slate-100/90 dark:bg-white/5 border border-slate-200/80 dark:border-white/10 text-xs shadow-2xs">
                <span class="font-black text-slate-700 dark:text-white/80 flex items-center gap-1.5">
                  <span>📌</span>
                  <span>زيارات الموقع الميدانية:</span>
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
                  ملخص تنفيذ المهام الميدانية
                </h3>
                <span class="text-[11px] font-bold text-slate-400 dark:text-white/40 block">
                  تحليل دقيق لعدد وتكرار المهام اليومية والإضافية
                </span>
              </div>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-mono font-black bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 shrink-0">
              إنجاز المهام ⚡
            </span>
          </div>

          <!-- Card Body: Two Side-by-Side Labeled Sections (Same Row Grid) -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <!-- Section 1: Daily Tasks -->
            <div class="p-3 rounded-2xl bg-slate-50/80 dark:bg-white/5 border border-slate-200/60 dark:border-white/10 space-y-2.5">
              <!-- Section Label -->
              <div class="flex items-center justify-between pb-1 border-b border-slate-200/40 dark:border-white/5">
                <span class="text-xs font-black text-slate-800 dark:text-white flex items-center gap-1.5">
                  <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                  <span>📋 المهام اليومية</span>
                </span>
                <span class="text-[10px] font-black text-blue-600 dark:text-blue-400 font-mono bg-blue-500/10 px-2 py-0.5 rounded-md border border-blue-500/20">
                  قياسية
                </span>
              </div>

              <!-- Two Rings: Count & Executions -->
              <div class="flex items-center justify-around gap-2 pt-1">
                <!-- Ring 1a: Count -->
                <div class="flex flex-col items-center justify-center text-center">
                  <SpatialCircularProgress
                    :percentage="100"
                    :size="58"
                    :strokeWidth="5"
                  >
                    <span class="text-base font-black font-mono text-blue-600 dark:text-blue-400">
                      {{ summary?.daily_tasks_count || 0 }}
                    </span>
                  </SpatialCircularProgress>
                  <span class="text-[10px] font-black text-slate-600 dark:text-white/70 mt-1.5">عدد المهام</span>
                </div>

                <!-- Vertical Divider -->
                <div class="w-px h-10 bg-slate-200 dark:bg-white/10"></div>

                <!-- Ring 1b: Repetitions -->
                <div class="flex flex-col items-center justify-center text-center">
                  <SpatialCircularProgress
                    :percentage="100"
                    :size="58"
                    :strokeWidth="5"
                  >
                    <span class="text-base font-black font-mono text-indigo-600 dark:text-indigo-400">
                      {{ summary?.daily_tasks_executions || 0 }}
                    </span>
                  </SpatialCircularProgress>
                  <span class="text-[10px] font-black text-slate-600 dark:text-white/70 mt-1.5 flex items-center gap-1 justify-center">
                    <svg class="w-3 h-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>تكرار التنفيذ</span>
                  </span>
                </div>
              </div>
            </div>

            <!-- Section 2: On-Demand Tasks -->
            <div class="p-3 rounded-2xl bg-slate-50/80 dark:bg-white/5 border border-slate-200/60 dark:border-white/10 space-y-2.5">
              <!-- Section Label -->
              <div class="flex items-center justify-between pb-1 border-b border-slate-200/40 dark:border-white/5">
                <span class="text-xs font-black text-slate-800 dark:text-white flex items-center gap-1.5">
                  <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                  <span>⚡ المهام الإضافية</span>
                </span>
                <span class="text-[10px] font-black text-amber-600 dark:text-amber-400 font-mono bg-amber-500/10 px-2 py-0.5 rounded-md border border-amber-500/20">
                  عند الحاجة
                </span>
              </div>

              <!-- Two Rings: Count & Executions -->
              <div class="flex items-center justify-around gap-2 pt-1">
                <!-- Ring 2a: Count -->
                <div class="flex flex-col items-center justify-center text-center">
                  <SpatialCircularProgress
                    :percentage="100"
                    :size="58"
                    :strokeWidth="5"
                  >
                    <span class="text-base font-black font-mono text-amber-600 dark:text-amber-400">
                      {{ summary?.on_demand_tasks_count || 0 }}
                    </span>
                  </SpatialCircularProgress>
                  <span class="text-[10px] font-black text-slate-600 dark:text-white/70 mt-1.5">عدد المهام</span>
                </div>

                <!-- Vertical Divider -->
                <div class="w-px h-10 bg-slate-200 dark:bg-white/10"></div>

                <!-- Ring 2b: Repetitions -->
                <div class="flex flex-col items-center justify-center text-center">
                  <SpatialCircularProgress
                    :percentage="100"
                    :size="58"
                    :strokeWidth="5"
                  >
                    <span class="text-base font-black font-mono text-orange-600 dark:text-orange-400">
                      {{ summary?.on_demand_tasks_executions || 0 }}
                    </span>
                  </SpatialCircularProgress>
                  <span class="text-[10px] font-black text-slate-600 dark:text-white/70 mt-1.5 flex items-center gap-1 justify-center">
                    <svg class="w-3 h-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>تكرار التنفيذ</span>
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

      <!-- Task Breakdown Table -->
      <SpatialCard padding="p-0 overflow-hidden" class="relative z-10">
        <div class="p-4 border-b border-slate-200 dark:border-white/10 flex items-center justify-between bg-slate-50/50 dark:bg-white/5">
          <h3 class="font-black text-sm text-slate-900 dark:text-white flex items-center gap-2">
            <span>📋 المهام المنفذة وتكرارها على هذا الموقع</span>
            <span class="px-2.5 py-0.5 rounded-full bg-primary/10 text-primary font-mono text-xs font-black">
              {{ taskBreakdown ? taskBreakdown.length : 0 }} مهمة مختلفة
            </span>
          </h3>
        </div>

        <div class="overflow-x-auto custom-scroll">
          <table class="w-full text-right border-collapse">
            <thead>
              <tr class="border-b border-slate-200 dark:border-white/10 text-xs font-black text-slate-600 dark:text-white/70 bg-slate-100/90 dark:bg-white/5">
                <th class="p-4">اسم المهمة</th>
                <th class="p-4">نوع المهمة</th>
                <th class="p-4 text-center">عدد مرات التكرار والإنجاز</th>
                <th class="p-4 text-center">عدد الاستشاريين المنفذين</th>
                <th class="p-4">أسماء الاستشاريين الذين نفذوها</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-sm font-bold">
              <tr
                v-for="task in taskBreakdown"
                :key="task.task_id"
                class="transition-colors hover:bg-slate-50/80 dark:hover:bg-white/5"
              >
                <td class="p-4 font-black text-slate-900 dark:text-white">
                  {{ task.title }}
                </td>
                <td class="p-4 text-xs font-bold">
                  <span
                    :class="[
                      'px-2.5 py-1 rounded-full text-xs font-black',
                      task.task_type.includes('إضافية')
                        ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400'
                        : 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'
                    ]"
                  >
                    {{ task.task_type }}
                  </span>
                </td>
                <td class="p-4 text-center">
                  <span class="px-3.5 py-1 rounded-xl bg-primary/10 text-primary font-mono text-base font-black">
                    🔄 {{ task.execution_count }} مرة
                  </span>
                </td>
                <td class="p-4 text-center font-mono text-sm font-black text-slate-700 dark:text-white/90">
                  👥 {{ task.consultants_count }} استشاري
                </td>
                <td class="p-4 text-center">
                  <div class="flex items-center justify-center">
                    <button
                      type="button"
                      @click="openConsultantsDrawer(task)"
                      title="عرض قائمة الاستشاريين"
                      style="border-radius: 50%;"
                      class="w-9 h-9 border border-indigo-500/40 text-indigo-600 dark:text-indigo-400 bg-indigo-500/10 hover:bg-indigo-600 hover:text-white flex items-center justify-center transition-all cursor-pointer shadow-xs"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>

              <tr v-if="!taskBreakdown || taskBreakdown.length === 0">
                <td colspan="5" class="p-12 text-center">
                  <SpatialEmptyState
                    title="لا توجد مهام نفذت على هذا الموقع"
                    description="لم يتم تسجيل أي مهام منفذة على هذا الموقع خلال الفترة المحددة."
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </SpatialCard>
    </div>

    <!-- Consultants List Drawer -->
    <SpatialDrawer
      :is-open="isDrawerOpen"
      title="قائمة الاستشاريين المنفذين للمهمة"
      width="w-[480px]"
      @close="isDrawerOpen = false"
    >
      <div v-if="selectedTaskForDrawer" class="space-y-6 dir-rtl text-right">
        <!-- Task Summary Card (Matching Consultant Visited Site Card Design) -->
        <div class="relative p-5 rounded-3xl bg-white dark:bg-slate-800/90 border border-slate-200/80 dark:border-white/10 shadow-lg space-y-4">
          <!-- Top Header: Title & Task Type Pill Badge -->
          <div class="pb-3 border-b border-slate-100 dark:border-white/10 space-y-2">
            <h3 class="text-lg font-black text-slate-900 dark:text-white leading-tight">
              {{ selectedTaskForDrawer.title }}
            </h3>

            <div class="flex items-center justify-between gap-2">
              <div class="flex items-center gap-1 text-xs font-black text-indigo-600 dark:text-indigo-400 bg-indigo-500/10 px-2.5 py-0.5 rounded-lg border border-indigo-500/20">
                <span>📍</span>
                <span>{{ siteData?.name }}</span>
              </div>

              <span
                :class="[
                  'px-3.5 py-1 rounded-full text-xs font-black shrink-0 border shadow-2xs',
                  selectedTaskForDrawer.task_type.includes('إضافية')
                    ? 'bg-amber-500/15 text-amber-700 dark:text-amber-300 border-amber-500/30'
                    : 'bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border-emerald-500/30'
                ]"
              >
                {{ selectedTaskForDrawer.task_type }}
              </span>
            </div>
          </div>

          <!-- Center Body: Dual Circular Progress Rings -->
          <div class="flex items-center justify-around gap-4 py-3 bg-slate-50/80 dark:bg-white/5 p-4 rounded-2xl border border-slate-200/60 dark:border-white/10 shadow-inner">
            <!-- Ring 1: Execution Count -->
            <div class="flex flex-col items-center justify-center text-center">
              <SpatialCircularProgress
                :percentage="100"
                :size="88"
                :strokeWidth="9"
              >
                <span class="text-2xl font-black font-mono text-indigo-600 dark:text-indigo-400">
                  {{ selectedTaskForDrawer.execution_count }}
                </span>
              </SpatialCircularProgress>
              <div class="flex items-center gap-1.5 mt-2.5">
                <div class="w-6 h-6 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                  </svg>
                </div>
                <span class="text-xs sm:text-sm font-black text-slate-800 dark:text-white">تكرار المهمة</span>
              </div>
            </div>

            <!-- Divider Line -->
            <div class="w-px h-20 bg-slate-200 dark:bg-white/10"></div>

            <!-- Ring 2: Consultants Count -->
            <div class="flex flex-col items-center justify-center text-center">
              <SpatialCircularProgress
                :percentage="100"
                :size="88"
                :strokeWidth="9"
              >
                <span class="text-2xl font-black font-mono text-emerald-600 dark:text-emerald-400">
                  {{ selectedTaskForDrawer.consultants_count }}
                </span>
              </SpatialCircularProgress>
              <div class="flex items-center gap-1.5 mt-2.5">
                <div class="w-6 h-6 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                  </svg>
                </div>
                <span class="text-xs sm:text-sm font-black text-slate-800 dark:text-white">عدد الاستشاريين</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Consultants List Stack -->
        <div class="space-y-3">
          <h5 class="text-sm font-black text-slate-600 dark:text-white/70 uppercase tracking-wider">
            الاستشاريون وتكرار التنفيذ لكل منهم ({{ selectedTaskForDrawer.consultants_list?.length || 0 }}):
          </h5>

          <div
            v-if="selectedTaskForDrawer.consultants_list && selectedTaskForDrawer.consultants_list.length > 0"
            class="space-y-2.5"
          >
            <div
              v-for="(item, idx) in selectedTaskForDrawer.consultants_list"
              :key="idx"
              class="flex items-center justify-between p-4 rounded-2xl bg-white dark:bg-slate-800/80 border border-slate-200/80 dark:border-white/10 shadow-xs hover:border-indigo-500/40 transition-all"
            >
              <div>
                <div class="text-base font-black text-slate-900 dark:text-white">
                  {{ getConsultantName(item) }}
                </div>
                <div class="text-xs font-mono text-slate-400 dark:text-white/50 mt-0.5">
                  استشاري ميداني
                </div>
              </div>
              <span class="px-4 py-1.5 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-mono text-base font-black border border-indigo-500/20">
                {{ getConsultantCount(item) }}
              </span>
            </div>
          </div>

          <div v-else class="p-6 text-center text-xs font-bold text-slate-400 dark:text-white/40 border border-dashed border-slate-300 dark:border-white/10 rounded-2xl">
            لم يتم تسجيل أي استشاريين لهذه المهمة.
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
import SpatialEmptyState from '@/Components/Spatial/SpatialEmptyState.vue';
import SpatialDrawer from '@/Components/Spatial/SpatialDrawer.vue';
import SpatialCircularProgress from '@/Components/Spatial/SpatialCircularProgress.vue';

const props = defineProps({
  siteData: Object,
  summary: Object,
  taskBreakdown: Array,
  filters: Object,
  sites: Array,
});

const isDrawerOpen = ref(false);
const selectedTaskForDrawer = ref(null);

function openConsultantsDrawer(task) {
  selectedTaskForDrawer.value = task;
  isDrawerOpen.value = true;
}

const getConsultantName = (item) => {
  return typeof item === 'object' && item !== null ? item.name : item;
};

const getConsultantCount = (item) => {
  return typeof item === 'object' && item !== null ? (item.count || 1) : 1;
};

const filterForm = reactive({
  date_from: props.filters?.date_from || '',
  date_to: props.filters?.date_to || '',
});

function applyFilters() {
  router.get(route('admin.reports.site-breakdown', props.siteData?.id), filterForm, {
    preserveState: true,
    preserveScroll: true,
  });
}

function resetFilters() {
  filterForm.date_from = '';
  filterForm.date_to = '';
  applyFilters();
}
</script>
