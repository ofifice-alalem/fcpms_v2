<template>
  <HRLayout title="لوحة التحكّم والرقابة الميدانية HR">
    <div class="space-y-8">
      
      <!-- Toast Feedback Component -->
      <SpatialToast ref="toastRef" />

      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white flex items-center gap-2">
            📊 لوحة التحكّم والرقابة الميدانية
          </h1>
          <p class="text-xs sm:text-sm font-bold text-slate-500 dark:text-white/60 mt-1">
            متابعة فورية لحضور وغياب الاستشاريين، تغطية المواقع، وإجمالي المهام الميدانية اليومية وعند الطلب
          </p>
        </div>

        <!-- Right Side Header Controls -->
        <div class="flex items-center gap-3 flex-wrap">
          <div class="px-4 py-2 rounded-2xl bg-white/80 dark:bg-slate-900/80 border border-slate-200/80 dark:border-white/10 shadow-xs flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="text-xs font-black text-slate-800 dark:text-white font-mono">
              {{ today_date_formatted }}
            </span>
          </div>

          <Link :href="route('admin.tasks.create')">
            <SpatialButton variant="primary">
              <div class="w-6 h-6 rounded-xl bg-white/20 flex items-center justify-center text-white">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/>
                </svg>
              </div>
              <span>بناء مهمة جديدة</span>
            </SpatialButton>
          </Link>
        </div>
      </div>

      <!-- Top Executive Glass Summary Widgets (4 High-Impact Frosted Cards) -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 relative z-10">
        
        <!-- Widget 1: Checked In Consultants Today -->
        <SpatialCard
          padding="p-6"
          class="min-h-[290px] flex flex-col justify-between border-t-4 border-t-emerald-500 rounded-3xl relative overflow-hidden text-center space-y-4 bg-white/75 dark:bg-slate-950/65 backdrop-blur-xl border border-white/50 dark:border-white/10 shadow-[0_8px_32px_rgba(0,0,0,0.06)] dark:shadow-[0_8px_32px_rgba(0,0,0,0.36)] transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl"
        >
          <!-- Ambient Glass Radial Glow -->
          <div class="absolute -top-14 -right-14 w-44 h-44 rounded-full bg-emerald-500/25 blur-3xl pointer-events-none"></div>

          <!-- Top SVG Circular Progress Gauge -->
          <div class="flex flex-col items-center justify-center space-y-2 relative z-10">
            <div class="relative drop-shadow-[0_0_18px_rgba(16,185,129,0.35)]">
              <SpatialCircularProgress
                :value="checkedInPercentage"
                :size="125"
                :stroke-width="10"
                color="emerald"
              >
                <div class="flex flex-col items-center justify-center">
                  <span class="text-3xl font-black font-stat-number tracking-tight text-emerald-600 dark:text-emerald-400 leading-none">
                    {{ stats.checked_in_consultants }}
                  </span>
                  <span class="text-[10px] font-bold text-slate-500 dark:text-white/60 mt-1">من أصل {{ stats.total_consultants }}</span>
                </div>
              </SpatialCircularProgress>
            </div>

            <span class="text-xs font-black text-slate-500 dark:text-white/60 uppercase tracking-widest block pt-1">
              الاستشاريون المسجلون لليوم
            </span>
          </div>

          <!-- Bottom Metric Pill -->
          <div class="pt-3 border-t border-slate-200/60 dark:border-white/10 flex items-center justify-between text-xs relative z-10">
            <span class="font-bold text-slate-500 dark:text-white/60">نسبة الحضور:</span>
            <span class="px-3 py-1 rounded-full bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 font-mono font-black border border-emerald-500/30">
              {{ checkedInPercentage }}% 🟢
            </span>
          </div>
        </SpatialCard>

        <!-- Widget 2: Absent Consultants Today -->
        <SpatialCard
          padding="p-6"
          class="min-h-[290px] flex flex-col justify-between border-t-4 border-t-rose-500 rounded-3xl relative overflow-hidden text-center space-y-4 bg-white/75 dark:bg-slate-950/65 backdrop-blur-xl border border-white/50 dark:border-white/10 shadow-[0_8px_32px_rgba(0,0,0,0.06)] dark:shadow-[0_8px_32px_rgba(0,0,0,0.36)] transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl"
        >
          <!-- Ambient Glass Radial Glow -->
          <div class="absolute -top-14 -right-14 w-44 h-44 rounded-full bg-rose-500/25 blur-3xl pointer-events-none"></div>

          <!-- Top SVG Circular Gauge -->
          <div class="flex flex-col items-center justify-center space-y-2 relative z-10">
            <div class="relative drop-shadow-[0_0_18px_rgba(244,63,94,0.35)]">
              <SpatialCircularProgress
                :value="absentPercentage"
                :size="125"
                :stroke-width="10"
                color="auto"
              >
                <div class="flex flex-col items-center justify-center">
                  <span class="text-3xl font-black font-stat-number tracking-tight text-rose-600 dark:text-rose-400 leading-none">
                    {{ stats.absent_consultants }}
                  </span>
                  <span class="text-[10px] font-bold text-slate-500 dark:text-white/60 mt-1">استشاري غائب</span>
                </div>
              </SpatialCircularProgress>
            </div>

            <span class="text-xs font-black text-slate-500 dark:text-white/60 uppercase tracking-widest block pt-1">
              عدد الغياب اليوم
            </span>
          </div>

          <!-- Bottom Metric Pill -->
          <div class="pt-3 border-t border-slate-200/60 dark:border-white/10 flex items-center justify-between text-xs relative z-10">
            <span class="font-bold text-slate-500 dark:text-white/60">حالة الغياب:</span>
            <span class="px-3 py-1 rounded-full bg-rose-500/15 text-rose-600 dark:text-rose-400 font-mono font-black border border-rose-500/30">
              {{ stats.absent_consultants }} لم يُسجّلوا 🔴
            </span>
          </div>
        </SpatialCard>

        <!-- Widget 3: Visited Sites Today -->
        <SpatialCard
          padding="p-6"
          class="min-h-[290px] flex flex-col justify-between border-t-4 border-t-indigo-500 rounded-3xl relative overflow-hidden text-center space-y-4 bg-white/75 dark:bg-slate-950/65 backdrop-blur-xl border border-white/50 dark:border-white/10 shadow-[0_8px_32px_rgba(0,0,0,0.06)] dark:shadow-[0_8px_32px_rgba(0,0,0,0.36)] transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl"
        >
          <!-- Ambient Glass Radial Glow -->
          <div class="absolute -top-14 -right-14 w-44 h-44 rounded-full bg-indigo-500/25 blur-3xl pointer-events-none"></div>

          <!-- Top SVG Circular Gauge -->
          <div class="flex flex-col items-center justify-center space-y-2 relative z-10">
            <div class="relative drop-shadow-[0_0_18px_rgba(99,102,241,0.35)]">
              <SpatialCircularProgress
                :value="stats.visited_sites > 0 ? 100 : 0"
                :size="125"
                :stroke-width="10"
                color="indigo"
              >
                <div class="flex flex-col items-center justify-center">
                  <span class="text-3xl font-black font-stat-number tracking-tight text-indigo-600 dark:text-indigo-400 leading-none">
                    {{ stats.visited_sites }}
                  </span>
                  <span class="text-[10px] font-bold text-slate-500 dark:text-white/60 mt-1">موقع مزار📍</span>
                </div>
              </SpatialCircularProgress>
            </div>

            <span class="text-xs font-black text-slate-500 dark:text-white/60 uppercase tracking-widest block pt-1">
              المواقع المزارة اليوم
            </span>
          </div>

          <!-- Bottom Metric Pill -->
          <div class="pt-3 border-t border-slate-200/60 dark:border-white/10 flex items-center justify-between text-xs relative z-10">
            <span class="font-bold text-slate-500 dark:text-white/60">إجمالي الزيارات:</span>
            <span class="px-3 py-1 rounded-full bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 font-mono font-black border border-indigo-500/30">
              {{ stats.total_site_visits }} زيارة 🏛️
            </span>
          </div>
        </SpatialCard>

        <!-- Widget 4: Total Tasks (Daily & On-Demand) -->
        <SpatialCard
          padding="p-6"
          class="min-h-[290px] flex flex-col justify-between border-t-4 border-t-amber-500 rounded-3xl relative overflow-hidden text-center space-y-4 bg-white/75 dark:bg-slate-950/65 backdrop-blur-xl border border-white/50 dark:border-white/10 shadow-[0_8px_32px_rgba(0,0,0,0.06)] dark:shadow-[0_8px_32px_rgba(0,0,0,0.36)] transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl"
        >
          <!-- Ambient Glass Radial Glow -->
          <div class="absolute -top-14 -right-14 w-44 h-44 rounded-full bg-amber-500/25 blur-3xl pointer-events-none"></div>

          <!-- Top SVG Circular Gauge -->
          <div class="flex flex-col items-center justify-center space-y-2 relative z-10">
            <div class="relative drop-shadow-[0_0_18px_rgba(245,158,11,0.35)]">
              <SpatialCircularProgress
                :value="taskExecutionPercentage"
                :size="125"
                :stroke-width="10"
                color="blue"
              >
                <div class="flex flex-col items-center justify-center">
                  <span class="text-3xl font-black font-stat-number tracking-tight text-amber-600 dark:text-amber-400 leading-none">
                    {{ stats.total_tasks_count }}
                  </span>
                  <span class="text-[10px] font-bold text-slate-500 dark:text-white/60 mt-1">مهمة كلية ⚡</span>
                </div>
              </SpatialCircularProgress>
            </div>

            <span class="text-xs font-black text-slate-500 dark:text-white/60 uppercase tracking-widest block pt-1">
              إجمالي المهام الميدانية
            </span>
          </div>

          <!-- Bottom Grid Badges Breakdown -->
          <div class="grid grid-cols-2 gap-2 pt-2 text-[11px] font-bold relative z-10">
            <div class="px-2.5 py-1 rounded-xl bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/25 flex items-center justify-between">
              <span>📅 يومية:</span>
              <span class="font-mono font-black">{{ stats.daily_tasks_count }}</span>
            </div>

            <div class="px-2.5 py-1 rounded-xl bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-500/25 flex items-center justify-between">
              <span>⚡ طلب:</span>
              <span class="font-mono font-black">{{ stats.on_demand_tasks_count }}</span>
            </div>
          </div>
        </SpatialCard>
      </div>

      <!-- SECTION 1: Consultants Live Attendance & Status Table -->
      <SpatialCard padding="p-6" class="space-y-5 relative z-10">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-4 border-b border-slate-200/60 dark:border-white/10">
          <div>
            <h2 class="text-lg font-black text-slate-900 dark:text-white flex items-center gap-2">
              👨‍💼 سجل حضور وغياب الاستشاريين اليوم
            </h2>
            <p class="text-xs font-bold text-slate-500 dark:text-white/60 mt-0.5">
              متابعة فورية لتسجيل الحضور، وقت البدء، ونسبة الإنجاز لكل استشاري
            </p>
          </div>

          <!-- Quick Status Filter Options -->
          <div class="flex items-center gap-1.5 p-1 rounded-xl bg-slate-100 dark:bg-white/5 border border-slate-200/60 dark:border-white/10 text-xs font-bold">
            <button
              @click="consultantsFilter = 'all'"
              :class="[
                'px-3 py-1.5 rounded-lg transition-all cursor-pointer select-none',
                consultantsFilter === 'all' ? 'bg-primary text-white shadow-xs' : 'text-slate-500 dark:text-white/60 hover:text-slate-900 dark:hover:text-white'
              ]"
            >
              الكل ({{ consultants_status.length }})
            </button>

            <button
              @click="consultantsFilter = 'checked_in'"
              :class="[
                'px-3 py-1.5 rounded-lg transition-all cursor-pointer select-none',
                consultantsFilter === 'checked_in' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-500 dark:text-white/60 hover:text-slate-900 dark:hover:text-white'
              ]"
            >
              🟢 حاضر ({{ stats.checked_in_consultants }})
            </button>

            <button
              @click="consultantsFilter = 'absent'"
              :class="[
                'px-3 py-1.5 rounded-lg transition-all cursor-pointer select-none',
                consultantsFilter === 'absent' ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-500 dark:text-white/60 hover:text-slate-900 dark:hover:text-white'
              ]"
            >
              🔴 غائب ({{ stats.absent_consultants }})
            </button>
          </div>
        </div>

        <!-- Table Grid -->
        <div class="overflow-x-auto">
          <table class="w-full text-right border-collapse">
            <thead>
              <tr class="border-b border-slate-200 dark:border-white/10 text-xs font-black text-slate-600 dark:text-white/70 bg-slate-100/90 dark:bg-white/5">
                <th class="p-4">اسم الاستشاري</th>
                <th class="p-4">الرقم الوظيفي</th>
                <th class="p-4">التخصص</th>
                <th class="p-4 text-center">حالة الحضور</th>
                <th class="p-4 text-center">وقت تسجيل الدخول</th>
                <th class="p-4">نسبة الإنجاز اليومية</th>
                <th class="p-4 text-center">الإجراءات</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-sm font-bold">
              <tr
                v-for="c in filteredConsultants"
                :key="c.id"
                class="transition-colors hover:bg-slate-50/80 dark:hover:bg-white/5"
              >
                <td class="p-4 font-black text-slate-900 dark:text-white">
                  {{ c.full_name }}
                </td>
                <td class="p-4 font-mono text-xs font-bold text-slate-500 dark:text-white/60">
                  {{ c.employee_number }}
                </td>
                <td class="p-4 text-xs text-slate-600 dark:text-white/70">
                  {{ c.specialization || 'استشاري ميداني' }}
                </td>
                <td class="p-4 text-center whitespace-nowrap">
                  <span
                    :class="[
                      'px-3 py-1 rounded-full text-xs font-black inline-flex items-center gap-1.5 border shadow-2xs',
                      c.status === 'checked_in'
                        ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border-emerald-500/30'
                        : (c.status === 'leave'
                            ? 'bg-amber-500/15 text-amber-600 dark:text-amber-400 border-amber-500/30'
                            : 'bg-rose-500/15 text-rose-600 dark:text-rose-400 border-rose-500/30')
                    ]"
                  >
                    <span :class="['w-2 h-2 rounded-full', c.status === 'checked_in' ? 'bg-emerald-500 animate-pulse' : (c.status === 'leave' ? 'bg-amber-500' : 'bg-rose-500')]"></span>
                    <span>{{ c.status === 'checked_in' ? '🟢 حاضر' : (c.status === 'leave' ? '🟡 في إجازة' : '🔴 غائب') }}</span>
                  </span>
                </td>
                <td class="p-4 text-center font-mono text-xs font-bold text-slate-700 dark:text-white/80 whitespace-nowrap">
                  {{ c.check_in_time ? `⏰ ${c.check_in_time}` : '--' }}
                </td>
                <td class="p-4 w-44">
                  <div v-if="c.status === 'checked_in'" class="space-y-1">
                    <div class="flex items-center justify-between text-[11px] font-mono font-bold">
                      <span class="text-emerald-500">{{ c.completion_percentage }}%</span>
                      <span class="text-slate-500 dark:text-white/50">{{ c.completed_daily_tasks }} / {{ c.required_daily_tasks }}</span>
                    </div>
                    <div class="w-full bg-slate-200 dark:bg-white/10 rounded-full h-2 overflow-hidden">
                      <div
                        class="bg-emerald-500 h-2 rounded-full transition-all duration-500"
                        :style="{ width: `${c.completion_percentage}%` }"
                      ></div>
                    </div>
                  </div>
                  <span v-else class="text-xs text-slate-400 dark:text-white/40 italic">--</span>
                </td>
                <td class="p-4 text-center whitespace-nowrap">
                  <Link :href="route('admin.consultants.show', c.id)">
                    <button
                      type="button"
                      title="عرض ملف الاستشاري"
                      class="w-8 h-8 border border-indigo-500/60 text-indigo-600 dark:text-indigo-400 bg-transparent hover:bg-indigo-500/15 flex items-center justify-center transition-all duration-200 hover:scale-105 cursor-pointer rounded-full mx-auto"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                      </svg>
                    </button>
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </SpatialCard>

      <!-- SECTION 2: Visited Sites Today Table -->
      <SpatialCard padding="p-6" class="space-y-5 relative z-10">
        <div class="flex items-center justify-between pb-4 border-b border-slate-200/60 dark:border-white/10">
          <div>
            <h2 class="text-lg font-black text-slate-900 dark:text-white flex items-center gap-2">
              🏛️ سجل المواقع المزارة اليوم
            </h2>
            <p class="text-xs font-bold text-slate-500 dark:text-white/60 mt-0.5">
              متابعة الزيارات الفردية المسجلة في المواقع الميدانية اليوم
            </p>
          </div>

          <span class="text-xs font-mono font-black text-slate-500 dark:text-white/60">
            إجمالي الزيارات: {{ recent_visits.length }}
          </span>
        </div>

        <div v-if="recent_visits.length > 0" class="overflow-x-auto">
          <table class="w-full text-right border-collapse">
            <thead>
              <tr class="border-b border-slate-200 dark:border-white/10 text-xs font-black text-slate-600 dark:text-white/70 bg-slate-100/90 dark:bg-white/5">
                <th class="p-4">اسم الموقع الميداني</th>
                <th class="p-4">كود الموقع</th>
                <th class="p-4">الاستشاري المنفّذ</th>
                <th class="p-4">وقت بدء الزيارة</th>
                <th class="p-4 text-center">المهام الموثقة</th>
                <th class="p-4 text-center">حالة الزيارة</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-sm font-bold">
              <tr
                v-for="v in recent_visits"
                :key="v.id"
                class="transition-colors hover:bg-slate-50/80 dark:hover:bg-white/5"
              >
                <td class="p-4 font-black text-slate-900 dark:text-white">
                  {{ v.site_name }}
                </td>
                <td class="p-4 font-mono text-xs font-bold text-slate-500 dark:text-white/60">
                  {{ v.site_code }}
                </td>
                <td class="p-4 text-xs font-bold text-slate-700 dark:text-white/80">
                  {{ v.consultant_name }}
                </td>
                <td class="p-4 font-mono text-xs font-bold text-slate-600 dark:text-white/70">
                  ⏰ {{ v.started_at }}
                </td>
                <td class="p-4 text-center whitespace-nowrap">
                  <span class="px-3 py-1 rounded-lg bg-purple-500/15 text-purple-600 dark:text-purple-300 font-mono font-black text-xs border border-purple-500/20">
                    {{ v.task_count }} مهام
                  </span>
                </td>
                <td class="p-4 text-center whitespace-nowrap">
                  <SpatialStatusPill :type="v.status === 'completed' ? 'completed' : 'pending'">
                    {{ v.status === 'completed' ? 'مكتملة 🟢' : 'قيد التنفيذ ⏳' }}
                  </SpatialStatusPill>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-else class="p-8 text-center rounded-2xl bg-slate-100/50 dark:bg-white/5 border border-dashed border-slate-300 dark:border-white/10 text-slate-500 dark:text-white/50 text-xs font-bold">
          لم تُسجل أي زيارات ميدانية للمواقع اليوم حتى الآن.
        </div>
      </SpatialCard>

    </div>
  </HRLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import HRLayout from '@/Layouts/HRLayout.vue';
import SpatialCard from '@/Components/Spatial/SpatialCard.vue';
import SpatialButton from '@/Components/Spatial/SpatialButton.vue';
import SpatialStatusPill from '@/Components/Spatial/SpatialStatusPill.vue';
import SpatialToast from '@/Components/Spatial/SpatialToast.vue';
import SpatialCircularProgress from '@/Components/Spatial/SpatialCircularProgress.vue';

const props = defineProps({
  stats: {
    type: Object,
    required: true,
  },
  consultants_status: {
    type: Array,
    default: () => [],
  },
  recent_visits: {
    type: Array,
    default: () => [],
  },
  today_date_formatted: {
    type: String,
    default: '',
  },
});

const toastRef = ref(null);
const consultantsFilter = ref('all'); // 'all' | 'checked_in' | 'absent'

const checkedInPercentage = computed(() => {
  if (!props.stats.total_consultants) return 0;
  return Math.round((props.stats.checked_in_consultants / props.stats.total_consultants) * 100);
});

const absentPercentage = computed(() => {
  if (!props.stats.total_consultants) return 0;
  return Math.round((props.stats.absent_consultants / props.stats.total_consultants) * 100);
});

const taskExecutionPercentage = computed(() => {
  if (!props.stats.total_tasks_count) return 0;
  return Math.round((props.stats.completed_tasks_count / props.stats.total_tasks_count) * 100);
});

const filteredConsultants = computed(() => {
  if (consultantsFilter.value === 'checked_in') {
    return props.consultants_status.filter(c => c.status === 'checked_in');
  }
  if (consultantsFilter.value === 'absent') {
    return props.consultants_status.filter(c => c.status === 'absent');
  }
  return props.consultants_status;
});
</script>
