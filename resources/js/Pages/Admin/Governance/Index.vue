<template>
  <HRLayout>
    <Head title="حوكمة النظام والصلاحيات وسجلات التدقيق" />

    <div class="space-y-6 dir-rtl">
      <!-- Header Area -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white/80 dark:bg-[#090d16]/80 backdrop-blur-xl p-6 rounded-3xl border border-black/10 dark:border-white/10 shadow-xs">
        <div class="space-y-1">
          <div class="flex items-center gap-2">
            <span class="px-3 py-1 rounded-full text-xs font-black bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20">
              🛡️ الموديول القيادي
            </span>
            <span class="text-xs font-bold text-slate-400 dark:text-white/50">Phase 07 — Governance & Audit</span>
          </div>
          <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
            حوكمة النظام والأدوار وسجلات الأمان
          </h1>
          <p class="text-xs font-bold text-slate-500 dark:text-white/60">
            إدارة الأدوار، مصفوفات الصلاحيات المتبوبة، مفاتيح التشغيل والإعدادات، وتتبع سجلات التدقيق الأمني بحرفية عالية.
          </p>
        </div>

        <div class="flex items-center gap-3">
          <SpatialButton
            variant="primary"
            class="shadow-lg shadow-primary/20"
            @click="openCreateRoleDrawer"
          >
            <span class="text-base">➕</span>
            <span>إنشاء دور صلاحيات جديد</span>
          </SpatialButton>
        </div>
      </div>

      <!-- Tab Navigation Bar -->
      <div class="flex items-center gap-2 p-1.5 rounded-2xl bg-slate-200/60 dark:bg-white/5 border border-black/5 dark:border-white/10 max-w-fit">
        <button
          @click="activeTab = 'roles'"
          :class="[
            'px-5 py-2.5 rounded-xl text-xs font-black transition-all cursor-pointer flex items-center gap-2',
            activeTab === 'roles'
              ? 'bg-primary text-white shadow-md'
              : 'text-slate-600 dark:text-white/70 hover:bg-black/5 dark:hover:bg-white/5'
          ]"
        >
          <span>🛡️ الأدوار والصلاحيات</span>
          <span class="px-2 py-0.5 rounded-full text-[10px] bg-white/20 font-mono">{{ roles.length }}</span>
        </button>

        <button
          @click="activeTab = 'settings'"
          :class="[
            'px-5 py-2.5 rounded-xl text-xs font-black transition-all cursor-pointer flex items-center gap-2',
            activeTab === 'settings'
              ? 'bg-primary text-white shadow-md'
              : 'text-slate-600 dark:text-white/70 hover:bg-black/5 dark:hover:bg-white/5'
          ]"
        >
          <span>⚙️ إعدادات النظام و Feature Flags</span>
          <span class="px-2 py-0.5 rounded-full text-[10px] bg-white/20 font-mono">{{ settings.length }}</span>
        </button>

        <button
          @click="activeTab = 'audit_logs'"
          :class="[
            'px-5 py-2.5 rounded-xl text-xs font-black transition-all cursor-pointer flex items-center gap-2',
            activeTab === 'audit_logs'
              ? 'bg-primary text-white shadow-md'
              : 'text-slate-600 dark:text-white/70 hover:bg-black/5 dark:hover:bg-white/5'
          ]"
        >
          <span>📜 سجلات التدقيق والحركات</span>
          <span class="px-2 py-0.5 rounded-full text-[10px] bg-white/20 font-mono">{{ activityLogs.length }}</span>
        </button>
      </div>

      <!-- TAB 1: ROLES & PERMISSIONS -->
      <div v-if="activeTab === 'roles'" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <SpatialCard
            v-for="role in roles"
            :key="role.id"
            class="space-y-4 relative group hover:border-primary/50 transition-all"
          >
            <!-- Card Header -->
            <div class="flex items-start justify-between">
              <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-primary/10 border border-primary/20 flex items-center justify-center text-primary text-xl font-black shrink-0">
                  🛡️
                </div>
                <div>
                  <h3 class="font-black text-base text-slate-900 dark:text-white flex items-center gap-2">
                    <span>{{ role.name }}</span>
                    <span v-if="role.name.toLowerCase().includes('super admin') || role.name.includes('قيادي')" class="text-[10px] px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-500 font-mono font-black border border-amber-500/30">
                      محمي
                    </span>
                  </h3>
                  <span class="text-xs font-bold text-slate-400 dark:text-white/50 block mt-0.5">
                    {{ role.users_count }} مستخدم مسند حالياً
                  </span>
                </div>
              </div>

              <SpatialStatusPill
                :variant="role.permissions_count > 0 ? 'success' : 'neutral'"
              >
                {{ role.permissions_count }} صلاحيات
              </SpatialStatusPill>
            </div>

            <!-- Permissions Preview Chips -->
            <div class="space-y-1.5 pt-2 border-t border-black/5 dark:border-white/10">
              <span class="text-[11px] font-bold text-slate-500 dark:text-white/60 block">عينة الصلاحيات الممنوحة:</span>
              <div class="flex flex-wrap gap-1.5 max-h-24 overflow-y-auto custom-scroll pr-1">
                <template v-if="role.permissions && role.permissions.length > 0">
                  <span
                    v-for="perm in role.permissions.slice(0, 8)"
                    :key="perm"
                    class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-white/10 text-slate-700 dark:text-white/80 text-[10px] font-bold border border-black/5 dark:border-white/10"
                  >
                    {{ perm }}
                  </span>
                  <span v-if="role.permissions.length > 8" class="px-2 py-1 rounded-lg bg-primary/10 text-primary text-[10px] font-black">
                    +{{ role.permissions.length - 8 }} المزيد
                  </span>
                </template>
                <span v-else class="text-xs font-bold text-slate-400 dark:text-white/40 italic">لا توجد صلاحيات مسندة</span>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-black/5 dark:border-white/10">
              <button
                @click="openEditRoleDrawer(role)"
                class="px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-white/10 hover:bg-slate-200 dark:hover:bg-white/20 text-slate-800 dark:text-white font-bold text-xs flex items-center gap-1.5 transition-all cursor-pointer border border-black/5 dark:border-white/10"
              >
                <span>تعديل الصلاحيات</span>
                <span>✏️</span>
              </button>

              <button
                v-if="!role.name.toLowerCase().includes('super admin') && !role.name.includes('قيادي')"
                @click="confirmDeleteRole(role)"
                class="px-3 py-2 rounded-xl bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white font-bold text-xs flex items-center gap-1 transition-all cursor-pointer border border-red-500/20"
              >
                <span>حذف</span>
                <span>🗑️</span>
              </button>
            </div>
          </SpatialCard>
        </div>
      </div>

      <!-- TAB 2: SYSTEM SETTINGS & FEATURE FLAGS -->
      <div v-else-if="activeTab === 'settings'" class="space-y-6">
        <SpatialCard class="space-y-6">
          <div class="flex items-center justify-between border-b border-black/5 dark:border-white/10 pb-4">
            <div>
              <h3 class="text-lg font-black text-slate-900 dark:text-white">إعدادات النظام ومفاتيح التشغيل</h3>
              <p class="text-xs font-bold text-slate-500 dark:text-white/60">تعديل قيم المعايير التشغيلية والخصائص الديناميكية لموديولات النظام.</p>
            </div>
            <SpatialButton variant="primary" @click="saveSettings">
              <span>حفظ التغييرات</span>
              <span>💾</span>
            </SpatialButton>
          </div>

          <div class="space-y-4">
            <div
              v-for="(st, idx) in editableSettings"
              :key="st.setting_key"
              class="p-4 rounded-2xl bg-slate-50 dark:bg-white/5 border border-black/5 dark:border-white/10 flex flex-col md:flex-row md:items-center justify-between gap-4"
            >
              <div class="space-y-1 min-w-[240px]">
                <span class="text-[10px] font-mono font-black uppercase text-primary bg-primary/10 px-2 py-0.5 rounded border border-primary/20">
                  {{ st.group || 'general' }}
                </span>
                <h4 class="font-black text-sm text-slate-900 dark:text-white font-mono">{{ st.setting_key }}</h4>
                <p class="text-xs font-bold text-slate-500 dark:text-white/60">{{ st.description || 'لا يوجد وصف' }}</p>
              </div>

              <div class="flex-1 max-w-md">
                <SpatialInput
                  v-model="editableSettings[idx].setting_value"
                  placeholder="أدخل قيمة الإعداد..."
                />
              </div>
            </div>
          </div>
        </SpatialCard>
      </div>

      <!-- TAB 3: AUDIT LOGS TABLE -->
      <div v-else-if="activeTab === 'audit_logs'" class="space-y-6">
        <SpatialCard class="space-y-4 p-0 overflow-hidden">
          <div class="p-6 pb-2 border-b border-black/5 dark:border-white/10 flex items-center justify-between">
            <div>
              <h3 class="text-lg font-black text-slate-900 dark:text-white">سجلات التدقيق الأمني والتغييرات</h3>
              <p class="text-xs font-bold text-slate-500 dark:text-white/60">سجل زمني حي لكافة الإجراءات التحويرية المنفذة في النظام مع تتبع الـ IP والبيانات.</p>
            </div>
            <span class="text-xs font-mono font-black text-primary bg-primary/10 px-3 py-1 rounded-full border border-primary/20">
              أحدث {{ activityLogs.length }} حركة
            </span>
          </div>

          <div class="overflow-x-auto custom-scroll">
            <table class="w-full text-right text-xs">
              <thead>
                <tr class="bg-slate-100 dark:bg-white/5 border-b border-black/10 dark:border-white/10 text-slate-700 dark:text-white/80 font-black">
                  <th class="p-4">#</th>
                  <th class="p-4">المستخدم المنفذ</th>
                  <th class="p-4">الإجراء</th>
                  <th class="p-4">الكيان المستهدف</th>
                  <th class="p-4">عنوان IP</th>
                  <th class="p-4">التاريخ والوقت</th>
                  <th class="p-4 text-center">التفاصيل</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-black/5 dark:divide-white/5">
                <tr
                  v-for="(log, index) in activityLogs"
                  :key="log.id"
                  class="hover:bg-slate-50 dark:hover:bg-white/5 transition-all"
                >
                  <td class="p-4 font-mono font-bold text-slate-400">#{{ String(index + 1).padStart(2, '0') }}</td>
                  <td class="p-4">
                    <div class="font-black text-slate-900 dark:text-white">{{ log.user_name }}</div>
                    <div class="text-[10px] text-slate-400 font-mono">{{ log.user_email || 'نظام' }}</div>
                  </td>
                  <td class="p-4">
                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase bg-primary/10 text-primary border border-primary/20">
                      {{ log.action }}
                    </span>
                  </td>
                  <td class="p-4 font-bold text-slate-700 dark:text-white/80">
                    {{ log.entity_type }} <span v-if="log.entity_id" class="font-mono text-slate-400">({{ log.entity_id }})</span>
                  </td>
                  <td class="p-4 font-mono text-slate-500 dark:text-white/60 dir-ltr text-right">{{ log.ip_address || '—' }}</td>
                  <td class="p-4 font-mono text-slate-500 dark:text-white/60">{{ log.created_at }}</td>
                  <td class="p-4 text-center">
                    <button
                      @click="viewAuditLogDetails(log)"
                      class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-white/10 hover:bg-primary hover:text-white text-slate-800 dark:text-white font-bold text-xs transition-all cursor-pointer border border-black/5 dark:border-white/10 flex items-center gap-1 mx-auto"
                    >
                      <span>استكشاف الحركة</span>
                      <span>👁️</span>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </SpatialCard>
      </div>

      <!-- DRAWER: CREATE / EDIT ROLE -->
      <SpatialDrawer
        v-model:is-open="isRoleDrawerOpen"
        :title="editingRole ? 'تعديل صلاحيات الدور: ' + editingRole.name : 'إنشاء دور صلاحيات جديد'"
        width="w-[600px]"
      >
        <form @submit.prevent="saveRole" class="space-y-6 dir-rtl py-2">
          <!-- Role Name -->
          <SpatialInput
            v-model="roleForm.name"
            label="اسم الدور باللغة العربية"
            placeholder="مثال: مشرف ميداني أخصائي"
            required
            :disabled="editingRole && (editingRole.name.toLowerCase().includes('super admin') || editingRole.name.includes('قيادي'))"
          />

          <!-- Permissions Categorized Matrix -->
          <div class="space-y-4 pt-2">
            <div class="flex items-center justify-between border-b border-black/10 dark:border-white/10 pb-2">
              <h4 class="font-black text-sm text-slate-900 dark:text-white">مصفوفة الصلاحيات المتبوبة</h4>
              <button
                type="button"
                @click="toggleAllPermissions"
                class="text-xs font-bold text-primary hover:underline cursor-pointer"
              >
                {{ isAllSelected ? 'إلغاء تحديد الكل' : 'تحديد الكل' }}
              </button>
            </div>

            <div class="space-y-5 max-h-[50vh] overflow-y-auto custom-scroll pr-1">
              <div
                v-for="group in permissionsGrouped"
                :key="group.group"
                class="p-4 rounded-2xl bg-slate-50 dark:bg-white/5 border border-black/5 dark:border-white/10 space-y-3"
              >
                <h5 class="font-black text-xs tracking-wider text-primary border-b border-black/5 dark:border-white/10 pb-1.5 flex items-center justify-between">
                  <span>{{ formatGroupTitle(group.group) }}</span>
                  <span class="text-[10px] font-mono font-bold text-slate-400">({{ group.items.length }} صلاحية)</span>
                </h5>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                  <label
                    v-for="item in group.items"
                    :key="item.id"
                    class="flex items-center gap-2.5 p-2 rounded-xl bg-white dark:bg-white/5 border border-black/5 dark:border-white/5 cursor-pointer hover:border-primary/40 transition-all"
                  >
                    <SpatialCheckbox
                      :model-value="roleForm.permissions.includes(item.name)"
                      @update:model-value="togglePermission(item.name)"
                    />
                    <span class="text-xs font-bold text-slate-800 dark:text-white/90 truncate">{{ item.name }}</span>
                  </label>
                </div>
              </div>
            </div>
          </div>

          <!-- Drawer Action Footer -->
          <div class="flex items-center justify-end gap-3 pt-4 border-t border-black/10 dark:border-white/10">
            <SpatialButton variant="secondary" type="button" @click="isRoleDrawerOpen = false">
              إلغاء
            </SpatialButton>
            <SpatialButton variant="primary" type="submit" :loading="roleForm.processing">
              <span>{{ editingRole ? 'تحديث الدور والصلاحيات' : 'حفظ وبناء الدور' }}</span>
              <span>💾</span>
            </SpatialButton>
          </div>
        </form>
      </SpatialDrawer>

      <!-- MODAL: AUDIT LOG DETAIL VIEW -->
      <SpatialModal
        v-model:is-open="isAuditModalOpen"
        title="تفاصيل حركة التدقيق والمقارنة البرمجية"
        max-width="2xl"
      >
        <div v-if="selectedAuditLog" class="space-y-5 dir-rtl py-2">
          <!-- Metadata grid -->
          <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 p-4 rounded-2xl bg-slate-100 dark:bg-white/5 border border-black/5 dark:border-white/10 text-xs">
            <div>
              <span class="text-slate-400 font-bold block text-[10px]">المستخدم:</span>
              <span class="font-black text-slate-900 dark:text-white">{{ selectedAuditLog.user_name }}</span>
            </div>
            <div>
              <span class="text-slate-400 font-bold block text-[10px]">نوع الإجراء:</span>
              <span class="font-mono font-black text-primary">{{ selectedAuditLog.action }}</span>
            </div>
            <div>
              <span class="text-slate-400 font-bold block text-[10px]">عنوان IP:</span>
              <span class="font-mono text-slate-700 dark:text-white/80 dir-ltr">{{ selectedAuditLog.ip_address || 'غير مسجل' }}</span>
            </div>
          </div>

          <!-- Description if exists -->
          <div v-if="selectedAuditLog.description" class="p-3 rounded-xl bg-primary/10 border border-primary/20 text-xs font-bold text-primary">
            ℹ️ {{ selectedAuditLog.description }}
          </div>

          <!-- JSON Diff Comparison -->
          <div class="space-y-3">
            <h4 class="font-black text-xs text-slate-900 dark:text-white">مقارنة التغييرات البرمجية (JSON Diff):</h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Old values -->
              <div class="space-y-1">
                <span class="text-[11px] font-black text-red-500 flex items-center gap-1">
                  <span>❌ القيم القديمة (Old Values)</span>
                </span>
                <pre class="p-3 rounded-xl bg-red-500/10 border border-red-500/20 text-[11px] font-mono text-slate-900 dark:text-white overflow-x-auto max-h-56 custom-scroll dir-ltr">{{ selectedAuditLog.old_values ? JSON.stringify(selectedAuditLog.old_values, null, 2) : '// لا تتوفر قيم قديمة' }}</pre>
              </div>

              <!-- New values -->
              <div class="space-y-1">
                <span class="text-[11px] font-black text-emerald-500 flex items-center gap-1">
                  <span>✅ القيم الجديدة (New Values)</span>
                </span>
                <pre class="p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-[11px] font-mono text-slate-900 dark:text-white overflow-x-auto max-h-56 custom-scroll dir-ltr">{{ selectedAuditLog.new_values ? JSON.stringify(selectedAuditLog.new_values, null, 2) : '// لا تتوفر قيم جديدة' }}</pre>
              </div>
            </div>
          </div>
        </div>

        <template #footer>
          <SpatialButton variant="secondary" @click="isAuditModalOpen = false">
            إغلاق النافذة
          </SpatialButton>
        </template>
      </SpatialModal>

      <!-- MODAL: DELETE CONFIRMATION -->
      <SpatialModal
        v-model:is-open="isDeleteModalOpen"
        title="تأكيد حذف دور الصلاحيات"
        max-width="sm"
      >
        <div v-if="deletingRole" class="space-y-3 dir-rtl py-2">
          <div class="p-4 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-400 space-y-2">
            <h4 class="font-black text-sm">⚠️ تحذير أمني:</h4>
            <p class="text-xs font-bold leading-relaxed">
              هل أنت أخصائي متأكد من حذف الدور "<span class="font-black underline">{{ deletingRole.name }}</span>"؟ لن تتمكن من التراجع عن هذه العملية.
            </p>
          </div>

          <p v-if="deletingRole.users_count > 0" class="text-xs font-black text-amber-600 dark:text-amber-400 bg-amber-500/10 p-3 rounded-xl border border-amber-500/20">
            ⛔ محظور: يحتوي هذا الدور على {{ deletingRole.users_count }} مستخدم نشط حالياً. يلزم إعادة توجيههم أولاً قبل الحذف.
          </p>
        </div>

        <template #footer>
          <div class="flex items-center justify-end gap-2 w-full">
            <SpatialButton variant="secondary" @click="isDeleteModalOpen = false">إلغاء</SpatialButton>
            <SpatialButton
              variant="danger"
              :disabled="deletingRole && deletingRole.users_count > 0"
              @click="executeDeleteRole"
            >
              تأكيد الحذف النهائي
            </SpatialButton>
          </div>
        </template>
      </SpatialModal>
    </div>
  </HRLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import HRLayout from '@/Layouts/HRLayout.vue';
import SpatialCard from '@/Components/Spatial/SpatialCard.vue';
import SpatialButton from '@/Components/Spatial/SpatialButton.vue';
import SpatialInput from '@/Components/Spatial/SpatialInput.vue';
import SpatialCheckbox from '@/Components/Spatial/SpatialCheckbox.vue';
import SpatialModal from '@/Components/Spatial/SpatialModal.vue';
import SpatialDrawer from '@/Components/Spatial/SpatialDrawer.vue';
import SpatialStatusPill from '@/Components/Spatial/SpatialStatusPill.vue';

const props = defineProps({
  roles: {
    type: Array,
    default: () => [],
  },
  permissionsGrouped: {
    type: Array,
    default: () => [],
  },
  settings: {
    type: Array,
    default: () => [],
  },
  activityLogs: {
    type: Array,
    default: () => [],
  },
});

const activeTab = ref('roles');

const groupTitlesMap = {
  sites: '📍 موديول المواقع الميدانية',
  consultants: '👷 موديول الاستشاريين الميدانيين',
  schedules: '📅 موديول الدوام والإجازات',
  tasks: '📋 موديول منشئ المهام والتكليفات',
  visits: '⚡ موديول الزيارات والتنفيذ',
  reports: '📊 موديول التقارير والتحليلات',
  governance: '🛡️ موديول حوكمة النظام والتدقيق',
  backups: '💾 موديول النسخ الاحتياطية',
};

function formatGroupTitle(groupKey) {
  return groupTitlesMap[groupKey] || `⚙️ موديول: ${groupKey}`;
}

// Drawer & Form for Role Create/Edit
const isRoleDrawerOpen = ref(false);
const editingRole = ref(null);

const roleForm = useForm({
  name: '',
  permissions: [],
});

function openCreateRoleDrawer() {
  editingRole.value = null;
  roleForm.reset();
  roleForm.permissions = [];
  isRoleDrawerOpen.value = true;
}

function openEditRoleDrawer(role) {
  editingRole.value = role;
  roleForm.name = role.name;
  roleForm.permissions = [...(role.permissions || [])];
  isRoleDrawerOpen.value = true;
}

function togglePermission(permName) {
  const idx = roleForm.permissions.indexOf(permName);
  if (idx > -1) {
    roleForm.permissions.splice(idx, 1);
  } else {
    roleForm.permissions.push(permName);
  }
}

const allPermissionsList = computed(() => {
  const list = [];
  props.permissionsGrouped.forEach(group => {
    group.items.forEach(item => list.push(item.name));
  });
  return list;
});

const isAllSelected = computed(() => {
  return allPermissionsList.value.length > 0 && roleForm.permissions.length === allPermissionsList.value.length;
});

function toggleAllPermissions() {
  if (isAllSelected.value) {
    roleForm.permissions = [];
  } else {
    roleForm.permissions = [...allPermissionsList.value];
  }
}

function saveRole() {
  if (editingRole.value) {
    roleForm.put(route('admin.governance.roles.update', editingRole.value.id), {
      onSuccess: () => {
        isRoleDrawerOpen.value = false;
      },
    });
  } else {
    roleForm.post(route('admin.governance.roles.store'), {
      onSuccess: () => {
        isRoleDrawerOpen.value = false;
      },
    });
  }
}

// Delete Role Modal
const isDeleteModalOpen = ref(false);
const deletingRole = ref(null);

function confirmDeleteRole(role) {
  deletingRole.value = role;
  isDeleteModalOpen.value = true;
}

function executeDeleteRole() {
  if (!deletingRole.value) return;
  router.delete(route('admin.governance.roles.destroy', deletingRole.value.id), {
    onSuccess: () => {
      isDeleteModalOpen.value = false;
      deletingRole.value = null;
    },
  });
}

// Editable Settings
const editableSettings = ref(
  props.settings.map(s => ({
    setting_key: s.setting_key,
    setting_value: s.setting_value,
    group: s.group || 'general',
    description: s.description || '',
  }))
);

function saveSettings() {
  router.post(route('admin.governance.settings.update'), {
    settings: editableSettings.value,
  });
}

// Audit Log Modal Detail
const isAuditModalOpen = ref(false);
const selectedAuditLog = ref(null);

function viewAuditLogDetails(log) {
  selectedAuditLog.value = log;
  isAuditModalOpen.value = true;
}
</script>
