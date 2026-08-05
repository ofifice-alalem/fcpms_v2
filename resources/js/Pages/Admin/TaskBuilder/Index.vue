<template>
  <HRLayout title="منشئ المهام الديناميكي وتكليفات الميدان">
    <div class="space-y-6">
      <!-- Header Bar -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
            <span>منشئ المهام وتكليفات الميدان</span>
            <span class="px-2.5 py-0.5 rounded-full bg-primary/10 text-primary text-xs font-bold border border-primary/20 font-mono">
              BR-027 / BR-031
            </span>
          </h1>
          <p class="text-xs font-bold text-slate-500 dark:text-white/60 mt-1">
            تصميم استبيانات التفتيش والحقول الشرطية الذكية وإسناد المهام للمواقع والاستشاريين
          </p>
        </div>

        <Link :href="route('admin.tasks.create')">
          <SpatialButton variant="primary" size="md">
            <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            بناء مهمة جديدة
          </SpatialButton>
        </Link>
      </div>

      <!-- Filter Bar -->
      <div class="p-4 rounded-2xl bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <!-- Quick Filter Tabs -->
        <div class="flex items-center gap-1.5 p-1 rounded-xl bg-black/5 dark:bg-white/5 border border-black/5 dark:border-white/5">
          <button
            @click="setTaskTypeFilter('')"
            :class="[
              'px-3.5 py-1.5 rounded-lg text-xs font-black transition-all cursor-pointer',
              currentTaskType === ''
                ? 'bg-primary text-white shadow-sm'
                : 'text-slate-600 dark:text-white/70 hover:text-slate-900 dark:hover:text-white'
            ]"
          >
            الكل ({{ tasks.total }})
          </button>

          <button
            @click="setTaskTypeFilter('daily')"
            :class="[
              'px-3.5 py-1.5 rounded-lg text-xs font-black transition-all cursor-pointer',
              currentTaskType === 'daily'
                ? 'bg-primary text-white shadow-sm'
                : 'text-slate-600 dark:text-white/70 hover:text-slate-900 dark:hover:text-white'
            ]"
          >
            📅 المهام اليومية (Daily)
          </button>

          <button
            @click="setTaskTypeFilter('on_demand')"
            :class="[
              'px-3.5 py-1.5 rounded-lg text-xs font-black transition-all cursor-pointer',
              currentTaskType === 'on_demand'
                ? 'bg-primary text-white shadow-sm'
                : 'text-slate-600 dark:text-white/70 hover:text-slate-900 dark:hover:text-white'
            ]"
          >
            ⚡ عند الطلب (On-Demand)
          </button>
        </div>

        <!-- Search Input -->
        <div class="flex items-center gap-3">
          <div class="w-64">
            <SpatialInput
              v-model="searchQuery"
              placeholder="ابحث بعنوان المهمة..."
              @input="handleSearch"
            />
          </div>
        </div>
      </div>

      <!-- Tasks Grid / Data List -->
      <div v-if="tasks.data && tasks.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <div
          v-for="task in tasks.data"
          :key="task.id"
          class="p-5 rounded-2xl bg-black/5 dark:bg-white/5 border border-black/10 dark:border-white/10 hover:border-primary/40 transition-all space-y-4 relative group flex flex-col justify-between"
        >
          <!-- Task Header -->
          <div class="space-y-2">
            <div class="flex items-center justify-between">
              <span
                :class="[
                  'px-2.5 py-0.5 rounded-full text-[11px] font-black',
                  task.task_type === 'daily'
                    ? 'bg-blue-500/15 text-blue-600 dark:text-blue-400 border border-blue-500/20'
                    : 'bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-500/20'
                ]"
              >
                {{ task.task_type === 'daily' ? '📅 مهمة يومية' : '⚡ عند الطلب' }}
              </span>

              <!-- Active Status Switch (BR-027) -->
              <div class="flex items-center gap-2">
                <span class="text-[10px] font-bold text-slate-500 dark:text-white/50">
                  {{ task.is_active ? 'مفعّلة' : 'معطّلة' }}
                </span>
                <SpatialSwitch
                  :model-value="task.is_active"
                  @update:model-value="toggleActiveStatus(task)"
                />
              </div>
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
          <div class="space-y-3 pt-3 border-t border-black/5 dark:border-white/5">
            <div class="flex items-center justify-between text-xs">
              <span class="font-bold text-slate-500 dark:text-white/60">حقول الاستبيان:</span>
              <span class="px-2 py-0.5 rounded-md bg-purple-500/15 text-purple-600 dark:text-purple-400 font-mono font-black">
                {{ task.components_count || (task.components ? task.components.length : 0) }} حقول
              </span>
            </div>

            <div class="flex items-center justify-between text-xs">
              <span class="font-bold text-slate-500 dark:text-white/60">المواقع المسندة:</span>
              <div class="flex items-center gap-1 overflow-x-auto max-w-[160px]">
                <template v-if="task.site_assignments && task.site_assignments.length > 0">
                  <span
                    v-for="sa in task.site_assignments.slice(0, 2)"
                    :key="sa.id"
                    class="px-1.5 py-0.5 rounded bg-black/10 dark:bg-white/10 text-[10px] font-mono font-bold text-slate-700 dark:text-white/80 shrink-0"
                  >
                    {{ sa.site ? sa.site.code : `Site #${sa.site_id}` }}
                  </span>
                  <span v-if="task.site_assignments.length > 2" class="text-[10px] font-bold text-slate-400">
                    +{{ task.site_assignments.length - 2 }}
                  </span>
                </template>
                <span v-else class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400">
                  🌐 عامة لجميع المواقع
                </span>
              </div>
            </div>
          </div>

          <!-- Action Buttons Bar -->
          <div class="pt-3 border-t border-black/5 dark:border-white/5 flex items-center justify-end gap-2">
            <button
              @click="openPreviewModal(task)"
              title="معاينة حية تفاعلية"
              class="p-2 rounded-xl bg-black/5 dark:bg-white/5 hover:bg-primary/10 text-slate-600 dark:text-white/80 hover:text-primary transition-all cursor-pointer"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
              </svg>
            </button>

            <Link
              :href="route('admin.tasks.edit', task.id)"
              title="تعديل بنية المهمة والتكليفات"
              class="p-2 rounded-xl bg-black/5 dark:bg-white/5 hover:bg-amber-500/10 text-slate-600 dark:text-white/80 hover:text-amber-500 transition-all cursor-pointer block"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
              </svg>
            </Link>

            <button
              @click="openDeleteModal(task)"
              title="أرشفة وتأكيد الحذف"
              class="p-2 rounded-xl bg-black/5 dark:bg-white/5 hover:bg-red-500/10 text-slate-600 dark:text-white/80 hover:text-red-500 transition-all cursor-pointer"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="p-12 text-center rounded-3xl bg-black/5 dark:bg-white/5 border border-dashed border-black/10 dark:border-white/10 space-y-3">
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
      </div>

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
import { ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import HRLayout from '@/Layouts/HRLayout.vue';
import SpatialButton from '@/Components/Spatial/SpatialButton.vue';
import SpatialInput from '@/Components/Spatial/SpatialInput.vue';
import SpatialSwitch from '@/Components/Spatial/SpatialSwitch.vue';
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

const currentTaskType = ref(props.filters.task_type || '');
const searchQuery = ref(props.filters.search || '');

const isPreviewModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const isDeleting = ref(false);
const selectedTask = ref(null);

function setTaskTypeFilter(type) {
  currentTaskType.value = type;
  applyFilters();
}

function handleSearch() {
  applyFilters();
}

function applyFilters() {
  router.get(
    route('admin.tasks.index'),
    {
      task_type: currentTaskType.value || undefined,
      search: searchQuery.value || undefined,
    },
    {
      preserveState: true,
      replace: true,
    }
  );
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
    },
    onError: () => {
      isDeleting.value = false;
    },
  });
}
</script>
