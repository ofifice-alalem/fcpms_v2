<template>
  <HRLayout>
    <!-- Toast Feedback Component -->
    <SpatialToast ref="toastRef" />

    <div class="space-y-6">
      
      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-black text-slate-900 dark:text-white flex items-center gap-2">
            📅 إدارة جداول الدوام والعطلات والإجازات
          </h1>
          <p class="text-xs font-bold text-slate-500 dark:text-white/60 mt-1">
            إدارة قوالب ساعات العمل الرسمية، أيام الراحة الأسبوعية، العطلات، وإجازات الميدان
          </p>
        </div>

        <div class="flex items-center gap-3 flex-wrap">
          <SpatialButton
            v-if="hasPerm('create-schedules')"
            variant="secondary"
            @click="openCreateHolidayModal"
          >
            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>+ عطلة رسمية</span>
          </SpatialButton>

          <SpatialButton
            v-if="hasPerm('create-schedules')"
            variant="secondary"
            @click="openCreateLeaveModal"
          >
            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>+ إجازة استشاري</span>
          </SpatialButton>

          <SpatialButton
            v-if="hasPerm('create-schedules')"
            variant="primary"
            @click="openCreateTemplateDrawer"
          >
            <div class="w-6 h-6 rounded-xl bg-white/20 flex items-center justify-center text-white">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/>
              </svg>
            </div>
            <span>+ قالب دوام جديد</span>
          </SpatialButton>
        </div>
      </div>

      <!-- Tab Navigation Bar -->
      <SpatialCard padding="p-2" class="relative z-20">
        <div class="flex items-center gap-2 overflow-x-auto custom-scroll">
          <button
            @click="activeTab = 'templates'"
            :class="[
              'px-5 py-3 rounded-2xl font-black text-xs transition-all duration-200 cursor-pointer flex items-center gap-2 shrink-0 select-none',
              activeTab === 'templates'
                ? 'bg-primary text-white shadow-lg shadow-primary/25 scale-[1.02]'
                : 'text-slate-600 dark:text-white/70 hover:bg-black/5 dark:hover:bg-white/5'
            ]"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <span>قوالب الدوام وساعات العمل</span>
            <span class="px-2 py-0.5 rounded-lg bg-white/20 text-[10px] font-mono font-black">
              {{ templates ? templates.length : 0 }}
            </span>
          </button>

          <button
            @click="activeTab = 'holidays'"
            :class="[
              'px-5 py-3 rounded-2xl font-black text-xs transition-all duration-200 cursor-pointer flex items-center gap-2 shrink-0 select-none',
              activeTab === 'holidays'
                ? 'bg-primary text-white shadow-lg shadow-primary/25 scale-[1.02]'
                : 'text-slate-600 dark:text-white/70 hover:bg-black/5 dark:hover:bg-white/5'
            ]"
          >
            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
            </svg>
            <span>العطلات الرسمية العامة</span>
            <span class="px-2 py-0.5 rounded-lg bg-white/20 text-[10px] font-mono font-black">
              {{ officialHolidays ? officialHolidays.length : 0 }}
            </span>
          </button>

          <button
            @click="activeTab = 'leaves'"
            :class="[
              'px-5 py-3 rounded-2xl font-black text-xs transition-all duration-200 cursor-pointer flex items-center gap-2 shrink-0 select-none',
              activeTab === 'leaves'
                ? 'bg-primary text-white shadow-lg shadow-primary/25 scale-[1.02]'
                : 'text-slate-600 dark:text-white/70 hover:bg-black/5 dark:hover:bg-white/5'
            ]"
          >
            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span>إجازات الاستشاريين الميدانيين</span>
            <span class="px-2 py-0.5 rounded-lg bg-white/20 text-[10px] font-mono font-black">
              {{ consultantLeaves ? consultantLeaves.length : 0 }}
            </span>
          </button>
        </div>
      </SpatialCard>

      <!-- TAB-SPECIFIC FILTER BAR -->
      <SpatialCard padding="p-4" class="relative z-30">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
          <div class="w-full sm:w-80">
            <SpatialInput
              v-model="tabSearch"
              :placeholder="tabSearchPlaceholder"
              label="البحث المباشر في هذا التاب"
            />
          </div>

          <!-- Template-specific Filter Dropdown -->
          <div v-if="activeTab === 'templates'" class="w-full sm:w-60 relative z-40">
            <SpatialDropdown
              v-model="templateFilterStatus"
              label="نوع القالب"
              placeholder="جميع القوالب"
              :options="[
                { label: 'جميع القوالب', value: '' },
                { label: 'القالب الافتراضي فقط', value: 'default' },
                { label: 'القوالب المخصصة', value: 'custom' },
              ]"
            />
          </div>

          <!-- Leaves-specific Filter Options -->
          <div v-if="activeTab === 'leaves'" class="flex-1 flex flex-wrap items-center justify-between gap-3 w-full">
            <!-- Status Buttons Toggle -->
            <div class="flex items-center gap-1 p-1 rounded-xl bg-slate-100 dark:bg-white/5 border border-slate-200/60 dark:border-white/10 text-xs font-bold">
              <button
                type="button"
                @click="leaveStatusFilter = 'all'"
                :class="[
                  'px-3 py-1.5 rounded-lg transition-all cursor-pointer select-none',
                  leaveStatusFilter === 'all' ? 'bg-indigo-600 text-white shadow-xs font-black' : 'text-slate-500 dark:text-white/60 hover:text-slate-900 dark:hover:text-white'
                ]"
              >
                الكل
              </button>

              <button
                type="button"
                @click="leaveStatusFilter = 'active'"
                :class="[
                  'px-3 py-1.5 rounded-lg transition-all cursor-pointer select-none',
                  leaveStatusFilter === 'active' ? 'bg-emerald-600 text-white shadow-xs font-black' : 'text-slate-500 dark:text-white/60 hover:text-slate-900 dark:hover:text-white'
                ]"
              >
                🟢 جارية الآن
              </button>

              <button
                type="button"
                @click="leaveStatusFilter = 'future'"
                :class="[
                  'px-3 py-1.5 rounded-lg transition-all cursor-pointer select-none',
                  leaveStatusFilter === 'future' ? 'bg-blue-600 text-white shadow-xs font-black' : 'text-slate-500 dark:text-white/60 hover:text-slate-900 dark:hover:text-white'
                ]"
              >
                📅 مستقبلية
              </button>

              <button
                type="button"
                @click="leaveStatusFilter = 'expired'"
                :class="[
                  'px-3 py-1.5 rounded-lg transition-all cursor-pointer select-none',
                  leaveStatusFilter === 'expired' ? 'bg-slate-700 text-white shadow-xs font-black' : 'text-slate-500 dark:text-white/60 hover:text-slate-900 dark:hover:text-white'
                ]"
              >
                ⚪ منتهية
              </button>
            </div>

            <!-- Date Range Filters -->
            <div class="flex items-center gap-2">
              <div class="flex items-center gap-1.5 bg-slate-100 dark:bg-white/5 border border-slate-200/60 dark:border-white/10 rounded-xl px-2.5 py-1">
                <span class="text-[11px] font-bold text-slate-400">من:</span>
                <input
                  type="date"
                  v-model="leaveStartDateFilter"
                  title="من تاريخ بداية الإجازة"
                  class="bg-transparent text-xs font-mono font-bold text-slate-800 dark:text-white focus:outline-hidden"
                />
              </div>

              <div class="flex items-center gap-1.5 bg-slate-100 dark:bg-white/5 border border-slate-200/60 dark:border-white/10 rounded-xl px-2.5 py-1">
                <span class="text-[11px] font-bold text-slate-400">إلى:</span>
                <input
                  type="date"
                  v-model="leaveEndDateFilter"
                  title="إلى تاريخ نهاية الإجازة"
                  class="bg-transparent text-xs font-mono font-bold text-slate-800 dark:text-white focus:outline-hidden"
                />
              </div>
            </div>
          </div>

          <div v-if="tabSearch || templateFilterStatus || leaveStatusFilter !== 'all' || leaveStartDateFilter || leaveEndDateFilter" class="self-end pb-1">
            <button
              @click="clearTabFilters"
              class="px-3 py-2 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 font-bold text-xs border border-rose-500/20 transition-all cursor-pointer flex items-center gap-1.5"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
              </svg>
              <span>إعادة تعيين</span>
            </button>
          </div>
        </div>
      </SpatialCard>

      <!-- TAB 1: SCHEDULE TEMPLATES GRID -->
      <div v-if="activeTab === 'templates'">
        <div v-if="filteredTemplates && filteredTemplates.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
          <ScheduleTemplateCard
            v-for="tpl in filteredTemplates"
            :key="tpl.id"
            :template="tpl"
            @view="openTemplateDetailModal"
            @edit="openEditTemplateDrawer"
            @delete="openDeleteTemplateModal"
          />
        </div>

        <SpatialCard v-else padding="p-12" class="text-center">
          <SpatialEmptyState
            title="لا يوجد قوالب دوام مطابقين"
            description="لم نجد أي قوالب دوام تتطابق مع معايير البحث والفلترة المحددة."
            action-text="إعادة تعيين الفلاتر"
            @action="clearTabFilters"
          />
        </SpatialCard>
      </div>

      <!-- TAB 2: OFFICIAL HOLIDAYS TABLE WITH EDIT & DELETE AND DATE RANGES -->
      <div v-if="activeTab === 'holidays'">
        <SpatialCard padding="p-0 overflow-hidden" class="relative z-10">
          <div class="overflow-x-auto custom-scroll">
            <table class="w-full text-right border-collapse">
              <thead>
                <tr class="border-b border-slate-200 dark:border-white/10 text-xs font-black text-slate-600 dark:text-white/70 bg-slate-100/90 dark:bg-white/5">
                  <th class="p-4">#</th>
                  <th class="p-4">اسم المناسبة / العطلة الرسمية</th>
                  <th class="p-4">نطاق التواريخ (من - إلى)</th>
                  <th class="p-4 text-center">المدة بالإجازة</th>
                  <th class="p-4">ملاحظات وقرارات</th>
                  <th class="p-4 text-center w-28">الإجراءات</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-sm font-bold">
                <tr
                  v-for="(holiday, index) in filteredHolidays"
                  :key="holiday.id"
                  class="transition-colors hover:bg-slate-50/80 dark:hover:bg-white/5"
                >
                  <td class="p-4 text-xs font-mono font-black text-slate-400">
                    {{ index + 1 }}
                  </td>
                  <td class="p-4 font-black text-slate-900 dark:text-white">
                    {{ holiday.name }}
                  </td>
                  <td class="p-4 font-mono text-xs font-black text-emerald-600 dark:text-emerald-400">
                    {{ formatDateRange(holiday.start_date || holiday.holiday_date, holiday.end_date) }}
                  </td>
                  <td class="p-4 text-center">
                    <span class="px-2.5 py-1 rounded-xl bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 font-mono text-xs font-black border border-emerald-500/20">
                      {{ getHolidayDurationDays(holiday.start_date || holiday.holiday_date, holiday.end_date) }} يوم
                    </span>
                  </td>
                  <td class="p-4 text-xs text-slate-500 dark:text-white/60">
                    {{ holiday.notes || '-' }}
                  </td>
                  <td class="p-4 text-center">
                    <div class="flex items-center justify-center gap-1.5">
                      <SpatialIconButton
                        v-if="hasPerm('edit-schedules')"
                        variant="info"
                        title="تعديل العطلة الرسمية"
                        @click="openEditHolidayModal(holiday)"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                      </SpatialIconButton>

                      <SpatialIconButton
                        v-if="hasPerm('delete-schedules')"
                        variant="danger"
                        title="حذف العطلة الرسمية"
                        @click="openDeleteHolidayModal(holiday)"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                      </SpatialIconButton>
                    </div>
                  </td>
                </tr>

                <tr v-if="!filteredHolidays || filteredHolidays.length === 0">
                  <td colspan="6" class="p-12 text-center">
                    <SpatialEmptyState
                      title="لا يوجد عطلات رسمية مسجلة"
                      description="يمكنك تسجيل المناسبات والعطلات الرسمية العامة لاستثنائها من احتساب الغياب."
                      :action-text="hasPerm('create-schedules') ? 'إضافة عطلة رسمية' : ''"
                      @action="openCreateHolidayModal"
                    />
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </SpatialCard>
      </div>

      <!-- TAB 3: CONSULTANT LEAVES TABLE WITH EDIT & DELETE -->
      <div v-if="activeTab === 'leaves'">
        <SpatialCard padding="p-0 overflow-hidden" class="relative z-10">
          <div class="overflow-x-auto custom-scroll">
            <table class="w-full text-right border-collapse">
              <thead>
                <tr class="border-b border-slate-200 dark:border-white/10 text-xs font-black text-slate-600 dark:text-white/70 bg-slate-100/90 dark:bg-white/5">
                  <th class="p-4">الاستشاري الميداني</th>
                  <th class="p-4">تاريخ البدء</th>
                  <th class="p-4">تاريخ الانتهاء</th>
                  <th class="p-4 text-center">المدة الإجمالية</th>
                  <th class="p-4">السبب والملاحظات</th>
                  <th class="p-4 text-center">الحالة التشغيلية</th>
                  <th class="p-4 text-center w-28">الإجراءات</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-sm font-bold">
                <tr
                  v-for="leave in filteredLeaves"
                  :key="leave.id"
                  class="transition-colors hover:bg-slate-50/80 dark:hover:bg-white/5"
                >
                  <td class="p-4">
                    <div class="font-black text-slate-900 dark:text-white">{{ leave.consultant?.full_name || 'غير معروف' }}</div>
                    <div class="font-mono text-xs text-slate-500 dark:text-white/50 mt-0.5">{{ leave.consultant?.employee_number }}</div>
                  </td>
                  <td class="p-4 font-mono text-xs font-black text-slate-700 dark:text-white/80">
                    {{ formatDate(leave.start_date) }}
                  </td>
                  <td class="p-4 font-mono text-xs font-black text-slate-700 dark:text-white/80">
                    {{ formatDate(leave.end_date) }}
                  </td>
                  <td class="p-4 text-center">
                    <span class="px-2.5 py-1 rounded-xl bg-amber-500/15 text-amber-700 dark:text-amber-300 font-mono text-xs font-black border border-amber-500/20">
                      {{ getHolidayDurationDays(leave.start_date, leave.end_date) }} أيام
                    </span>
                  </td>
                  <td class="p-4 text-xs font-bold text-slate-700 dark:text-white/80">
                    <div>{{ leave.reason || 'إجازة رسمية' }}</div>
                    <div v-if="leave.notes" class="text-[11px] text-slate-400 font-normal mt-0.5">{{ leave.notes }}</div>
                  </td>
                  <td class="p-4 text-center">
                    <SpatialStatusPill :type="getLeaveStatus(leave).type" :pulse="getLeaveStatus(leave).pulse">
                      {{ getLeaveStatus(leave).label }}
                    </SpatialStatusPill>
                  </td>
                  <td class="p-4 text-center">
                    <div class="flex items-center justify-center gap-1.5">
                      <SpatialIconButton
                        v-if="hasPerm('edit-schedules')"
                        variant="info"
                        title="تعديل الإجازة"
                        @click="openEditLeaveModal(leave)"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                      </SpatialIconButton>

                      <SpatialIconButton
                        v-if="hasPerm('delete-schedules')"
                        variant="danger"
                        title="حذف الإجازة"
                        @click="openDeleteLeaveModal(leave)"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                      </SpatialIconButton>
                    </div>
                  </td>
                </tr>

                <tr v-if="!filteredLeaves || filteredLeaves.length === 0">
                  <td colspan="7" class="p-12 text-center">
                    <SpatialEmptyState
                      title="لا يوجد إجازات استشاريين مسجلة"
                      description="سجل إجازات الاستشاريين لتحديث حالتهم التشغيلية تلقائياً وحماية أداء الميدان."
                      action-text="تسجيل إجازة استشاري"
                      @action="openCreateLeaveModal"
                    />
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </SpatialCard>
      </div>

    </div>

    <!-- Drawers & Modals -->
    <ScheduleTemplateDrawer
      :is-open="isTemplateDrawerOpen"
      :is-edit="isTemplateEditMode"
      :template-data="selectedTemplate"
      :errors="formErrors"
      :loading="isSubmitting"
      @close="isTemplateDrawerOpen = false"
      @submit="handleTemplateDrawerSubmit"
    />

    <ScheduleTemplateDetailModal
      :is-open="isDetailModalOpen"
      :template="viewingTemplate"
      @close="isDetailModalOpen = false"
    />

    <HolidayModal
      :is-open="isHolidayModalOpen"
      :is-edit="isHolidayEditMode"
      :holiday-data="selectedHoliday"
      :errors="formErrors"
      :loading="isSubmitting"
      @close="isHolidayModalOpen = false"
      @submit="handleHolidaySubmit"
    />

    <LeaveModal
      :is-open="isLeaveModalOpen"
      :is-edit="isLeaveEditMode"
      :leave-data="selectedLeave"
      :active-consultants="activeConsultants"
      :errors="formErrors"
      :loading="isSubmitting"
      @close="isLeaveModalOpen = false"
      @submit="handleLeaveSubmit"
    />

    <!-- Template Delete Confirmation Modal -->
    <DeleteTemplateModal
      :is-open="isDeleteModalOpen"
      :template="deletingTemplate"
      :loading="isSubmitting"
      @close="isDeleteModalOpen = false"
      @confirm="executeDeleteTemplate"
    />

    <!-- Generic Holiday & Leave Delete Confirmation Modal -->
    <DeleteConfirmModal
      :is-open="isGenericDeleteModalOpen"
      :title="genericDeleteTitle"
      :message="genericDeleteMessage"
      :loading="isSubmitting"
      @close="isGenericDeleteModalOpen = false"
      @confirm="executeGenericDelete"
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
import SpatialEmptyState from '@/Components/Spatial/SpatialEmptyState.vue';
import ScheduleTemplateCard from '@/Components/WorkSchedules/ScheduleTemplateCard.vue';
import ScheduleTemplateDrawer from '@/Components/WorkSchedules/ScheduleTemplateDrawer.vue';
import ScheduleTemplateDetailModal from '@/Components/WorkSchedules/ScheduleTemplateDetailModal.vue';
import HolidayModal from '@/Components/WorkSchedules/HolidayModal.vue';
import LeaveModal from '@/Components/WorkSchedules/LeaveModal.vue';
import DeleteTemplateModal from '@/Components/WorkSchedules/DeleteTemplateModal.vue';
import DeleteConfirmModal from '@/Components/WorkSchedules/DeleteConfirmModal.vue';

const props = defineProps({
  templates: {
    type: Array,
    default: () => [],
  },
  officialHolidays: {
    type: Array,
    default: () => [],
  },
  consultantLeaves: {
    type: Array,
    default: () => [],
  },
  activeConsultants: {
    type: Array,
    default: () => [],
  },
});

const page = usePage();
const userPermissions = computed(() => page.props.auth?.user?.permissions || []);
const userRoles = computed(() => page.props.auth?.user?.roles || []);

const hasPerm = (perm) => {
  if (userRoles.value.includes('admin')) return true;
  return userPermissions.value.includes(perm);
};

// Toast Ref
const toastRef = ref(null);

// Active Tab State ('templates', 'holidays', 'leaves')
const activeTab = ref('templates');

// Tab Filters State
const tabSearch = ref('');
const templateFilterStatus = ref('');
const leaveStatusFilter = ref('all');
const leaveStartDateFilter = ref('');
const leaveEndDateFilter = ref('');

const tabSearchPlaceholder = computed(() => {
  if (activeTab.value === 'templates') return 'البحث باسم القالب أو الوصف...';
  if (activeTab.value === 'holidays') return 'البحث باسم المناسبة أو الملاحظات...';
  return 'البحث باسم الاستشاري، الرقم الوظيفي، أو السبب...';
});

function clearTabFilters() {
  tabSearch.value = '';
  templateFilterStatus.value = '';
  leaveStatusFilter.value = 'all';
  leaveStartDateFilter.value = '';
  leaveEndDateFilter.value = '';
}

// Filtered Lists
const filteredTemplates = computed(() => {
  let result = props.templates || [];
  if (tabSearch.value.trim()) {
    const q = tabSearch.value.trim().toLowerCase();
    result = result.filter(t => (t.name && t.name.toLowerCase().includes(q)) || (t.description && t.description.toLowerCase().includes(q)));
  }
  if (templateFilterStatus.value === 'default') {
    result = result.filter(t => t.is_default);
  } else if (templateFilterStatus.value === 'custom') {
    result = result.filter(t => !t.is_default);
  }
  return result;
});

const filteredHolidays = computed(() => {
  let result = props.officialHolidays || [];
  if (tabSearch.value.trim()) {
    const q = tabSearch.value.trim().toLowerCase();
    result = result.filter(h => (h.name && h.name.toLowerCase().includes(q)) || (h.notes && h.notes.toLowerCase().includes(q)));
  }
  return result;
});

const filteredLeaves = computed(() => {
  let result = props.consultantLeaves || [];

  // Search Filter
  if (tabSearch.value.trim()) {
    const q = tabSearch.value.trim().toLowerCase();
    result = result.filter(l => 
      (l.consultant?.full_name && l.consultant.full_name.toLowerCase().includes(q)) ||
      (l.consultant?.employee_number && l.consultant.employee_number.toLowerCase().includes(q)) ||
      (l.reason && l.reason.toLowerCase().includes(q)) ||
      (l.notes && l.notes.toLowerCase().includes(q))
    );
  }

  // Status Filter
  if (leaveStatusFilter.value !== 'all') {
    result = result.filter(leave => {
      const status = getLeaveStatus(leave);
      return status.key === leaveStatusFilter.value;
    });
  }

  // Date Range Overlap Filters (shows any leave overlapping even by 1 day with the selected range)
  if (leaveStartDateFilter.value && leaveEndDateFilter.value) {
    const filterFrom = leaveStartDateFilter.value;
    const filterTo = leaveEndDateFilter.value;
    result = result.filter(l => {
      const start = String(l.start_date).substring(0, 10);
      const end = String(l.end_date).substring(0, 10);
      return start <= filterTo && end >= filterFrom;
    });
  } else if (leaveStartDateFilter.value) {
    const filterFrom = leaveStartDateFilter.value;
    result = result.filter(l => {
      const end = String(l.end_date).substring(0, 10);
      return end >= filterFrom;
    });
  } else if (leaveEndDateFilter.value) {
    const filterTo = leaveEndDateFilter.value;
    result = result.filter(l => {
      const start = String(l.start_date).substring(0, 10);
      return start <= filterTo;
    });
  }

  return result;
});

// Form Errors & Submitting State
const formErrors = ref({});
const isSubmitting = ref(false);

// Template Drawer & Modal States
const isTemplateDrawerOpen = ref(false);
const isTemplateEditMode = ref(false);
const selectedTemplate = ref(null);

const isDetailModalOpen = ref(false);
const viewingTemplate = ref(null);

const isDeleteModalOpen = ref(false);
const deletingTemplate = ref(null);

// Holiday Modal States
const isHolidayModalOpen = ref(false);
const isHolidayEditMode = ref(false);
const selectedHoliday = ref(null);

// Leave Modal States
const isLeaveModalOpen = ref(false);
const isLeaveEditMode = ref(false);
const selectedLeave = ref(null);

// Generic Delete Confirmation Modal
const isGenericDeleteModalOpen = ref(false);
const genericDeleteTitle = ref('');
const genericDeleteMessage = ref('');
const deleteActionType = ref(''); // 'holiday' or 'leave'
const itemToDelete = ref(null);

function formatDate(dateStr) {
  if (!dateStr) return 'غير محدد';
  return String(dateStr).substring(0, 10);
}

function formatDateRange(startStr, endStr) {
  const start = formatDate(startStr);
  const end = formatDate(endStr);
  if (start === end || !endStr) return start;
  return `${start} ⬅️ ${end}`;
}

function getHolidayDurationDays(startStr, endStr) {
  if (!startStr) return 1;
  const start = new Date(startStr);
  const end = endStr ? new Date(endStr) : start;
  const diffTime = Math.abs(end - start);
  return Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
}

function getLeaveStatus(leave) {
  if (!leave || !leave.start_date || !leave.end_date) {
    return { type: 'neutral', label: 'غير محدد', pulse: false, key: 'unknown' };
  }
  const today = new Date().toISOString().substring(0, 10);
  const start = String(leave.start_date).substring(0, 10);
  const end = String(leave.end_date).substring(0, 10);

  if (today < start) {
    return { type: 'info', label: 'مستقبلية 📅', pulse: false, key: 'future' };
  } else if (today >= start && today <= end) {
    return { type: 'completed', label: 'جارية الآن 🟢', pulse: true, key: 'active' };
  } else {
    return { type: 'neutral', label: 'منتهية ⚪', pulse: false, key: 'expired' };
  }
}

// Template Actions
function openCreateTemplateDrawer() {
  selectedTemplate.value = null;
  isTemplateEditMode.value = false;
  formErrors.value = {};
  isTemplateDrawerOpen.value = true;
}

function openEditTemplateDrawer(template) {
  selectedTemplate.value = template;
  isTemplateEditMode.value = true;
  formErrors.value = {};
  isTemplateDrawerOpen.value = true;
}

function openTemplateDetailModal(template) {
  viewingTemplate.value = template;
  isDetailModalOpen.value = true;
}

function openDeleteTemplateModal(template) {
  deletingTemplate.value = template;
  isDeleteModalOpen.value = true;
}

function handleTemplateDrawerSubmit(formData) {
  isSubmitting.value = true;
  formErrors.value = {};

  if (isTemplateEditMode.value) {
    router.put(
      route('admin.schedules.templates.update', formData.id),
      formData,
      {
        onSuccess: () => {
          isTemplateDrawerOpen.value = false;
          toastRef.value?.addToast('success', 'تم تحديث قالب الدوام وأيام العمل بنجاح.');
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
      route('admin.schedules.templates.store'),
      formData,
      {
        onSuccess: () => {
          isTemplateDrawerOpen.value = false;
          toastRef.value?.addToast('success', 'تم إنشاء قالب الدوام وتخصيص أيام العمل بنجاح.');
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

function executeDeleteTemplate() {
  if (!deletingTemplate.value) return;
  isSubmitting.value = true;

  router.delete(
    route('admin.schedules.templates.destroy', deletingTemplate.value.id),
    {
      onSuccess: () => {
        isDeleteModalOpen.value = false;
        toastRef.value?.addToast('error', 'تم حذف قالب الدوام بنجاح.');
      },
      onError: (errs) => {
        const msg = errs.template || 'تعذر حذف القالب.';
        toastRef.value?.addToast('error', msg);
      },
      onFinish: () => {
        isSubmitting.value = false;
      },
    }
  );
}

// Holiday Actions
function openCreateHolidayModal() {
  selectedHoliday.value = null;
  isHolidayEditMode.value = false;
  formErrors.value = {};
  isHolidayModalOpen.value = true;
}

function openEditHolidayModal(holiday) {
  selectedHoliday.value = holiday;
  isHolidayEditMode.value = true;
  formErrors.value = {};
  isHolidayModalOpen.value = true;
}

function openDeleteHolidayModal(holiday) {
  itemToDelete.value = holiday;
  deleteActionType.value = 'holiday';
  genericDeleteTitle.value = 'تأكيد حذف العطلة الرسمية';
  genericDeleteMessage.value = `هل أنت تأكد من رغبتك في حذف العطلة الرسمية (${holiday.name})؟`;
  isGenericDeleteModalOpen.value = true;
}

function handleHolidaySubmit(formData) {
  isSubmitting.value = true;
  formErrors.value = {};

  if (isHolidayEditMode.value) {
    router.put(
      route('admin.schedules.holidays.update', formData.id),
      formData,
      {
        onSuccess: () => {
          isHolidayModalOpen.value = false;
          toastRef.value?.addToast('success', 'تم تحديث بيانات العطلة الرسمية بنجاح.');
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
      route('admin.schedules.holidays.store'),
      formData,
      {
        onSuccess: () => {
          isHolidayModalOpen.value = false;
          toastRef.value?.addToast('success', 'تم تسجيل العطلة الرسمية بنجاح.');
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

// Leave Actions
function openCreateLeaveModal() {
  selectedLeave.value = null;
  isLeaveEditMode.value = false;
  formErrors.value = {};
  isLeaveModalOpen.value = true;
}

function openEditLeaveModal(leave) {
  selectedLeave.value = leave;
  isLeaveEditMode.value = true;
  formErrors.value = {};
  isLeaveModalOpen.value = true;
}

function openDeleteLeaveModal(leave) {
  itemToDelete.value = leave;
  deleteActionType.value = 'leave';
  genericDeleteTitle.value = 'تأكيد حذف إجازة الاستشاري';
  genericDeleteMessage.value = `هل أنت تأكد من رغبتك في حذف إجازة الاستشاري (${leave.consultant?.full_name || 'غير محدد'})؟`;
  isGenericDeleteModalOpen.value = true;
}

function handleLeaveSubmit(formData) {
  isSubmitting.value = true;
  formErrors.value = {};

  if (isLeaveEditMode.value) {
    router.put(
      route('admin.schedules.leaves.update', formData.id),
      formData,
      {
        onSuccess: () => {
          isLeaveModalOpen.value = false;
          toastRef.value?.addToast('success', 'تم تحديث بيانات الإجازة بنجاح.');
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
      route('admin.schedules.leaves.store'),
      formData,
      {
        onSuccess: () => {
          isLeaveModalOpen.value = false;
          toastRef.value?.addToast('success', 'تم تسجيل إجازة الاستشاري وتحديث حالته التشغيلية إلى "في إجازة".');
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

function executeGenericDelete() {
  if (!itemToDelete.value) return;
  isSubmitting.value = true;

  if (deleteActionType.value === 'holiday') {
    router.delete(
      route('admin.schedules.holidays.destroy', itemToDelete.value.id),
      {
        onSuccess: () => {
          isGenericDeleteModalOpen.value = false;
          toastRef.value?.addToast('error', 'تم حذف العطلة الرسمية بنجاح.');
        },
        onFinish: () => {
          isSubmitting.value = false;
        },
      }
    );
  } else if (deleteActionType.value === 'leave') {
    router.delete(
      route('admin.schedules.leaves.destroy', itemToDelete.value.id),
      {
        onSuccess: () => {
          isGenericDeleteModalOpen.value = false;
          toastRef.value?.addToast('error', 'تم حذف سجل الإجازة بنجاح.');
        },
        onFinish: () => {
          isSubmitting.value = false;
        },
      }
    );
  }
}
</script>
