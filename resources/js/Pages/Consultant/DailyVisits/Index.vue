<template>
  <ConsultantLayout title="سجل الزيارات اليومية الميدانية">
    <div class="space-y-6">
      
      <!-- Toast Feedback Component -->
      <SpatialToast ref="toastRef" />

      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-black text-slate-900 dark:text-white flex items-center gap-2">
            📍 سجل الزيارات اليومية الميدانية
          </h1>
          <p class="text-xs font-bold text-slate-500 dark:text-white/60 mt-1">
            متابعة إنجاز مواقع العمل، توثيق النماذج، وإضافة المهام عند الحاجة
          </p>
        </div>

        <div class="flex items-center gap-3 flex-wrap">
          <!-- View Mode Toggle (Grid vs Table) -->
          <div class="flex items-center gap-1 p-1 rounded-xl bg-slate-100 dark:bg-white/5 border border-slate-200/60 dark:border-white/10">
            <button
              @click="viewMode = 'grid'"
              :class="[
                'p-2 rounded-lg transition-all cursor-pointer select-none flex items-center gap-1 text-xs font-bold',
                viewMode === 'grid'
                  ? 'bg-primary text-white shadow-sm'
                  : 'text-slate-500 dark:text-white/60 hover:text-slate-900 dark:hover:text-white'
              ]"
              title="عرض كروت شبكية"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
              </svg>
              <span>كروت</span>
            </button>

            <button
              @click="viewMode = 'table'"
              :class="[
                'p-2 rounded-lg transition-all cursor-pointer select-none flex items-center gap-1 text-xs font-bold',
                viewMode === 'table'
                  ? 'bg-primary text-white shadow-sm'
                  : 'text-slate-500 dark:text-white/60 hover:text-slate-900 dark:hover:text-white'
              ]"
              title="عرض جدول تفصيلي"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
              </svg>
              <span>جدول</span>
            </button>
          </div>

          <!-- START DAY / NEW VISIT BUTTONS -->
          <SpatialButton
            v-if="!dailyRecord || !dailyRecord.check_in_time"
            variant="primary"
            :disabled="isSubmitting"
            @click="handleStartDay"
          >
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            <span>بدء اليوم العملي</span>
          </SpatialButton>

          <Link :href="isHistorical && selectedDate ? route('consultant.site-visits.create', { date: selectedDate }) : route('consultant.site-visits.create')">
            <SpatialButton variant="primary">
              <div class="w-6 h-6 rounded-xl bg-white/20 flex items-center justify-center text-white">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/>
                </svg>
              </div>
              <span>بدء زيارة موقع جديد</span>
            </SpatialButton>
          </Link>
        </div>
      </div>

      <!-- Top Summary Widgets (Glassmorphic Frosted Executive Cards) -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 relative z-10">
        <!-- Widget 1: Consultant Profile -->
        <SpatialCard
          padding="p-7"
          class="min-h-[295px] flex flex-col justify-between border-t-4 border-t-primary rounded-3xl relative overflow-hidden text-center space-y-4 bg-white/70 dark:bg-slate-950/60 backdrop-blur-xl border border-white/50 dark:border-white/10 shadow-[0_8px_32px_rgba(0,0,0,0.06)] dark:shadow-[0_8px_32px_rgba(0,0,0,0.36)] transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl"
        >
          <!-- Ambient Glass Radial Glow -->
          <div class="absolute -top-14 -right-14 w-44 h-44 rounded-full bg-primary/20 blur-3xl pointer-events-none"></div>

          <!-- Top Centered Avatar Ring -->
          <div class="flex flex-col items-center space-y-2.5 relative z-10">
            <div class="w-20 h-20 rounded-full border-2 border-primary/40 bg-gradient-to-br from-primary/30 via-primary/10 to-transparent text-primary flex items-center justify-center text-3xl font-black shadow-lg shadow-primary/20 ring-4 ring-primary/10 backdrop-blur-md p-1">
              👨‍💼
            </div>
            <span class="text-xs font-mono font-black text-primary px-3.5 py-1 rounded-full bg-primary/15 dark:bg-primary/20 border border-primary/30 backdrop-blur-md shadow-sm">
              {{ consultant.employee_number }}
            </span>
          </div>

          <!-- Middle Name & Subtitle -->
          <div class="space-y-1 relative z-10">
            <h3 class="text-xl font-black text-slate-900 dark:text-white leading-tight">
              {{ consultant.full_name }}
            </h3>
            <p class="text-xs font-black text-slate-500 dark:text-white/60 uppercase tracking-widest">
              الاستشاري الميداني
            </p>
          </div>

          <!-- Bottom Grid Items (Enlarged & High-Impact Numbers) -->
          <div class="grid grid-cols-2 gap-4 pt-3 text-xs font-bold relative z-10">
            <div class="text-center flex flex-col items-center space-y-2">
              <div class="w-16 h-16 rounded-full border-2 border-emerald-500 bg-emerald-500/15 text-emerald-500 font-stat-number font-black text-2xl flex items-center justify-center shadow-xl shadow-emerald-500/25 ring-4 ring-emerald-500/15 transition-transform hover:scale-105">
                🟢
              </div>
              <span class="text-sm font-black text-slate-800 dark:text-white block">نشط ومعتمد</span>
            </div>

            <div class="text-center flex flex-col items-center space-y-2">
              <div class="w-16 h-16 rounded-full border-2 border-primary/50 bg-primary/15 text-primary font-stat-number font-black text-sm flex items-center justify-center shadow-xl shadow-primary/20 ring-4 ring-primary/15 transition-transform hover:scale-105">
                ID
              </div>
              <span class="text-sm font-black text-slate-800 dark:text-white block font-stat-number">{{ consultant.employee_number }}</span>
            </div>
          </div>
        </SpatialCard>

        <!-- Widget 2: Daily Progress Ring (Glass Progress Ring) -->
        <SpatialCard
          padding="p-7"
          class="min-h-[310px] flex flex-col justify-between border-t-4 border-t-emerald-500 rounded-3xl relative overflow-hidden text-center space-y-4 bg-white/70 dark:bg-slate-950/60 backdrop-blur-xl border border-white/50 dark:border-white/10 shadow-[0_8px_32px_rgba(0,0,0,0.06)] dark:shadow-[0_8px_32px_rgba(0,0,0,0.36)] transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl"
        >
          <!-- Ambient Glass Radial Glow -->
          <div class="absolute -top-14 -right-14 w-44 h-44 rounded-full bg-emerald-500/25 blur-3xl pointer-events-none"></div>

          <!-- Top Centered SVG Circular Progress Ring -->
          <div class="flex flex-col items-center justify-center space-y-2 relative z-10">
            <div class="relative drop-shadow-[0_0_18px_rgba(16,185,129,0.35)]">
              <SpatialCircularProgress
                :value="dailyRecord.completion_percentage || 0"
                :size="135"
                :stroke-width="11"
                color="emerald"
              >
                <div class="flex flex-col items-center justify-center">
                  <span class="text-3xl font-black font-stat-number tracking-tight text-slate-900 dark:text-white leading-none">
                    {{ Number(dailyRecord.completion_percentage || 0).toFixed(0) }}%
                  </span>
                  <span v-if="(dailyRecord.completion_percentage || 0) >= 100" class="text-base mt-1 animate-bounce">🏆</span>
                </div>
              </SpatialCircularProgress>
            </div>

            <span class="text-xs font-black text-slate-500 dark:text-white/60 uppercase tracking-widest block pt-1">
              نسبة إنجاز اليوم
            </span>
          </div>

          <!-- Bottom Grid Items (Enlarged & High-Impact Numbers) -->
          <div class="grid grid-cols-2 gap-4 pt-3 text-xs font-bold relative z-10">
            <div class="text-center flex flex-col items-center space-y-2">
              <div class="w-16 h-16 rounded-full border-2 border-emerald-500 bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-stat-number font-black text-2xl flex items-center justify-center shadow-xl shadow-emerald-500/30 ring-4 ring-emerald-500/20 drop-shadow-[0_2px_10px_rgba(16,185,129,0.4)] transition-transform hover:scale-105">
                {{ dailyRecord.completed_daily_tasks || 0 }}
              </div>
              <span class="text-sm font-black text-slate-800 dark:text-white block">المهام المكتملة</span>
            </div>

            <div class="text-center flex flex-col items-center space-y-2">
              <div class="w-16 h-16 rounded-full border-2 border-blue-500 bg-blue-500/20 text-blue-600 dark:text-blue-400 font-stat-number font-black text-2xl flex items-center justify-center shadow-xl shadow-blue-500/30 ring-4 ring-blue-500/20 drop-shadow-[0_2px_10px_rgba(59,130,246,0.4)] transition-transform hover:scale-105">
                {{ dailyRecord.required_daily_tasks || 0 }}
              </div>
              <span class="text-sm font-black text-slate-800 dark:text-white block">المهام المطلوبة</span>
            </div>
          </div>
        </SpatialCard>

        <!-- Widget 3: Attendance & Visits Stats -->
        <SpatialCard
          padding="p-7"
          class="min-h-[310px] flex flex-col justify-between border-t-4 border-t-indigo-500 rounded-3xl relative overflow-hidden text-center space-y-4 bg-white/70 dark:bg-slate-950/60 backdrop-blur-xl border border-white/50 dark:border-white/10 shadow-[0_8px_32px_rgba(0,0,0,0.06)] dark:shadow-[0_8px_32px_rgba(0,0,0,0.36)] transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl"
        >
          <!-- Ambient Glass Radial Glow -->
          <div class="absolute -top-14 -right-14 w-44 h-44 rounded-full bg-indigo-500/25 blur-3xl pointer-events-none"></div>

          <!-- Top Centered SVG Circular Gauge Ring -->
          <div class="flex flex-col items-center justify-center space-y-2 relative z-10">
            <div class="relative drop-shadow-[0_0_18px_rgba(99,102,241,0.35)]">
              <SpatialCircularProgress
                :value="siteVisits.length > 0 ? 100 : 0"
                :size="135"
                :stroke-width="11"
                color="indigo"
              >
                <div class="flex flex-col items-center justify-center">
                  <span class="text-3xl font-black font-stat-number tracking-tight text-indigo-600 dark:text-indigo-400 leading-none">
                    {{ siteVisits.length }}
                  </span>
                  <span class="text-xs font-bold text-slate-400 dark:text-white/40 mt-1">موقع 📍</span>
                </div>
              </SpatialCircularProgress>
            </div>

            <span class="text-xs font-black text-slate-500 dark:text-white/60 uppercase tracking-widest block pt-1">
              حالة اليوم والزيارات
            </span>
          </div>

          <!-- Bottom Grid Items (Enlarged & High-Impact Numbers) -->
          <div class="grid grid-cols-2 gap-4 pt-3 text-xs font-bold relative z-10">
            <div class="text-center flex flex-col items-center space-y-2">
              <div class="w-16 h-16 rounded-full border-2 border-indigo-500 bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 font-stat-number font-black text-2xl flex items-center justify-center shadow-xl shadow-indigo-500/30 ring-4 ring-indigo-500/20 drop-shadow-[0_2px_10px_rgba(99,102,241,0.4)] transition-transform hover:scale-105">
                1
              </div>
              <span class="text-sm font-black text-slate-800 dark:text-white block">
                {{ dailyRecord && dailyRecord.check_in_time ? 'اليوم قائم 🟢' : 'لم يبدأ' }}
              </span>
            </div>

            <div class="text-center flex flex-col items-center space-y-2">
              <div class="w-16 h-16 rounded-full border-2 border-slate-400 dark:border-white/30 bg-slate-100 dark:bg-white/10 text-slate-700 dark:text-white font-black text-xl flex items-center justify-center shadow-md ring-4 ring-slate-400/10 transition-transform hover:scale-105">
                📅
              </div>
              <span class="text-sm font-black text-slate-800 dark:text-white block truncate max-w-full">
                {{ todayDateFormatted }}
              </span>
            </div>
          </div>
        </SpatialCard>
      </div>

      <!-- Advanced Search & Filter Bar (Matching HR Style) -->
      <SpatialCard padding="p-5" class="relative z-20 space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
          <SpatialInput
            v-model="searchQuery"
            label="البحث السريع"
            placeholder="ابحث باسم أو كود الموقع..."
          />

          <SpatialDropdown
            v-model="selectedStatusFilter"
            label="حالة الزيارة"
            placeholder="جميع الحالات"
            :options="statusOptions"
          />

          <div class="flex items-center justify-end">
            <span class="text-xs font-bold text-slate-500 dark:text-white/60 font-mono">
              إجمالي نتائج العرض: {{ filteredVisits.length }}
            </span>
          </div>
        </div>
      </SpatialCard>

      <!-- 1. GRID CARDS VIEW (Ultra-Clean Dual Theme Design matching Past Records) -->
      <div v-if="viewMode === 'grid' && filteredVisits.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <div
          v-for="visit in filteredVisits"
          :key="visit.id"
          :class="[
            'relative p-5 rounded-3xl transition-all duration-200 flex flex-col justify-between space-y-4 border shadow-lg',
            getVisitProgress(visit) >= 100
              ? 'bg-white dark:bg-slate-800/80 border-slate-200/80 dark:border-white/10 shadow-slate-200/50 dark:shadow-black/30'
              : 'bg-amber-50/70 dark:bg-slate-800/90 border-amber-500/40 shadow-amber-500/10'
          ]"
        >
          <!-- Top Header: Site Name, Code Badge, & Status Pill -->
          <div class="flex items-start justify-between gap-2 pb-3 border-b border-slate-100 dark:border-white/10">
            <div class="space-y-1">
              <h3 class="text-base font-black text-slate-900 dark:text-white leading-tight">
                {{ visit.site ? visit.site.name : 'موقع ميداني' }}
              </h3>
              <span class="text-xs font-mono font-black text-slate-700 dark:text-slate-200 dir-ltr inline-block bg-slate-100 dark:bg-white/10 px-2.5 py-0.5 rounded-md border border-slate-200 dark:border-white/10">
                كود: {{ visit.site ? visit.site.code : '' }}
              </span>
            </div>

            <SpatialStatusPill
              :type="getVisitProgress(visit) >= 100 ? 'completed' : 'pending'"
              :pulse="getVisitProgress(visit) < 100"
              class="shrink-0"
            >
              {{ getVisitProgress(visit) >= 100 ? 'مكتملة' : 'قيد التنفيذ' }}
            </SpatialStatusPill>
          </div>

          <!-- Center Body: Circular Progress Ring & Metrics -->
          <div class="flex items-center justify-between gap-4 py-1">
            <!-- Center Circular Progress Ring -->
            <div class="flex flex-col items-center justify-center shrink-0 pr-1">
              <SpatialCircularProgress
                :percentage="getVisitProgress(visit)"
                :size="76"
                :strokeWidth="7"
              >
                <span class="text-base font-black font-mono text-slate-900 dark:text-white">
                  {{ getVisitProgress(visit) }}%
                </span>
              </SpatialCircularProgress>
              <span class="text-[10px] font-black text-slate-500 dark:text-white/60 mt-1">نسبة الإنجاز</span>
            </div>

            <!-- Right Metrics Stack -->
            <div class="flex-1 space-y-2">
              <!-- Check-in / Start Time -->
              <div class="flex items-center justify-between p-2 rounded-xl bg-slate-100/90 dark:bg-white/5 border border-slate-200/80 dark:border-white/10 text-xs shadow-2xs">
                <span class="font-black text-slate-700 dark:text-white/70">⏰ وقت البدء:</span>
                <span class="font-mono font-black text-slate-900 dark:text-white">
                  {{ formatTime(visit.visit_started_at) || '--' }}
                </span>
              </div>

              <!-- On-Demand Tasks Count -->
              <div class="flex items-center justify-between p-2 rounded-xl bg-slate-100/90 dark:bg-white/5 border border-slate-200/80 dark:border-white/10 text-xs shadow-2xs">
                <span class="font-black text-slate-700 dark:text-white/70">⚡ إضافية:</span>
                <span :class="['font-mono font-black', getOnDemandTasksCount(visit) > 0 ? 'text-amber-600 dark:text-amber-400 text-sm' : 'text-slate-400 dark:text-white/40']">
                  {{ getOnDemandTasksCount(visit) > 0 ? getOnDemandTasksCount(visit) : '--' }}
                </span>
              </div>

              <!-- Daily Tasks Fraction -->
              <div class="flex items-center justify-between p-2 rounded-xl bg-slate-100/90 dark:bg-white/5 border border-slate-200/80 dark:border-white/10 text-xs shadow-2xs">
                <span class="font-black text-slate-700 dark:text-white/70">📋 المهام:</span>
                <span class="font-mono font-black text-slate-900 dark:text-white dir-ltr">
                  {{ getDailyTasksFraction(visit) }}
                </span>
              </div>
            </div>
          </div>

          <!-- Bottom Actions Bar (Circular Ring Buttons - Enlarged Icons) -->
          <div class="pt-3 border-t border-slate-200/80 dark:border-white/10 flex items-center justify-between">
            <div class="flex items-center gap-3">
              <!-- View / Details Button -->
              <button
                @click="openDetailsModal(visit)"
                class="w-11 h-11 rounded-full border-2 border-blue-500/40 hover:border-blue-600 dark:border-blue-400/40 text-blue-600 dark:text-blue-400 bg-transparent hover:bg-blue-50 dark:hover:bg-blue-500/10 flex items-center justify-center transition-all duration-200 cursor-pointer shadow-xs active:scale-95"
                title="عرض تفاصيل الزيارة والمهام"
              >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
              </button>

              <!-- Edit / Execute Button -->
              <button
                @click="goToExecuteVisit(visit)"
                class="w-11 h-11 rounded-full border-2 border-amber-500/40 hover:border-amber-600 dark:border-amber-400/40 text-amber-600 dark:text-amber-400 bg-transparent hover:bg-amber-50 dark:hover:bg-amber-500/10 flex items-center justify-center transition-all duration-200 cursor-pointer shadow-xs active:scale-95"
                title="تعديل واستكمال البيانات"
              >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
              </button>
            </div>

            <!-- Delete / Cancel Button -->
            <button
              @click="confirmCancelVisit(visit)"
              class="w-11 h-11 rounded-full border-2 border-rose-500/40 hover:border-rose-600 dark:border-rose-400/40 text-rose-600 dark:text-rose-400 bg-transparent hover:bg-rose-50 dark:hover:bg-rose-500/10 flex items-center justify-center transition-all duration-200 cursor-pointer shadow-xs active:scale-95"
              title="إلغاء الزيارة"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- 2. HIGH-FIDELITY TABLE VIEW (MATCHING HR TABLE DESIGN) -->
      <SpatialCard v-else-if="viewMode === 'table' && filteredVisits.length > 0" padding="p-0 overflow-hidden" class="relative z-10">
        <div class="overflow-x-auto">
          <table class="w-full text-right border-collapse">
            <thead>
              <tr class="border-b border-slate-200 dark:border-white/10 text-xs font-black text-slate-600 dark:text-white/70 bg-slate-100/90 dark:bg-white/5">
                <th class="p-4">الموقع الميداني</th>
                <th class="p-4">كود الموقع</th>
                <th class="p-4 text-center">حالة الزيارة</th>
                <th class="p-4">نسبة الإنجاز</th>
                <th class="p-4 text-center">المهام الإضافية</th>
                <th class="p-4">وقت البدء</th>
                <th class="p-4 text-center">الإجراءات</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-sm font-bold">
              <tr
                v-for="visit in filteredVisits"
                :key="visit.id"
                class="transition-colors hover:bg-slate-50/80 dark:hover:bg-white/5"
              >
                <td class="p-4 font-black text-slate-900 dark:text-white">
                  {{ visit.site ? visit.site.name : 'موقع ميداني' }}
                </td>
                <td class="p-4 font-mono text-xs font-bold text-slate-500 dark:text-white/60">
                  {{ visit.site ? visit.site.code : '' }}
                </td>
                <td class="p-4 text-center whitespace-nowrap">
                  <SpatialStatusPill :type="getVisitProgress(visit) >= 100 ? 'completed' : 'pending'">
                    {{ getVisitProgress(visit) >= 100 ? 'مكتملة' : 'قيد التنفيذ' }}
                  </SpatialStatusPill>
                </td>
                <td class="p-4 w-44">
                  <div class="space-y-1">
                    <span class="text-[11px] font-bold font-mono text-emerald-500">{{ getVisitProgress(visit) }}%</span>
                    <SpatialProgressBar :value="getVisitProgress(visit)" />
                  </div>
                </td>
                <td class="p-4 text-center whitespace-nowrap">
                  <span class="px-2.5 py-1 rounded-lg bg-amber-500/15 text-amber-600 dark:text-amber-400 font-mono font-black text-xs border border-amber-500/20">
                    {{ getOnDemandTasksCount(visit) }} مهام
                  </span>
                </td>
                <td class="p-4 font-mono text-xs text-slate-500 dark:text-white/50 whitespace-nowrap">
                  {{ formatTime(visit.visit_started_at) }}
                </td>
                <td class="p-4 text-center whitespace-nowrap">
                  <div class="flex items-center justify-center gap-2">
                    <SpatialIconButton variant="ghost" title="عرض" @click="openDetailsModal(visit)">
                      <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </SpatialIconButton>
                    <SpatialIconButton variant="warning" title="تعديل" @click="goToExecuteVisit(visit)">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </SpatialIconButton>
                    <SpatialIconButton variant="danger" title="مسح" @click="confirmCancelVisit(visit)">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </SpatialIconButton>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </SpatialCard>

      <!-- Empty State -->
      <div v-else class="p-12 text-center rounded-3xl bg-slate-100 dark:bg-white/5 border border-dashed border-slate-300 dark:border-white/10 text-slate-500 dark:text-white/50 space-y-3">
        <p class="text-xs font-bold">لم تقم بزيارة أي موقع اليوم بعد. انقر على "بدء زيارة موقع جديد" للبدء.</p>
      </div>

    </div>

    <!-- MODALS -->
    <VisitDetailModal
      :is-open="showDetailModal"
      :visit="selectedModalVisit"
      @close="showDetailModal = false"
    />

    <CancelVisitModal
      :is-open="showCancelModal"
      :loading="isSubmitting"
      @close="showCancelModal = false"
      @confirm="handleCancelVisit"
    />
  </ConsultantLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import ConsultantLayout from '@/Layouts/ConsultantLayout.vue';
import SpatialCard from '@/Components/Spatial/SpatialCard.vue';
import SpatialButton from '@/Components/Spatial/SpatialButton.vue';
import SpatialIconButton from '@/Components/Spatial/SpatialIconButton.vue';
import SpatialStatusPill from '@/Components/Spatial/SpatialStatusPill.vue';
import SpatialProgressBar from '@/Components/Spatial/SpatialProgressBar.vue';
import SpatialInput from '@/Components/Spatial/SpatialInput.vue';
import SpatialDropdown from '@/Components/Spatial/SpatialDropdown.vue';
import SpatialToast from '@/Components/Spatial/SpatialToast.vue';
import SpatialCircularProgress from '@/Components/Spatial/SpatialCircularProgress.vue';
import VisitDetailModal from '@/Components/Consultant/VisitDetailModal.vue';
import CancelVisitModal from '@/Components/Consultant/CancelVisitModal.vue';

const props = defineProps({
  consultant: { type: Object, required: true },
  dailyRecord: { type: Object, required: true },
  availableSites: { type: Array, default: () => [] },
  activeVisit: { type: Object, default: null },
  availableOnDemandTasks: { type: Array, default: () => [] },
  isHistorical: { type: Boolean, default: false },
  selectedDate: { type: String, default: null },
});

const isSubmitting = ref(false);
const viewMode = ref('grid');
const searchQuery = ref('');
const selectedStatusFilter = ref('');

// Modals State
const showDetailModal = ref(false);
const selectedModalVisit = ref(null);

const showCancelModal = ref(false);
const visitToCancel = ref(null);

const statusOptions = [
  { label: 'جميع الحالات', value: '' },
  { label: 'قيد التنفيذ', value: 'in_progress' },
  { label: 'مكتملة', value: 'completed' },
];

const todayDateFormatted = computed(() => {
  return new Date().toLocaleDateString('ar-LY', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
});

const siteVisits = computed(() => {
  return props.dailyRecord && props.dailyRecord.site_visits ? props.dailyRecord.site_visits : [];
});

const filteredVisits = computed(() => {
  return siteVisits.value.filter((visit) => {
    const matchesSearch = !searchQuery.value ||
      (visit.site && (
        visit.site.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        visit.site.code.toLowerCase().includes(searchQuery.value.toLowerCase())
      ));
    const matchesStatus = !selectedStatusFilter.value || visit.status === selectedStatusFilter.value;
    return matchesSearch && matchesStatus;
  });
});

const getVisitProgress = (visit) => {
  if (!visit.task_responses || visit.task_responses.length === 0) return 0;
  const completed = visit.task_responses.filter((r) => {
    const hasValues = r.values && r.values.some(v => v.value && String(v.value).trim() !== '' && String(v.value) !== '[]' && String(v.value) !== 'null');
    return r.status === 'submitted' || (r.completed_at && hasValues);
  }).length;
  return Math.round((completed / visit.task_responses.length) * 100);
};

const getOnDemandTasksCount = (visit) => {
  if (!visit.task_responses) return 0;
  return visit.task_responses.filter(
    (r) => r.task_definition && (r.task_definition.task_type === 'on_demand' || r.task_definition.task_type?.value === 'on_demand')
  ).length;
};

const getDailyTasksFraction = (visit) => {
  if (!visit.task_responses) return '0 / 0';
  const dailyResponses = visit.task_responses.filter((r) => {
    return r.task_definition && (r.task_definition.task_type === 'daily' || r.task_definition.task_type?.value === 'daily');
  });
  if (dailyResponses.length === 0) return '0 / 0';
  const completed = dailyResponses.filter((r) => {
    const hasValues = r.values && r.values.some(v => v.value && String(v.value).trim() !== '' && String(v.value) !== '[]' && String(v.value) !== 'null');
    return r.status === 'submitted' || (r.completed_at && hasValues);
  }).length;
  return `${completed} / ${dailyResponses.length}`;
};

const formatTime = (isoString) => {
  if (!isoString) return '';
  return new Date(isoString).toLocaleTimeString('ar-LY', { hour: '2-digit', minute: '2-digit' });
};

// Handlers
const handleStartDay = () => {
  isSubmitting.value = true;
  const payload = {};
  if (props.isHistorical && props.selectedDate) {
    payload.date = props.selectedDate;
  }
  router.post('/consultant/daily-visits/start-day', payload, {
    onFinish: () => (isSubmitting.value = false),
  });
};

const goToExecuteVisit = (visit) => {
  if (props.isHistorical && props.selectedDate) {
    router.get(`/consultant/site-visits/${visit.id}/execute`, { date: props.selectedDate });
  } else {
    router.get(`/consultant/site-visits/${visit.id}/execute`);
  }
};

const openDetailsModal = (visit) => {
  selectedModalVisit.value = visit;
  showDetailModal.value = true;
};

const confirmCancelVisit = (visit) => {
  visitToCancel.value = visit;
  showCancelModal.value = true;
};

const handleCancelVisit = () => {
  if (!visitToCancel.value) return;
  isSubmitting.value = true;
  router.delete(`/consultant/site-visits/${visitToCancel.value.id}`, {
    onSuccess: () => {
      showCancelModal.value = false;
      visitToCancel.value = null;
      if (toastRef.value) {
        toastRef.value.show('تم إلغاء ومسح الزيارة المعلقة بنجاح', 'success');
      }
    },
    onError: () => {
      if (toastRef.value) {
        toastRef.value.show('عذراً، لا يمكن إلغاء أو مسح زيادة موثقة ومكتملة نهائياً', 'error');
      }
    },
    onFinish: () => (isSubmitting.value = false),
  });
};
</script>
