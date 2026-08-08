<template>
  <HRLayout>
    <Head title="إدارة مستخدمي وحسابات النظام" />

    <div class="space-y-8 dir-rtl font-['Tajawal',sans-serif]">
      <!-- Header Banner -->
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white/70 dark:bg-white/5 backdrop-blur-xl p-6 rounded-3xl border border-black/5 dark:border-white/10 shadow-sm">
        <div class="space-y-1">
          <div class="flex items-center gap-2">
            <span class="text-2xl">👤</span>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">إدارة مستخدمي وحسابات النظام</h1>
          </div>
          <p class="text-xs font-bold text-slate-500 dark:text-white/60">
            إنشاء وإدارة حسابات المستخدمين (مدراء النظام، أخصائيي الموارد البشرية، والاستشاريين) وتعيين الصلاحيات.
          </p>
        </div>

        <SpatialButton variant="primary" @click="openCreateUserDrawer" class="shadow-lg shadow-primary/25 shrink-0">
          <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
          </svg>
          <span>إنشاء حساب مستخدم جديد</span>
        </SpatialButton>
      </div>

      <!-- Stats Metric Cards Grid -->
      <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <SpatialCard class="p-4 flex flex-col justify-between">
          <div class="flex items-center justify-between text-slate-500 dark:text-white/60">
            <span class="text-xs font-black">إجمالي الحسابات</span>
            <span class="text-lg">👥</span>
          </div>
          <div class="mt-3">
            <span class="text-2xl font-black text-slate-900 dark:text-white">{{ stats.total }}</span>
            <span class="text-[10px] font-bold text-slate-400 block mt-0.5">مستخدم مسجل في النظام</span>
          </div>
        </SpatialCard>

        <SpatialCard class="p-4 flex flex-col justify-between border-r-4 border-r-purple-500">
          <div class="flex items-center justify-between text-purple-600 dark:text-purple-400">
            <span class="text-xs font-black">مدراء النظام</span>
            <span class="text-lg">🛡️</span>
          </div>
          <div class="mt-3">
            <span class="text-2xl font-black text-purple-600 dark:text-purple-400">{{ stats.admins }}</span>
            <span class="text-[10px] font-bold text-slate-400 block mt-0.5">صلاحيات كاملة</span>
          </div>
        </SpatialCard>

        <SpatialCard class="p-4 flex flex-col justify-between border-r-4 border-r-indigo-500">
          <div class="flex items-center justify-between text-indigo-600 dark:text-indigo-400">
            <span class="text-xs font-black">الموارد البشرية</span>
            <span class="text-lg">👤</span>
          </div>
          <div class="mt-3">
            <span class="text-2xl font-black text-indigo-600 dark:text-indigo-400">{{ stats.hr_staff }}</span>
            <span class="text-[10px] font-bold text-slate-400 block mt-0.5">إدارة وإشراف</span>
          </div>
        </SpatialCard>

        <SpatialCard class="p-4 flex flex-col justify-between border-r-4 border-r-emerald-500">
          <div class="flex items-center justify-between text-emerald-600 dark:text-emerald-400">
            <span class="text-xs font-black">استشاريون ميدانيون</span>
            <span class="text-lg">👷</span>
          </div>
          <div class="mt-3">
            <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ stats.consultants }}</span>
            <span class="text-[10px] font-bold text-slate-400 block mt-0.5">تطبيق الميدان</span>
          </div>
        </SpatialCard>

        <SpatialCard class="p-4 flex flex-col justify-between border-r-4 border-r-blue-500 col-span-2 md:col-span-1">
          <div class="flex items-center justify-between text-blue-600 dark:text-blue-400">
            <span class="text-xs font-black">الحسابات النشطة</span>
            <span class="text-lg">✅</span>
          </div>
          <div class="mt-3">
            <span class="text-2xl font-black text-blue-600 dark:text-blue-400">{{ stats.active }}</span>
            <span class="text-[10px] font-bold text-slate-400 block mt-0.5">مفعل ومسموح لدخوله</span>
          </div>
        </SpatialCard>
      </div>

      <!-- Filters & Search Bar -->
      <SpatialCard class="p-4 space-y-4">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
          <!-- Search Input -->
          <div class="w-full md:w-80">
            <SpatialInput
              v-model="filterSearch"
              placeholder="ابحث بالاسم، البريد أو اسم المستخدم..."
              type="search"
            />
          </div>

          <!-- Role & Status Select Filters -->
          <div class="flex items-center gap-3 w-full md:w-auto">
            <select
              v-model="filterRole"
              class="w-full md:w-48 h-12 rounded-xl bg-slate-100 dark:bg-white/10 border border-black/10 dark:border-white/15 px-3 text-xs font-bold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/50"
            >
              <option value="all">كل الأدوار الصلاحية</option>
              <option value="admin">مدير نظام الفائق (Admin)</option>
              <option value="hr">مدير موارد بشرية (HR)</option>
              <option value="consultant">استشاري ميداني (Consultant)</option>
            </select>

            <select
              v-model="filterStatus"
              class="w-full md:w-40 h-12 rounded-xl bg-slate-100 dark:bg-white/10 border border-black/10 dark:border-white/15 px-3 text-xs font-bold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/50"
            >
              <option value="all">كل حالات الحسابات</option>
              <option value="active">نشط فقط</option>
              <option value="inactive">معطل فقط</option>
            </select>
          </div>
        </div>
      </SpatialCard>

      <!-- Users Table Card -->
      <SpatialCard class="overflow-hidden">
        <div class="p-4 border-b border-black/5 dark:border-white/10 flex items-center justify-between">
          <h3 class="font-black text-sm text-slate-900 dark:text-white flex items-center gap-2">
            <span>📋 قائمة حسابات المستخدمين</span>
            <span class="px-2 py-0.5 text-xs rounded-lg bg-primary/10 text-primary font-bold">{{ filteredUsers.length }} حساب</span>
          </h3>
        </div>

        <div class="overflow-x-auto custom-scroll">
          <table class="w-full text-right border-collapse">
            <thead>
              <tr class="bg-black/5 dark:bg-white/5 text-[11px] font-black text-slate-500 dark:text-white/60 uppercase tracking-wider border-b border-black/5 dark:border-white/10">
                <th class="py-3.5 px-4">المستخدم</th>
                <th class="py-3.5 px-4">اسم المستخدم (Username)</th>
                <th class="py-3.5 px-4">دور الصلاحيات</th>
                <th class="py-3.5 px-4">الحالة</th>
                <th class="py-3.5 px-4">تاريخ التسجيل</th>
                <th class="py-3.5 px-4 text-center">الإجراءات</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-black/5 dark:divide-white/10 text-xs font-bold">
              <tr
                v-for="user in filteredUsers"
                :key="user.id"
                class="hover:bg-black/5 dark:hover:bg-white/5 transition-colors"
              >
                <td class="py-3.5 px-4">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-primary/20 text-primary border border-primary/30 flex items-center justify-center font-black text-sm shrink-0">
                      {{ user.name.charAt(0) }}
                    </div>
                    <div class="space-y-0.5">
                      <span class="font-black text-slate-900 dark:text-white block">{{ user.name }}</span>
                      <span class="text-[11px] font-medium text-slate-500 dark:text-white/60 block">{{ user.email }}</span>
                    </div>
                  </div>
                </td>

                <td class="py-3.5 px-4">
                  <span class="font-mono text-xs text-slate-700 dark:text-slate-300 dir-ltr inline-block bg-black/5 dark:bg-white/10 px-2 py-0.5 rounded-md">
                    @{{ user.username || '—' }}
                  </span>
                </td>

                <td class="py-3.5 px-4">
                  <div class="flex flex-wrap gap-1">
                    <span
                      v-for="(rName, idx) in user.role_names"
                      :key="idx"
                      :class="[
                        'px-2.5 py-1 rounded-xl text-[11px] font-black border',
                        user.roles[idx] === 'admin' ? 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/20' :
                        user.roles[idx] === 'hr' ? 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border-indigo-500/20' :
                        'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20'
                      ]"
                    >
                      {{ rName }}
                    </span>
                  </div>
                </td>

                <td class="py-3.5 px-4">
                  <SpatialStatusPill
                    :status="user.status === 'active' ? 'active' : 'inactive'"
                    :label="user.status === 'active' ? 'نشط' : 'معطل'"
                  />
                </td>

                <td class="py-3.5 px-4 text-slate-500 dark:text-white/60 text-[11px]">
                  {{ user.created_at || '—' }}
                </td>

                <td class="py-3.5 px-4">
                  <div class="flex items-center justify-center gap-2">
                    <button
                      @click="openEditUserDrawer(user)"
                      title="تعديل حساب المستخدم"
                      class="p-2 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/20 transition-all cursor-pointer"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                    </button>

                    <button
                      @click="toggleUserStatus(user)"
                      :title="user.status === 'active' ? 'تعطيل الحساب' : 'تفعيل الحساب'"
                      class="p-2 rounded-xl bg-blue-500/10 hover:bg-blue-500/20 text-blue-600 dark:text-blue-400 border border-blue-500/20 transition-all cursor-pointer"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                      </svg>
                    </button>

                    <button
                      @click="confirmDeleteUser(user)"
                      title="حذف الحساب نهائياً"
                      class="p-2 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/20 transition-all cursor-pointer"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>

              <tr v-if="filteredUsers.length === 0">
                <td colspan="6" class="py-12 text-center text-slate-500 dark:text-white/60 space-y-2">
                  <span class="text-3xl block">🔍</span>
                  <span class="font-bold text-sm block">لا توجد حسابات مستخدمين تطابق معايير البحث والفلترة</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </SpatialCard>

      <!-- DRAWER: CREATE / EDIT USER -->
      <SpatialDrawer
        v-model:is-open="isUserDrawerOpen"
        :title="editingUser ? 'تعديل حساب مستخدم' : 'إنشاء حساب مستخدم جديد'"
        width="w-[480px]"
      >
        <form @submit.prevent="saveUser" class="space-y-5 dir-rtl p-1">
          <SpatialInput
            v-model="userForm.name"
            label="الاسم الكامل"
            placeholder="مثال: د. عبد الله بن علي"
            :error="userForm.errors.name"
            required
          />

          <SpatialInput
            v-model="userForm.email"
            label="البريد الإلكتروني"
            type="email"
            placeholder="user@fcpms.test"
            :error="userForm.errors.email"
            required
          />

          <SpatialInput
            v-model="userForm.username"
            label="اسم المستخدم (Username للدخول)"
            placeholder="abdullah"
            :error="userForm.errors.username"
          />

          <SpatialInput
            v-model="userForm.password"
            :label="editingUser ? 'كلمة المرور الجديدة (أتركها فارغة إذا لم تود التغيير)' : 'كلمة المرور'"
            type="password"
            placeholder="••••••••"
            :error="userForm.errors.password"
            :required="!editingUser"
          />

          <div class="space-y-1.5">
            <label class="block text-xs font-black text-slate-700 dark:text-white/90">دور الصلاحيات الإداري *</label>
            <select
              v-model="userForm.role"
              class="w-full h-12 rounded-xl bg-slate-100 dark:bg-white/10 border border-black/10 dark:border-white/15 px-3 text-xs font-bold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/50"
              required
            >
              <option v-for="r in availableRoles" :key="r.name" :value="r.name">
                {{ r.label }}
              </option>
            </select>
            <span v-if="userForm.errors.role" class="text-[11px] font-bold text-rose-500 block">{{ userForm.errors.role }}</span>
          </div>

          <div class="space-y-1.5">
            <label class="block text-xs font-black text-slate-700 dark:text-white/90">حالة الحساب *</label>
            <select
              v-model="userForm.status"
              class="w-full h-12 rounded-xl bg-slate-100 dark:bg-white/10 border border-black/10 dark:border-white/15 px-3 text-xs font-bold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/50"
              required
            >
              <option value="active">نشط (مسموح بالدخول للنظام)</option>
              <option value="inactive">معطل (محظور من الدخول)</option>
            </select>
          </div>

          <div class="pt-4 border-t border-black/10 dark:border-white/10 flex items-center justify-end gap-3">
            <SpatialButton type="button" variant="secondary" @click="isUserDrawerOpen = false">إلغاء</SpatialButton>
            <SpatialButton type="submit" variant="primary" :disabled="userForm.processing">
              {{ editingUser ? 'حفظ التغييرات' : 'إنشاء الحساب الآن' }}
            </SpatialButton>
          </div>
        </form>
      </SpatialDrawer>

      <!-- MODAL: DELETE CONFIRMATION -->
      <SpatialModal
        v-model:is-open="isDeleteModalOpen"
        title="تأكيد حذف حساب المستخدم"
        max-width="sm"
      >
        <div v-if="deletingUser" class="space-y-3 dir-rtl py-2">
          <div class="p-4 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-400 space-y-2">
            <h4 class="font-black text-sm">⚠️ تحذير:</h4>
            <p class="text-xs font-bold leading-relaxed">
              هل أنت متأكد من حذف حساب المستخدم "<span class="font-black underline">{{ deletingUser.name }}</span>"؟ لن تتمكن من التراجع عن هذه العملية.
            </p>
          </div>
        </div>

        <template #footer>
          <div class="flex items-center justify-end gap-2 w-full">
            <SpatialButton variant="secondary" @click="isDeleteModalOpen = false">إلغاء</SpatialButton>
            <SpatialButton variant="danger" @click="executeDeleteUser">تأكيد الحذف النهائي</SpatialButton>
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
import SpatialModal from '@/Components/Spatial/SpatialModal.vue';
import SpatialDrawer from '@/Components/Spatial/SpatialDrawer.vue';
import SpatialStatusPill from '@/Components/Spatial/SpatialStatusPill.vue';

const props = defineProps({
  users: {
    type: Array,
    default: () => [],
  },
  availableRoles: {
    type: Array,
    default: () => [],
  },
  filters: {
    type: Object,
    default: () => ({ search: '', role: 'all', status: 'all' }),
  },
  stats: {
    type: Object,
    default: () => ({ total: 0, admins: 0, hr_staff: 0, consultants: 0, active: 0 }),
  },
});

const filterSearch = ref(props.filters.search || '');
const filterRole = ref(props.filters.role || 'all');
const filterStatus = ref(props.filters.status || 'all');

const filteredUsers = computed(() => {
  return props.users.filter(u => {
    const s = filterSearch.value.trim().toLowerCase();
    const matchesSearch = !s || 
      (u.name && u.name.toLowerCase().includes(s)) ||
      (u.email && u.email.toLowerCase().includes(s)) ||
      (u.username && u.username.toLowerCase().includes(s));

    const matchesRole = filterRole.value === 'all' || (u.roles && u.roles.includes(filterRole.value));
    const matchesStatus = filterStatus.value === 'all' || u.status === filterStatus.value;

    return matchesSearch && matchesRole && matchesStatus;
  });
});

// Create / Edit Drawer
const isUserDrawerOpen = ref(false);
const editingUser = ref(null);

const userForm = useForm({
  name: '',
  email: '',
  username: '',
  password: '',
  role: 'hr',
  status: 'active',
});

function openCreateUserDrawer() {
  editingUser.value = null;
  userForm.reset();
  userForm.role = 'hr';
  userForm.status = 'active';
  isUserDrawerOpen.value = true;
}

function openEditUserDrawer(user) {
  editingUser.value = user;
  userForm.name = user.name;
  userForm.email = user.email;
  userForm.username = user.username || '';
  userForm.password = '';
  userForm.role = user.roles[0] || 'hr';
  userForm.status = user.status || 'active';
  isUserDrawerOpen.value = true;
}

function saveUser() {
  if (editingUser.value) {
    userForm.put(route('admin.users.update', editingUser.value.id), {
      onSuccess: () => {
        isUserDrawerOpen.value = false;
      },
    });
  } else {
    userForm.post(route('admin.users.store'), {
      onSuccess: () => {
        isUserDrawerOpen.value = false;
      },
    });
  }
}

function toggleUserStatus(user) {
  router.patch(route('admin.users.toggle-status', user.id));
}

// Delete Confirmation
const isDeleteModalOpen = ref(false);
const deletingUser = ref(null);

function confirmDeleteUser(user) {
  deletingUser.value = user;
  isDeleteModalOpen.value = true;
}

function executeDeleteUser() {
  if (!deletingUser.value) return;
  router.delete(route('admin.users.destroy', deletingUser.value.id), {
    onSuccess: () => {
      isDeleteModalOpen.value = false;
      deletingUser.value = null;
    },
  });
}
</script>
