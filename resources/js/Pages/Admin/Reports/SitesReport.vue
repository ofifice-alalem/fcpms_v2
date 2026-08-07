<template>
  <HRLayout title="تقارير إحصائيات الأماكن والمواقع الميدانية">
    <SpatialToast ref="toastRef" />

    <div class="space-y-6">
      <!-- Header Banner -->
      <SpatialCard padding="p-6" class="relative overflow-hidden z-10">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
          <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500/20 to-primary/20 border border-indigo-500/30 flex items-center justify-center text-indigo-500 shadow-inner">
              <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h5m0 0v-5a2 2 0 00-2-2h-2a2 2 0 00-2 2v5"/>
              </svg>
            </div>
            <div>
              <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">
                تقارير الأداء التفصيلي للمواقع والأماكن الميدانية
              </h1>
              <p class="text-xs font-bold text-slate-500 dark:text-white/60 mt-1">
                تتبع حركة زيارات الاستشاريين، المهام القياسية وإحساس المهام الإضافية بكل موقع على حدة خلال الفترة المحددة
              </p>
            </div>
          </div>
        </div>
      </SpatialCard>

      <!-- Reports Navigation Tabs -->
      <div class="flex items-center gap-2 p-1.5 bg-slate-100 dark:bg-white/5 rounded-2xl border border-slate-200/80 dark:border-white/10">
        <Link
          :href="route('admin.reports.index')"
          class="flex items-center gap-2 px-4 py-2.5 rounded-xl font-black text-xs text-slate-600 dark:text-white/70 hover:text-slate-900 dark:hover:text-white transition-all"
        >
          <span>📊 التقرير الشامل العام</span>
        </Link>
        <Link
          :href="route('admin.reports.sites')"
          class="flex items-center gap-2 px-4 py-2.5 rounded-xl font-black text-xs transition-all bg-primary text-white shadow-sm"
        >
          <span>🏢 تقارير إحصائيات المواقع والأماكن</span>
        </Link>
      </div>

      <!-- Multi-Filter Bar -->
      <SpatialCard padding="p-5" class="relative z-30">
        <form @submit.prevent="applyFilters" class="space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
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

      <!-- Sites Data Grid -->
      <SpatialCard padding="p-0 overflow-hidden" class="relative z-10">
        <div class="p-4 border-b border-slate-200 dark:border-white/10 flex items-center justify-between bg-slate-50/50 dark:bg-white/5">
          <h3 class="font-black text-sm text-slate-900 dark:text-white flex items-center gap-2">
            <span>🏢 قائمة إحصائيات الأماكن والمواقع</span>
            <span class="px-2.5 py-0.5 rounded-full bg-primary/10 text-primary font-mono text-xs font-black">
              {{ sitesReport ? sitesReport.length : 0 }} موقع
            </span>
          </h3>
        </div>

        <div class="overflow-x-auto custom-scroll">
          <table class="w-full text-right border-collapse">
            <thead>
              <tr class="border-b border-slate-200 dark:border-white/10 text-xs font-black text-slate-600 dark:text-white/70 bg-slate-100/90 dark:bg-white/5">
                <th class="p-4">اسم الموقع</th>
                <th class="p-4">المدينة</th>
                <th class="p-4 text-center">عدد الاستشاريين الزائرين</th>
                <th class="p-4 text-center">إجمالي الزيارات</th>
                <th class="p-4 text-center">المهام اليومية المنفذة</th>
                <th class="p-4 text-center">المهام الإضافية (عند الحاجة)</th>
                <th class="p-4 text-center w-44">تقرير تفاصيل الموقع</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-sm font-bold">
              <tr
                v-for="site in sitesReport"
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
                  <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-mono text-xs font-black border border-emerald-500/20">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>{{ site.consultants_count }} استشاري</span>
                  </span>
                </td>
                <td class="p-4 text-center">
                  <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-mono text-xs font-black border border-indigo-500/20">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>{{ site.total_visits_count }} زيارة</span>
                  </span>
                </td>
                <td class="p-4 text-center">
                  <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 font-mono text-xs font-black border border-blue-500/20">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    <span>{{ site.regular_tasks_count }} مهمة</span>
                  </span>
                </td>
                <td class="p-4 text-center">
                  <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 font-mono text-xs font-black border border-amber-500/20">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span>{{ site.on_demand_tasks_count }} مهمة</span>
                  </span>
                </td>
                <td class="p-4 text-center">
                  <Link
                    :href="route('admin.reports.site-breakdown', site.site_id)"
                    class="inline-flex items-center justify-center gap-2 px-3.5 py-2 rounded-xl bg-gradient-to-r from-primary/15 to-indigo-500/15 hover:from-primary hover:to-indigo-600 text-primary hover:text-white border border-primary/20 hover:border-transparent text-xs font-black transition-all shadow-2xs hover:shadow-md group"
                  >
                    <svg class="w-4 h-4 text-primary group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z" />
                    </svg>
                    <span>تحليل المهام والتكرار</span>
                    <svg class="w-3.5 h-3.5 text-primary group-hover:text-white transform group-hover:-translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                  </Link>
                </td>
              </tr>

              <tr v-if="!sitesReport || sitesReport.length === 0">
                <td colspan="7" class="p-12 text-center">
                  <SpatialEmptyState
                    title="لا توجد بيانات مواضع مطابقة"
                    description="لم نجد أي نتائج للمواقع تتطابق مع التصفية الحالية."
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

const props = defineProps({
  sitesReport: Array,
  filters: Object,
  sites: Array,
  cities: Array,
});

const toastRef = ref(null);

const filterForm = reactive({
  site_id: props.filters?.site_id || '',
  city: props.filters?.city || '',
  date_from: props.filters?.date_from || '',
  date_to: props.filters?.date_to || '',
});

const hasActiveFilters = computed(() => {
  return (
    filterForm.site_id ||
    filterForm.city ||
    filterForm.date_from ||
    filterForm.date_to
  );
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
  router.get(route('admin.reports.sites'), filterForm, {
    preserveState: true,
    preserveScroll: true,
  });
}

function resetFilters() {
  filterForm.site_id = '';
  filterForm.city = '';
  filterForm.date_from = '';
  filterForm.date_to = '';
  applyFilters();
}
</script>
