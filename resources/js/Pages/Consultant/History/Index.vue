<template>
  <ConsultantLayout title="سجل الأيام السابقة والأستدراك">
    <div class="max-w-7xl mx-auto space-y-6 dir-rtl text-right">

      <!-- Top Header & Month Filter Bar -->
      <div class="p-6 rounded-3xl bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl border border-black/10 dark:border-white/10 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="space-y-1">
          <div class="flex items-center gap-2">
            <span class="px-2.5 py-1 rounded-full text-xs font-black bg-primary/10 text-primary dark:bg-primary/20 border border-primary/20">
              سجل الحضور والإنجاز الشهري
            </span>
          </div>
          <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight mt-1">
            سجل الأيام السابقة والفتح بأثر رجعي
          </h1>
          <p class="text-xs text-slate-500 dark:text-white/60 font-bold">
            استعرض حالة كافة أيام العمل، وقم بفتح الأيام السابقة للتوثيق والاستدراك
          </p>
        </div>

        <!-- Month Picker -->
        <div class="flex items-center gap-3 bg-slate-100 dark:bg-white/5 p-2 rounded-2xl border border-slate-200 dark:border-white/10 shrink-0">
          <label class="text-xs font-black text-slate-700 dark:text-white/80 shrink-0 pr-2">اختر الشهر:</label>
          <input
            type="month"
            v-model="monthFilter"
            @change="handleMonthChange"
            class="px-3 py-2 rounded-xl bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-black border border-slate-200 dark:border-white/10 focus:outline-none focus:ring-2 focus:ring-primary dir-ltr"
          />
        </div>
      </div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <SpatialCard padding="p-5" class="space-y-2">
          <span class="text-xs font-bold text-slate-500 dark:text-white/50 block">إجمالي أيام العمل</span>
          <span class="text-2xl font-black text-slate-900 dark:text-white font-mono block">
            {{ stats.total_working_days }} <span class="text-xs font-normal">يوم</span>
          </span>
        </SpatialCard>

        <SpatialCard padding="p-5" class="space-y-2 border-emerald-500/30">
          <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 block">أيام الحضور والتوثيق 🟢</span>
          <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400 font-mono block">
            {{ stats.attended_count }} <span class="text-xs font-normal">يوم</span>
          </span>
        </SpatialCard>

        <SpatialCard padding="p-5" class="space-y-2 border-rose-500/30">
          <span class="text-xs font-bold text-rose-600 dark:text-rose-400 block">أيام الغياب / بدون سجل 🔴</span>
          <span class="text-2xl font-black text-rose-600 dark:text-rose-400 font-mono block">
            {{ stats.absent_count }} <span class="text-xs font-normal">يوم</span>
          </span>
        </SpatialCard>

        <SpatialCard padding="p-5" class="space-y-2 border-primary/30">
          <span class="text-xs font-bold text-primary dark:text-blue-400 block">متوسط إنجاز الشهر 📊</span>
          <span class="text-2xl font-black text-primary dark:text-blue-400 font-mono block">
            {{ stats.avg_progress }}%
          </span>
        </SpatialCard>
      </div>

      <!-- Days Table Card -->
      <SpatialCard padding="p-6" class="space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-white/10 pb-4">
          <h3 class="text-base font-black text-slate-900 dark:text-white flex items-center gap-2">
            <span>📅 جدول السجلات لأيام شهر {{ formattedMonthName }}</span>
          </h3>
          <span class="text-xs font-mono text-slate-500 dark:text-white/50">
            إجمالي السجلات: {{ days.length }} يوم
          </span>
        </div>

        <!-- Desktop View Table (High-Fidelity Enterprise Design) -->
        <div class="hidden md:block overflow-x-auto rounded-2xl border border-slate-200/80 dark:border-white/10">
          <table class="w-full text-right border-collapse">
            <thead>
              <tr class="bg-slate-100/70 dark:bg-white/5 border-b border-slate-200/80 dark:border-white/10 text-xs font-black text-slate-600 dark:text-white/60 uppercase tracking-wider">
                <th class="py-3.5 px-4">التاريخ واليوم</th>
                <th class="py-3.5 px-4">آخر تعديل</th>
                <th class="py-3.5 px-4">الحالة</th>
                <th class="py-3.5 px-4">المواقع المزارة</th>
                <th class="py-3.5 px-4 text-center">المهام الإضافية</th>
                <th class="py-3.5 px-4">نسبة الإنجاز</th>
                <th class="py-3.5 px-4 text-center">الإجراء والفتح</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-sm">
              <tr
                v-for="day in days"
                :key="day.date"
                :class="[
                  'hover:bg-slate-50/70 dark:hover:bg-white/[0.03] transition-colors align-middle',
                  day.is_today ? 'bg-primary/5 dark:bg-primary/10 font-bold' : ''
                ]"
              >
                <!-- Date & Day Name & Start Time -->
                <td class="py-4 px-4 font-bold text-slate-900 dark:text-white whitespace-nowrap align-middle">
                  <div class="flex items-start gap-2.5">
                    <span v-if="day.is_today" class="w-2.5 h-2.5 mt-1 rounded-full bg-primary animate-ping shrink-0"></span>
                    <div>
                      <div class="font-black text-sm text-slate-900 dark:text-white">{{ day.day_name }}</div>
                      <div class="text-xs font-mono text-slate-500 dark:text-white/50 dir-ltr text-right mt-0.5">{{ day.formatted_date }}</div>
                      <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 text-[11px] font-extrabold border border-indigo-500/20 mt-1.5" v-if="day.record && day.record.check_in_time">
                        ⏰ {{ day.record.check_in_time }}
                      </div>
                    </div>
                  </div>
                </td>

                <!-- Last Modified -->
                <td class="py-4 px-4 font-bold text-slate-900 dark:text-white whitespace-nowrap align-middle">
                  <div
                    v-if="day.record && day.record.updated_date"
                    :class="[
                      'inline-block px-3.5 py-2 rounded-2xl transition-all',
                      !day.record.is_same_day_update
                        ? 'border-2 border-amber-500/50 dark:border-amber-400/50 bg-amber-500/10 dark:bg-amber-400/10 shadow-2xs'
                        : 'border border-slate-200/80 dark:border-white/10 bg-slate-100/60 dark:bg-white/5'
                    ]"
                  >
                    <div class="flex items-center gap-1.5">
                      <span class="font-black text-sm" :class="!day.record.is_same_day_update ? 'text-amber-700 dark:text-amber-300' : 'text-slate-900 dark:text-white'">
                        {{ day.record.updated_day_name }}
                      </span>
                      <span v-if="!day.record.is_same_day_update" class="text-[10px] px-1.5 py-0.2 rounded bg-amber-500/20 text-amber-800 dark:text-amber-300 font-bold border border-amber-500/30">
                        أثر رجعي
                      </span>
                    </div>
                    <div class="text-xs font-mono text-slate-500 dark:text-white/50 dir-ltr text-right mt-0.5">{{ day.record.updated_date }}</div>
                    <div class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 mt-1" v-if="day.record.updated_time">
                      🕒 {{ day.record.updated_time }}
                    </div>
                  </div>
                  <div v-else class="text-slate-400 dark:text-white/30 text-xs font-normal">
                    --
                  </div>
                </td>

                <!-- Status Pill -->
                <td class="py-4 px-4 align-middle">
                  <span
                    :class="[
                      'inline-flex items-center px-3 py-1 rounded-full text-xs font-black border shadow-2xs',
                      getStatusClass(day.status_type)
                    ]"
                  >
                    {{ day.status_label }}
                  </span>
                </td>

                <!-- Visited sites -->
                <td class="py-4 px-4 align-middle">
                  <span v-if="day.record" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-slate-100 dark:bg-white/5 font-mono text-xs font-black text-slate-800 dark:text-white border border-slate-200/80 dark:border-white/10">
                    📍 {{ day.record.site_visits_count }} مواقع
                  </span>
                  <span v-else class="text-slate-400 dark:text-white/30 text-xs font-normal">--</span>
                </td>

                <!-- On-demand tasks count -->
                <td class="py-4 px-4 text-center align-middle">
                  <span v-if="day.record && day.record.on_demand_tasks_count > 0" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-amber-500/10 text-amber-700 dark:text-amber-400 font-mono text-xs font-black border border-amber-500/20">
                    ⚡ {{ day.record.on_demand_tasks_count }}
                  </span>
                  <span v-else class="text-slate-400 dark:text-white/30 text-xs font-normal">
                    --
                  </span>
                </td>

                <!-- Progress percentage -->
                <td class="py-4 px-4 align-middle">
                  <div v-if="day.record" class="flex items-center gap-2.5 w-36">
                    <div class="flex-1 bg-slate-200/80 dark:bg-slate-700/80 h-2.5 rounded-full overflow-hidden p-0.5 border border-slate-200 dark:border-white/10">
                      <div
                        class="h-full bg-primary rounded-full transition-all duration-300 shadow-xs"
                        :style="{ width: `${day.record.completion_percentage}%` }"
                      ></div>
                    </div>
                    <span class="text-xs font-mono font-black text-slate-800 dark:text-white">
                      {{ Math.round(day.record.completion_percentage) }}%
                    </span>
                  </div>
                  <span v-else class="text-xs text-slate-400 dark:text-white/30">-</span>
                </td>

                <!-- Action Button -->
                <td class="py-4 px-4 text-center">
                  <span v-if="day.status === 'before_hire'" class="text-xs font-bold text-slate-400 dark:text-white/40">
                    قبل التعيين ⚪
                  </span>

                  <SpatialButton
                    v-else-if="day.is_today"
                    variant="primary"
                    size="sm"
                    @click="openDateRecord(day.date)"
                  >
                    <span>سجل اليوم الحالي 📍</span>
                  </SpatialButton>

                  <SpatialButton
                    v-else-if="day.status === 'absent'"
                    variant="danger"
                    size="sm"
                    @click="openDateRecord(day.date)"
                  >
                    <span>فتح واستدراك 🔓</span>
                  </SpatialButton>

                  <SpatialButton
                    v-else-if="!day.is_future"
                    variant="secondary"
                    size="sm"
                    @click="openDateRecord(day.date)"
                  >
                    <span>تعديل واستعراض ✏️</span>
                  </SpatialButton>

                  <span v-else class="text-xs font-bold text-slate-400 dark:text-white/30">
                    غير متاح
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Mobile Cards List (Ultra-Clean Dual Theme Design) -->
        <div class="block md:hidden space-y-4">
          <div
            v-for="day in days"
            :key="day.date"
            :class="[
              'relative p-5 rounded-3xl transition-all duration-200 space-y-4 border shadow-lg',
              day.is_today
                ? 'bg-blue-50/90 dark:bg-slate-800/90 border-blue-500/50 shadow-blue-500/10'
                : 'bg-white dark:bg-slate-800/80 border-slate-200/80 dark:border-white/10 shadow-slate-200/50 dark:shadow-black/30'
            ]"
          >
            <!-- Top Header: Day Name, Prominent Date Badge & Status -->
            <div class="flex items-center justify-between gap-2 pb-3 border-b border-slate-100 dark:border-white/10">
              <div class="flex items-center gap-2.5">
                <span v-if="day.is_today" class="relative flex h-3 w-3 shrink-0">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-600"></span>
                </span>
                <div>
                  <h4 class="text-lg font-black text-slate-900 dark:text-white leading-tight">
                    {{ day.day_name }}
                  </h4>
                  <span class="text-xs font-mono font-black text-slate-700 dark:text-slate-200 dir-ltr inline-block bg-slate-100 dark:bg-white/10 px-2.5 py-0.5 rounded-md border border-slate-200 dark:border-white/10 mt-1">
                    {{ day.formatted_date }}
                  </span>
                </div>
              </div>

              <!-- Status Badge -->
              <span
                :class="[
                  'inline-flex items-center px-3 py-1.5 rounded-full text-xs font-black border shadow-xs shrink-0',
                  getStatusClass(day.status_type)
                ]"
              >
                {{ day.status_label }}
              </span>
            </div>

            <!-- Center Body: Circular Progress Ring & Metrics (Always Rendered) -->
            <div class="flex items-center justify-between gap-4 py-1">
              <!-- Center Circular Ring -->
              <div class="flex flex-col items-center justify-center shrink-0 pr-1">
                <SpatialCircularProgress
                  :percentage="day.record ? day.record.completion_percentage : 0"
                  :size="76"
                  :strokeWidth="7"
                >
                  <span class="text-base font-black font-mono text-slate-900 dark:text-white">
                    {{ day.record ? Math.round(day.record.completion_percentage) : 0 }}%
                  </span>
                </SpatialCircularProgress>
                <span class="text-[10px] font-black text-slate-500 dark:text-white/60 mt-1">نسبة الإنجاز</span>
              </div>

              <!-- Right Metrics Stack -->
              <div class="flex-1 space-y-2">
                <!-- Check-in Time -->
                <div class="flex items-center justify-between p-2 rounded-xl bg-slate-100/90 dark:bg-white/5 border border-slate-200/80 dark:border-white/10 text-xs shadow-2xs">
                  <span class="font-black text-slate-700 dark:text-white/70">⏰ وقت البدء:</span>
                  <span class="font-mono font-black text-slate-900 dark:text-white">
                    {{ day.record ? (day.record.check_in_time || '--') : '--' }}
                  </span>
                </div>

                <!-- Visited Sites -->
                <div class="flex items-center justify-between p-2 rounded-xl bg-slate-100/90 dark:bg-white/5 border border-slate-200/80 dark:border-white/10 text-xs shadow-2xs">
                  <span class="font-black text-slate-700 dark:text-white/70">📍 المواقع:</span>
                  <span class="font-mono font-black text-slate-900 dark:text-white">
                    {{ day.record ? day.record.site_visits_count : 0 }} مواقع
                  </span>
                </div>

                <!-- On-Demand Tasks -->
                <div class="flex items-center justify-between p-2 rounded-xl bg-slate-100/90 dark:bg-white/5 border border-slate-200/80 dark:border-white/10 text-xs shadow-2xs">
                  <span class="font-black text-slate-700 dark:text-white/70">⚡ إضافية:</span>
                  <span :class="['font-mono font-black', day.record && day.record.on_demand_tasks_count > 0 ? 'text-amber-600 dark:text-amber-400 text-sm' : 'text-slate-400 dark:text-white/40']">
                    {{ day.record && day.record.on_demand_tasks_count > 0 ? day.record.on_demand_tasks_count : '--' }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Last Modified Strip (Full Width to Prevent Overflow) -->
            <div
              v-if="day.record && day.record.updated_date"
              :class="[
                'flex items-center justify-between text-xs px-3.5 py-2 rounded-2xl transition-all',
                !day.record.is_same_day_update
                  ? 'border-2 border-amber-500/50 dark:border-amber-400/50 bg-amber-500/10 dark:bg-amber-400/10 shadow-2xs'
                  : 'border border-slate-200/60 dark:border-white/10 bg-slate-100/80 dark:bg-white/5'
              ]"
            >
              <span class="font-bold flex items-center gap-1.5" :class="!day.record.is_same_day_update ? 'text-amber-700 dark:text-amber-300 font-black' : 'text-slate-500 dark:text-white/60'">
                <span>🕒</span>
                <span>آخر تعديل:</span>
              </span>
              <span class="font-mono text-xs font-black text-slate-800 dark:text-white dir-ltr">
                {{ day.record.updated_day_name }} {{ day.record.updated_date }} <span class="text-emerald-600 dark:text-emerald-400">({{ day.record.updated_time }})</span>
              </span>
            </div>

            <!-- Action Button -->
            <div class="pt-1">
              <div v-if="day.status === 'before_hire'" class="text-center text-xs font-bold text-slate-400 dark:text-white/40 py-2">
                خارج فترة الخدمة (قبل التعيين) ⚪
              </div>

              <SpatialButton
                v-else-if="day.is_today"
                variant="primary"
                size="md"
                class="w-full justify-center shadow-md"
                @click="openDateRecord(day.date)"
              >
                <span>سجل اليوم الحالي 📍</span>
              </SpatialButton>

              <SpatialButton
                v-else-if="day.status === 'absent'"
                variant="danger"
                size="md"
                class="w-full justify-center shadow-md"
                @click="openDateRecord(day.date)"
              >
                <span>فتح واستدراك السجل 🔓</span>
              </SpatialButton>

              <SpatialButton
                v-else-if="!day.is_future"
                variant="secondary"
                size="md"
                class="w-full justify-center shadow-xs"
                @click="openDateRecord(day.date)"
              >
                <span>تعديل واستعراض السجل ✏️</span>
              </SpatialButton>

              <div v-else class="text-center text-xs font-bold text-slate-400 dark:text-white/30 py-2">
                غير متاح 🔒
              </div>
            </div>
          </div>
        </div>

      </SpatialCard>

    </div>
  </ConsultantLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import ConsultantLayout from '@/Layouts/ConsultantLayout.vue';
import SpatialCard from '@/Components/Spatial/SpatialCard.vue';
import SpatialButton from '@/Components/Spatial/SpatialButton.vue';
import SpatialCircularProgress from '@/Components/Spatial/SpatialCircularProgress.vue';

const props = defineProps({
  consultant: { type: Object, required: true },
  selectedMonth: { type: String, required: true },
  days: { type: Array, required: true },
  stats: { type: Object, required: true },
});

const monthFilter = ref(props.selectedMonth);

const formattedMonthName = computed(() => {
  if (!props.selectedMonth) return '';
  const date = new Date(props.selectedMonth + '-01');
  return date.toLocaleDateString('ar-LY', { month: 'long', year: 'numeric' });
});

const handleMonthChange = () => {
  router.get('/consultant/past-records', { month: monthFilter.value }, { preserveState: true });
};

const openDateRecord = (dateStr) => {
  router.get('/consultant/daily-visits', { date: dateStr });
};

const getStatusClass = (type) => {
  switch (type) {
    case 'success':
      return 'bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border-emerald-500/30';
    case 'warning':
      return 'bg-amber-500/15 text-amber-700 dark:text-amber-300 border-amber-500/30';
    case 'danger':
      return 'bg-rose-500/15 text-rose-700 dark:text-rose-300 border-rose-500/30 animate-pulse';
    case 'weekend':
      return 'bg-slate-200/80 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border-slate-300 dark:border-slate-700';
    case 'neutral':
      return 'bg-slate-100 dark:bg-white/10 text-slate-500 dark:text-white/60 border-slate-300 dark:border-white/20';
    default:
      return 'bg-slate-100 dark:bg-white/5 text-slate-400 dark:text-white/40 border-slate-200 dark:border-white/10';
  }
};
</script>
