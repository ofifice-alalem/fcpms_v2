<template>
  <HRLayout>
    <!-- Toast Feedback Component -->
    <SpatialToast ref="toastRef" />

    <div class="space-y-6">
      
      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-black text-slate-900 dark:text-white flex items-center gap-2">
            🏛️ دليل المواقع والمنشآت الميدانية
          </h1>
          <p class="text-xs font-bold text-slate-500 dark:text-white/60 mt-1">
            إدارة وتتبع بيانات المنشآت والحقول والزيارات الميدانية في النظام
          </p>
        </div>

        <button
          @click="openCreateDrawer"
          class="spatial-button text-sm px-5 py-3 shadow-lg flex items-center gap-2 cursor-pointer shrink-0"
        >
          <span class="text-lg font-black">+</span>
          إضافة موقع جديد
        </button>
      </div>

      <!-- Filter & Search Bar -->
      <SpatialCard padding="p-5" class="relative z-30">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
          
          <!-- Live Search Input -->
          <SpatialInput
            v-model="searchQuery"
            label="البحث المباشر"
            placeholder="اسم، رمز، أو عنوان الموقع..."
            @input="handleFilterChange"
          />

          <!-- City Spatial Dropdown -->
          <SpatialDropdown
            v-model="selectedCity"
            label="تصفية حسب المدينة"
            placeholder="جميع المدن"
            :options="cityFilterOptions"
            @change="handleFilterChange"
          />

          <!-- Status Spatial Dropdown -->
          <SpatialDropdown
            v-model="selectedStatus"
            label="الحالة التشغيلية"
            placeholder="جميع الحالات"
            :options="statusFilterOptions"
            @change="handleFilterChange"
          />

          <!-- Sort Spatial Dropdown -->
          <SpatialDropdown
            v-model="selectedSort"
            label="ترتيب النتائج"
            placeholder="اختر الترتيب..."
            :options="sortFilterOptions"
            @change="handleFilterChange"
          />

        </div>
      </SpatialCard>

      <!-- Data Grid Table -->
      <SpatialCard padding="p-0 overflow-hidden" class="relative z-10">
        <div class="overflow-x-auto">
          <table class="w-full text-right border-collapse">
            <thead>
              <tr class="border-b border-black/10 dark:border-white/10 text-xs font-black text-slate-500 dark:text-white/60 bg-black/5 dark:bg-white/5">
                <th class="p-4 w-12 text-center">
                  <SpatialCheckbox v-model="selectAll" @change="toggleSelectAll" />
                </th>
                <th class="p-4">رمز الموقع</th>
                <th class="p-4">اسم المنشأة / العنوان</th>
                <th class="p-4">المدينة</th>
                <th class="p-4">الحالة التشغيلية</th>
                <th class="p-4 text-center">عدد الزيارات</th>
                <th class="p-4 text-center">الإجراءات</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-black/5 dark:divide-white/5 text-sm font-bold">
              <tr
                v-for="site in sites.data"
                :key="site.id"
                :class="[
                  'transition-colors hover:bg-black/5 dark:hover:bg-white/5',
                  selectedIds.includes(site.id) ? 'bg-primary/10 dark:bg-primary/20 border-r-4 border-r-primary' : ''
                ]"
              >
                <!-- Checkbox -->
                <td class="p-4 text-center">
                  <SpatialCheckbox v-model="selectedIds" :value="site.id" />
                </td>

                <!-- Code -->
                <td class="p-4">
                  <span class="inline-block px-3 py-1 rounded-xl bg-slate-200 dark:bg-white/10 text-slate-900 dark:text-white font-mono text-xs font-black border border-black/10 dark:border-white/15">
                    {{ site.code }}
                  </span>
                </td>

                <!-- Name & Address -->
                <td class="p-4">
                  <div class="font-black text-slate-900 dark:text-white">{{ site.name }}</div>
                  <div class="text-xs text-slate-500 dark:text-white/50 mt-0.5">{{ site.address || 'لا يوجد عنوان مباشر' }}</div>
                </td>

                <!-- City -->
                <td class="p-4 text-slate-700 dark:text-white/80">
                  {{ site.city || 'غير محددة' }}
                </td>

                <!-- Status Pill -->
                <td class="p-4">
                  <SpatialStatusPill :type="site.status === 'active' ? 'completed' : 'cancelled'" :pulse="site.status === 'active'">
                    {{ site.status === 'active' ? 'نشط' : 'غير نشط' }}
                  </SpatialStatusPill>
                </td>

                <!-- Visits Count -->
                <td class="p-4 text-center font-mono font-black text-primary">
                  {{ site.visits_count || 0 }}
                </td>

                <!-- Actions Buttons -->
                <td class="p-4 text-center">
                  <div class="flex items-center justify-center gap-1.5">
                    <SpatialIconButton
                      variant="primary"
                      title="عرض التفاصيل"
                      @click="openDetailModal(site)"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                      </svg>
                    </SpatialIconButton>

                    <SpatialIconButton
                      variant="info"
                      title="تعديل الموقع"
                      @click="openEditDrawer(site)"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                    </SpatialIconButton>

                    <!-- Dynamic Toggle Status Button -->
                    <SpatialIconButton
                      :variant="site.status === 'active' ? 'warning' : 'success'"
                      :title="site.status === 'active' ? 'تعطيل الموقع' : 'تفعيل الموقع'"
                      @click="toggleSiteStatus(site)"
                    >
                      <!-- Previous Lightning/Zap Icon if Active -->
                      <svg v-if="site.status === 'active'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                      </svg>
                      <!-- Play / Activate Icon if Inactive -->
                      <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                    </SpatialIconButton>

                    <SpatialIconButton
                      variant="danger"
                      title="حذف الموقع"
                      @click="confirmDeleteSite(site)"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                    </SpatialIconButton>
                  </div>
                </td>

              </tr>

              <!-- Empty State -->
              <tr v-if="!sites.data || sites.data.length === 0">
                <td colspan="7" class="p-12 text-center text-slate-500 dark:text-white/50 font-bold">
                  لا توجد مواقع ميدانية مسجلة تطابق محددات البحث.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="sites.links && sites.links.length > 3" class="p-4 border-t border-black/10 dark:border-white/10 flex items-center justify-between">
          <div class="text-xs font-bold text-slate-500 dark:text-white/60">
            إجمالي النتائج: {{ sites.total }} موقع
          </div>
          <div class="flex items-center gap-1.5">
            <Link
              v-for="(link, i) in sites.links"
              :key="i"
              :href="link.url || '#'"
              v-html="link.label"
              :class="[
                'px-3 py-1.5 rounded-xl text-xs font-bold transition-all',
                link.active ? 'bg-primary text-white shadow-md' : 'bg-black/5 dark:bg-white/10 text-slate-700 dark:text-white hover:bg-black/10',
                !link.url ? 'opacity-40 pointer-events-none' : 'cursor-pointer'
              ]"
            />
          </div>
        </div>

      </SpatialCard>

    </div>

    <!-- Floating Bulk Bar -->
    <SpatialFloatingBulkBar :selectedCount="selectedIds.length">
      <button
        @click="selectedIds = []"
        class="text-xs font-bold text-slate-400 hover:text-white underline cursor-pointer"
      >
        إلغاء التحديد
      </button>
    </SpatialFloatingBulkBar>

    <!-- Create / Edit Site Drawer -->
    <SpatialDrawer
      :show="showFormDrawer"
      @close="showFormDrawer = false"
      :title="isEditing ? 'تعديل بيانات الموقع' : 'إضافة موقع ميداني جديد'"
      width="480px"
    >
      <form @submit.prevent="submitForm" class="space-y-5">
        
        <SpatialInput
          v-model="form.code"
          label="رمز الموقع (Unique Code)"
          placeholder="مثال: TRIPOLI-SITE-01"
          required
          :disabled="isEditing && editingSiteHasVisits"
          :error="form.errors.code"
        />
        <div v-if="isEditing && editingSiteHasVisits" class="text-[11px] font-bold text-amber-500">
          🔒 تجميد تعديل الرمز نظراً لوجود زيارات ميدانية سابقة مسجلة له (BR-020).
        </div>

        <SpatialInput
          v-model="form.name"
          label="اسم المنشأة / الموقع"
          placeholder="ادخل الاسم الكامل للموقع..."
          required
          :error="form.errors.name"
        />

        <SpatialDropdown
          v-model="form.city"
          label="المدينة"
          placeholder="اختر المدينة..."
          :options="cityFormOptions"
        />

        <SpatialInput
          v-model="form.address"
          label="العنوان المباشر"
          placeholder="مثال: طريق الشط، القادسية..."
          :error="form.errors.address"
        />

        <SpatialDropdown
          v-model="form.status"
          label="الحالة التشغيلية"
          placeholder="اختر الحالة..."
          :options="statusFormOptions"
        />

        <div class="space-y-1.5 w-full">
          <label class="text-xs font-bold text-slate-700 dark:text-white/75">ملاحظات هندسية / ميدانية</label>
          <textarea
            v-model="form.notes"
            rows="3"
            placeholder="ادخل أي ملاحظات إضافية..."
            class="spatial-input rounded-[18px] p-4 w-full text-sm font-bold bg-transparent custom-scroll"
          ></textarea>
        </div>

        <div class="pt-4 flex items-center justify-end gap-3">
          <button
            type="button"
            @click="showFormDrawer = false"
            class="px-5 py-3 rounded-2xl bg-black/5 dark:bg-white/10 text-slate-700 dark:text-white font-bold text-xs cursor-pointer hover:bg-black/10"
          >
            إلغاء
          </button>
          
          <button
            type="submit"
            :disabled="form.processing"
            class="spatial-button text-xs px-6 py-3 font-black shadow-lg cursor-pointer"
          >
            <span v-if="form.processing">جاري الحفظ...</span>
            <span v-else>{{ isEditing ? 'تحديث البيانات' : 'حفظ الموقع' }}</span>
          </button>
        </div>

      </form>
    </SpatialDrawer>

    <!-- Detail Modal -->
    <SpatialModal :show="showDetailModal" @close="showDetailModal = false" title="تفاصيل الموقع الميداني">
      <div v-if="activeSite" class="space-y-6">
        <div class="flex items-center justify-between border-b border-black/10 dark:border-white/10 pb-4">
          <div>
            <h3 class="text-xl font-black text-slate-900 dark:text-white">{{ activeSite.name }}</h3>
            <span class="font-mono text-xs font-black text-primary">{{ activeSite.code }}</span>
          </div>
          <SpatialStatusPill :type="activeSite.status === 'active' ? 'completed' : 'cancelled'">
            {{ activeSite.status === 'active' ? 'نشط' : 'غير نشط' }}
          </SpatialStatusPill>
        </div>

        <div class="grid grid-cols-2 gap-4 text-xs font-bold">
          <div>
            <span class="text-slate-500 dark:text-white/50 block">المدينة:</span>
            <span class="text-slate-900 dark:text-white text-sm">{{ activeSite.city || 'غير محددة' }}</span>
          </div>

          <div>
            <span class="text-slate-500 dark:text-white/50 block">إجمالي الزيارات:</span>
            <span class="text-slate-900 dark:text-white text-sm font-mono">{{ activeSite.visits_count || 0 }} زيارة</span>
          </div>
        </div>

        <div>
          <span class="text-xs text-slate-500 dark:text-white/50 block mb-1">العنوان التفصيلي:</span>
          <p class="text-sm font-bold text-slate-900 dark:text-white bg-black/5 dark:bg-white/5 p-3 rounded-2xl">
            {{ activeSite.address || 'لا يوجد عنوان مسجل.' }}
          </p>
        </div>

        <div v-if="activeSite.notes">
          <span class="text-xs text-slate-500 dark:text-white/50 block mb-1">ملاحظات هندسية:</span>
          <p class="text-xs font-bold text-slate-700 dark:text-white/80 bg-black/5 dark:bg-white/5 p-3 rounded-2xl">
            {{ activeSite.notes }}
          </p>
        </div>
      </div>
    </SpatialModal>

    <!-- Delete Confirmation Modal -->
    <SpatialModal :show="showDeleteModal" @close="showDeleteModal = false" title="تأكيد حذف الموقع">
      <div v-if="activeSite" class="space-y-5 text-right">
        <p class="text-sm font-bold text-slate-700 dark:text-white/90 leading-relaxed">
          هل أنت تأكد من رغبتك في حذف وأرشفة الموقع الميداني <span class="font-black text-red-500">{{ activeSite.name }}</span> ({{ activeSite.code }})؟
        </p>

        <div class="p-4 rounded-2xl bg-amber-500/15 border border-amber-500/30 text-amber-500 text-xs font-bold leading-relaxed">
          ⚠️ تنبيه: سيتم تطبيق الحذف الأرشيفي SoftDeletes لحفظ الزيارات التاريخية المسجلة.
        </div>

        <div class="pt-2 flex items-center justify-end gap-3">
          <button
            @click="showDeleteModal = false"
            class="px-5 py-2.5 rounded-xl bg-black/5 dark:bg-white/10 text-slate-700 dark:text-white font-bold text-xs cursor-pointer"
          >
            إلغاء
          </button>
          
          <button
            @click="executeDeleteSite"
            class="px-5 py-2.5 rounded-xl bg-red-500 hover:bg-red-600 text-white font-black text-xs shadow-lg cursor-pointer transition-all"
          >
            تأكيد الحذف
          </button>
        </div>
      </div>
    </SpatialModal>

  </HRLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router, useForm, Link } from '@inertiajs/vue3';
import HRLayout from '@/Layouts/HRLayout.vue';
import SpatialCard from '@/Components/Spatial/SpatialCard.vue';
import SpatialInput from '@/Components/Spatial/SpatialInput.vue';
import SpatialDropdown from '@/Components/Spatial/SpatialDropdown.vue';
import SpatialStatusPill from '@/Components/Spatial/SpatialStatusPill.vue';
import SpatialCheckbox from '@/Components/Spatial/SpatialCheckbox.vue';
import SpatialIconButton from '@/Components/Spatial/SpatialIconButton.vue';
import SpatialDrawer from '@/Components/Spatial/SpatialDrawer.vue';
import SpatialModal from '@/Components/Spatial/SpatialModal.vue';
import SpatialToast from '@/Components/Spatial/SpatialToast.vue';
import SpatialFloatingBulkBar from '@/Components/Spatial/SpatialFloatingBulkBar.vue';

const props = defineProps({
  sites: Object,
  filters: Object,
});

const toastRef = ref(null);
const searchQuery = ref(props.filters.search || '');
const selectedCity = ref(props.filters.city || '');
const selectedStatus = ref(props.filters.status || '');
const selectedSort = ref(props.filters.sort || 'latest');

const selectedIds = ref([]);
const selectAll = ref(false);

const showFormDrawer = ref(false);
const showDetailModal = ref(false);
const showDeleteModal = ref(false);
const isEditing = ref(false);
const activeSite = ref(null);
const editingSiteHasVisits = ref(false);

const cityFilterOptions = [
  { label: 'جميع المدن', value: '' },
  { label: 'طرابلس', value: 'طرابلس' },
  { label: 'بنغازي', value: 'بنغازي' },
  { label: 'مصراتة', value: 'مصراتة' },
  { label: 'الزاوية', value: 'الزاوية' },
  { label: 'سبها', value: 'سبها' },
];

const statusFilterOptions = [
  { label: 'جميع الحالات', value: '' },
  { label: 'نشط (Active)', value: 'active' },
  { label: 'غير نشط (Inactive)', value: 'inactive' },
];

const sortFilterOptions = [
  { label: 'الأحدث إضافة (افتراضي)', value: 'latest' },
  { label: 'الأكثر زيارات 📈', value: 'visits_desc' },
  { label: 'الأقل زيارات 📉', value: 'visits_asc' },
  { label: 'اسم الموقع (أ - ي)', value: 'name_asc' },
];

const cityFormOptions = [
  { label: 'طرابلس', value: 'طرابلس' },
  { label: 'بنغازي', value: 'بنغازي' },
  { label: 'مصراتة', value: 'مصراتة' },
  { label: 'الزاوية', value: 'الزاوية' },
  { label: 'سبها', value: 'سبها' },
];

const statusFormOptions = [
  { label: 'نشط (Active)', value: 'active' },
  { label: 'غير نشط (Inactive)', value: 'inactive' },
];

const form = useForm({
  id: null,
  code: '',
  name: '',
  city: '',
  address: '',
  status: 'active',
  notes: '',
});

function handleFilterChange() {
  router.get(
    route('admin.sites.index'),
    {
      search: searchQuery.value,
      city: selectedCity.value,
      status: selectedStatus.value,
      sort: selectedSort.value,
    },
    { preserveState: true, replace: true }
  );
}

function toggleSelectAll() {
  if (selectAll.value) {
    selectedIds.value = props.sites.data.map((s) => s.id);
  } else {
    selectedIds.value = [];
  }
}

function openCreateDrawer() {
  isEditing.value = false;
  editingSiteHasVisits.value = false;
  form.reset();
  form.clearErrors();
  showFormDrawer.value = true;
}

function openEditDrawer(site) {
  isEditing.value = true;
  activeSite.value = site;
  editingSiteHasVisits.value = (site.visits_count || 0) > 0;
  
  form.id = site.id;
  form.code = site.code;
  form.name = site.name;
  form.city = site.city || '';
  form.address = site.address || '';
  form.status = site.status;
  form.notes = site.notes || '';
  
  form.clearErrors();
  showFormDrawer.value = true;
}

function openDetailModal(site) {
  activeSite.value = site;
  showDetailModal.value = true;
}

function confirmDeleteSite(site) {
  activeSite.value = site;
  showDeleteModal.value = true;
}

function submitForm() {
  if (isEditing.value) {
    form.put(route('admin.sites.update', form.id), {
      onSuccess: () => {
        showFormDrawer.value = false;
        toastRef.value?.addToast('success', 'تم تحديث بيانات الموقع بنجاح.');
      },
      onError: (errors) => {
        const msg = Object.values(errors)[0] || 'حدث خطأ أثناء التحديث.';
        toastRef.value?.addToast('error', msg);
      },
    });
  } else {
    form.post(route('admin.sites.store'), {
      onSuccess: () => {
        showFormDrawer.value = false;
        toastRef.value?.addToast('success', 'تم إضافة الموقع الميداني بنجاح.');
      },
      onError: (errors) => {
        const msg = Object.values(errors)[0] || 'حدث خطأ أثناء الإضافة.';
        toastRef.value?.addToast('error', msg);
      },
    });
  }
}

function toggleSiteStatus(site) {
  router.patch(route('admin.sites.toggle-status', site.id), {}, {
    onSuccess: () => {
      toastRef.value?.addToast('success', 'تم تغيير حالة الموقع بنجاح.');
    },
    onError: () => {
      toastRef.value?.addToast('error', 'تعذر تغيير حالة الموقع.');
    },
  });
}

function executeDeleteSite() {
  if (!activeSite.value) return;

  router.delete(route('admin.sites.destroy', activeSite.value.id), {
    onSuccess: () => {
      showDeleteModal.value = false;
      toastRef.value?.addToast('success', 'تم حذف وأرشفة الموقع بنجاح.');
    },
    onError: (errors) => {
      showDeleteModal.value = false;
      const msg = errors.site || Object.values(errors)[0] || 'تعذر حذف الموقع.';
      toastRef.value?.addToast('error', msg);
    },
  });
}
</script>
