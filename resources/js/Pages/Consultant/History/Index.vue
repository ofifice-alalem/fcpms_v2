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

        <!-- Desktop View Table -->
        <div class="hidden md:block overflow-x-auto">
          <table class="w-full text-right border-collapse">
            <thead>
              <tr class="border-b border-slate-200 dark:border-white/10 text-xs font-black text-slate-500 dark:text-white/50 uppercase tracking-wider">
                <th class="py-3 px-4">التاريخ واليوم</th>
                <th class="py-3 px-4">الحالة</th>
                <th class="py-3 px-4">وقت البدء</th>
                <th class="py-3 px-4">المواقع المزارة</th>
                <th class="py-3 px-4 text-center">المهام الإضافية</th>
                <th class="py-3 px-4">نسبة الإنجاز</th>
                <th class="py-3 px-4 text-center">الإجراء والفتح</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-sm">
              <tr
                v-for="day in days"
                :key="day.date"
                :class="[
                  'hover:bg-slate-50/50 dark:hover:bg-white/5 transition-colors',
                  day.is_today ? 'bg-primary/5 dark:bg-primary/10 font-bold' : ''
                ]"
              >
                <!-- Date & Day Name -->
                <td class="py-4 px-4 font-bold text-slate-900 dark:text-white">
                  <div class="flex items-center gap-2">
                    <span v-if="day.is_today" class="w-2 h-2 rounded-full bg-primary animate-ping"></span>
                    <div>
                      <div class="font-black text-sm">{{ day.day_name }}</div>
                      <div class="text-xs font-mono text-slate-500 dark:text-white/50 dir-ltr text-right">{{ day.formatted_date }}</div>
                    </div>
                  </div>
                </td>

                <!-- Status Pill -->
                <td class="py-4 px-4">
                  <span
                    :class="[
                      'inline-flex items-center px-3 py-1 rounded-full text-xs font-black border',
                      getStatusClass(day.status_type)
                    ]"
                  >
                    {{ day.status_label }}
                  </span>
                </td>

                <!-- Check-in time -->
                <td class="py-4 px-4 font-mono text-xs text-slate-600 dark:text-white/70">
                  {{ day.record && day.record.check_in_time ? day.record.check_in_time : '-' }}
                </td>

                <!-- Visited sites -->
                <td class="py-4 px-4 font-mono text-xs font-black text-slate-800 dark:text-white">
                  {{ day.record ? day.record.site_visits_count + ' مواقع' : '-' }}
                </td>

                <!-- On-demand tasks count -->
                <td class="py-4 px-4 text-center font-mono text-base font-black">
                  <span v-if="day.record && day.record.on_demand_tasks_count > 0" class="text-amber-600 dark:text-amber-400 text-base font-black">
                    {{ day.record.on_demand_tasks_count }}
                  </span>
                  <span v-else class="text-slate-400 dark:text-white/30 text-sm font-bold">
                    --
                  </span>
                </td>

                <!-- Progress percentage -->
                <td class="py-4 px-4">
                  <div v-if="day.record" class="flex items-center gap-2 w-32">
                    <div class="flex-1 bg-slate-200 dark:bg-slate-700 h-2 rounded-full overflow-hidden">
                      <div
                        class="h-full bg-primary transition-all duration-300"
                        :style="{ width: `${day.record.completion_percentage}%` }"
                      ></div>
                    </div>
                    <span class="text-xs font-mono font-black text-slate-700 dark:text-white/80">
                      {{ day.record.completion_percentage }}%
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
                    v-else-if="!day.is_future && !day.is_weekend"
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
                v-else-if="!day.is_future && !day.is_weekend"
                variant="secondary"
                size="md"
                class="w-full justify-center shadow-xs"
                @click="openDateRecord(day.date)"
              >
                <span>تعديل واستعراض السجل ✏️</span>
              </SpatialButton>

              <div v-else class="text-center text-xs font-bold text-slate-400 dark:text-white/30 py-2">
                عطلة أسبوعية / غير متاح 🌴
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
