<template>
  <HRLayout :title="`تقرير تحليل المهام والتكرار للاستشاري: ${consultant?.full_name || ''}`">
    <SpatialToast ref="toastRef" />

    <div class="space-y-6">
      <!-- Top Navigation & Consultant Banner -->
      <SpatialCard padding="p-6" class="relative overflow-hidden z-10">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
          <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-500/20 to-orange-500/20 border border-amber-500/30 flex items-center justify-center text-amber-500 shadow-inner">
              <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
              </svg>
            </div>
            <div>
              <div class="flex items-center gap-2">
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">
                  تحليل تكرار المهام عبر المواقع للاستشاري: {{ consultant?.full_name }}
                </h1>
                <span class="px-2.5 py-0.5 rounded-full bg-amber-500/10 text-amber-600 dark:text-amber-400 font-mono text-xs font-black">
                  كود: {{ consultant?.employee_number }}
                </span>
              </div>
              <p class="text-xs font-bold text-slate-500 dark:text-white/60 mt-1">
                اختر أي مهمة من القائمة أدناه لمعاينة المواقع الميدانية التي نفذ الاستشاري فيها هذه المهمة وكم مرة تكررت
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

      <!-- Task Selection Bar -->
      <SpatialCard padding="p-5" class="relative z-30">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-end">
          <SpatialDropdown
            v-model="selectedTaskId"
            label="اختر المهمة الميدانية للمعاينة"
            placeholder="عرض جميع المهام المنفذة"
            :options="taskOptions"
          />

          <div class="flex items-center gap-2 pb-1">
            <span class="text-xs font-bold text-slate-500 dark:text-white/60">
              إجمالي المهام المتاحة لهذا الاستشاري:
            </span>
            <span class="px-3 py-1 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 font-mono text-xs font-black border border-amber-500/20">
              {{ executedTasks ? executedTasks.length : 0 }} مهمة
            </span>
          </div>
        </div>
      </SpatialCard>

      <!-- Task Results Display -->
      <div v-if="filteredTasksBreakdown && filteredTasksBreakdown.length > 0" class="space-y-6">
        <div
          v-for="task in filteredTasksBreakdown"
          :key="task.task_id"
          class="space-y-3"
        >
          <!-- Task Header Card -->
          <SpatialCard padding="p-4 overflow-hidden" class="relative z-10">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-3 border-b border-slate-200 dark:border-white/10">
              <div class="flex items-center gap-3">
                <div
                  :class="[
                    'w-10 h-10 rounded-xl flex items-center justify-center font-bold text-base shrink-0',
                    task.task_type === 'إضافية'
                      ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20'
                      : 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20'
                  ]"
                >
                  {{ task.task_type === 'إضافية' ? '⚡' : '📋' }}
                </div>
                <div>
                  <h3 class="font-black text-base text-slate-900 dark:text-white">
                    {{ task.title }}
                  </h3>
                  <div class="flex items-center gap-2 mt-0.5">
                    <span
                      :class="[
                        'px-2.5 py-0.5 rounded-full text-[11px] font-black',
                        task.task_type === 'إضافية'
                          ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400'
                          : 'bg-blue-500/10 text-blue-600 dark:text-blue-400'
                      ]"
                    >
                      مهمة {{ task.task_type }}
                    </span>
                    <span class="text-xs font-bold text-slate-400">•</span>
                    <span class="text-xs font-bold text-slate-500 dark:text-white/60">
                      نفذت في {{ task.sites ? task.sites.length : 0 }} مواقع مختلفة
                    </span>
                  </div>
                </div>
              </div>

              <div class="flex items-center gap-2">
                <span class="px-3.5 py-1.5 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-mono text-xs font-black border border-indigo-500/20">
                  🔄 إجمالي تكرار الإنجاز: {{ task.total_executions }} مرة
                </span>
              </div>
            </div>

            <!-- Sites Table for this Task -->
            <div class="overflow-x-auto custom-scroll mt-3">
              <table class="w-full text-right border-collapse">
                <thead>
                  <tr class="border-b border-slate-200 dark:border-white/10 text-xs font-black text-slate-600 dark:text-white/70 bg-slate-100/90 dark:bg-white/5">
                    <th class="p-3.5">اسم الموقع</th>
                    <th class="p-3.5">المدينة</th>
                    <th class="p-3.5 text-center">تكرار تنفيذ المهمة بالموقع</th>
                    <th class="p-3.5 text-center">أحدث تاريخ تنفيذ</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-sm font-bold">
                  <tr
                    v-for="site in task.sites"
                    :key="site.site_id"
                    class="transition-colors hover:bg-slate-50/80 dark:hover:bg-white/5"
                  >
                    <td class="p-3.5">
                      <div class="font-black text-slate-900 dark:text-white">
                        {{ site.site_name }}
                      </div>
                      <div class="font-mono text-xs text-slate-400 mt-0.5">
                        كود: {{ site.site_code }}
                      </div>
                    </td>
                    <td class="p-3.5 text-xs font-bold text-slate-600 dark:text-white/70">
                      {{ site.site_city }}
                    </td>
                    <td class="p-3.5 text-center">
                      <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 font-mono text-xs font-black border border-amber-500/20">
                        <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>{{ site.execution_count }} مرة</span>
                      </span>
                    </td>
                    <td class="p-3.5 text-center font-mono text-xs text-slate-500 dark:text-white/70">
                      {{ formatDate(site.last_executed) }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </SpatialCard>
        </div>
      </div>

      <!-- Empty State -->
      <SpatialCard v-else padding="p-12 text-center" class="relative z-10">
        <SpatialEmptyState
          title="لم يتم تسجيل أي تكرارات تنفيذ لهذه المهمة"
          description="لم يقم الاستشاري بتنفيذ المهمة المحددة خلال الفترة المحددة."
        />
      </SpatialCard>
    </div>
  </HRLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import HRLayout from '@/Layouts/HRLayout.vue';
import SpatialToast from '@/Components/Spatial/SpatialToast.vue';
import SpatialCard from '@/Components/Spatial/SpatialCard.vue';
import SpatialDropdown from '@/Components/Spatial/SpatialDropdown.vue';
import SpatialEmptyState from '@/Components/Spatial/SpatialEmptyState.vue';

const props = defineProps({
  consultant: Object,
  executedTasks: Array,
  tasksBreakdown: Array,
  filters: Object,
});

const toastRef = ref(null);
const selectedTaskId = ref('');

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

const taskOptions = computed(() => {
  const opts = [{ label: 'عرض جميع المهام المنفذة', value: '' }];
  (props.executedTasks || []).forEach((t) => {
    opts.push({
      label: `${t.title} [مهمة ${t.task_type}]`,
      value: String(t.id),
    });
  });
  return opts;
});

const filteredTasksBreakdown = computed(() => {
  if (!props.tasksBreakdown) return [];
  if (!selectedTaskId.value) return props.tasksBreakdown;
  return props.tasksBreakdown.filter(
    (t) => String(t.task_id) === String(selectedTaskId.value)
  );
});
</script>
