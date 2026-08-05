<template>
  <div class="min-h-screen bg-slate-900 text-slate-100 p-4 sm:p-6 lg:p-8 dir-rtl text-right font-sans">
    
    <!-- MAIN CONTAINER -->
    <div class="max-w-6xl mx-auto space-y-6">

      <!-- TOP HEADER BAR & BREADCRUMB -->
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-6 rounded-3xl bg-slate-800/80 backdrop-blur-xl border border-white/10 shadow-2xl">
        <div class="space-y-1">
          <div class="flex items-center gap-2">
            <SpatialStatusPill type="completed" :pulse="true">
              البوابة الميدانية للاستشاري
            </SpatialStatusPill>
            <span class="text-xs text-white/50 font-mono dir-ltr">{{ todayDateFormatted }}</span>
          </div>
          <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight">
            تنفيذ الزيارات اليومية الميدانية
          </h1>
          <p class="text-xs text-white/60 font-bold">
            تسجيل الحضور اليومي، إشعار زيارات المواقع الميدانية، واستكمال التوثيق عبر النماذج الذكية.
          </p>
        </div>

        <!-- START DAY ACTION BUTTON -->
        <div class="flex items-center gap-3">
          <div v-if="dailyRecord && dailyRecord.check_in_time" class="text-left">
            <SpatialStatusPill type="completed" :pulse="true">
              اليوم العملي قائم
            </SpatialStatusPill>
            <span class="text-[10px] text-white/50 font-mono block mt-1 dir-ltr">بدء: {{ formatTime(dailyRecord.check_in_time) }}</span>
          </div>

          <SpatialButton
            v-else
            variant="primary"
            size="lg"
            :disabled="isSubmitting"
            @click="handleStartDay"
          >
            <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            <span>بدء اليوم العملي</span>
          </SpatialButton>
        </div>
      </div>

      <!-- STATS OVERVIEW CARDS & NEW VISIT ACTION -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Card 1: Consultant Details -->
        <SpatialCard padding="p-5" class="space-y-2">
          <div class="flex items-center justify-between">
            <span class="text-xs font-black text-white/50">الاستشاري الميداني</span>
            <span class="text-xs font-mono text-primary font-bold">{{ consultant.employee_number }}</span>
          </div>
          <h3 class="text-base font-black text-white">{{ consultant.full_name }}</h3>
          <p class="text-xs text-white/60 font-bold">{{ consultant.specialization || 'مفتش ميداني' }}</p>
        </SpatialCard>

        <!-- Card 2: Daily Progress -->
        <SpatialCard padding="p-5" class="space-y-2">
          <div class="flex items-center justify-between">
            <span class="text-xs font-black text-white/50">إجمالي إنجاز اليوم</span>
            <span class="text-xs font-mono text-emerald-400 font-bold">{{ dailyRecord.completion_percentage || 0 }}%</span>
          </div>
          <SpatialProgressBar :value="dailyRecord.completion_percentage || 0" />
          <div class="flex justify-between text-[11px] text-white/60 font-bold pt-1">
            <span>المهام المكتملة: {{ dailyRecord.completed_daily_tasks || 0 }}</span>
            <span>المطلوبة: {{ dailyRecord.required_daily_tasks || 0 }}</span>
          </div>
        </SpatialCard>

        <!-- Card 3: New Visit Trigger Button -->
        <SpatialCard padding="p-5" class="flex flex-col justify-between space-y-3 bg-gradient-to-br from-primary/10 via-slate-800 to-slate-800 border-primary/30">
          <div class="space-y-1">
            <span class="text-xs font-black text-primary">المواقع المزارة</span>
            <h3 class="text-xl font-black text-white">
              {{ dailyRecord.site_visits ? dailyRecord.site_visits.length : 0 }} موقع اليوم
            </h3>
          </div>

          <SpatialButton
            variant="primary"
            size="md"
            class="w-full justify-center"
            :disabled="isSubmitting"
            @click="goToCreateVisit"
          >
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>بدء زيارة موقع جديد</span>
          </SpatialButton>
        </SpatialCard>
      </div>

      <!-- VISITED SITES SECTION (SPATIAL CARDS GRID) -->
      <div class="space-y-4">
        <div class="flex items-center justify-between px-1">
          <h2 class="text-base font-black text-white flex items-center gap-2">
            <span>سجل المواقع المزارة اليوم</span>
          </h2>
          <span class="text-xs font-mono text-white/50 font-bold">
            {{ siteVisits.length }} موقع مسجل
          </span>
        </div>

        <div v-if="siteVisits.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <SpatialCard
            v-for="visit in siteVisits"
            :key="visit.id"
            padding="p-5"
            class="space-y-4 hover:border-white/20 transition-all flex flex-col justify-between"
          >
            <!-- Card Header -->
            <div class="space-y-2">
              <div class="flex items-start justify-between gap-2">
                <div class="space-y-0.5">
                  <h3 class="text-base font-black text-white">
                    {{ visit.site ? visit.site.name : 'موقع ميداني' }}
                  </h3>
                  <p class="text-xs text-white/50 font-mono">
                    كود: {{ visit.site ? visit.site.code : '' }}
                  </p>
                </div>

                <SpatialStatusPill
                  :type="visit.status === 'completed' ? 'completed' : 'pending'"
                  :pulse="visit.status !== 'completed'"
                >
                  {{ visit.status === 'completed' ? 'مكتملة' : 'قيد التنفيذ' }}
                </SpatialStatusPill>
              </div>

              <!-- Metrics inside Card -->
              <div class="p-3 rounded-2xl bg-white/5 space-y-2 border border-white/5">
                <div class="flex items-center justify-between text-xs">
                  <span class="font-bold text-white/60">نسبة الإنجاز:</span>
                  <span class="font-black text-emerald-400 font-mono">{{ getVisitProgress(visit) }}%</span>
                </div>
                <SpatialProgressBar :value="getVisitProgress(visit)" />

                <div class="flex items-center justify-between text-[11px] pt-1">
                  <span class="text-white/60 font-bold">المهام الإضافية المنجزة:</span>
                  <span class="px-2 py-0.5 rounded-md bg-amber-500/15 text-amber-400 font-black font-mono">
                    {{ getOnDemandTasksCount(visit) }} مهام
                  </span>
                </div>
              </div>
            </div>

            <!-- Card Actions Footer -->
            <div class="pt-3 border-t border-slate-200/60 dark:border-white/10 flex items-center justify-between">
              <div class="flex items-center gap-2">
                <SpatialIconButton
                  variant="ghost"
                  title="عرض التفاصيل"
                  @click="openDetailsModal(visit)"
                >
                  <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                  </svg>
                </SpatialIconButton>

                <SpatialIconButton
                  variant="warning"
                  title="فتح صفحة إدخال وتعديل المهام"
                  @click="goToExecuteVisit(visit)"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                  </svg>
                </SpatialIconButton>
              </div>

              <SpatialIconButton
                variant="danger"
                title="إلغاء أو مسح الزيارة"
                @click="confirmCancelVisit(visit)"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
              </SpatialIconButton>
            </div>
          </SpatialCard>
        </div>

        <div v-else class="p-12 text-center rounded-3xl bg-slate-800/50 border border-dashed border-white/10 text-white/50 space-y-3">
          <p class="text-xs font-bold">لم تقم بزيارة أي موقع اليوم بعد. انقر على "بدء زيارة موقع جديد" للبدء.</p>
        </div>
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

  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import SpatialCard from '@/Components/Spatial/SpatialCard.vue';
import SpatialButton from '@/Components/Spatial/SpatialButton.vue';
import SpatialIconButton from '@/Components/Spatial/SpatialIconButton.vue';
import SpatialStatusPill from '@/Components/Spatial/SpatialStatusPill.vue';
import SpatialProgressBar from '@/Components/Spatial/SpatialProgressBar.vue';
import VisitDetailModal from '@/Components/Consultant/VisitDetailModal.vue';
import CancelVisitModal from '@/Components/Consultant/CancelVisitModal.vue';

const props = defineProps({
  consultant: { type: Object, required: true },
  dailyRecord: { type: Object, required: true },
  availableSites: { type: Array, default: () => [] },
  activeVisit: { type: Object, default: null },
  availableOnDemandTasks: { type: Array, default: () => [] },
});

const isSubmitting = ref(false);

// Modals State
const showDetailModal = ref(false);
const selectedModalVisit = ref(null);

const showCancelModal = ref(false);
const visitToCancel = ref(null);

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

const getVisitProgress = (visit) => {
  if (!visit.task_responses || visit.task_responses.length === 0) return 0;
  const completed = visit.task_responses.filter((r) => r.status === 'submitted' || r.completed_at).length;
  return Math.round((completed / visit.task_responses.length) * 100);
};

const getOnDemandTasksCount = (visit) => {
  if (!visit.task_responses) return 0;
  return visit.task_responses.filter(
    (r) => r.task_definition && r.task_definition.task_type === 'on_demand'
  ).length;
};

const formatTime = (isoString) => {
  if (!isoString) return '';
  return new Date(isoString).toLocaleTimeString('ar-LY', { hour: '2-digit', minute: '2-digit' });
};

// Navigation Handlers
const handleStartDay = () => {
  isSubmitting.value = true;
  router.post('/consultant/daily-visits/start-day', {}, {
    onFinish: () => (isSubmitting.value = false),
  });
};

const goToCreateVisit = () => {
  router.get('/consultant/site-visits/create');
};

const goToExecuteVisit = (visit) => {
  router.get(`/consultant/site-visits/${visit.id}/execute`);
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
    },
    onFinish: () => (isSubmitting.value = false),
  });
};
</script>
