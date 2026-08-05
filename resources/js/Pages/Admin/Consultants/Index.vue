<template>
  <HRLayout>
    <!-- Toast Feedback Component -->
    <SpatialToast ref="toastRef" />

    <div class="space-y-6">
      
      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-black text-slate-900 dark:text-white flex items-center gap-2">
            👨‍💼 دليل الاستشاريين الميدانيين
          </h1>
          <p class="text-xs font-bold text-slate-500 dark:text-white/60 mt-1">
            إدارة وتتبع سجلات وحسابات الاستشاريين الميدانيين وحالات التوظيف التشغيلية
          </p>
        </div>

        <!-- Premium Spatial UI Primary Button Component -->
        <SpatialButton
          variant="primary"
          @click="openCreateDrawer"
        >
          <div class="w-6 h-6 rounded-xl bg-white/20 flex items-center justify-center text-white">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/>
            </svg>
          </div>
          <span>إضافة استشاري جديد</span>
        </SpatialButton>
      </div>

      <!-- KPI Overview Cards -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <SpatialCard padding="p-4" class="space-y-1 border-l-4 border-l-blue-500">
          <span class="text-[11px] font-bold text-slate-400 dark:text-white/50 block">إجمالي الاستشاريين</span>
          <span class="text-2xl font-black text-slate-900 dark:text-white">{{ stats.total || 0 }}</span>
        </SpatialCard>

        <SpatialCard padding="p-4" class="space-y-1 border-l-4 border-l-emerald-500">
          <span class="text-[11px] font-bold text-slate-400 dark:text-white/50 block">الاستشاريين النشطين</span>
          <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ stats.active || 0 }}</span>
        </SpatialCard>

        <SpatialCard padding="p-4" class="space-y-1 border-l-4 border-l-rose-500">
          <span class="text-[11px] font-bold text-slate-400 dark:text-white/50 block">الموقوفين مؤقتاً</span>
          <span class="text-2xl font-black text-rose-600 dark:text-rose-400">{{ stats.suspended || 0 }}</span>
        </SpatialCard>

        <SpatialCard padding="p-4" class="space-y-1 border-l-4 border-l-amber-500">
          <span class="text-[11px] font-bold text-slate-400 dark:text-white/50 block">في إجازة</span>
          <span class="text-2xl font-black text-amber-600 dark:text-amber-400">{{ stats.vacation || 0 }}</span>
        </SpatialCard>
      </div>

      <!-- Featured Consultant KPI Card (Only shown if a consultant is explicitly selected) -->
      <ConsultantKpiCard
        v-if="featuredConsultant"
        :consultant="featuredConsultant"
        @view="openViewModal"
        @edit="openEditDrawer"
      />

      <!-- Filter & Search Bar -->
      <SpatialCard padding="p-5" class="relative z-30">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
          
          <!-- Live Search Input -->
          <SpatialInput
            v-model="searchQuery"
            label="البحث المباشر"
            placeholder="اسم، رقم وظيفي، هاتف، أو بريد..."
            @input="handleFilterChange"
          />

          <!-- Status Spatial Dropdown -->
          <SpatialDropdown
            v-model="selectedStatus"
            label="حالة التوظيف"
            placeholder="جميع الحالات"
            :options="statusFilterOptions"
            @change="handleFilterChange"
          />

          <!-- Specialization Dropdown -->
          <SpatialDropdown
            v-model="selectedSpecialization"
            label="التخصص الميداني"
            placeholder="جميع التخصصات"
            :options="specializationFilterOptions"
            @change="handleFilterChange"
          />

          <!-- Sorting Dropdown -->
          <SpatialDropdown
            v-model="selectedSort"
            label="ترتيب النتائج حسب"
            placeholder="ترتيب حسب"
            :options="sortFilterOptions"
            @change="handleFilterChange"
          />
        </div>

        <!-- Spatial Reset Filters Button -->
        <div v-if="hasActiveFilters" class="mt-4 pt-3 border-t border-black/5 dark:border-white/5 flex justify-end">
          <button
            @click="resetFilters"
            class="px-4 py-2 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 font-bold text-xs border border-rose-500/20 hover:border-rose-500/30 transition-all flex items-center gap-2 cursor-pointer shadow-sm"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            <span>إعادة تعيين الفلاتر</span>
          </button>
        </div>
      </SpatialCard>

      <!-- Data Table Grid -->
      <SpatialCard padding="p-0 overflow-hidden" class="relative z-10">
        <div class="overflow-x-auto custom-scroll">
          <table class="w-full text-right border-collapse">
            <thead>
              <tr class="border-b border-slate-200 dark:border-white/10 text-xs font-black text-slate-600 dark:text-white/70 bg-slate-100/90 dark:bg-white/5">
                <th class="p-4 w-12 text-center">
                  <SpatialCheckbox v-model="isAllSelected" @change="toggleSelectAll" />
                </th>
                <th class="p-4">الرقم الوظيفي</th>
                <th class="p-4">الاسم الكامل / التعيين</th>
                <th class="p-4">البريد والتواصل</th>
                <th class="p-4">التخصص الميداني</th>
                <th class="p-4 text-center">الحالة التشغيلية</th>
                <th class="p-4 text-center w-36">الإجراءات</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-sm font-bold">
              <tr
                v-for="consultant in consultants.data"
                :key="consultant.id"
                :class="[
                  'transition-colors hover:bg-slate-50/80 dark:hover:bg-white/5',
                  selectedIds.includes(consultant.id) ? 'bg-blue-100/90 dark:bg-primary/25 border-r-4 border-r-primary text-blue-950 dark:text-white shadow-sm' : ''
                ]"
              >
                <!-- Checkbox -->
                <td class="p-4 text-center">
                  <SpatialCheckbox v-model="selectedIds" :value="consultant.id" />
                </td>

                <!-- Employee Number Code Badge -->
                <td class="p-4">
                  <span class="inline-block px-3 py-1 rounded-xl bg-slate-200 dark:bg-white/10 text-slate-900 dark:text-white font-mono text-xs font-black border border-black/10 dark:border-white/15">
                    {{ consultant.employee_number }}
                  </span>
                </td>

                <!-- Full Name & Hire Date -->
                <td class="p-4">
                  <div class="font-black text-slate-900 dark:text-white">{{ consultant.full_name }}</div>
                  <div class="text-xs text-slate-500 dark:text-white/50 mt-0.5">تعيين: {{ formatDate(consultant.hire_date) }}</div>
                </td>

                <!-- Contact & Email -->
                <td class="p-4">
                  <div class="font-black text-slate-900 dark:text-white text-xs font-mono">{{ consultant.user?.email || '-' }}</div>
                  <div class="text-xs text-slate-500 dark:text-white/50 mt-0.5">📞 {{ consultant.phone || 'غير مسجل' }}</div>
                </td>

                <!-- Specialization -->
                <td class="p-4 text-xs font-bold text-slate-700 dark:text-white/80">
                  {{ consultant.specialization || 'عام' }}
                </td>

                <!-- Status Pill Component -->
                <td class="p-4 text-center">
                  <SpatialStatusPill
                    :type="consultant.employment_status === 'active' ? 'completed' : (consultant.employment_status === 'suspended' ? 'overdue' : 'pending')"
                    :pulse="consultant.employment_status === 'active'"
                  >
                    {{ consultant.employment_status === 'active' ? 'نشط' : (consultant.employment_status === 'suspended' ? 'موقوف مؤقتاً' : 'في إجازة') }}
                  </SpatialStatusPill>
                </td>

                <!-- Actions Buttons matching Sites/Index.vue -->
                <td class="p-4 text-center">
                  <div class="flex items-center justify-center gap-1.5">
                    <!-- View Profile SVG -->
                    <SpatialIconButton
                      variant="primary"
                      title="عرض ملف الاستشاري"
                      @click="openViewModal(consultant)"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                      </svg>
                    </SpatialIconButton>

                    <!-- Edit Profile SVG -->
                    <SpatialIconButton
                      variant="info"
                      title="تعديل بيانات الاستشاري"
                      @click="openEditDrawer(consultant)"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                    </SpatialIconButton>

                    <!-- Toggle Status Quick Button SVG (BR-015) -->
                    <SpatialIconButton
                      :variant="consultant.employment_status === 'active' ? 'warning' : 'success'"
                      :title="consultant.employment_status === 'active' ? 'تعليق وحظر الجلسات' : 'إعادة تفعيل الحساب'"
                      @click="toggleConsultantStatus(consultant)"
                    >
                      <svg v-if="consultant.employment_status === 'active'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                      <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                    </SpatialIconButton>

                    <!-- Soft Delete SVG -->
                    <SpatialIconButton
                      variant="danger"
                      title="نقل للأرشيف"
                      @click="openDeleteModal(consultant)"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                    </SpatialIconButton>
                  </div>
                </td>
              </tr>

              <!-- Empty State if no data -->
              <tr v-if="!consultants.data || consultants.data.length === 0">
                <td colspan="7" class="p-12 text-center">
                  <SpatialEmptyState
                    title="لا يوجد استشاريين مطابقين"
                    description="لم نجد أي نتائج تطابق محددات البحث والفلاتر الحالية."
                    action-text="إعادة تعيين الفلاتر"
                    @action="resetFilters"
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination Bar -->
        <div v-if="consultants.links && consultants.links.length > 3" class="p-4 border-t border-black/10 dark:border-white/10 flex items-center justify-between flex-wrap gap-4 text-xs font-bold">
          <div class="text-slate-500 dark:text-white/60">
            عرض النتائج من {{ consultants.from || 0 }} إلى {{ consultants.to || 0 }} من أصل {{ consultants.total || 0 }} استشاري
          </div>
          <div class="flex items-center gap-1">
            <button
              v-for="(link, i) in consultants.links"
              :key="i"
              @click="navigatePage(link.url)"
              :disabled="!link.url"
              :class="[
                'px-3 py-1.5 rounded-lg transition-all cursor-pointer',
                link.active ? 'bg-primary text-white font-black shadow-md' : 'text-slate-600 dark:text-white/70 hover:bg-black/5 dark:hover:bg-white/10',
                !link.url ? 'opacity-40 cursor-not-allowed' : ''
              ]"
              v-html="link.label"
            ></button>
          </div>
        </div>
      </SpatialCard>

      <!-- Floating Bulk Bar -->
      <SpatialFloatingBulkBar
        :selected-count="selectedIds.length"
        @clear="selectedIds = []"
      >
        <button
          @click="handleBulkDeactivate"
          class="px-3.5 py-1.5 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 text-amber-600 dark:text-amber-300 font-bold text-xs transition-all cursor-pointer"
        >
          ⏸️ تعليق المحددين
        </button>
      </SpatialFloatingBulkBar>
    </div>

    <!-- Consultant Add/Edit Side Drawer -->
    <ConsultantDrawer
      :is-open="isDrawerOpen"
      :is-edit="isEditMode"
      :consultant-data="selectedConsultant"
      :work-schedule-templates="workScheduleTemplates"
      :errors="formErrors"
      :loading="isSubmitting"
      @close="closeDrawer"
      @submit="handleDrawerSubmit"
    />

    <!-- Consultant View Profile Modal -->
    <ConsultantDetailModal
      :is-open="isViewModalOpen"
      :consultant="viewingConsultant"
      @close="isViewModalOpen = false"
    />

    <!-- Delete Confirmation Modal -->
    <DeleteConsultantModal
      :is-open="isDeleteModalOpen"
      :consultant-name="deletingConsultant?.full_name || ''"
      :loading="isDeleting"
      @close="isDeleteModalOpen = false"
      @confirm="executeDelete"
    />
  </HRLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import HRLayout from '@/Layouts/HRLayout.vue';
import SpatialToast from '@/Components/Spatial/SpatialToast.vue';
import SpatialCard from '@/Components/Spatial/SpatialCard.vue';
import SpatialButton from '@/Components/Spatial/SpatialButton.vue';
import SpatialInput from '@/Components/Spatial/SpatialInput.vue';
import SpatialDropdown from '@/Components/Spatial/SpatialDropdown.vue';
import SpatialIconButton from '@/Components/Spatial/SpatialIconButton.vue';
import SpatialStatusPill from '@/Components/Spatial/SpatialStatusPill.vue';
import SpatialCheckbox from '@/Components/Spatial/SpatialCheckbox.vue';
import SpatialEmptyState from '@/Components/Spatial/SpatialEmptyState.vue';
import SpatialFloatingBulkBar from '@/Components/Spatial/SpatialFloatingBulkBar.vue';
import ConsultantKpiCard from '@/Components/Consultants/ConsultantKpiCard.vue';
import ConsultantDrawer from '@/Components/Consultants/ConsultantDrawer.vue';
import ConsultantDetailModal from '@/Components/Consultants/ConsultantDetailModal.vue';
import DeleteConsultantModal from '@/Components/Consultants/DeleteConsultantModal.vue';

const props = defineProps({
  consultants: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
  stats: {
    type: Object,
    default: () => ({ total: 0, active: 0, suspended: 0, vacation: 0 }),
  },
  workScheduleTemplates: {
    type: Array,
    default: () => [],
  },
});

// Toast Ref
const toastRef = ref(null);

// Filters State
const searchQuery = ref(props.filters.search || '');
const selectedStatus = ref(props.filters.status || '');
const selectedSpecialization = ref(props.filters.specialization || '');
const selectedSort = ref(props.filters.sort || 'latest');

// Selection State
const selectedIds = ref([]);
const featuredConsultant = ref(null);

// Drawer & Modal States
const isDrawerOpen = ref(false);
const isEditMode = ref(false);
const selectedConsultant = ref(null);
const formErrors = ref({});
const isSubmitting = ref(false);

// View Profile State
const isViewModalOpen = ref(false);
const viewingConsultant = ref(null);

// Delete State
const isDeleteModalOpen = ref(false);
const deletingConsultant = ref(null);
const isDeleting = ref(false);

// Filter Options
const statusFilterOptions = [
  { label: 'جميع الحالات', value: '' },
  { label: 'نشط (Active)', value: 'active' },
  { label: 'موقوف مؤقتاً (Suspended)', value: 'suspended' },
  { label: 'في إجازة (Vacation)', value: 'vacation' },
];

const specializationFilterOptions = [
  { label: 'جميع التخصصات', value: '' },
  { label: 'هندسة مدنية وشبكات', value: 'هندسة مدنية وشبكات' },
  { label: 'صيانة ومتابعة تشغيلية', value: 'صيانة ومتابعة تشغيلية' },
  { label: 'سلامة وبيئة ميدانية', value: 'سلامة وبيئة ميدانية' },
  { label: 'تقنية معلومات واتصالات', value: 'تقنية معلومات واتصالات' },
];

const sortFilterOptions = [
  { label: 'الأحدث أولاً', value: 'latest' },
  { label: 'الأقدم أولاً', value: 'oldest' },
  { label: 'الاسم (أ - ي)', value: 'name_asc' },
  { label: 'الاسم (ي - أ)', value: 'name_desc' },
  { label: 'الرقم الوظيفي', value: 'emp_asc' },
];

const hasActiveFilters = computed(() => {
  return searchQuery.value || selectedStatus.value || selectedSpecialization.value || (selectedSort.value && selectedSort.value !== 'latest');
});

const isAllSelected = computed({
  get() {
    if (!props.consultants.data || props.consultants.data.length === 0) return false;
    return props.consultants.data.every(c => selectedIds.value.includes(c.id));
  },
  set(val) {
    if (val) {
      selectedIds.value = props.consultants.data.map(c => c.id);
    } else {
      selectedIds.value = [];
    }
  }
});

function toggleSelectAll(event) {
  const checked = typeof event === 'boolean' ? event : event?.target?.checked;
  if (checked || isAllSelected.value) {
    selectedIds.value = props.consultants.data.map(c => c.id);
  } else {
    selectedIds.value = [];
  }
}

function handleFilterChange() {
  router.get(
    route('admin.consultants.index'),
    {
      search: searchQuery.value,
      status: selectedStatus.value,
      specialization: selectedSpecialization.value,
      sort: selectedSort.value,
    },
    { preserveState: true, replace: true }
  );
}

function resetFilters() {
  searchQuery.value = '';
  selectedStatus.value = '';
  selectedSpecialization.value = '';
  selectedSort.value = 'latest';
  handleFilterChange();
}

function navigatePage(url) {
  if (url) {
    router.get(url, {}, { preserveState: true, preserveScroll: true });
  }
}

function formatDate(dateStr) {
  if (!dateStr) return 'غير محدد';
  return dateStr.substring(0, 10);
}

// Drawer Controls
function openCreateDrawer() {
  selectedConsultant.value = null;
  isEditMode.value = false;
  formErrors.value = {};
  isDrawerOpen.value = true;
}

function openEditDrawer(consultant) {
  selectedConsultant.value = consultant;
  isEditMode.value = true;
  formErrors.value = {};
  isDrawerOpen.value = true;
}

function closeDrawer() {
  isDrawerOpen.value = false;
}

function handleDrawerSubmit(formData) {
  isSubmitting.value = true;
  formErrors.value = {};

  if (isEditMode.value) {
    router.put(
      route('admin.consultants.update', formData.id),
      formData,
      {
        onSuccess: () => {
          closeDrawer();
          toastRef.value?.addToast('success', 'تم تحديث بيانات الاستشاري بنجاح.');
        },
        onError: (errs) => {
          formErrors.value = errs;
        },
        onFinish: () => {
          isSubmitting.value = false;
        },
      }
    );
  } else {
    router.post(
      route('admin.consultants.store'),
      formData,
      {
        onSuccess: () => {
          closeDrawer();
          toastRef.value?.addToast('success', 'تم إنشاء سجل الاستشاري وحساب المستخدم الموازي بنجاح.');
        },
        onError: (errs) => {
          formErrors.value = errs;
        },
        onFinish: () => {
          isSubmitting.value = false;
        },
      }
    );
  }
}

// View Profile Modal
function openViewModal(consultant) {
  viewingConsultant.value = consultant;
  featuredConsultant.value = consultant;
  isViewModalOpen.value = true;
}

// Toggle Employment Status (BR-015)
function toggleConsultantStatus(consultant) {
  const newStatus = consultant.employment_status === 'active' ? 'suspended' : 'active';
  router.patch(
    route('admin.consultants.update-status', consultant.id),
    { status: newStatus },
    {
      onSuccess: () => {
        const msg = newStatus === 'suspended' ? 'تم تعليق الاستشاري وإلغاء جلسات الدخول الحالية.' : 'تم إعادة تفعيل الاستشاري وحسابه بنجاح.';
        toastRef.value?.addToast('info', msg);
      },
    }
  );
}

// Delete Controls
function openDeleteModal(consultant) {
  deletingConsultant.value = consultant;
  isDeleteModalOpen.value = true;
}

function executeDelete() {
  if (!deletingConsultant.value) return;
  isDeleting.value = true;

  router.delete(
    route('admin.consultants.destroy', deletingConsultant.value.id),
    {
      onSuccess: () => {
        isDeleteModalOpen.value = false;
        toastRef.value?.addToast('error', 'تم نقل سجل الاستشاري وحسابه إلى الأرشيف.');
      },
      onError: (errs) => {
        const errMsg = errs.consultant || 'تعذر إجراء الحذف.';
        toastRef.value?.addToast('error', errMsg);
      },
      onFinish: () => {
        isDeleting.value = false;
      },
    }
  );
}

// Bulk Actions
function handleBulkDeactivate() {
  if (selectedIds.value.length === 0) return;
  toastRef.value?.addToast('info', `تم تطبيق الإجراء على ${selectedIds.value.length} استشاريين.`);
  selectedIds.value = [];
}
</script>
