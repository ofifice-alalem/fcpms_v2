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
                  <th class="p-4">نوع الإجراء الإداري</th>
                  <th class="p-4">الكيان/العنصر المستهدف</th>
                  <th class="p-4">عنوان IP</th>
                  <th class="p-4">التاريخ والوقت</th>
                  <th class="p-4 text-center">التفاصيل والبيانات</th>
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
                    <div class="font-black text-slate-900 dark:text-white flex items-center gap-1.5">
                      <span>👤</span>
                      <span>{{ log.user_name }}</span>
                    </div>
                    <div class="text-[10px] text-slate-400 font-mono pr-5">{{ log.user_email || 'نظام آلي' }}</div>
                  </td>
                  <td class="p-4">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-black" :class="getActionBadgeClass(log.action)">
                      <span>{{ formatAction(log.action).icon }}</span>
                      <span>{{ formatAction(log.action).label }}</span>
                    </div>
                  </td>
                  <td class="p-4">
                    <div class="font-bold text-slate-800 dark:text-white flex items-center gap-1.5 text-xs">
                      <span>{{ formatEntity(log.entity_type).icon }}</span>
                      <span>{{ formatEntity(log.entity_type).label }}</span>
                      <span v-if="log.entity_id" class="px-2 py-0.5 rounded-lg bg-slate-200/80 dark:bg-white/10 font-mono font-black text-[11px] text-slate-700 dark:text-white/90">#{{ log.entity_id }}</span>
                    </div>
                  </td>
                  <td class="p-4 font-mono text-slate-500 dark:text-white/60 dir-ltr text-right text-[11px]">{{ log.ip_address || '—' }}</td>
                  <td class="p-4 font-mono text-slate-500 dark:text-white/60 text-[11px]">{{ log.created_at }}</td>
                  <td class="p-4 text-center">
                    <button
                      @click="viewAuditLogDetails(log)"
                      class="px-3.5 py-1.5 rounded-xl bg-slate-100 dark:bg-white/10 hover:bg-primary hover:text-white text-slate-800 dark:text-white font-bold text-xs transition-all cursor-pointer border border-black/5 dark:border-white/10 flex items-center gap-1.5 mx-auto shadow-2xs"
                    >
                      <span>تفاصيل الحركة</span>
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
        title="تفاصيل حركة التدقيق الأمني والبيانات"
        max-width="2xl"
      >
        <div v-if="selectedAuditLog" class="space-y-6 dir-rtl py-2">
          <!-- Action Summary Banner -->
          <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 dark:bg-white/5 border border-black/5 dark:border-white/10">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-primary/10 border border-primary/20 flex items-center justify-center text-xl shrink-0">
                {{ formatAction(selectedAuditLog.action).icon }}
              </div>
              <div>
                <h4 class="font-black text-sm text-slate-900 dark:text-white">
                  {{ formatAction(selectedAuditLog.action).label }}
                </h4>
                <span class="text-xs text-slate-500 dark:text-white/60 font-bold flex items-center gap-1.5 mt-0.5">
                  <span>العنصر المستهدف:</span>
                  <span class="font-black text-slate-900 dark:text-white">{{ formatEntity(selectedAuditLog.entity_type).label }}</span>
                  <span v-if="selectedAuditLog.entity_id" class="px-1.5 py-0.5 rounded bg-primary/10 font-mono text-primary text-[10px] font-black">#{{ selectedAuditLog.entity_id }}</span>
                </span>
              </div>
            </div>

            <span class="text-xs font-mono font-bold text-slate-500 dir-ltr bg-slate-200/60 dark:bg-white/10 px-3 py-1 rounded-full">
              {{ selectedAuditLog.created_at }}
            </span>
          </div>

          <!-- Metadata grid -->
          <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 p-4 rounded-2xl bg-slate-100 dark:bg-white/5 border border-black/5 dark:border-white/10 text-xs">
            <div>
              <span class="text-slate-400 font-bold block text-[10px]">المستخدم المنفذ:</span>
              <span class="font-black text-slate-900 dark:text-white flex items-center gap-1 mt-1">
                <span>👤</span>
                <span>{{ selectedAuditLog.user_name }}</span>
              </span>
            </div>
            <div>
              <span class="text-slate-400 font-bold block text-[10px]">نوع الإجراء:</span>
              <span class="font-bold text-primary flex items-center gap-1 mt-1">
                <span>{{ formatAction(selectedAuditLog.action).icon }}</span>
                <span>{{ formatAction(selectedAuditLog.action).label }}</span>
              </span>
            </div>
            <div>
              <span class="text-slate-400 font-bold block text-[10px]">عنوان IP:</span>
              <span class="font-mono text-slate-700 dark:text-white/80 dir-ltr block mt-1">{{ selectedAuditLog.ip_address || 'غير مسجل' }}</span>
            </div>
          </div>

          <!-- Description if exists -->
          <div v-if="selectedAuditLog.description" class="p-3.5 rounded-2xl bg-purple-500/10 border border-purple-500/20 text-xs font-bold text-purple-700 dark:text-purple-300 flex items-start gap-2">
            <span class="text-base shrink-0">ℹ️</span>
            <span class="leading-relaxed">{{ selectedAuditLog.description }}</span>
          </div>

          <!-- Human Readable Comparison vs JSON Diff -->
          <div class="space-y-4">
            <div class="flex items-center justify-between border-b border-black/5 dark:border-white/10 pb-2">
              <h4 class="font-black text-xs text-slate-900 dark:text-white">مقارنة البيانات والتغييرات المسجلة:</h4>
              <button
                type="button"
                @click="showRawJson = !showRawJson"
                class="text-[11px] font-black text-primary hover:underline cursor-pointer flex items-center gap-1.5"
              >
                <span>{{ showRawJson ? 'عرض جدول البيانات الواضح' : 'عرض كود JSON البرمجي' }}</span>
                <span>{{ showRawJson ? '📊' : '💻' }}</span>
              </button>
            </div>

            <!-- Unified Visual Comparison List -->
            <div v-if="!showRawJson" class="space-y-3.5 dir-rtl">
              <div
                v-for="(item, idx) in getMergedDiffList(selectedAuditLog)"
                :key="idx"
                class="rounded-2xl border p-4 transition-all"
                :class="item.isHeader ? 'bg-primary/10 border-primary/20 text-slate-900 dark:text-white' : 'bg-slate-50 dark:bg-white/5 border-black/5 dark:border-white/10'"
              >
                <!-- Site Name Header Card -->
                <template v-if="item.isHeader">
                  <div class="flex items-center gap-2.5 font-black text-sm text-primary">
                    <span class="text-lg">📍</span>
                    <span>اسم الموقع الميداني:</span>
                    <span class="text-slate-900 dark:text-white font-extrabold text-base">{{ item.value }}</span>
                  </div>
                </template>

                <!-- Unified Task or Field Comparison Card -->
                <template v-else>
                  <div class="space-y-2.5">
                    <div class="flex items-center justify-between border-b border-black/5 dark:border-white/10 pb-1.5">
                      <h5 class="font-black text-xs text-slate-900 dark:text-white flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-primary shrink-0"></span>
                        <span>{{ item.label }}</span>
                      </h5>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                      <!-- Right Side: Old Value (القيم القديمة ❌) -->
                      <div class="p-3 rounded-xl bg-red-500/10 border border-red-500/20 text-xs space-y-1">
                        <span class="text-[10px] font-black text-red-600 dark:text-red-400 flex items-center gap-1">
                          <span>❌</span>
                          <span>القيمة القديمة (السابقة):</span>
                        </span>
                        <span class="font-['Tajawal',sans-serif] font-bold text-slate-900 dark:text-white block break-words leading-relaxed whitespace-pre-line">
                          {{ formatValue(item.oldVal) }}
                        </span>
                      </div>

                      <!-- Left Side: New Value (القيم الجديدة ✅) -->
                      <div class="p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-xs space-y-1">
                        <span class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                          <span>✅</span>
                          <span>القيمة الجديدة (المحدثة):</span>
                        </span>
                        <span class="font-['Tajawal',sans-serif] font-bold text-slate-900 dark:text-white block break-words leading-relaxed whitespace-pre-line">
                          {{ formatValue(item.newVal) }}
                        </span>
                      </div>
                    </div>
                  </div>
                </template>
              </div>
            </div>

            <!-- Raw JSON View -->
            <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Old values -->
              <div class="space-y-1">
                <span class="text-[11px] font-black text-red-500 flex items-center gap-1">
                  <span>❌ القيم القديمة (Old Values)</span>
                </span>
                <pre class="p-3.5 rounded-xl bg-red-500/10 border border-red-500/20 text-[11px] font-mono text-slate-900 dark:text-white overflow-x-auto max-h-56 custom-scroll dir-ltr">{{ selectedAuditLog.old_values ? JSON.stringify(selectedAuditLog.old_values, null, 2) : '// لا تتوفر قيم قديمة' }}</pre>
              </div>

              <!-- New values -->
              <div class="space-y-1">
                <span class="text-[11px] font-black text-emerald-500 flex items-center gap-1">
                  <span>✅ القيم الجديدة (New Values)</span>
                </span>
                <pre class="p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-[11px] font-mono text-slate-900 dark:text-white overflow-x-auto max-h-56 custom-scroll dir-ltr">{{ selectedAuditLog.new_values ? JSON.stringify(selectedAuditLog.new_values, null, 2) : '// لا تتوفر قيم جديدة' }}</pre>
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
const showRawJson = ref(false);

function viewAuditLogDetails(log) {
  selectedAuditLog.value = log;
  showRawJson.value = false;
  isAuditModalOpen.value = true;
}

const actionTranslations = {
  login: { label: 'تسجيل دخول', icon: '🔑', badge: 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20' },
  logout: { label: 'تسجيل خروج', icon: '🚪', badge: 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border border-slate-500/20' },
  create_site: { label: 'إضافة موقع ميداني', icon: '📍', badge: 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' },
  create_user: { label: 'إنشاء حساب مستخدم', icon: '👤', badge: 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' },
  update_user: { label: 'تعديل حساب مستخدم', icon: '✏️', badge: 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20' },
  toggle_user_status: { label: 'تغيير حالة حساب', icon: '🔄', badge: 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20' },
  delete_user: { label: 'حذف حساب مستخدم', icon: '🗑️', badge: 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20' },
  update_site: { label: 'تعديل بيانات موقع', icon: '✏️', badge: 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20' },
  toggle_site_status: { label: 'تغيير حالة موقع', icon: '🔄', badge: 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20' },
  delete_site: { label: 'حذف موقع ميداني', icon: '🗑️', badge: 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20' },
  create_consultant: { label: 'إضافة استشاري', icon: '👷', badge: 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' },
  register_consultant: { label: 'تسجيل استشاري جديد', icon: '👷', badge: 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' },
  'register consultant': { label: 'تسجيل استشاري جديد', icon: '👷', badge: 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' },
  update_consultant: { label: 'تعديل ملف استشاري', icon: '✏️', badge: 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20' },
  toggle_consultant_status: { label: 'تغيير حالة استشاري', icon: '🔄', badge: 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20' },
  change_consultant_status: { label: 'تغيير حالة استشاري', icon: '🔄', badge: 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20' },
  delete_consultant: { label: 'حذف استشاري', icon: '🗑️', badge: 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20' },
  execute_site_visit: { label: 'تنفيذ زيارة ميدانية', icon: '⚡', badge: 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20' },
  open_site_visit: { label: 'فتح زيارة ميدانية', icon: '🚀', badge: 'bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border border-cyan-500/20' },
  start_daily_visit: { label: 'بدء دوام ميداني', icon: '🚀', badge: 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20' },
  create_backup: { label: 'إنشاء نسخة احتياطية', icon: '💾', badge: 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' },
  restore_backup: { label: 'استعادة نسخة احتياطية', icon: '🔄', badge: 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20' },
  delete_backup: { label: 'حذف نسخة احتياطية', icon: '🗑️', badge: 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20' },
  create_role: { label: 'إنشاء دور صلاحيات', icon: '🛡️', badge: 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' },
  update_role: { label: 'تعديل دور صلاحيات', icon: '✏️', badge: 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20' },
  delete_role: { label: 'حذف دور صلاحيات', icon: '🗑️', badge: 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20' },
  update_settings: { label: 'تحديث إعدادات النظام', icon: '⚙️', badge: 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20' },
  create_task: { label: 'إنشاء مهمة جديدة', icon: '📋', badge: 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' },
  update_task: { label: 'تعديل مهمة', icon: '✏️', badge: 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20' },
  toggle_task_active: { label: 'تغيير تفعيل مهمة', icon: '🔄', badge: 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20' },
  delete_task: { label: 'حذف مهمة', icon: '🗑️', badge: 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20' },
  create_schedule_template: { label: 'إنشاء قالب دوام', icon: '📅', badge: 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' },
  update_schedule_template: { label: 'تعديل قالب دوام', icon: '✏️', badge: 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20' },
  delete_schedule_template: { label: 'حذف قالب دوام', icon: '🗑️', badge: 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20' },
  add_official_holiday: { label: 'تسجيل عطلة رسمية', icon: '🌴', badge: 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' },
  update_official_holiday: { label: 'تعديل عطلة رسمية', icon: '✏️', badge: 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20' },
  delete_official_holiday: { label: 'حذف عطلة رسمية', icon: '🗑️', badge: 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20' },
  record_consultant_leave: { label: 'تسجيل إجازة استشاري', icon: '🏖️', badge: 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' },
  update_consultant_leave: { label: 'تعديل إجازة استشاري', icon: '✏️', badge: 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20' },
  delete_consultant_leave: { label: 'حذف إجازة استشاري', icon: '🗑️', badge: 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20' },
};

function formatAction(actionKey) {
  if (!actionKey) return { label: 'إجراء عام', icon: '⚡', badge: 'bg-slate-500/10 text-slate-600 border border-slate-500/20' };
  if (actionTranslations[actionKey]) return actionTranslations[actionKey];

  const formatted = actionKey.replace(/_/g, ' ');
  return { label: formatted, icon: '📌', badge: 'bg-primary/10 text-primary border border-primary/20' };
}

function getActionBadgeClass(actionKey) {
  return formatAction(actionKey).badge;
}

const entityTranslations = {
  SiteVisit: { label: 'زيارة ميدانية', icon: '⚡' },
  Site: { label: 'موقع ميداني', icon: '📍' },
  Consultant: { label: 'استشاري ميداني', icon: '👷' },
  ConsultantDailyVisit: { label: 'سجل دوام يومي', icon: '📅' },
  User: { label: 'حساب مستخدم', icon: '👤' },
  Role: { label: 'دور صلاحيات', icon: '🛡️' },
  Setting: { label: 'إعداد نظام', icon: '⚙️' },
  TaskDefinition: { label: 'تعريف مهمة', icon: '📋' },
  WorkScheduleTemplate: { label: 'قالب دوام', icon: '📅' },
  OfficialHoliday: { label: 'عطلة رسمية', icon: '🌴' },
  ConsultantLeave: { label: 'إجازة استشاري', icon: '🏖️' },
  Backup: { label: 'نسخة احتياطية', icon: '💾' },
};

function formatEntity(entityKey) {
  if (!entityKey) return { label: 'عام', icon: '⚙️' };
  if (entityTranslations[entityKey]) return entityTranslations[entityKey];
  return { label: entityKey, icon: '📦' };
}

const keyTranslations = {
  status: 'حالة الإجراء',
  in_progress: 'قيد التنفيذ',
  completed: 'مكتملة',
  pending: 'قيد الانتظار',
  site_visit_id: 'رقم الزيارة الميدانية',
  site_id: 'رقم الموقع الميداني',
  site_name: '📍 اسم الموقع الميداني',
  submitted_responses_count: 'عدد المهام المنفذة والمستلمة',
  previous_responses_count: 'عدد المهام المنفذة سابقاً',
  tasks_details: '📋 نتائج واستجابات المهام الجديدة',
  previous_tasks_details: '📋 نتائج واستجابات المهام السابقة',
  notes: 'الملاحظات الميدانية',
  responses_count: 'عدد المهام المنفذة',
  days: 'أيام الأسبوع وجدول العمل',
  is_default: 'قالب افتراضي للنظام',
  daily_hours: 'ساعات العمل اليومية',
  user_id: 'رقم المستخدم',
  user: '👤 حساب المستخدم المرتبط',
  work_schedule_template: '📅 قالب الدوام المعتمد',
  work_schedule_template_id: 'رقم قالب الدوام',
  hire_date: '📅 تاريخ التعيين',
  national_id: 'الرقم الوطني / الهوية',
  specialization: 'التخصص الفني',
  department: 'القسم / الإدارة',
  consultant_id: 'رقم الاستشاري الميداني',
  consultant: '👷 الاستشاري الميداني',
  components: '🧩 حقول عناصر المهمة',
  site_assignments: '📍 المواقع الميدانية المسندة',
  consultant_assignments: '👷 الاستشاريين المسندين',
  ip: 'عنوان IP',
  name: 'الاسم',
  name_ar: 'الاسم بالعربية',
  code: 'الرمز المرجعي',
  is_active: 'حالة التفعيل',
  employment_status: 'الحالة الوظيفية',
  phone: 'رقم الهاتف',
  email: 'البريد الإلكتروني',
  start_date: 'تاريخ البداية',
  end_date: 'تاريخ النهاية',
  permissions: 'الصلاحيات المسندة',
  password: '🔑 كلمة المرور',
  active: 'نشط',
  inactive: 'غير نشط',
  vacation: 'في إجازة',
  description: 'الوصف التفصيلي',
};

function translateKey(key) {
  return keyTranslations[key] || key;
}

const dayNamesMap = {
  0: 'الأحد',
  1: 'الاثنين',
  2: 'الثلاثاء',
  3: 'الأربعاء',
  4: 'الخميس',
  5: 'الجمعة',
  6: 'السبت',
};

function formatDaysSchedule(daysArr) {
  if (!Array.isArray(daysArr)) return null;
  const working = [];
  const off = [];
  daysArr.forEach(d => {
    const dName = dayNamesMap[d.day_of_week];
    if (dName) {
      if (d.is_working_day) {
        working.push(dName);
      } else {
        off.push(dName);
      }
    }
  });

  if (working.length === 0 && off.length === 0) return null;

  let result = `أيام العمل: ${working.join('، ')}`;
  if (off.length > 0) {
    result += ` (العطلة: ${off.join('، ')})`;
  }
  return result;
}

function formatValue(val) {
  if (val === null || val === undefined) return '—';
  if (typeof val === 'boolean') return val ? 'نعم (مفعل)' : 'لا (معطل)';

  let parsed = val;
  if (typeof val === 'string' && (val.startsWith('[') || val.startsWith('{'))) {
    try {
      parsed = JSON.parse(val);
    } catch (e) {
      parsed = val;
    }
  }

  if (Array.isArray(parsed) && parsed.length > 0 && parsed[0] && parsed[0].day_of_week !== undefined) {
    const formattedDays = formatDaysSchedule(parsed);
    if (formattedDays) return formattedDays;
  }

  // Format arrays of objects (e.g. task components, site assignments)
  if (Array.isArray(parsed)) {
    if (parsed.length === 0) return 'لا يوجد (فارغ)';
    if (parsed[0] && parsed[0].label) {
      return parsed.map(item => `• ${item.label}`).join('\n');
    }
    if (parsed[0] && typeof parsed[0] === 'object') {
      const names = parsed.map(item => {
        if (item.site && item.site.name) return `• ${item.site.name}`;
        if (item.consultant && item.consultant.full_name) return `• ${item.consultant.full_name}`;
        const nameVal = item.name || item.full_name || item.name_ar || item.title;
        if (nameVal) return `• ${nameVal}`;
        if (item.site_id) return `• موقع رقم #${item.site_id}`;
        return null;
      }).filter(Boolean);
      if (names.length > 0) return names.join('\n');
    }
  }

  // Format model objects cleanly
  if (typeof parsed === 'object' && parsed !== null) {
    if (parsed.full_name) return parsed.full_name;
    if (parsed.name) {
      return parsed.email ? `${parsed.name} (${parsed.email})` : parsed.name;
    }
    if (parsed.title) return parsed.title;
    return JSON.stringify(parsed);
  }

  // Format ISO timestamp strings cleanly (e.g. 2026-08-07T22:00:00.000000Z -> 2026-08-07)
  if (typeof parsed === 'string' && /^\d{4}-\d{2}-\d{2}T/.test(parsed)) {
    return parsed.split('T')[0];
  }

  if (keyTranslations[val]) return keyTranslations[val];
  return String(val);
}

function getMergedDiffList(log) {
  if (!log) return [];

  const oldVals = log.old_values || {};
  const newVals = log.new_values || {};

  const siteName = newVals.site_name || oldVals.site_name || null;
  const ignoredKeys = [
    'updated_at',
    'created_at',
    'deleted_at',
    'id',
    'template_id',
    'created_by',
    'site_name',
    'site_id',
    'site_visit_id',
    'responses_count',
    'previous_responses_count',
    'submitted_responses_count',
    'tasks_details',
    'previous_tasks_details',
    'user_id',
    'work_schedule_template_id',
    'consultant_id',
  ];

  const allKeys = Array.from(new Set([...Object.keys(oldVals), ...Object.keys(newVals)]));
  const regularKeys = allKeys.filter(k => !ignoredKeys.includes(k));

  const diffItems = [];

  if (siteName) {
    diffItems.push({
      isHeader: true,
      title: 'اسم الموقع الميداني',
      value: siteName,
    });
  }

  const oldTasksStr = oldVals.previous_tasks_details || oldVals.tasks_details || '';
  const newTasksStr = newVals.tasks_details || '';

  if (oldTasksStr || newTasksStr) {
    const parseTaskString = (str) => {
      if (!str || typeof str !== 'string') return {};
      const map = {};
      str.split(' | ').forEach(item => {
        const parts = item.split(': ');
        if (parts.length >= 2 && parts[0].trim() !== '') {
          const tName = parts[0].trim();
          const tVal = parts.slice(1).join(': ').trim();
          map[tName] = tVal;
        } else if (item.trim() !== '') {
          map['استجابة مهمة ميدانية'] = item.trim();
        }
      });
      return map;
    };

    const oldTaskMap = parseTaskString(oldTasksStr);
    const newTaskMap = parseTaskString(newTasksStr);
    const allTaskNames = Array.from(new Set([...Object.keys(oldTaskMap), ...Object.keys(newTaskMap)]));

    allTaskNames.forEach(tName => {
      const oldV = oldTaskMap[tName] || '—';
      const newV = newTaskMap[tName] || '—';

      // Skip task if old and new values are identical (focus only on changes)
      if (oldV !== '—' && newV !== '—' && oldV === newV) {
        return;
      }

      const cleanTaskName = tName.replace(/^📋\s*/, '');
      diffItems.push({
        isTask: true,
        label: `📋 المهمة: ${cleanTaskName}`,
        oldVal: oldV,
        newVal: newV,
      });
    });
  }

  // Handle 'days' schedule array diff specifically if present
  if (oldVals.days || newVals.days) {
    const parseDaysArr = (val) => {
      if (!val) return null;
      let p = val;
      if (typeof val === 'string' && (val.startsWith('[') || val.startsWith('{'))) {
        try { p = JSON.parse(val); } catch(e) {}
      }
      return (Array.isArray(p) && p.length > 0 && p[0] && p[0].day_of_week !== undefined) ? p : null;
    };

    const oldDays = parseDaysArr(oldVals.days);
    const newDays = parseDaysArr(newVals.days);

    if (oldDays && newDays) {
      const oldMap = {};
      oldDays.forEach(d => { oldMap[d.day_of_week] = d.is_working_day; });
      const newMap = {};
      newDays.forEach(d => { newMap[d.day_of_week] = d.is_working_day; });

      let anyDayChanged = false;
      for (let dayNum = 0; dayNum <= 6; dayNum++) {
        const oldW = oldMap[dayNum];
        const newW = newMap[dayNum];
        if (oldW !== undefined && newW !== undefined && oldW !== newW) {
          anyDayChanged = true;
          const dayName = dayNamesMap[dayNum] || `يوم #${dayNum}`;
          diffItems.push({
            isTask: false,
            label: `📅 حالة يوم (${dayName})`,
            oldVal: oldW ? 'يوم عمل رسمي' : 'عطلة أسبوعية',
            newVal: newW ? 'يوم عمل رسمي' : 'عطلة أسبوعية',
          });
        }
      }

      // If we handled day-by-day comparison, prevent the raw 'days' key from showing as a lump text
      if (anyDayChanged || (oldDays && newDays)) {
        ignoredKeys.push('days');
      }
    }
  }

  regularKeys.forEach(k => {
    if (ignoredKeys.includes(k)) return;
    const oldV = oldVals[k];
    const newV = newVals[k];

    const formattedOld = formatValue(oldV);
    const formattedNew = formatValue(newV);

    // Skip if formatted values are identical (no actual human-visible change occurred)
    if (formattedOld === formattedNew) {
      return;
    }

    diffItems.push({
      key: k,
      label: translateKey(k),
      oldVal: formattedOld,
      newVal: formattedNew,
    });
  });

  return diffItems;
}
</script>
