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

          <Link :href="route('consultant.site-visits.create')">
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

      <!-- Top Summary Widgets (HR Style Stats) -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Widget 1: Consultant Profile -->
        <SpatialCard padding="p-5" class="space-y-2">
          <div class="flex items-center justify-between">
            <span class="text-xs font-black text-slate-500 dark:text-white/50">الاستشاري الميداني</span>
            <span class="text-xs font-mono text-primary font-bold">{{ consultant.employee_number }}</span>
          </div>
          <h3 class="text-base font-black text-slate-900 dark:text-white">{{ consultant.full_name }}</h3>
          <p class="text-xs text-slate-500 dark:text-white/60 font-bold">{{ consultant.specialization || 'مفتش ميداني' }}</p>
        </SpatialCard>

        <!-- Widget 2: Daily Progress -->
        <SpatialCard padding="p-5" class="space-y-3 relative overflow-hidden">
          <div class="flex items-center justify-between">
            <span class="text-xs font-black text-slate-500 dark:text-white/50">نسبة إنجاز اليوم</span>
            <div class="flex items-center gap-1.5">
              <span
                class="text-xs font-black px-2.5 py-0.5 rounded-full border font-mono tracking-tight"
                :class="
                  (dailyRecord.completion_percentage || 0) >= 100
                    ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/30'
                    : (dailyRecord.completion_percentage || 0) >= 50
                      ? 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/30'
                      : 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/30'
                "
              >
                {{ Number(dailyRecord.completion_percentage || 0).toFixed(0) }}%
              </span>
              <span v-if="(dailyRecord.completion_percentage || 0) >= 100" class="text-sm">🏆</span>
            </div>
          </div>

          <SpatialProgressBar :value="dailyRecord.completion_percentage || 0" height="h-3" />

          <div class="flex items-center justify-between text-xs font-bold pt-0.5">
            <div class="flex items-center gap-1.5 text-slate-700 dark:text-white/80">
              <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
              <span>المهام المكتملة: <strong class="text-slate-900 dark:text-white font-black font-mono text-sm">{{ dailyRecord.completed_daily_tasks || 0 }}</strong></span>
            </div>
            <div class="flex items-center gap-1 text-slate-500 dark:text-white/50">
              <span>المطلوبة: <strong class="text-slate-700 dark:text-white/80 font-black font-mono text-sm">{{ dailyRecord.required_daily_tasks || 0 }}</strong></span>
            </div>
          </div>
        </SpatialCard>

        <!-- Widget 3: Attendance & Visits Stats -->
        <SpatialCard padding="p-5" class="space-y-2">
          <div class="flex items-center justify-between">
            <span class="text-xs font-black text-slate-500 dark:text-white/50">حالة اليوم والزيارات</span>
            <SpatialStatusPill :type="dailyRecord && dailyRecord.check_in_time ? 'completed' : 'pending'">
              {{ dailyRecord && dailyRecord.check_in_time ? 'اليوم قائم' : 'لم يبدأ' }}
            </SpatialStatusPill>
          </div>
          <h3 class="text-base font-black text-slate-900 dark:text-white">
            {{ siteVisits.length }} موقع مزار اليوم
          </h3>
          <p class="text-xs text-slate-500 dark:text-white/60 font-bold">
            التاريخ: <span class="font-mono">{{ todayDateFormatted }}</span>
          </p>
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

      <!-- 1. GRID CARDS VIEW -->
      <div v-if="viewMode === 'grid' && filteredVisits.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <SpatialCard
          v-for="visit in filteredVisits"
          :key="visit.id"
          padding="p-6"
          class="relative group flex flex-col justify-between space-y-4 hover:border-primary/50 transition-all"
        >
          <!-- Card Header -->
          <div class="space-y-3">
            <div class="flex items-start justify-between gap-2">
              <div class="space-y-0.5">
                <h3 class="text-base font-black text-slate-900 dark:text-white">
                  {{ visit.site ? visit.site.name : 'موقع ميداني' }}
                </h3>
                <p class="text-xs text-slate-500 dark:text-white/50 font-mono">
                  كود الموقع: {{ visit.site ? visit.site.code : '' }}
                </p>
              </div>

              <SpatialStatusPill
                :type="visit.status === 'completed' ? 'completed' : 'pending'"
                :pulse="visit.status !== 'completed'"
              >
                {{ visit.status === 'completed' ? 'مكتملة' : 'قيد التنفيذ' }}
              </SpatialStatusPill>
            </div>

            <!-- Metrics -->
            <div class="p-3 rounded-2xl bg-slate-100 dark:bg-white/5 space-y-2 border border-slate-200/60 dark:border-white/5">
              <div class="flex items-center justify-between text-xs">
                <span class="font-bold text-slate-500 dark:text-white/60">نسبة الإنجاز:</span>
                <span class="font-black text-emerald-500 font-mono">{{ getVisitProgress(visit) }}%</span>
              </div>
              <SpatialProgressBar :value="getVisitProgress(visit)" />

              <div class="flex items-center justify-between text-[11px] pt-1">
                <span class="text-slate-500 dark:text-white/60 font-bold">المهام الإضافية:</span>
                <span class="px-2 py-0.5 rounded-md bg-amber-500/15 text-amber-600 dark:text-amber-400 font-black font-mono">
                  {{ getOnDemandTasksCount(visit) }} مهام عند الحاجة
                </span>
              </div>
            </div>
          </div>

          <!-- Actions Bar -->
          <div class="pt-3 border-t border-slate-200/60 dark:border-white/10 flex items-center justify-between">
            <div class="flex items-center gap-2">
              <SpatialIconButton
                variant="ghost"
                title="عرض التفاصيل"
                @click="openDetailsModal(visit)"
              >
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
              </SpatialIconButton>

              <SpatialIconButton
                variant="warning"
                title="تعديل النماذج والبيانات"
                @click="goToExecuteVisit(visit)"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
              </SpatialIconButton>
            </div>

            <SpatialIconButton
              variant="danger"
              title="إلغاء الزيارة"
              @click="confirmCancelVisit(visit)"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
              </svg>
            </SpatialIconButton>
          </div>
        </SpatialCard>
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
                  <SpatialStatusPill :type="visit.status === 'completed' ? 'completed' : 'pending'">
                    {{ visit.status === 'completed' ? 'مكتملة' : 'قيد التنفيذ' }}
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

// Handlers
const handleStartDay = () => {
  isSubmitting.value = true;
  router.post('/consultant/daily-visits/start-day', {}, {
    onFinish: () => (isSubmitting.value = false),
  });
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
