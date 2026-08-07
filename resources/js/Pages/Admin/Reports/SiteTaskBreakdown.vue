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
                <td class="p-4">
                  <div class="flex flex-wrap gap-1.5">
                    <span
                      v-for="(name, idx) in task.consultants_list"
                      :key="idx"
                      class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-white/10 text-slate-800 dark:text-white/90 text-xs font-bold"
                    >
                      👨‍💼 {{ name }}
                    </span>
                    <span v-if="!task.consultants_list || task.consultants_list.length === 0" class="text-slate-400 text-xs">
                      -
                    </span>
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
  </HRLayout>
</template>

<script setup>
import { reactive } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import HRLayout from '@/Layouts/HRLayout.vue';
import SpatialToast from '@/Components/Spatial/SpatialToast.vue';
import SpatialCard from '@/Components/Spatial/SpatialCard.vue';
import SpatialButton from '@/Components/Spatial/SpatialButton.vue';
import SpatialInput from '@/Components/Spatial/SpatialInput.vue';
import SpatialEmptyState from '@/Components/Spatial/SpatialEmptyState.vue';

const props = defineProps({
  siteData: Object,
  taskBreakdown: Array,
  filters: Object,
  sites: Array,
});

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
