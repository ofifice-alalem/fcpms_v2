<template>
  <HRLayout title="لوحة التقارير والتحليلات الأداء الميداني">
    <SpatialToast ref="toastRef" />

    <div class="space-y-6">
      <!-- Header Banner -->
      <SpatialCard padding="p-6" class="relative overflow-hidden z-10">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
          <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary/20 to-indigo-500/20 border border-primary/30 flex items-center justify-center text-primary shadow-inner">
              <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
            </div>
            <div>
              <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">
                لوحة تحليلات الأداء الميداني والتصفيات التنفيذية
              </h1>
              <p class="text-xs font-bold text-slate-500 dark:text-white/60 mt-1">
                متابعة أداء الاستشاريين، مؤشرات الإنجاز، وإجمالي الزيارات والمهام على مستوى كافة الموقع
              </p>
            </div>
          </div>

          <!-- Export Actions -->
          <div class="flex items-center gap-2.5 w-full sm:w-auto">
            <SpatialButton
              variant="primary"
              size="md"
              @click="handleExport('excel')"
              class="flex-1 sm:flex-initial"
            >
              <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
              <span>تصدير التقرير التنفيذي (Excel)</span>
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

      <!-- Report Type Selector Hub (مركز اختيار نوع التقرير) -->
      <div class="space-y-3">
        <div class="flex items-center justify-between">
          <h2 class="text-base font-black text-slate-900 dark:text-white flex items-center gap-2">
            <span>📑 اختر نوع التقرير المطلوب</span>
            <span class="text-xs font-bold text-slate-400 dark:text-white/50">(حدد التقرير للانتقال للتفاصيل والتحليلات المخصصة)</span>
          </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
          <!-- Report 1: Executive Overview -->
          <div
            class="p-5 rounded-2xl border-2 transition-all cursor-pointer bg-primary/10 border-primary shadow-md relative overflow-hidden group"
          >
            <div class="flex items-start justify-between">
              <div class="w-10 h-10 rounded-xl bg-primary text-white flex items-center justify-center font-black text-lg">
                📊
              </div>
              <span class="px-2.5 py-1 rounded-full bg-primary/20 text-primary font-black text-[11px]">
                التقرير النشط ⚡
              </span>
            </div>
            <h3 class="mt-4 font-black text-slate-900 dark:text-white text-base">
              1. التقرير الشامل العام
            </h3>
            <p class="mt-1 text-xs font-bold text-slate-600 dark:text-white/70 leading-relaxed">
              عرض المؤشرات العامة، إجمالي الزيارات، ومتابعة سجلات كل استشاري وموقع.
            </p>
          </div>

          <!-- Report 2: Sites Performance Report -->
          <Link
            :href="route('admin.reports.sites')"
            class="p-5 rounded-2xl border border-slate-200/80 dark:border-white/10 transition-all cursor-pointer bg-white/60 dark:bg-white/5 hover:border-indigo-500/50 hover:shadow-lg relative overflow-hidden group"
          >
            <div class="flex items-start justify-between">
              <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-500 flex items-center justify-center font-black text-lg">
                🏢
              </div>
              <span class="px-2.5 py-1 rounded-full bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-black text-[11px] group-hover:bg-indigo-500 group-hover:text-white transition-all">
                فتح ⬅️
              </span>
            </div>
            <h3 class="mt-4 font-black text-slate-900 dark:text-white text-base group-hover:text-indigo-500 transition-colors">
              2. تقرير المواقع الميدانية
            </h3>
            <p class="mt-1 text-xs font-bold text-slate-500 dark:text-white/60 leading-relaxed">
              عرض المواقع المزارة، الاستشاريين الزائرين، والمهام المنفذة.
            </p>
          </Link>

          <!-- Report 3: Consultants Performance Report -->
          <Link
            :href="route('admin.reports.consultants')"
            class="p-5 rounded-2xl border border-slate-200/80 dark:border-white/10 transition-all cursor-pointer bg-white/60 dark:bg-white/5 hover:border-emerald-500/50 hover:shadow-lg relative overflow-hidden group"
          >
            <div class="flex items-start justify-between">
              <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center font-black text-lg">
                👨‍💼
              </div>
              <span class="px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-black text-[11px] group-hover:bg-emerald-500 group-hover:text-white transition-all">
                فتح ⬅️
              </span>
            </div>
            <h3 class="mt-4 font-black text-slate-900 dark:text-white text-base group-hover:text-emerald-500 transition-colors">
              3. تقرير الاستشاريين والتحليل
            </h3>
            <p class="mt-1 text-xs font-bold text-slate-500 dark:text-white/60 leading-relaxed">
              أداء الاستشاريين، المواقع المزارة، والتحليل المخصص حسب الموقع أو المهمة.
            </p>
          </Link>

          <!-- Report 4: Specific Site Task Breakdown Report -->
          <div
            @click="openSiteSelectorModal"
            class="p-5 rounded-2xl border border-slate-200/80 dark:border-white/10 transition-all cursor-pointer bg-white/60 dark:bg-white/5 hover:border-amber-500/50 hover:shadow-lg relative overflow-hidden group"
          >
            <div class="flex items-start justify-between">
              <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center font-black text-lg">
                🔍
              </div>
              <span class="px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-600 dark:text-amber-400 font-black text-[11px] group-hover:bg-amber-500 group-hover:text-white transition-all">
                اختر ⬅️
              </span>
            </div>
            <h3 class="mt-4 font-black text-slate-900 dark:text-white text-base group-hover:text-amber-500 transition-colors">
              4. تكرار المهام لموقع معين
            </h3>
            <p class="mt-1 text-xs font-bold text-slate-500 dark:text-white/60 leading-relaxed">
              مكان محدد لعرض تكرار كل مهمة وعدد وأسماء الاستشاريين.
            </p>
          </div>
        </div>
      </div>

      <!-- Enterprise KPI Highlight Cards Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Global Total Visits Card -->
        <SpatialCard padding="p-5" class="relative z-10">
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-500 dark:text-white/60">إجمالي الزيارات بالميدان</span>
            <div class="w-9 h-9 rounded-2xl bg-primary/10 text-primary flex items-center justify-center font-black">
              📍
            </div>
          </div>
          <div class="mt-3 flex items-baseline gap-2">
            <span class="text-3xl font-black font-mono text-slate-900 dark:text-white">
              {{ metrics?.system_total_visits || 0 }}
            </span>
            <span class="text-xs font-bold text-slate-400">زيارة ميدانية</span>
          </div>
          <div class="mt-2 text-[11px] font-bold text-slate-500 dark:text-white/50">
            مكتملة: {{ metrics?.completed_visits || 0 }} | قيد التنفيذ: {{ metrics?.in_progress_visits || 0 }}
          </div>
        </SpatialCard>

        <!-- Active Consultants Count Card -->
        <SpatialCard padding="p-5" class="relative z-10">
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-500 dark:text-white/60">الاستشاريين النشطين</span>
            <div class="w-9 h-9 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-black">
              👨‍💼
            </div>
          </div>
          <div class="mt-3 flex items-baseline gap-2">
            <span class="text-3xl font-black font-mono text-slate-900 dark:text-white">
              {{ metrics?.active_consultants_count || 0 }}
            </span>
            <span class="text-xs font-bold text-slate-400">استشاري فعال</span>
          </div>
          <div class="mt-2 text-[11px] font-bold text-emerald-600 dark:text-emerald-400">
            حالة نشطة ومتاحة في الميدان
          </div>
        </SpatialCard>

        <!-- System Completion Rate Card -->
        <SpatialCard padding="p-5" class="relative z-10">
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-500 dark:text-white/60">معدل الإلتزام والإكتمال</span>
            <div class="w-9 h-9 rounded-2xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center font-black">
              %
            </div>
          </div>
          <div class="mt-3 flex items-baseline gap-2">
            <span class="text-3xl font-black font-mono text-slate-900 dark:text-white">
              {{ metrics?.system_completion_rate || 0 }}%
            </span>
            <span class="text-xs font-bold text-blue-600 dark:text-blue-400">نسبة التزام كلي</span>
          </div>
          <div class="w-full h-2 bg-slate-100 dark:bg-white/10 rounded-full mt-3 overflow-hidden">
            <div
              class="h-full bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full transition-all duration-500"
              :style="{ width: (metrics?.system_completion_rate || 0) + '%' }"
            ></div>
          </div>
        </SpatialCard>

        <!-- Total On-Demand Tasks Card -->
        <SpatialCard padding="p-5" class="relative z-10">
          <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-500 dark:text-white/60">إجمالي المهام الإضافية</span>
            <div class="w-9 h-9 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center font-black">
              ⚡
            </div>
          </div>
          <div class="mt-3 flex items-baseline gap-2">
            <span class="text-3xl font-black font-mono text-slate-900 dark:text-white">
              {{ metrics?.on_demand_tasks_count || 0 }}
            </span>
            <span class="text-xs font-bold text-slate-400">مهمة عند الحاجة</span>
          </div>
          <div class="mt-2 text-[11px] font-bold text-amber-600 dark:text-amber-400">
            تم افتتاحها وإنجازها طارئاً
          </div>
        </SpatialCard>
      </div>

      <!-- Multi-Filter Bar -->
      <SpatialCard padding="p-5" class="relative z-30">
        <form @submit.prevent="applyFilters" class="space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <SpatialDropdown
              v-model="filterForm.consultant_id"
              label="الاستشاري الميداني"
              placeholder="جميع الاستشاريين"
              :options="consultantOptions"
            />

            <SpatialDropdown
              v-model="filterForm.site_id"
              label="موقع العمل"
              placeholder="جميع المواقع"
              :options="siteOptions"
            />

            <SpatialDropdown
              v-model="filterForm.city"
              label="المدينة"
              placeholder="جميع المدن"
              :options="cityOptions"
            />

            <div class="grid grid-cols-2 gap-2">
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

      <!-- Enterprise Field Data Grid -->
      <SpatialCard padding="p-0 overflow-hidden" class="relative z-10">
        <div class="p-4 border-b border-slate-200 dark:border-white/10 flex items-center justify-between bg-slate-50/50 dark:bg-white/5">
          <h3 class="font-black text-sm text-slate-900 dark:text-white flex items-center gap-2">
            <span>📊 سجل النشاطات والزيارات الميدانية للتطبيق</span>
            <span class="px-2.5 py-0.5 rounded-full bg-primary/10 text-primary font-mono text-xs font-black">
              {{ metrics?.visits_log ? metrics.visits_log.length : 0 }} سجل
            </span>
          </h3>
        </div>

        <div class="overflow-x-auto custom-scroll">
          <table class="w-full text-right border-collapse">
            <thead>
              <tr class="border-b border-slate-200 dark:border-white/10 text-xs font-black text-slate-600 dark:text-white/70 bg-slate-100/90 dark:bg-white/5">
                <th class="p-4">تاريخ اليوم</th>
                <th class="p-4">الاستشاري الميداني</th>
                <th class="p-4">موقع العمل</th>
                <th class="p-4">المدينة</th>
                <th class="p-4">وقت الدخول / الخروج</th>
                <th class="p-4 text-center">حالة الزيارة</th>
                <th class="p-4 text-center w-28">الإجراءات</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-sm font-bold">
              <tr
                v-for="visit in metrics?.visits_log"
                :key="visit.id"
                class="transition-colors hover:bg-slate-50/80 dark:hover:bg-white/5"
              >
                <td class="p-4 font-mono text-xs font-black text-slate-700 dark:text-white/80">
                  {{ formatDate(visit.daily_record?.record_date || visit.created_at) }}
                </td>
                <td class="p-4">
                  <div class="font-black text-slate-900 dark:text-white">
                    {{ visit.daily_record?.consultant?.full_name || 'غير معروف' }}
                  </div>
                  <div class="font-mono text-xs text-slate-400 mt-0.5">
                    {{ visit.daily_record?.consultant?.employee_number }}
                  </div>
                </td>
                <td class="p-4 font-black text-slate-800 dark:text-white/90">
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
                    title="معاينة تفاصيل الإجابات"
                    @click="openVisitModal(visit.id)"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                  </SpatialIconButton>
                </td>
              </tr>

              <tr v-if="!metrics?.visits_log || metrics.visits_log.length === 0">
                <td colspan="7" class="p-12 text-center">
                  <SpatialEmptyState
                    title="لا يوجد زيارات ميدانية مطابقة"
                    description="لم نجد أي زيارة ميدانية تتطابق مع معايير التصفية والخيارات المحددة."
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

    <!-- Detail Modal -->
    <ReportVisitDetailModal
      :is-open="isModalOpen"
      :visit-data="activeVisitData"
      @close="isModalOpen = false"
    />

    <!-- Site Selector Modal for Report 3 -->
    <SpatialModal
      :is-open="isSiteSelectorOpen"
      title="اختيار موقع لعرض تقرير تكرار المهام"
      max-width="md"
      @close="isSiteSelectorOpen = false"
    >
      <div class="space-y-4 py-2">
        <p class="text-xs font-bold text-slate-600 dark:text-white/70">
          حدد موقع العمل الميداني لعرض قائمة المهام المنفذة عليه، كم مرة تكررت كل مهمة وأسماء الاستشاريين الذين نفذوها:
        </p>

        <SpatialDropdown
          v-model="selectedSiteId"
          label="موقع العمل الميداني"
          placeholder="اختر الموقع..."
          :options="modalSiteOptions"
        />
      </div>

      <template #footer>
        <div class="flex items-center justify-end gap-2 w-full">
          <SpatialButton
            variant="secondary"
            size="sm"
            @click="isSiteSelectorOpen = false"
          >
            إلغاء
          </SpatialButton>

          <SpatialButton
            variant="primary"
            size="sm"
            :disabled="!selectedSiteId"
            @click="goToSiteBreakdown"
          >
            فتح التقرير التفصيلي 🚀
          </SpatialButton>
        </div>
      </template>
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
import SpatialStatusPill from '@/Components/Spatial/SpatialStatusPill.vue';
import SpatialIconButton from '@/Components/Spatial/SpatialIconButton.vue';
import SpatialEmptyState from '@/Components/Spatial/SpatialEmptyState.vue';
import SpatialModal from '@/Components/Spatial/SpatialModal.vue';
import ReportVisitDetailModal from '@/Components/Reports/ReportVisitDetailModal.vue';

const props = defineProps({
  metrics: Object,
  filters: Object,
  consultants: Array,
  sites: Array,
  cities: Array,
});

const toastRef = ref(null);
const isModalOpen = ref(false);
const activeVisitData = ref(null);

const isSiteSelectorOpen = ref(false);
const selectedSiteId = ref('');

const modalSiteOptions = computed(() => {
  const opts = [];
  (props.sites || []).forEach((s) => {
    opts.push({ label: `${s.name} - ${s.city}`, value: s.id });
  });
  return opts;
});

function openSiteSelectorModal() {
  selectedSiteId.value = props.filters?.site_id || '';
  isSiteSelectorOpen.value = true;
}

function goToSiteBreakdown() {
  if (!selectedSiteId.value) return;
  isSiteSelectorOpen.value = false;
  router.get(route('admin.reports.site-breakdown', selectedSiteId.value));
}

const filterForm = reactive({
  consultant_id: props.filters?.consultant_id || '',
  site_id: props.filters?.site_id || '',
  city: props.filters?.city || '',
  date_from: props.filters?.date_from || '',
  date_to: props.filters?.date_to || '',
});

const hasActiveFilters = computed(() => {
  return (
    filterForm.consultant_id ||
    filterForm.site_id ||
    filterForm.city ||
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

const siteOptions = computed(() => {
  const opts = [{ label: 'جميع المواقع', value: '' }];
  (props.sites || []).forEach((s) => {
    opts.push({ label: `${s.name} - ${s.city}`, value: s.id });
  });
  return opts;
});

const cityOptions = computed(() => {
  const opts = [{ label: 'جميع المدن', value: '' }];
  (props.cities || []).forEach((c) => {
    opts.push({ label: c, value: c });
  });
  return opts;
});

function applyFilters() {
  router.get(route('admin.reports.index'), filterForm, {
    preserveState: true,
    preserveScroll: true,
  });
}

function resetFilters() {
  filterForm.consultant_id = '';
  filterForm.site_id = '';
  filterForm.city = '';
  filterForm.date_from = '';
  filterForm.date_to = '';
  applyFilters();
}

function handleExport(format) {
  toastRef.value?.addToast('info', `جاري تصدير التقرير التنفيذي بصيغة ${format.toUpperCase()}...`);
  const queryParams = new URLSearchParams({
    format,
    ...filterForm,
  }).toString();
  window.location.href = route('admin.reports.export') + '?' + queryParams;
}

async function openVisitModal(visitId) {
  try {
    const response = await fetch(route('admin.reports.visit-detail', visitId), {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
    });
    const json = await response.json();
    if (json.success) {
      activeVisitData.value = json.data;
      isModalOpen.value = true;
    } else {
      toastRef.value?.addToast('danger', json.message || 'تعذر جلب تفاصيل الزيارة الميدانية');
    }
  } catch (err) {
    toastRef.value?.addToast('danger', 'تعذر جلب تفاصيل الزيارة الميدانية');
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
