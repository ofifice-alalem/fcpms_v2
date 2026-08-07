<template>
  <ConsultantLayout title="تقاريري وسجلاتي الميدانية">
    <SpatialToast ref="toastRef" />

    <div class="max-w-6xl mx-auto space-y-6 pb-20">
      <!-- Header Banner -->
      <SpatialCard padding="p-6" class="relative overflow-hidden z-10">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
          <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500/20 to-primary/20 border border-primary/30 flex items-center justify-center text-primary shadow-inner">
              <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
              </svg>
            </div>
            <div>
              <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">
                تقاريري وسجلاتي الميدانية الشخصية
              </h1>
              <p class="text-xs font-bold text-slate-500 dark:text-white/60 mt-1">
                الاستشاري: {{ consultant?.full_name }} ({{ consultant?.employee_number }})
              </p>
            </div>
          </div>

          <!-- Export Action Buttons -->
          <div class="flex items-center gap-2 w-full sm:w-auto">
            <SpatialButton
              variant="primary"
              size="md"
              @click="handleExport('excel')"
              class="flex-1 sm:flex-initial"
            >
              <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
              <span>تصدير Excel</span>
            </SpatialButton>

            <SpatialButton
              variant="secondary"
              size="md"
              @click="handleExport('csv')"
              class="flex-1 sm:flex-initial"
            >
              <span>تصدير CSV</span>
            </SpatialButton>
          </div>
        </div>
      </SpatialCard>

      <!-- KPI Highlight Cards Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Completion Rate Card -->
        <SpatialCard padding="p-5" class="relative z-10">
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-500 dark:text-white/60">مؤشر الإنجاز الميداني</span>
            <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-black">
              %
            </div>
          </div>
          <div class="mt-3 flex items-baseline gap-2">
            <span class="text-3xl font-black font-mono text-slate-900 dark:text-white">
              {{ metrics?.completion_rate || 0 }}%
            </span>
            <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400">معدل الإكتمال</span>
          </div>
          <div class="w-full h-2 bg-slate-100 dark:bg-white/10 rounded-full mt-3 overflow-hidden">
            <div
              class="h-full bg-gradient-to-r from-emerald-500 to-teal-400 rounded-full transition-all duration-500"
              :style="{ width: (metrics?.completion_rate || 0) + '%' }"
            ></div>
          </div>
        </SpatialCard>

        <!-- Total Site Visits Card -->
        <SpatialCard padding="p-5" class="relative z-10">
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-500 dark:text-white/60">إجمالي الزيارات</span>
            <div class="w-8 h-8 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-black">
              📍
            </div>
          </div>
          <div class="mt-3 flex items-baseline gap-2">
            <span class="text-3xl font-black font-mono text-slate-900 dark:text-white">
              {{ metrics?.total_site_visits || 0 }}
            </span>
            <span class="text-xs font-bold text-slate-400">زيارة ميدانية</span>
          </div>
          <div class="mt-2 text-[11px] font-bold text-slate-500 dark:text-white/50">
            مكتملة: {{ metrics?.completed_visits || 0 }} | قيد التنفيذ: {{ metrics?.in_progress_visits || 0 }}
          </div>
        </SpatialCard>

        <!-- Total Days Worked Card -->
        <SpatialCard padding="p-5" class="relative z-10">
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-500 dark:text-white/60">أيام الدوام المنجزة</span>
            <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center font-black">
              📅
            </div>
          </div>
          <div class="mt-3 flex items-baseline gap-2">
            <span class="text-3xl font-black font-mono text-slate-900 dark:text-white">
              {{ metrics?.total_days_worked || 0 }}
            </span>
            <span class="text-xs font-bold text-slate-400">يوم دوام</span>
          </div>
          <div class="mt-2 text-[11px] font-bold text-amber-600 dark:text-amber-400">
            أيام مكتملة بالسجل اليومي
          </div>
        </SpatialCard>

        <!-- On-Demand Tasks Card -->
        <SpatialCard padding="p-5" class="relative z-10">
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-500 dark:text-white/60">المهام الإضافية</span>
            <div class="w-8 h-8 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-black">
              ⚡
            </div>
          </div>
          <div class="mt-3 flex items-baseline gap-2">
            <span class="text-3xl font-black font-mono text-slate-900 dark:text-white">
              {{ metrics?.on_demand_tasks || 0 }}
            </span>
            <span class="text-xs font-bold text-slate-400">مهمة عند الحاجة</span>
          </div>
          <div class="mt-2 text-[11px] font-bold text-indigo-600 dark:text-indigo-400">
            تم تنفيذها بالميدان
          </div>
        </SpatialCard>
      </div>

      <!-- Filter Controls Bar -->
      <SpatialCard padding="p-4" class="relative z-20">
        <form @submit.prevent="applyFilters" class="flex flex-col sm:flex-row items-end gap-3">
          <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-3 w-full">
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
            <SpatialInput
              v-model="filterForm.city"
              placeholder="ابحث بالمدينة..."
              label="المدينة"
            />
          </div>

          <div class="flex items-center gap-2 self-stretch sm:self-auto">
            <SpatialButton type="submit" variant="primary" size="md" class="flex-1 sm:flex-initial">
              <span>تطبيق الفلترة</span>
            </SpatialButton>

            <SpatialButton
              v-if="filterForm.date_from || filterForm.date_to || filterForm.city"
              type="button"
              variant="secondary"
              size="md"
              @click="resetFilters"
            >
              <span>إعادة تعيين</span>
            </SpatialButton>
          </div>
        </form>
      </SpatialCard>

      <!-- Personal Log Data Grid -->
      <SpatialCard padding="p-0 overflow-hidden" class="relative z-10">
        <div class="p-4 border-b border-slate-200 dark:border-white/10 flex items-center justify-between bg-slate-50/50 dark:bg-white/5">
          <h3 class="font-black text-sm text-slate-900 dark:text-white flex items-center gap-2">
            <span>📋 أرشيف سجل الزيارات الميدانية</span>
            <span class="px-2.5 py-0.5 rounded-full bg-primary/10 text-primary font-mono text-xs font-black">
              {{ metrics?.recent_visits_log ? metrics.recent_visits_log.length : 0 }}
            </span>
          </h3>
        </div>

        <div class="overflow-x-auto custom-scroll">
          <table class="w-full text-right border-collapse">
            <thead>
              <tr class="border-b border-slate-200 dark:border-white/10 text-xs font-black text-slate-600 dark:text-white/70 bg-slate-100/90 dark:bg-white/5">
                <th class="p-4">تاريخ الزيارة</th>
                <th class="p-4">موقع العمل المزار</th>
                <th class="p-4">المدينة</th>
                <th class="p-4">وقت الدخول / الخروج</th>
                <th class="p-4 text-center">حالة الزيارة</th>
                <th class="p-4 text-center w-28">الإجراءات</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-sm font-bold">
              <tr
                v-for="visit in metrics?.recent_visits_log"
                :key="visit.id"
                class="transition-colors hover:bg-slate-50/80 dark:hover:bg-white/5"
              >
                <td class="p-4 font-mono text-xs font-black text-slate-700 dark:text-white/80">
                  {{ formatDate(visit.created_at) }}
                </td>
                <td class="p-4 font-black text-slate-900 dark:text-white">
                  {{ visit.site?.name || 'موقع ميداني' }}
                </td>
                <td class="p-4 text-xs font-bold text-slate-600 dark:text-white/70">
                  {{ visit.site?.city || '-' }}
                </td>
                <td class="p-4 font-mono text-xs text-slate-600 dark:text-white/80">
                  {{ formatTime(visit.check_in_time) }} ⬅️ {{ formatTime(visit.check_out_time) }}
                </td>
                <td class="p-4 text-center">
                  <SpatialStatusPill :type="visit.status === 'completed' ? 'completed' : 'pending'">
                    {{ visit.status === 'completed' ? 'مكتملة 🟢' : 'قيد التنفيذ 🟡' }}
                  </SpatialStatusPill>
                </td>
                <td class="p-4 text-center">
                  <SpatialIconButton
                    variant="info"
                    title="استكشاف تفاصيل الزيارة"
                    @click="openVisitModal(visit.id)"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                  </SpatialIconButton>
                </td>
              </tr>

              <tr v-if="!metrics?.recent_visits_log || metrics.recent_visits_log.length === 0">
                <td colspan="6" class="p-12 text-center">
                  <SpatialEmptyState
                    title="لا يوجد سجلات زيارات ميدانية"
                    description="لم نجد أي سجل زيارة يطابق خيارات الفلترة المحددة."
                    action-text="إعادة تعيين الفلترة"
                    @action="resetFilters"
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </SpatialCard>
    </div>

    <!-- Detail Modal -->
    <ReportVisitDetailModal
      :is-open="isModalOpen"
      :visit-data="activeVisitData"
      @close="isModalOpen = false"
    />
  </ConsultantLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { router } from '@inertiajs/vue3';
import ConsultantLayout from '@/Layouts/ConsultantLayout.vue';
import SpatialToast from '@/Components/Spatial/SpatialToast.vue';
import SpatialCard from '@/Components/Spatial/SpatialCard.vue';
import SpatialButton from '@/Components/Spatial/SpatialButton.vue';
import SpatialInput from '@/Components/Spatial/SpatialInput.vue';
import SpatialStatusPill from '@/Components/Spatial/SpatialStatusPill.vue';
import SpatialIconButton from '@/Components/Spatial/SpatialIconButton.vue';
import SpatialEmptyState from '@/Components/Spatial/SpatialEmptyState.vue';
import ReportVisitDetailModal from '@/Components/Reports/ReportVisitDetailModal.vue';

const props = defineProps({
  consultant: Object,
  metrics: Object,
  filters: Object,
});

const toastRef = ref(null);
const isModalOpen = ref(false);
const activeVisitData = ref(null);

const filterForm = reactive({
  date_from: props.filters?.date_from || '',
  date_to: props.filters?.date_to || '',
  city: props.filters?.city || '',
});

function applyFilters() {
  router.get(route('consultant.reports.index'), filterForm, {
    preserveState: true,
    preserveScroll: true,
  });
}

function resetFilters() {
  filterForm.date_from = '';
  filterForm.date_to = '';
  filterForm.city = '';
  applyFilters();
}

function handleExport(format) {
  toastRef.value?.addToast('info', `جاري تصدير التقرير بصيغة ${format.toUpperCase()}...`);
  const queryParams = new URLSearchParams({
    format,
    ...filterForm,
  }).toString();
  window.location.href = route('consultant.reports.export') + '?' + queryParams;
}

async function openVisitModal(visitId) {
  try {
    const response = await fetch(route('consultant.reports.visit-detail', visitId));
    const json = await response.json();
    if (json.success) {
      activeVisitData.value = json.data;
      isModalOpen.value = true;
    }
  } catch (err) {
    toastRef.value?.addToast('danger', 'تعذر جلب تفاصيل الزيرة الميدانية');
  }
}

function formatDate(dateStr) {
  if (!dateStr) return '-';
  return String(dateStr).substring(0, 10);
}

function formatTime(timeStr) {
  if (!timeStr) return '--:--';
  return String(timeStr).substring(11, 16) || String(timeStr).substring(0, 5);
}
</script>
