<template>
  <HRLayout title="تقارير إحصائيات وتفاصيل أداء الاستشاريين">
    <SpatialToast ref="toastRef" />

    <div class="space-y-6">
      <!-- Header Banner -->
      <SpatialCard padding="p-6" class="relative overflow-hidden z-10">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
          <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 border border-emerald-500/30 flex items-center justify-center text-emerald-500 shadow-inner">
              <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
            </div>
            <div>
              <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">
                تقارير إحصائيات وتفاصيل أداء الاستشاريين
              </h1>
              <p class="text-xs font-bold text-slate-500 dark:text-white/60 mt-1">
                عرض شامل لعدد المواقع المزارة، إجمالي الزيارات الميدانية، والمهام المنفذة لكل استشاري مع إمكانية التحليل المخصص
              </p>
            </div>
          </div>
        </div>
      </SpatialCard>

      <!-- Reports Navigation Tabs -->
      <div class="flex items-center gap-2 p-1.5 bg-slate-100 dark:bg-white/5 rounded-2xl border border-slate-200/80 dark:border-white/10 overflow-x-auto">
        <Link
          :href="route('admin.reports.index')"
          class="flex items-center gap-2 px-4 py-2.5 rounded-xl font-black text-xs text-slate-600 dark:text-white/70 hover:text-slate-900 dark:hover:text-white transition-all shrink-0"
        >
          <span>📊 التقرير الشامل العام</span>
        </Link>
        <Link
          :href="route('admin.reports.sites')"
          class="flex items-center gap-2 px-4 py-2.5 rounded-xl font-black text-xs text-slate-600 dark:text-white/70 hover:text-slate-900 dark:hover:text-white transition-all shrink-0"
        >
          <span>🏢 تقارير إحصائيات المواقع والأماكن</span>
        </Link>
        <Link
          :href="route('admin.reports.consultants')"
          class="flex items-center gap-2 px-4 py-2.5 rounded-xl font-black text-xs transition-all bg-emerald-600 text-white shadow-sm shrink-0"
        >
          <span>👨‍💼 تقارير إحصائيات الاستشاريين</span>
        </Link>
      </div>

      <!-- Enterprise KPI Highlight Cards Grid (Spatial UI v3.0 Ultra-High Fidelity) -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Card 1: Total Consultants Card (Emerald Theme) -->
        <SpatialCard
          padding="p-6"
          class="min-h-[260px] flex flex-col justify-between border-t-4 border-t-emerald-500 rounded-3xl relative overflow-hidden bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-slate-200/80 dark:border-white/10 shadow-[0_8px_32px_rgba(0,0,0,0.06)] dark:shadow-[0_8px_32px_rgba(0,0,0,0.36)] transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl space-y-4"
        >
          <div class="absolute -top-12 -right-12 w-40 h-40 rounded-full bg-emerald-500/20 blur-3xl pointer-events-none"></div>

          <div class="text-center relative z-10">
            <span class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-white/60 block">
              إجمالي الاستشاريين
            </span>
          </div>

          <div class="flex flex-col items-center justify-center relative z-10 py-1">
            <div class="w-20 h-20 rounded-full border-2 border-emerald-500 bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 font-stat-number font-black text-3xl flex items-center justify-center shadow-lg shadow-emerald-500/25 ring-4 ring-emerald-500/20 transition-transform hover:scale-105">
              {{ totalConsultants }}
            </div>
            <span class="text-xs font-bold text-slate-400 dark:text-white/50 mt-2">استشاري بالميدان</span>
          </div>

          <div class="pt-3 border-t border-slate-100 dark:border-white/10 text-xs font-bold text-emerald-600 dark:text-emerald-400 text-center truncate relative z-10">
            مستوفي لبيانات التقرير
          </div>
        </SpatialCard>

        <!-- Card 2: Total Sites Visited Card (Indigo Theme) -->
        <SpatialCard
          padding="p-6"
          class="min-h-[260px] flex flex-col justify-between border-t-4 border-t-indigo-500 rounded-3xl relative overflow-hidden bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-slate-200/80 dark:border-white/10 shadow-[0_8px_32px_rgba(0,0,0,0.06)] dark:shadow-[0_8px_32px_rgba(0,0,0,0.36)] transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl space-y-4"
        >
          <div class="absolute -top-12 -right-12 w-40 h-40 rounded-full bg-indigo-500/20 blur-3xl pointer-events-none"></div>

          <div class="text-center relative z-10">
            <span class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-white/60 block">
              المواقع المزارة من الاستشاريين
            </span>
          </div>

          <div class="flex flex-col items-center justify-center relative z-10 py-1">
            <div class="w-20 h-20 rounded-full border-2 border-indigo-500 bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 font-stat-number font-black text-3xl flex items-center justify-center shadow-lg shadow-indigo-500/25 ring-4 ring-indigo-500/20 transition-transform hover:scale-105">
              {{ totalVisitedSites }}
            </div>
            <span class="text-xs font-bold text-slate-400 dark:text-white/50 mt-2">موقع تم زيارته</span>
          </div>

          <div class="pt-3 border-t border-slate-100 dark:border-white/10 text-xs font-bold text-indigo-600 dark:text-indigo-400 text-center truncate relative z-10">
            شملتها جولات الاستشاريين
          </div>
        </SpatialCard>

        <!-- Card 3: Total Visits Card (Blue Theme) -->
        <SpatialCard
          padding="p-6"
          class="min-h-[260px] flex flex-col justify-between border-t-4 border-t-blue-500 rounded-3xl relative overflow-hidden bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-slate-200/80 dark:border-white/10 shadow-[0_8px_32px_rgba(0,0,0,0.06)] dark:shadow-[0_8px_32px_rgba(0,0,0,0.36)] transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl space-y-4"
        >
          <div class="absolute -top-12 -right-12 w-40 h-40 rounded-full bg-blue-500/20 blur-3xl pointer-events-none"></div>

          <div class="text-center relative z-10">
            <span class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-white/60 block">
              إجمالي الزيارات الميدانية
            </span>
          </div>

          <div class="flex flex-col items-center justify-center relative z-10 py-1">
            <div class="w-20 h-20 rounded-full border-2 border-blue-500 bg-blue-500/15 text-blue-600 dark:text-blue-400 font-stat-number font-black text-3xl flex items-center justify-center shadow-lg shadow-blue-500/25 ring-4 ring-blue-500/20 transition-transform hover:scale-105">
              {{ totalVisits }}
            </div>
            <span class="text-xs font-bold text-slate-400 dark:text-white/50 mt-2">زيارة ميدانية</span>
          </div>

          <div class="pt-3 border-t border-slate-100 dark:border-white/10 text-xs font-bold text-blue-600 dark:text-blue-400 text-center truncate relative z-10">
            مستوفاة بالتقرير
          </div>
        </SpatialCard>

        <!-- Card 4: Total Executed Tasks Card (Amber Theme) -->
        <SpatialCard
          padding="p-6"
          class="min-h-[260px] flex flex-col justify-between border-t-4 border-t-amber-500 rounded-3xl relative overflow-hidden bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-slate-200/80 dark:border-white/10 shadow-[0_8px_32px_rgba(0,0,0,0.06)] dark:shadow-[0_8px_32px_rgba(0,0,0,0.36)] transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl space-y-4"
        >
          <div class="absolute -top-12 -right-12 w-40 h-40 rounded-full bg-amber-500/20 blur-3xl pointer-events-none"></div>

          <div class="text-center relative z-10">
            <span class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-white/60 block">
              إجمالي المهام المنفذة
            </span>
          </div>

          <div class="flex flex-col items-center justify-center relative z-10 py-1">
            <div class="w-20 h-20 rounded-full border-2 border-amber-500 bg-amber-500/15 text-amber-600 dark:text-amber-400 font-stat-number font-black text-3xl flex items-center justify-center shadow-lg shadow-amber-500/25 ring-4 ring-amber-500/20 transition-transform hover:scale-105">
              {{ totalExecutedTasks }}
            </div>
            <span class="text-xs font-bold text-slate-400 dark:text-white/50 mt-2">تنفيذ (يومي + طارئ)</span>
          </div>

          <div class="pt-3 border-t border-slate-100 dark:border-white/10 text-xs font-bold text-amber-600 dark:text-amber-400 text-center truncate relative z-10">
            من قِبل جميع الاستشاريين
          </div>
        </SpatialCard>
      </div>

      <!-- Multi-Filter Bar -->
      <SpatialCard padding="p-5" class="relative z-30">
        <form @submit.prevent="applyFilters" class="space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <SpatialDropdown
              v-model="filterForm.consultant_id"
              label="الاستشاري"
              placeholder="جميع الاستشاريين"
              :options="consultantOptions"
            />

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

          <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200/60 dark:border-white/10">
            <SpatialButton type="submit" variant="primary" size="md">
              <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
              </svg>
              <span>تطبيق التصفية</span>
            </SpatialButton>

            <SpatialButton
              v-if="hasActiveFilters"
              type="button"
              variant="secondary"
              size="md"
              @click="resetFilters"
            >
              <span>إعادة تعيين الفلاتر</span>
            </SpatialButton>
          </div>
        </form>
      </SpatialCard>

      <!-- Consultants Data Grid -->
      <SpatialCard padding="p-0 overflow-hidden" class="relative z-10">
        <div class="p-4 border-b border-slate-200 dark:border-white/10 flex items-center justify-between bg-slate-50/50 dark:bg-white/5">
          <h3 class="font-black text-sm text-slate-900 dark:text-white flex items-center gap-2">
            <span>👨‍💼 قائمة الاستشاريين وإحصائيات الأداء</span>
            <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-mono text-xs font-black">
              {{ consultantsReport ? consultantsReport.length : 0 }} استشاري
            </span>
          </h3>
        </div>

        <div class="overflow-x-auto custom-scroll">
          <table class="w-full text-right border-collapse">
            <thead>
              <tr class="border-b border-slate-200 dark:border-white/10 text-xs font-black text-slate-600 dark:text-white/70 bg-slate-100/90 dark:bg-white/5">
                <th class="p-4">اسم الاستشاري</th>
                <th class="p-4 text-center">عدد المواقع المزارة</th>
                <th class="p-4 text-center">إجمالي الزيارات الميدانية</th>
                <th class="p-4 text-center">المهام اليومية المنفذة</th>
                <th class="p-4 text-center">المهام الإضافية (عند الحاجة)</th>
                <th class="p-4 text-center w-48">الإجراءات والتحليل</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-sm font-bold">
              <tr
                v-for="c in consultantsReport"
                :key="c.consultant_id"
                class="transition-colors hover:bg-slate-50/80 dark:hover:bg-white/5"
              >
                <td class="p-4">
                  <div class="font-black text-slate-900 dark:text-white">
                    {{ c.consultant_name }}
                  </div>
                  <div class="font-mono text-xs text-slate-400 mt-0.5 flex items-center gap-2">
                    <span>كود: {{ c.employee_number }}</span>
                    <span>•</span>
                    <span>{{ c.specialization }}</span>
                  </div>
                </td>
                <td class="p-4 text-center">
                  <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-mono text-xs font-black border border-indigo-500/20">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h5m0 0v-5a2 2 0 00-2-2h-2a2 2 0 00-2 2v5"/>
                    </svg>
                    <span>{{ c.visited_sites_count }} موقع</span>
                  </span>
                </td>
                <td class="p-4 text-center">
                  <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-mono text-xs font-black border border-emerald-500/20">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>{{ c.total_visits_count }} زيارة</span>
                  </span>
                </td>
                <td class="p-4 text-center">
                  <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 font-mono text-xs font-black border border-blue-500/20">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    <span>{{ c.daily_tasks_executions }} تنفيذ</span>
                  </span>
                </td>
                <td class="p-4 text-center">
                  <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 font-mono text-xs font-black border border-amber-500/20">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span>{{ c.on_demand_tasks_executions }} تنفيذ</span>
                  </span>
                </td>
                <td class="p-4 text-center">
                  <button
                    type="button"
                    @click="openChoiceModal(c)"
                    class="inline-flex items-center justify-center gap-2 px-3.5 py-2 rounded-xl bg-gradient-to-r from-emerald-500/15 to-teal-500/15 hover:from-emerald-600 hover:to-teal-600 text-emerald-600 dark:text-emerald-400 hover:text-white border border-emerald-500/20 hover:border-transparent text-xs font-black transition-all shadow-2xs hover:shadow-md cursor-pointer group"
                  >
                    <svg class="w-4 h-4 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z" />
                    </svg>
                    <span>تفاصيل الأداء والتحليل</span>
                  </button>
                </td>
              </tr>

              <tr v-if="!consultantsReport || consultantsReport.length === 0">
                <td colspan="6" class="p-12 text-center">
                  <SpatialEmptyState
                    title="لا توجد بيانات استشاريين مطابقة"
                    description="لم نجد أي نتائج للاستشاريين تتطابق مع التصفية الحالية."
                    action-text="إعادة تعيين الفلاتر"
                    @action="resetFilters"
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </SpatialCard>
    </div>

    <!-- Spatial Choice Modal for Consultant Report View -->
    <SpatialModal
      :isOpen="isChoiceModalOpen"
      title="اختر نوع التحليل المطلوب للاستشاري"
      maxWidth="md"
      @close="isChoiceModalOpen = false"
    >
      <div class="space-y-4 py-2" v-if="selectedConsultant">
        <!-- Selected Consultant Info Card -->
        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200/80 dark:border-white/10 flex items-center justify-between">
          <div>
            <h4 class="font-black text-sm text-slate-900 dark:text-white">
              {{ selectedConsultant.consultant_name }}
            </h4>
            <span class="text-xs font-mono text-slate-400">
              كود: {{ selectedConsultant.employee_number }} | {{ selectedConsultant.specialization }}
            </span>
          </div>
          <span class="px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-mono text-xs font-black">
            استشاري 👨‍💼
          </span>
        </div>

        <p class="text-xs font-bold text-slate-500 dark:text-white/60">
          يرجى تحديد أسلوب تحليل البيانات المطلوب لعرض هذا التقرير:
        </p>

        <!-- Two Big Spatial Choice Cards -->
        <div class="grid grid-cols-1 gap-3">
          <!-- Option 1: By Site -->
          <Link
            :href="route('admin.reports.consultant-sites', selectedConsultant.consultant_id)"
            class="p-4 rounded-2xl border border-slate-200 dark:border-white/10 bg-white/60 dark:bg-white/5 hover:border-indigo-500 hover:bg-indigo-500/5 transition-all group flex items-start gap-3.5"
          >
            <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-lg shrink-0 group-hover:bg-indigo-500 group-hover:text-white transition-all">
              🏢
            </div>
            <div class="flex-1">
              <div class="flex items-center justify-between">
                <h5 class="font-black text-sm text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                  1. التحليل حسب الموقع الميداني
                </h5>
                <span class="text-xs text-indigo-500 font-black flex items-center gap-1">
                  <span>فتح</span>
                  <svg class="w-3.5 h-3.5 transform group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                  </svg>
                </span>
              </div>
              <p class="text-xs font-bold text-slate-500 dark:text-white/60 mt-1 leading-relaxed">
                عرض جميع المواقع التي زارها هذا الاستشاري، وعدد زياراته لكل موقع، وتفاصيل المهام المنفذة فيها.
              </p>
            </div>
          </Link>

          <!-- Option 2: By Task -->
          <Link
            :href="route('admin.reports.consultant-tasks', selectedConsultant.consultant_id)"
            class="p-4 rounded-2xl border border-slate-200 dark:border-white/10 bg-white/60 dark:bg-white/5 hover:border-amber-500 hover:bg-amber-500/5 transition-all group flex items-start gap-3.5"
          >
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-lg shrink-0 group-hover:bg-amber-500 group-hover:text-white transition-all">
              📋
            </div>
            <div class="flex-1">
              <div class="flex items-center justify-between">
                <h5 class="font-black text-sm text-slate-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">
                  2. التحليل حسب المهمة والتكرار
                </h5>
                <span class="text-xs text-amber-500 font-black flex items-center gap-1">
                  <span>فتح</span>
                  <svg class="w-3.5 h-3.5 transform group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                  </svg>
                </span>
              </div>
              <p class="text-xs font-bold text-slate-500 dark:text-white/60 mt-1 leading-relaxed">
                استعراض المهام المنفذة، واختيار مهمة محددة لمعرفة جميع المواقع التي نفذ فيها هذه المهمة وتكرارها.
              </p>
            </div>
          </Link>
        </div>
      </div>
    </SpatialModal>
  </HRLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import HRLayout from '@/Layouts/HRLayout.vue';
import SpatialToast from '@/Components/Spatial/SpatialToast.vue';
import SpatialCard from '@/Components/Spatial/SpatialCard.vue';
import SpatialButton from '@/Components/Spatial/SpatialButton.vue';
import SpatialInput from '@/Components/Spatial/SpatialInput.vue';
import SpatialDropdown from '@/Components/Spatial/SpatialDropdown.vue';
import SpatialEmptyState from '@/Components/Spatial/SpatialEmptyState.vue';
import SpatialModal from '@/Components/Spatial/SpatialModal.vue';

const props = defineProps({
  consultantsReport: Array,
  filters: Object,
  consultants: Array,
});

const toastRef = ref(null);
const isChoiceModalOpen = ref(false);
const selectedConsultant = ref(null);

const totalConsultants = computed(() => (props.consultantsReport || []).length);
const totalVisitedSites = computed(() => (props.consultantsReport || []).reduce((acc, c) => acc + (Number(c.visited_sites_count) || 0), 0));
const totalVisits = computed(() => (props.consultantsReport || []).reduce((acc, c) => acc + (Number(c.total_visits_count) || 0), 0));
const totalExecutedTasks = computed(() => (props.consultantsReport || []).reduce((acc, c) => acc + (Number(c.daily_tasks_executions) || 0) + (Number(c.on_demand_tasks_executions) || 0), 0));

const filterForm = reactive({
  consultant_id: props.filters?.consultant_id || '',
  date_from: props.filters?.date_from || '',
  date_to: props.filters?.date_to || '',
});

const hasActiveFilters = computed(() => {
  return (
    filterForm.consultant_id ||
    filterForm.date_from ||
    filterForm.date_to
  );
});

const consultantOptions = computed(() => {
  const opts = [{ label: 'جميع الاستشاريين', value: '' }];
  (props.consultants || []).forEach((c) => {
    opts.push({ label: `${c.full_name} (${c.employee_number})`, value: c.id });
  });
  return opts;
});

function applyFilters() {
  router.get(route('admin.reports.consultants'), filterForm, {
    preserveState: true,
    preserveScroll: true,
  });
}

function resetFilters() {
  filterForm.consultant_id = '';
  filterForm.date_from = '';
  filterForm.date_to = '';
  applyFilters();
}

function openChoiceModal(consultant) {
  selectedConsultant.value = consultant;
  isChoiceModalOpen.value = true;
}
</script>
