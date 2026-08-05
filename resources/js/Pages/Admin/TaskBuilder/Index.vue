<template>
  <HRLayout title="منشئ المهام وتكليفات الميدان">
    <div class="space-y-6">
      
      <!-- Toast Feedback Component -->
      <SpatialToast ref="toastRef" />

      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-black text-slate-900 dark:text-white flex items-center gap-2">
            📋 منشئ المهام وتكليفات الميدان
          </h1>
          <p class="text-xs font-bold text-slate-500 dark:text-white/60 mt-1">
            تصميم استبيانات التفتيش والحقول الشرطية الذكية وإسناد المهام للمواقع والاستشاريين
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

      <!-- Advanced Filter Bar -->
      <SpatialCard padding="p-5" class="relative z-30 space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
          
          <!-- Live Search Input -->
          <SpatialInput
            v-model="searchQuery"
            label="البحث المباشر"
            placeholder="ابحث بعنوان أو وصف المهمة..."
            @input="applyFilters"
          />

          <!-- Task Type Spatial Dropdown -->
          <SpatialDropdown
            v-model="selectedTaskType"
            label="نوع المهمة والتكرار"
            placeholder="جميع الأنواع"
            :options="taskTypeOptions"
            @change="applyFilters"
          />

          <!-- Status Spatial Dropdown -->
          <SpatialDropdown
            v-model="selectedStatus"
            label="حالة التفعيل"
            placeholder="جميع الحالات"
            :options="statusOptions"
            @change="applyFilters"
          />

          <!-- Site Spatial Dropdown -->
          <SpatialDropdown
            v-model="selectedSiteId"
            label="تصفية حسب الموقع"
            placeholder="جميع المواقع"
            :options="siteOptions"
            @change="applyFilters"
          />

          <!-- Consultant Spatial Dropdown -->
          <SpatialDropdown
            v-model="selectedConsultantId"
            label="تصفية حسب الاستشاري"
            placeholder="جميع الاستشاريين"
            :options="consultantOptions"
            @change="applyFilters"
          />

        </div>

        <!-- Filter Reset Quick Action -->
        <div v-if="hasActiveFilters" class="flex items-center justify-end pt-2">
          <button
            @click="resetFilters"
            class="text-xs font-bold text-red-500 hover:text-red-600 dark:text-red-400 flex items-center gap-1.5 cursor-pointer"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <span>إعادة ضبط الفلاتر</span>
          </button>
        </div>
      </SpatialCard>

      <!-- 1. GRID CARDS VIEW -->
      <div v-if="viewMode === 'grid' && tasks.data && tasks.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <SpatialCard
          v-for="task in tasks.data"
          :key="task.id"
          padding="p-6"
          class="relative group flex flex-col justify-between space-y-4 hover:border-primary/50 transition-all"
        >
          <!-- Task Header -->
          <div class="space-y-3">
            <div class="flex items-center justify-between">
              <!-- Task Type Pill -->
              <SpatialStatusPill :type="task.task_type === 'daily' ? 'completed' : 'pending'">
                {{ task.task_type === 'daily' ? '📅 مهمة يومية' : '⚡ عند الطلب' }}
              </SpatialStatusPill>

              <!-- Active Status Toggle Button (BR-027) -->
              <button
                @click.stop="toggleActiveStatus(task)"
                :class="[
                  'px-3 py-1 rounded-full text-xs font-black transition-all flex items-center gap-2 cursor-pointer border select-none',
                  task.is_active
                    ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border-emerald-500/30 hover:bg-emerald-500/25'
                    : 'bg-slate-100 dark:bg-white/5 text-slate-500 dark:text-white/40 border-slate-200 dark:border-white/10 hover:bg-slate-200'
                ]"
                title="انقر لتغيير حالة تفعيل المهمة"
              >
                <span :class="['w-2 h-2 rounded-full transition-all', task.is_active ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400 dark:bg-white/30']"></span>
                <span>{{ task.is_active ? 'مفعّلة' : 'معطّلة' }}</span>
                <SpatialSwitch
                  :model-value="task.is_active"
                  class="pointer-events-none scale-90"
                />
              </button>
            </div>

            <h3 class="text-base font-black text-slate-900 dark:text-white line-clamp-1">
              {{ task.title }}
            </h3>

            <p v-if="task.description" class="text-xs text-slate-500 dark:text-white/60 line-clamp-2 min-h-[32px]">
              {{ task.description }}
            </p>
            <p v-else class="text-xs text-slate-400 dark:text-white/40 italic min-h-[32px]">
              لا يوجد وصف مضاف لهذا النموذج.
            </p>
          </div>

          <!-- Task Metrics & Assignments Badges -->
          <div class="space-y-3 pt-3 border-t border-slate-200/60 dark:border-white/10">
            <div class="flex items-center justify-between text-xs">
              <span class="font-bold text-slate-500 dark:text-white/60">حقول الاستبيان:</span>
              <span class="px-2.5 py-0.5 rounded-lg bg-purple-500/15 text-purple-600 dark:text-purple-300 font-mono font-black border border-purple-500/20">
                {{ task.components_count || (task.components ? task.components.length : 0) }} حقول
              </span>
            </div>

            <div class="space-y-1.5">
              <span class="font-bold text-slate-500 dark:text-white/60 text-xs block">المواقع المسندة:</span>
              <div class="flex flex-col gap-1 max-h-28 overflow-y-auto custom-scroll pr-1">
                <template v-if="task.site_assignments && task.site_assignments.length > 0">
                  <div
                    v-for="sa in task.site_assignments"
                    :key="sa.id"
                    class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-white/10 text-xs font-bold text-slate-800 dark:text-white/90 border border-black/5 dark:border-white/5 flex items-center gap-1.5"
                  >
                    <span>🏛️</span>
                    <span class="truncate">{{ sa.site ? sa.site.name : `موقع #${sa.site_id}` }}</span>
                  </div>
                </template>
                <div v-else class="px-2.5 py-1 rounded-lg bg-emerald-500/10 text-xs font-bold text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 flex items-center gap-1.5">
                  <span>🌐</span>
                  <span>عامة لجميع المواقع</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Action Buttons Bar -->
          <div class="pt-3 border-t border-slate-200/60 dark:border-white/10 flex items-center justify-end gap-2">
            <SpatialIconButton
              variant="ghost"
              title="معاينة حية تفاعلية"
              @click="openPreviewModal(task)"
            >
              <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
              </svg>
            </SpatialIconButton>

            <Link :href="route('admin.tasks.edit', task.id)">
              <SpatialIconButton
                variant="ghost"
                title="تعديل بنية المهمة والتكليفات"
              >
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
              </SpatialIconButton>
            </Link>

            <SpatialIconButton
              variant="danger"
              title="أرشفة وتأكيد الحذف"
              @click="openDeleteModal(task)"
            >
              <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
              </svg>
            </SpatialIconButton>
          </div>
        </SpatialCard>
      </div>

      <!-- 2. HIGH-FIDELITY TABLE VIEW -->
      <SpatialCard v-else-if="viewMode === 'table' && tasks.data && tasks.data.length > 0" padding="p-0 overflow-hidden" class="relative z-10">
        <div class="overflow-x-auto">
          <table class="w-full text-right border-collapse">
            <thead>
              <tr class="border-b border-slate-200 dark:border-white/10 text-xs font-black text-slate-600 dark:text-white/70 bg-slate-100/90 dark:bg-white/5">
                <th class="p-4">عنوان النموذج / الوصف</th>
                <th class="p-4">النوع والتكرار</th>
                <th class="p-4 text-center">عدد الحقول</th>
                <th class="p-4">المواقع المسندة</th>
                <th class="p-4 text-center">حالة التفعيل</th>
                <th class="p-4 text-center">الإجراءات</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-sm font-bold">
              <tr
                v-for="task in tasks.data"
                :key="task.id"
                class="transition-colors hover:bg-slate-50/80 dark:hover:bg-white/5"
              >
                <!-- Task Title & Description -->
                <td class="p-4 max-w-xs">
                  <div class="font-black text-slate-900 dark:text-white truncate">
                    {{ task.title }}
                  </div>
                  <div class="text-xs text-slate-500 dark:text-white/50 truncate mt-0.5">
                    {{ task.description || 'لا يوجد وصف مضاف' }}
                  </div>
                </td>

                <!-- Type Pill -->
                <td class="p-4 whitespace-nowrap">
                  <SpatialStatusPill :type="task.task_type === 'daily' ? 'completed' : 'pending'">
                    {{ task.task_type === 'daily' ? '📅 يومية' : '⚡ عند الطلب' }}
                  </SpatialStatusPill>
                </td>

                <!-- Components Count -->
                <td class="p-4 text-center whitespace-nowrap">
                  <span class="px-2.5 py-1 rounded-lg bg-purple-500/15 text-purple-600 dark:text-purple-300 font-mono font-black text-xs border border-purple-500/20">
                    {{ task.components_count || (task.components ? task.components.length : 0) }} حقول
                  </span>
                </td>

                <!-- Site Assignments -->
                <td class="p-4">
                  <div class="flex flex-col gap-1 max-h-28 overflow-y-auto custom-scroll max-w-[220px]">
                    <template v-if="task.site_assignments && task.site_assignments.length > 0">
                      <div
                        v-for="sa in task.site_assignments"
                        :key="sa.id"
                        class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-white/10 text-xs font-bold text-slate-800 dark:text-white/90 border border-black/5 dark:border-white/5 flex items-center gap-1.5 whitespace-nowrap"
                      >
                        <span>🏛️</span>
                        <span>{{ sa.site ? sa.site.name : `موقع #${sa.site_id}` }}</span>
                      </div>
                    </template>
                    <div v-else class="px-2.5 py-1 rounded-lg bg-emerald-500/10 text-xs font-bold text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 flex items-center gap-1.5 whitespace-nowrap">
                      <span>🌐</span>
                      <span>عامة لجميع المواقع</span>
                    </div>
                  </div>
                </td>

                <!-- Active Status Toggle -->
                <td class="p-4 text-center whitespace-nowrap">
                  <button
                    @click.stop="toggleActiveStatus(task)"
                    :class="[
                      'px-3 py-1 rounded-full text-xs font-black transition-all inline-flex items-center gap-2 cursor-pointer border select-none',
                      task.is_active
                        ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border-emerald-500/30 hover:bg-emerald-500/25'
                        : 'bg-slate-100 dark:bg-white/5 text-slate-500 dark:text-white/40 border-slate-200 dark:border-white/10 hover:bg-slate-200'
                    ]"
                  >
                    <span :class="['w-2 h-2 rounded-full transition-all', task.is_active ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400 dark:bg-white/30']"></span>
                    <span>{{ task.is_active ? 'مفعّلة' : 'معطّلة' }}</span>
                    <SpatialSwitch
                      :model-value="task.is_active"
                      class="pointer-events-none scale-90"
                    />
                  </button>
                </td>

                <!-- Action Buttons -->
                <td class="p-4 text-center whitespace-nowrap">
                  <div class="flex items-center justify-center gap-1.5">
                    <SpatialIconButton
                      variant="ghost"
                      title="معاينة حية تفاعلية"
                      @click="openPreviewModal(task)"
                    >
                      <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                      </svg>
                    </SpatialIconButton>

                    <Link :href="route('admin.tasks.edit', task.id)">
                      <SpatialIconButton
                        variant="ghost"
                        title="تعديل بنية المهمة والتكليفات"
                      >
                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                      </SpatialIconButton>
                    </Link>

                    <SpatialIconButton
                      variant="danger"
                      title="أرشفة وتأكيد الحذف"
                      @click="openDeleteModal(task)"
                    >
                      <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                    </SpatialIconButton>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </SpatialCard>

      <!-- Empty State -->
      <SpatialCard v-else padding="p-12" class="text-center border-dashed space-y-3">
        <div class="w-16 h-16 mx-auto rounded-2xl bg-primary/10 flex items-center justify-center text-primary">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
          </svg>
        </div>
        <h3 class="text-base font-black text-slate-900 dark:text-white">لا توجد مهام ميدانية معرفة</h3>
        <p class="text-xs font-bold text-slate-500 dark:text-white/60 max-w-sm mx-auto">
          لم يتم إنشاء أي مهمة أو استبيان تفتيش يطابق الفلاتر المحددة حالياً. اضغط على الزر أدناه لبناء أول مهمة.
        </p>
        <Link :href="route('admin.tasks.create')">
          <SpatialButton variant="primary" size="md" class="mt-2">
            + بناء أول مهمة ميدانية
          </SpatialButton>
        </Link>
      </SpatialCard>

      <!-- Modals -->
      <TaskPreviewModal
        :is-open="isPreviewModalOpen"
        :task="selectedTask"
        @close="isPreviewModalOpen = false"
      />

      <DeleteTaskModal
        :is-open="isDeleteModalOpen"
        :task="selectedTask"
        :loading="isDeleting"
        @close="isDeleteModalOpen = false"
        @confirm="confirmDelete"
      />
    </div>
  </HRLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import HRLayout from '@/Layouts/HRLayout.vue';
import SpatialCard from '@/Components/Spatial/SpatialCard.vue';
import SpatialButton from '@/Components/Spatial/SpatialButton.vue';
import SpatialIconButton from '@/Components/Spatial/SpatialIconButton.vue';
import SpatialStatusPill from '@/Components/Spatial/SpatialStatusPill.vue';
import SpatialInput from '@/Components/Spatial/SpatialInput.vue';
import SpatialDropdown from '@/Components/Spatial/SpatialDropdown.vue';
import SpatialSwitch from '@/Components/Spatial/SpatialSwitch.vue';
import SpatialToast from '@/Components/Spatial/SpatialToast.vue';
import TaskPreviewModal from '@/Components/TaskBuilder/TaskPreviewModal.vue';
import DeleteTaskModal from '@/Components/TaskBuilder/DeleteTaskModal.vue';

const props = defineProps({
  tasks: {
    type: Object,
    default: () => ({ data: [], total: 0 }),
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
  sites: {
    type: Array,
    default: () => [],
  },
  consultants: {
    type: Array,
    default: () => [],
  },
});

const toastRef = ref(null);
const viewMode = ref('grid'); // 'grid' | 'table'

const searchQuery = ref(props.filters.search || '');
const selectedTaskType = ref(props.filters.task_type || '');
const selectedStatus = ref(props.filters.is_active !== undefined ? String(props.filters.is_active) : '');
const selectedSiteId = ref(props.filters.site_id || '');
const selectedConsultantId = ref(props.filters.consultant_id || '');

const taskTypeOptions = [
  { label: 'جميع أنواع المهام', value: '' },
  { label: '📅 مهمة يومية (Daily)', value: 'daily' },
  { label: '⚡ عند الطلب (On-Demand)', value: 'on_demand' },
];

const statusOptions = [
  { label: 'جميع الحالات', value: '' },
  { label: '🟢 مفعّلة فقط', value: '1' },
  { label: '🔴 معطّلة فقط', value: '0' },
];

const siteOptions = computed(() => {
  const list = [{ label: 'جميع المواقع', value: '' }];
  props.sites.forEach(s => {
    list.push({
      label: `${s.name} (${s.code})`,
      value: s.id,
    });
  });
  return list;
});

const consultantOptions = computed(() => {
  const list = [{ label: 'جميع الاستشاريين', value: '' }];
  props.consultants.forEach(c => {
    list.push({
      label: `${c.full_name} (${c.employee_number})`,
      value: c.id,
    });
  });
  return list;
});

const hasActiveFilters = computed(() => {
  return (
    searchQuery.value ||
    selectedTaskType.value ||
    selectedStatus.value !== '' ||
    selectedSiteId.value ||
    selectedConsultantId.value
  );
});

const isPreviewModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const isDeleting = ref(false);
const selectedTask = ref(null);

function applyFilters() {
  router.get(
    route('admin.tasks.index'),
    {
      search: searchQuery.value || undefined,
      task_type: selectedTaskType.value || undefined,
      is_active: selectedStatus.value !== '' ? selectedStatus.value : undefined,
      site_id: selectedSiteId.value || undefined,
      consultant_id: selectedConsultantId.value || undefined,
    },
    {
      preserveState: true,
      replace: true,
    }
  );
}

function resetFilters() {
  searchQuery.value = '';
  selectedTaskType.value = '';
  selectedStatus.value = '';
  selectedSiteId.value = '';
  selectedConsultantId.value = '';
  applyFilters();
}

function openPreviewModal(task) {
  selectedTask.value = task;
  isPreviewModalOpen.value = true;
}

function openDeleteModal(task) {
  selectedTask.value = task;
  isDeleteModalOpen.value = true;
}

function toggleActiveStatus(task) {
  router.patch(route('admin.tasks.toggle-active', task.id), {}, {
    preserveScroll: true,
    onSuccess: () => {
      toastRef.value?.addToast('success', 'تم تغيير حالة تفعيل المهمة بنجاح.');
    },
    onError: () => {
      toastRef.value?.addToast('error', 'حدث خطأ أثناء تغيير حالة المهمة.');
    },
  });
}

function confirmDelete() {
  if (!selectedTask.value) return;
  isDeleting.value = true;
  router.delete(route('admin.tasks.destroy', selectedTask.value.id), {
    onSuccess: () => {
      isDeleteModalOpen.value = false;
      isDeleting.value = false;
      selectedTask.value = null;
      toastRef.value?.addToast('success', 'تم أرشفة وحذف المهمة بنجاح.');
    },
    onError: () => {
      isDeleting.value = false;
      toastRef.value?.addToast('error', 'تعذر أرشفة المهمة.');
    },
  });
}
</script>
