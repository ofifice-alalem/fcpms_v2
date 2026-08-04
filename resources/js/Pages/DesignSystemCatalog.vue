<template>
  <div class="min-h-screen transition-colors duration-300 p-6 lg:p-10 space-y-12 custom-scroll font-sans">
    
    <!-- ===== TOAST CONTAINER COMPONENT ===== -->
    <SpatialToast ref="toastRef" />

    <!-- ===== FLOATING BULK BAR COMPONENT ===== -->
    <SpatialFloatingBulkBar
      :selected-count="selectedRows.length"
      @clear="unselectAllRows"
    >
      <button class="px-4 py-1.5 rounded-xl bg-primary text-white font-bold text-xs shadow-md cursor-pointer hover:bg-blue-600" @click="notify('success', 'تم طباعة العناصر المحددة')">
        طباعة
      </button>
      <button class="px-4 py-1.5 rounded-xl bg-emerald-500 text-white font-bold text-xs shadow-md cursor-pointer hover:bg-emerald-600" @click="notify('info', 'تم تصدير البيانات بنجاح')">
        تصدير
      </button>
      <button class="px-4 py-1.5 rounded-xl bg-red-500 text-white font-bold text-xs shadow-md cursor-pointer hover:bg-red-600" @click="showDeleteModal = true">
        حذف مجمع
      </button>
    </SpatialFloatingBulkBar>

    <!-- ===== HEADER BANNER WITH THEME TOGGLE ===== -->
    <header class="sticky top-0 z-40 backdrop-blur-2xl bg-white/80 dark:bg-[#0f172a]/80 border-b border-black/10 dark:border-white/10 px-6 py-4 rounded-2xl flex items-center justify-between shadow-xl mb-8">
      <div class="flex items-center gap-3">
        <div class="w-3 h-3 rounded-full bg-primary animate-pulse"></div>
        <h1 class="text-xl font-black text-slate-900 dark:text-white">
          FCPMS <span class="text-xs px-2.5 py-1 rounded-full bg-primary/20 text-primary font-bold mr-2">Spatial UI v3.0 Vue Catalog</span>
        </h1>
      </div>
      <div class="flex items-center gap-4">
        <!-- Theme Toggle Button matching components_catalog.html -->
        <button
          @click="toggleTheme"
          class="flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-black/10 dark:bg-white/10 hover:bg-black/20 dark:hover:bg-white/20 border border-black/10 dark:border-white/15 text-slate-900 dark:text-white font-bold text-xs transition-all cursor-pointer shadow-sm"
        >
          <span v-if="isDark">☀️ الوضع الفاتح</span>
          <span v-else>🌙 الوضع الداكن</span>
        </button>
        <span class="text-xs font-mono bg-black/5 dark:bg-white/10 px-3 py-2 rounded-xl border border-black/10 dark:border-white/15 text-blue-500 dark:text-blue-400 font-bold">
          URL: /design-system
        </span>
      </div>
    </header>

    <!-- ===== MAIN CATALOG CONTENT ===== -->
    <main class="max-w-7xl mx-auto space-y-12">

      <!-- INTRO SECTION -->
      <SpatialCard padding="p-8 relative overflow-hidden">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 relative z-10">
          <div class="space-y-3 max-w-3xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 border border-primary/20 text-primary font-bold text-xs">
              <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span> المعاينة التفاعلية لمكونات Vue 3
            </div>
            <h2 class="text-3xl font-black leading-tight text-slate-900 dark:text-white">
              كتالوج المكونات التفاعلي الشامل (Spatial UI v3.0)<br>
              <span class="text-base font-medium text-slate-500 dark:text-white/50">مطابق 100% للتصميم والمرجع الرسمي components_catalog.html بخط Tajawal</span>
            </h2>
          </div>
        </div>
      </SpatialCard>

      <!-- ===== SECTION 1: BUTTONS & BADGES ===== -->
      <section id="buttonsSection" class="space-y-6">
        <div class="flex items-center gap-3">
          <span class="w-3.5 h-9 rounded-full bg-primary shadow-lg shadow-primary/40"></span>
          <div>
            <h2 class="text-2xl font-black text-slate-900 dark:text-white">1. الأزرار والحالات</h2>
            <p class="text-xs font-bold text-slate-500 dark:text-white/50">أزرار تفاعلية وشارات بصرية ملونة (Pills)</p>
          </div>
        </div>

        <SpatialCard class="p-6 space-y-6">
          <div class="flex flex-wrap items-center gap-4">
            <button class="spatial-button text-sm px-6 py-3" @click="notify('success', 'زر رئيسي')">زر رئيسي (Primary)</button>
            <button class="spatial-button !bg-gradient-to-r !from-emerald-600 !to-emerald-500 text-sm px-6 py-3" @click="notify('success', 'عملية ناجحة')">زر نجاح (Success)</button>
            <button class="spatial-button !bg-gradient-to-r !from-red-600 !to-red-500 text-sm px-6 py-3" @click="notify('error', 'حذف سجل')">زر خطر (Danger)</button>
            <button class="spatial-button !bg-gradient-to-r !from-amber-500 !to-amber-600 text-sm px-6 py-3" @click="notify('warning', 'تنبيه تحذيري')">زر تحذير (Warning)</button>
            <button class="spatial-icon-btn" @click="notify('info', 'زر أيقونة')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </button>
          </div>
          <div class="flex flex-wrap items-center gap-3 pt-4 border-t border-black/10 dark:border-white/10">
            <SpatialStatusPill type="completed" pulse>مكفول / نشط</SpatialStatusPill>
            <SpatialStatusPill type="pending">قيد المعاينة ⏱</SpatialStatusPill>
            <SpatialStatusPill type="overdue">متوقف / غائب ✕</SpatialStatusPill>
          </div>
        </SpatialCard>
      </section>

      <!-- ===== SECTION 2: CARDS & KPI ===== -->
      <section id="cardsSection" class="space-y-6">
        <div class="flex items-center gap-3">
          <span class="w-3.5 h-9 rounded-full bg-primary shadow-lg shadow-primary/40"></span>
          <div>
            <h2 class="text-2xl font-black text-slate-900 dark:text-white">2. البطاقات ومؤشرات الأداء</h2>
            <p class="text-xs font-bold text-slate-500 dark:text-white/50">بطاقات قياس الأداء المتدرجة مع شريط الإنجاز وقالب بطاقة الاستشاري الكامل</p>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <SpatialCard class="flex flex-col justify-between space-y-4 p-6">
            <div class="flex items-center justify-between">
              <div class="w-12 h-12 rounded-[18px] bg-primary/15 border border-primary/25 flex items-center justify-center text-primary">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
              </div>
              <span class="text-xs font-black px-3 py-1 rounded-full bg-emerald-500/15 text-emerald-500">+12% ↗</span>
            </div>
            <div><span class="text-xs font-bold text-slate-500 dark:text-white/50 uppercase block">الاستشاريون النشطون</span><h3 class="text-2xl font-black text-slate-900 dark:text-white">48</h3></div>
            <SpatialProgressBar :percentage="85" color="primary" />
          </SpatialCard>

          <SpatialCard class="flex flex-col justify-between space-y-4 p-6">
            <div class="flex items-center justify-between">
              <div class="w-12 h-12 rounded-[18px] bg-emerald-500/15 border border-emerald-500/25 flex items-center justify-center text-emerald-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              </div>
              <span class="text-xs font-black px-3 py-1 rounded-full bg-emerald-500/15 text-emerald-500">+8% ↗</span>
            </div>
            <div><span class="text-xs font-bold text-slate-500 dark:text-white/50 uppercase block">نسبة الإنجاز اليومي</span><h3 class="text-2xl font-black text-slate-900 dark:text-white">94%</h3></div>
            <SpatialProgressBar :percentage="94" color="success" />
          </SpatialCard>

          <SpatialCard class="flex flex-col justify-between space-y-4 p-6">
            <div class="flex items-center justify-between">
              <div class="w-12 h-12 rounded-[18px] bg-amber-500/15 border border-amber-500/25 flex items-center justify-center text-amber-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
              </div>
              <span class="text-xs font-black px-3 py-1 rounded-full bg-amber-500/15 text-amber-500">+3% ↗</span>
            </div>
            <div><span class="text-xs font-bold text-slate-500 dark:text-white/50 uppercase block">زيارات اليوم</span><h3 class="text-2xl font-black text-slate-900 dark:text-white">142</h3></div>
            <SpatialProgressBar :percentage="78" color="warning" />
          </SpatialCard>

          <SpatialCard class="flex flex-col justify-between space-y-4 p-6">
            <div class="flex items-center justify-between">
              <div class="w-12 h-12 rounded-[18px] bg-red-500/15 border border-red-500/25 flex items-center justify-center text-red-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              </div>
              <span class="text-xs font-black px-3 py-1 rounded-full bg-red-500/15 text-red-500">12 غائب</span>
            </div>
            <div><span class="text-xs font-bold text-slate-500 dark:text-white/50 uppercase block">الغياب اليوم</span><h3 class="text-2xl font-black text-slate-900 dark:text-white">12</h3></div>
            <SpatialProgressBar :percentage="15" color="danger" />
          </SpatialCard>
        </div>

        <!-- CONSULTANT CARD -->
        <SpatialCard padding="p-6">
          <div class="flex flex-col md:flex-row items-center gap-6">
            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 text-white flex items-center justify-center text-3xl font-black border-4 border-primary/30 shrink-0 shadow-lg">
              أ
            </div>
            <div class="flex-1 space-y-2">
              <div class="flex items-center gap-3 flex-wrap">
                <h3 class="text-xl font-black text-slate-900 dark:text-white">أحمد السالم</h3>
                <SpatialStatusPill type="completed" pulse>نشط وميداني</SpatialStatusPill>
                <span class="text-xs font-mono text-slate-500 dark:text-white/50">#EMP-1024</span>
              </div>
              <div class="flex flex-wrap gap-4 text-sm font-bold text-slate-600 dark:text-white/70">
                <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg> 051-234-5678</span>
                <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg> طرابلس</span>
                <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> 01-01-2024</span>
              </div>
            </div>
            <div class="flex gap-2 shrink-0">
              <button @click="showDetailModal = true" class="spatial-button text-sm px-5 py-2.5 cursor-pointer">عرض الملف</button>
              <button @click="showDrawer = true" class="px-5 py-2.5 rounded-[16px] bg-black/5 dark:bg-white/10 hover:bg-black/10 dark:hover:bg-white/15 transition-all text-sm font-bold border border-black/10 dark:border-white/15 flex items-center gap-1.5 text-slate-900 dark:text-white cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                تعديل
              </button>
            </div>
          </div>
        </SpatialCard>
      </section>

      <!-- ===== SECTION 3: TABLES & BULK ACTION BAR ===== -->
      <section id="tablesSection" class="space-y-6">
        <div class="flex items-center gap-3">
          <span class="w-3.5 h-9 rounded-full bg-primary shadow-lg shadow-primary/40"></span>
          <div>
            <h2 class="text-2xl font-black text-slate-900 dark:text-white">3. الجداول والبيانات</h2>
            <p class="text-xs font-bold text-slate-500 dark:text-white/50">عرض البيانات مع إبراز الصفوف المحددة وشريط إجراءات عائم متطور</p>
          </div>
        </div>

        <SpatialCard padding="p-0 overflow-hidden">
          <div class="p-6 border-b border-black/10 dark:border-white/10 flex flex-col md:flex-row items-center justify-between gap-4">
            <h3 class="font-black text-lg text-slate-900 dark:text-white">قائمة الاستشاريين الميدانيين</h3>
            <div class="flex flex-wrap items-center gap-3">
              <input
                type="text"
                v-model="searchQuery"
                placeholder="بحث عن استشاري..."
                class="spatial-input h-10 rounded-[14px] px-4 text-xs font-bold w-56"
              />
              <button @click="showSkeleton = !showSkeleton" class="px-3 py-1.5 rounded-xl bg-black/5 dark:bg-white/10 text-slate-900 dark:text-white text-xs font-bold hover:bg-black/10 transition-all flex items-center gap-1 cursor-pointer">
                💀 Skeleton
              </button>
              <button @click="showEmptyState = !showEmptyState" class="px-3 py-1.5 rounded-xl bg-black/5 dark:bg-white/10 text-slate-900 dark:text-white text-xs font-bold hover:bg-black/10 transition-all flex items-center gap-1 cursor-pointer">
                📂 فارغ
              </button>
            </div>
          </div>

          <div v-if="showSkeleton" class="p-6 space-y-3">
            <SpatialSkeleton height="h-8" />
            <SpatialSkeleton height="h-14" />
            <SpatialSkeleton height="h-14" />
          </div>

          <div v-else-if="showEmptyState" class="p-8">
            <SpatialEmptyState title="لا توجد بيانات" description="لم يتم العثور على استشاريين مطابقين للبحث.">
              <template #action>
                <button @click="showEmptyState = false" class="spatial-button text-sm px-4 py-2 mt-4 cursor-pointer">إعادة تعيين</button>
              </template>
            </SpatialEmptyState>
          </div>

          <div v-else class="overflow-x-auto">
            <table class="w-full text-right text-sm">
              <thead class="bg-black/5 dark:bg-white/5 text-slate-500 dark:text-white/60 font-bold uppercase text-xs">
                <tr>
                  <th class="p-4 w-12 text-center">
                    <SpatialCheckbox v-model="selectAllMaster" @change="toggleSelectAll" />
                  </th>
                  <th class="p-4">الرقم</th>
                  <th class="p-4">الاسم</th>
                  <th class="p-4">الموقع</th>
                  <th class="p-4">نسبة الإنجاز</th>
                  <th class="p-4">الحالة</th>
                  <th class="p-4 text-center">إجراء</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-black/5 dark:divide-white/5 font-bold text-slate-800 dark:text-white">
                <tr
                  v-for="row in filteredConsultants"
                  :key="row.id"
                  :class="{ 'selected-row': selectedRows.includes(row.id) }"
                >
                  <td class="p-4 text-center">
                    <SpatialCheckbox
                      :model-value="selectedRows.includes(row.id)"
                      @update:model-value="toggleRow(row.id)"
                    />
                  </td>
                  <td class="p-4 font-mono">{{ row.code }}</td>
                  <td class="p-4 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-primary/20 text-primary text-xs font-black flex items-center justify-center">
                      {{ row.name.charAt(0) }}
                    </div>
                    {{ row.name }}
                  </td>
                  <td class="p-4">{{ row.city }}</td>
                  <td class="p-4">
                    <div class="flex items-center gap-2">
                      <span class="font-black" :class="row.pct >= 80 ? 'text-emerald-500' : row.pct >= 60 ? 'text-amber-500' : 'text-red-500'">{{ row.pct }}%</span>
                      <SpatialProgressBar :percentage="row.pct" :color="row.pct >= 80 ? 'success' : row.pct >= 60 ? 'warning' : 'danger'" class="w-16" />
                    </div>
                  </td>
                  <td class="p-4">
                    <SpatialStatusPill :type="row.statusType">{{ row.statusText }}</SpatialStatusPill>
                  </td>
                  <td class="p-4 text-center">
                    <button @click="showDetailModal = true" class="spatial-icon-btn w-9 h-9 inline-flex cursor-pointer">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </SpatialCard>
      </section>

      <!-- ===== SECTION 4: FORMS & INPUTS ===== -->
      <section id="formsSection" class="space-y-6">
        <div class="flex items-center gap-3">
          <span class="w-3.5 h-9 rounded-full bg-primary shadow-lg shadow-primary/40"></span>
          <div>
            <h2 class="text-2xl font-black text-slate-900 dark:text-white">4. النماذج والأزرار</h2>
            <p class="text-xs font-bold text-slate-500 dark:text-white/50">حقول إدخال إلزامية (*)، تحقق فوري (Validation)، وقوائم اختيار مفردة تفاعلية موحدة</p>
          </div>
        </div>

        <SpatialCard class="p-6 lg:p-8 space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <SpatialInput v-model="formName" label="الاسم الكامل" placeholder="أحمد السالم" required successMsg="تم التحقق" />
            <SpatialInput v-model="formEmail" label="البريد الإلكتروني" placeholder="invalid-email" required error="البريد الإلكتروني غير صالح" />
            <SpatialInput v-model="formPhone" label="رقم الهاتف" placeholder="051-234-5678" />
            <SpatialDropdown v-model="formCity" label="الموقع" :options="cityOptions" required />
          </div>

          <div class="flex flex-wrap items-center gap-4 pt-4 border-t border-black/10 dark:border-white/10">
            <button @click="notify('success', 'تم حفظ البيانات بنجاح!')" class="spatial-button h-14 text-sm px-6 cursor-pointer">
              حفظ البيانات
            </button>
            <button @click="simulateSubmit" class="spatial-button !bg-emerald-500 hover:!bg-emerald-600 h-14 text-sm px-6 cursor-pointer">
              <span v-if="btnLoading" class="animate-spin w-4 h-4 border-2 border-white border-t-transparent rounded-full ml-2"></span>
              حفظ وإرسال
            </button>
            <button @click="showDeleteModal = true" class="px-6 h-14 rounded-[18px] bg-red-600 hover:bg-red-700 text-white font-bold text-sm transition-all cursor-pointer shadow-md">
              حذف
            </button>
            <button @click="notify('info', 'تم إلغاء العملية')" class="px-6 h-14 rounded-[18px] bg-black/5 dark:bg-white/10 hover:bg-black/10 dark:hover:bg-white/15 text-slate-700 dark:text-white font-bold text-sm transition-all border border-black/10 dark:border-white/15 cursor-pointer">
              إلغاء
            </button>
          </div>
        </SpatialCard>
      </section>

      <!-- ===== SECTION 5: DYNAMIC TASK COMPONENTS ===== -->
      <section id="taskComponentsSection" class="space-y-6">
        <div class="flex items-center gap-3">
          <span class="w-3.5 h-9 rounded-full bg-primary shadow-lg shadow-primary/40"></span>
          <div>
            <h2 class="text-2xl font-black text-slate-900 dark:text-white">5. مكونات المهام الديناميكية</h2>
            <p class="text-xs font-bold text-slate-500 dark:text-white/50">بطاقات مهام تفاعلية مخصصة: خيارات مرئية، قوائم تفقدية متطورة، مرفقات صور، وحقول تكيفية</p>
          </div>
        </div>

        <SpatialCard class="p-6 lg:p-8 space-y-8">
          <!-- 5.1 Choice Card -->
          <div class="space-y-4 border-b border-black/10 dark:border-white/10 pb-6">
            <h3 class="font-black text-lg flex items-center gap-2 text-slate-900 dark:text-white"><span class="w-3 h-3 rounded-full bg-primary"></span> 1. اختيار حالة التنفيذ (TaskOptionCard)</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <div @click="taskChoice = 'yes'" :class="['task-option-card flex flex-col justify-between space-y-3', taskChoice === 'yes' ? 'active' : '']">
                <div class="flex items-center justify-between">
                  <span class="w-8 h-8 rounded-full bg-emerald-500/20 text-emerald-500 flex items-center justify-center font-bold">✓</span>
                  <SpatialStatusPill type="completed">مكتمل</SpatialStatusPill>
                </div>
                <div><h4 class="font-black text-sm text-slate-900 dark:text-white">تم التنفيذ بالكامل</h4><p class="text-xs text-slate-500 dark:text-white/50 mt-1">الأعمال مطابقة للمواصفات</p></div>
              </div>
              <div @click="taskChoice = 'partial'" :class="['task-option-card flex flex-col justify-between space-y-3', taskChoice === 'partial' ? 'active' : '']">
                <div class="flex items-center justify-between">
                  <span class="w-8 h-8 rounded-full bg-amber-500/20 text-amber-500 flex items-center justify-center font-bold">⏱</span>
                  <SpatialStatusPill type="pending">جزئي</SpatialStatusPill>
                </div>
                <div><h4 class="font-black text-sm text-slate-900 dark:text-white">تنفيذ جزئي</h4><p class="text-xs text-slate-500 dark:text-white/50 mt-1">تتطلب التقرير القادم</p></div>
              </div>
              <div @click="taskChoice = 'no'" :class="['task-option-card flex flex-col justify-between space-y-3', taskChoice === 'no' ? 'active' : '']">
                <div class="flex items-center justify-between">
                  <span class="w-8 h-8 rounded-full bg-red-500/20 text-red-500 flex items-center justify-center font-bold">✕</span>
                  <SpatialStatusPill type="overdue">متوقف</SpatialStatusPill>
                </div>
                <div><h4 class="font-black text-sm text-slate-900 dark:text-white">لم يتم التنفيذ</h4><p class="text-xs text-slate-500 dark:text-white/50 mt-1">يتطلب السبب والصورة</p></div>
              </div>
            </div>
          </div>

          <!-- 5.2 Interactive Checklist -->
          <div class="space-y-4 border-b border-black/10 dark:border-white/10 pb-6">
            <div class="flex items-center justify-between">
              <h3 class="font-black text-lg flex items-center gap-2 text-slate-900 dark:text-white"><span class="w-3 h-3 rounded-full bg-emerald-500"></span> 2. قائمة المراجعة التفقدية (Interactive Checklist)</h3>
              <span class="text-xs font-black px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-500">الإنجاز: {{ checklistPct }}%</span>
            </div>
            <SpatialProgressBar :percentage="checklistPct" color="success" class="h-2" />
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
              <label v-for="(item, idx) in checklistItems" :key="idx" class="flex items-center justify-between p-3.5 rounded-[16px] bg-black/5 dark:bg-white/5 border border-black/5 dark:border-white/10 cursor-pointer">
                <span class="flex items-center gap-3 text-sm font-bold text-slate-900 dark:text-white">
                  <SpatialCheckbox v-model="item.checked" />
                  {{ item.title }}
                </span>
                <span v-if="item.checked" class="text-xs text-emerald-500 font-bold">مكفول ✓</span>
                <span v-else class="text-xs text-amber-500 font-bold">قيد الفحص ⏱</span>
              </label>
            </div>
          </div>

          <!-- 5.3 Rich Inputs & Counter -->
          <div class="space-y-4 border-b border-black/10 dark:border-white/10 pb-6">
            <h3 class="font-black text-lg flex items-center gap-2 text-slate-900 dark:text-white"><span class="w-3 h-3 rounded-full bg-blue-500"></span> 3. المدخلات النصية والملاحظات (Rich Inputs)</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <SpatialInput v-model="fieldItemCode" label="رقم البند الميداني" placeholder="B-1024-TR" />
              <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                  <label class="text-xs font-bold text-slate-700 dark:text-white/75 flex items-center gap-1">ملاحظات المهندس <span class="required-star">*</span></label>
                  <span class="text-[11px] font-mono text-slate-400">{{ notesText.length }} / 200</span>
                </div>
                <textarea v-model="notesText" class="spatial-input rounded-[18px] px-5 py-3.5 w-full text-sm font-bold" rows="3" placeholder="اكتب تقرير معاينة الموقع بالتفصيل..."></textarea>
              </div>
            </div>
          </div>

          <!-- 5.4 Stepper & Date -->
          <div class="space-y-4 border-b border-black/10 dark:border-white/10 pb-6">
            <h3 class="font-black text-lg flex items-center gap-2 text-slate-900 dark:text-white"><span class="w-3 h-3 rounded-full bg-purple-500"></span> 4. عداد الكميات والتاريخ الميداني (Stepper & Date)</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700 dark:text-white/75">عدد العينات المفحوصة</label>
                <div class="flex items-center gap-3">
                  <button @click="stepperVal = Math.max(0, stepperVal - 1)" class="w-14 h-14 rounded-[18px] bg-black/5 dark:bg-white/10 hover:bg-primary hover:text-white font-black text-xl flex items-center justify-center transition-all cursor-pointer text-slate-900 dark:text-white">-</button>
                  <input type="number" v-model="stepperVal" class="spatial-input h-14 rounded-[18px] px-4 w-24 text-center font-black text-lg" />
                  <button @click="stepperVal++" class="w-14 h-14 rounded-[18px] bg-black/5 dark:bg-white/10 hover:bg-primary hover:text-white font-black text-xl flex items-center justify-center transition-all cursor-pointer text-slate-900 dark:text-white">+</button>
                </div>
              </div>
              <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700 dark:text-white/75">تاريخ المعاينة المستهدفة</label>
                <input type="date" v-model="surveyDate" class="spatial-input h-14 rounded-[18px] px-5 w-full text-sm font-bold" />
              </div>
            </div>
          </div>

          <!-- 5.5 Advanced Image Upload Dropzone -->
          <div class="space-y-4 border-b border-black/10 dark:border-white/10 pb-6">
            <h3 class="font-black text-lg flex items-center gap-2 text-slate-900 dark:text-white"><span class="w-3 h-3 rounded-full bg-amber-500"></span> 5. مرفقات الصور والإثباتات (Interactive Image Upload Dropzone)</h3>
            <SpatialImageUpload @change="handleUploadedImage" />
          </div>

          <!-- 5.6 Smart Conditional Logic Block -->
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <h3 class="font-black text-lg flex items-center gap-2 text-slate-900 dark:text-white"><span class="w-3 h-3 rounded-full bg-red-500"></span> 6. الحقول الشرطية الذكية (Smart Adaptive Form)</h3>
              <span class="text-xs font-bold text-primary">تظهر تلقائياً حسب الاختيار ⚡</span>
            </div>
            <div class="p-5 rounded-[24px] bg-black/5 dark:bg-white/5 space-y-4">
              <SpatialDropdown
                v-model="workDoneVal"
                label="هل تم إنجاز الأعمال المطلوبة؟"
                :options="[
                  { label: 'نعم، تم إنجاز العمل', value: 'yes' },
                  { label: 'لا، لم يتم إنجاز العمل', value: 'no' }
                ]"
                required
              />
              <div v-if="workDoneVal === 'no'" class="pt-2 animate-spatial-in">
                <div class="p-4 rounded-[20px] bg-red-500/10 border-r-4 border-red-500 space-y-4">
                  <div class="flex items-center gap-2 text-red-500 font-black text-xs uppercase"><span>⚠️</span> مطلوب تسجيل سبب التوقف وإثبات بالصورة</div>
                  <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700 dark:text-white/75 flex items-center gap-1">سبب عدم التنفيذ بالتفصيل <span class="required-star">*</span></label>
                    <textarea v-model="failReason" class="spatial-input rounded-[18px] px-5 py-3.5 w-full text-sm font-bold" rows="2" placeholder="اذكر النقص الحاصل أو العائق الميداني..."></textarea>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </SpatialCard>
      </section>

      <!-- ===== SECTION 6: DROPDOWNS ===== -->
      <section id="dropdownsSection" class="space-y-6">
        <div class="flex items-center gap-3">
          <span class="w-3.5 h-9 rounded-full bg-primary shadow-lg shadow-primary/40"></span>
          <div>
            <h2 class="text-2xl font-black text-slate-900 dark:text-white">6. القوائم المنسدلة التفاعلية الموحدة</h2>
            <p class="text-xs font-bold text-slate-500 dark:text-white/50">اختيار مفرد تفاعلي فاخر بارتفاع 56px (h-14)</p>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <SpatialCard class="p-6 space-y-4">
            <h3 class="font-black text-base text-slate-900 dark:text-white">1. اختيار المدينة الميدانية</h3>
            <SpatialDropdown v-model="formCity" :options="cityOptions" />
          </SpatialCard>

          <SpatialCard class="p-6 space-y-4">
            <h3 class="font-black text-base text-slate-900 dark:text-white">2. اختيار حالة الاستشاري</h3>
            <SpatialDropdown v-model="formStatus" :options="statusOptions" />
          </SpatialCard>

          <SpatialCard class="p-6 space-y-4">
            <h3 class="font-black text-base text-slate-900 dark:text-white">3. اختيار متعدد بالأوسمة</h3>
            <SpatialMultiSelect v-model="selectedTags" :options="tagOptions" />
          </SpatialCard>
        </div>
      </section>

      <!-- ===== SECTION 7: MODALS ===== -->
      <section id="modalsSection" class="space-y-6">
        <div class="flex items-center gap-3">
          <span class="w-3.5 h-9 rounded-full bg-primary shadow-lg shadow-primary/40"></span>
          <div>
            <h2 class="text-2xl font-black text-slate-900 dark:text-white">7. النوافذ المنبثقة</h2>
            <p class="text-xs font-bold text-slate-500 dark:text-white/50">تأكيد، حذف، وتفاصيل كاملة</p>
          </div>
        </div>

        <SpatialCard class="p-6 flex flex-wrap gap-4">
          <button @click="showConfirmModal = true" class="px-6 py-3 rounded-[18px] bg-amber-500 hover:bg-amber-600 text-white font-bold shadow-md cursor-pointer">
            تأكيد العملية
          </button>
          <button @click="showDeleteModal = true" class="px-6 py-3 rounded-[18px] bg-red-600 hover:bg-red-700 text-white font-bold shadow-md cursor-pointer">
            حذف سجل
          </button>
          <button @click="showDetailModal = true" class="px-6 py-3 rounded-[18px] bg-primary hover:bg-blue-600 text-white font-bold shadow-md cursor-pointer">
            عرض تفاصيل الاستشاري
          </button>
        </SpatialCard>
      </section>

      <!-- ===== SECTION 8: FEEDBACK ===== -->
      <section id="feedbackSection" class="space-y-6">
        <div class="flex items-center gap-3">
          <span class="w-3.5 h-9 rounded-full bg-primary shadow-lg shadow-primary/40"></span>
          <div>
            <h2 class="text-2xl font-black text-slate-900 dark:text-white">8. التغذية الراجعة والحالات</h2>
            <p class="text-xs font-bold text-slate-500 dark:text-white/50">إشعارات، هيكل تحميل، وحالة فارغة</p>
          </div>
        </div>

        <SpatialCard class="p-6 flex flex-wrap gap-4">
          <button @click="notify('success', 'تمت العملية بنجاح! 🎉')" class="px-4 py-2 rounded-[14px] bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-sm shadow-md cursor-pointer">
            Toast نجاح
          </button>
          <button @click="notify('error', 'حدث خطأ أثناء الحفظ! ❌')" class="px-4 py-2 rounded-[14px] bg-red-500 hover:bg-red-600 text-white font-bold text-sm shadow-md cursor-pointer">
            Toast خطأ
          </button>
          <button @click="showDrawer = true" class="px-4 py-2 rounded-[14px] bg-purple-600 hover:bg-purple-700 text-white font-bold text-sm shadow-md cursor-pointer">
            📂 فتح Drawer
          </button>
        </SpatialCard>
      </section>

      <!-- ===== SECTION 9: TABS (MIRRORING COMPONENTS_CATALOG.HTML EXACTLY) ===== -->
      <section id="tabsSection" class="space-y-6">
        <div class="flex items-center gap-3">
          <span class="w-3.5 h-9 rounded-full bg-primary shadow-lg shadow-primary/40"></span>
          <div>
            <h2 class="text-2xl font-black text-slate-900 dark:text-white">9. علامات التبويب</h2>
            <p class="text-xs font-bold text-slate-500 dark:text-white/50">تبديل المحتوى التفاعلي</p>
          </div>
        </div>

        <SpatialCard class="p-6 lg:p-8 space-y-6">
          <div class="flex flex-wrap gap-2 p-1.5 rounded-[22px] bg-black/5 dark:bg-white/5 border border-black/5 dark:border-white/8 w-fit">
            <button
              @click="activeTab = 'tab1'"
              :class="[
                'px-6 py-3 rounded-[16px] font-bold text-sm transition-all cursor-pointer',
                activeTab === 'tab1' ? 'bg-primary text-white shadow-md' : 'text-slate-600 dark:text-white/70 hover:text-slate-900 dark:hover:text-white'
              ]"
            >
              المهام اليومية
            </button>
            <button
              @click="activeTab = 'tab2'"
              :class="[
                'px-6 py-3 rounded-[16px] font-bold text-sm transition-all cursor-pointer',
                activeTab === 'tab2' ? 'bg-primary text-white shadow-md' : 'text-slate-600 dark:text-white/70 hover:text-slate-900 dark:hover:text-white'
              ]"
            >
              المهام الإضافية
            </button>
            <button
              @click="activeTab = 'tab3'"
              :class="[
                'px-6 py-3 rounded-[16px] font-bold text-sm transition-all cursor-pointer',
                activeTab === 'tab3' ? 'bg-primary text-white shadow-md' : 'text-slate-600 dark:text-white/70 hover:text-slate-900 dark:hover:text-white'
              ]"
            >
              التقارير
            </button>
          </div>

          <!-- Tab Panel 1 -->
          <div v-if="activeTab === 'tab1'" class="p-6 rounded-[26px] bg-black/5 dark:bg-white/5 space-y-4 animate-spatial-in">
            <h4 class="font-black text-lg text-slate-900 dark:text-white">المهام اليومية</h4>
            <p class="text-sm text-slate-600 dark:text-white/70">قائمة المهام اليومية ونسب الإنجاز.</p>
            <div class="flex flex-wrap gap-4">
              <SpatialStatusPill type="completed">فحص السلامة</SpatialStatusPill>
              <SpatialStatusPill type="pending">مراجعة العمال</SpatialStatusPill>
            </div>
          </div>

          <!-- Tab Panel 2 -->
          <div v-if="activeTab === 'tab2'" class="p-6 rounded-[26px] bg-black/5 dark:bg-white/5 space-y-4 animate-spatial-in">
            <h4 class="font-black text-lg text-slate-900 dark:text-white">المهام الإضافية</h4>
            <p class="text-sm text-slate-600 dark:text-white/70">المهام حسب الحاجة.</p>
            <div class="flex flex-wrap gap-4">
              <SpatialStatusPill type="completed">استلام خرسانة</SpatialStatusPill>
            </div>
          </div>

          <!-- Tab Panel 3 -->
          <div v-if="activeTab === 'tab3'" class="p-6 rounded-[26px] bg-black/5 dark:bg-white/5 space-y-4 animate-spatial-in">
            <h4 class="font-black text-lg text-slate-900 dark:text-white">التقارير</h4>
            <p class="text-sm text-slate-600 dark:text-white/70">ملخص الأداء الأسبوعي.</p>
            <div class="progress-bar w-full">
              <div class="fill bg-primary" style="width:88%"></div>
            </div>
            <span class="text-xs font-bold text-slate-900 dark:text-white">نسبة الإنجاز: 88%</span>
          </div>
        </SpatialCard>
      </section>

    </main>

    <!-- ===== MODALS ===== -->
    <SpatialModal
      :show="showConfirmModal"
      title="تأكيد العملية"
      @close="showConfirmModal = false"
    >
      <p class="text-sm font-bold text-slate-500 dark:text-white/60">هل أنت متأكد من تنفيذ هذه العملية؟</p>
      <template #footer>
        <button @click="showConfirmModal = false" class="flex-1 h-11 rounded-[14px] bg-black/10 dark:bg-white/10 font-bold text-sm text-slate-900 dark:text-white cursor-pointer">إلغاء</button>
        <button @click="showConfirmModal = false; notify('success', 'تم التأكيد بنجاح 🎉')" class="flex-1 h-11 rounded-[14px] bg-amber-500 hover:bg-amber-600 text-white font-bold text-sm shadow-md cursor-pointer">تأكيد</button>
      </template>
    </SpatialModal>

    <SpatialModal
      :show="showDeleteModal"
      title="حذف السجل"
      @close="showDeleteModal = false"
    >
      <p class="text-sm font-bold text-slate-500 dark:text-white/60">سيتم حذف السجل ولا يمكن التراجع.</p>
      <template #footer>
        <button @click="showDeleteModal = false" class="flex-1 h-11 rounded-[14px] bg-black/10 dark:bg-white/10 font-bold text-sm text-slate-900 dark:text-white cursor-pointer">إلغاء</button>
        <button @click="showDeleteModal = false; notify('error', 'تم الحذف نهائياً ❌')" class="flex-1 h-11 rounded-[14px] bg-red-600 hover:bg-red-700 text-white font-bold text-sm shadow-md cursor-pointer">حذف</button>
      </template>
    </SpatialModal>

    <SpatialModal
      :show="showDetailModal"
      title="تفاصيل الاستشاري"
      maxWidth="max-w-md"
      @close="showDetailModal = false"
    >
      <div class="space-y-3 text-sm font-bold text-slate-900 dark:text-white">
        <div class="flex justify-between"><span class="text-slate-500 dark:text-white/50">الاسم:</span><span>أحمد السالم</span></div>
        <div class="flex justify-between"><span class="text-slate-500 dark:text-white/50">الرقم:</span><span>#101</span></div>
        <div class="flex justify-between"><span class="text-slate-500 dark:text-white/50">الموقع:</span><span>طرابلس</span></div>
        <div class="flex justify-between"><span class="text-slate-500 dark:text-white/50">نسبة الإنجاز:</span><span class="text-emerald-500 font-black">94%</span></div>
        <div class="flex justify-between"><span class="text-slate-500 dark:text-white/50">الحالة:</span><SpatialStatusPill type="completed">نشط</SpatialStatusPill></div>
      </div>
      <template #footer>
        <button @click="showDetailModal = false" class="w-full h-12 rounded-[16px] bg-primary hover:bg-blue-600 text-white font-bold shadow-md cursor-pointer">إغلاق</button>
      </template>
    </SpatialModal>

    <!-- ===== DRAWER COMPONENT ===== -->
    <SpatialDrawer
      :show="showDrawer"
      title="تعديل بيانات الاستشاري الميداني"
      @close="showDrawer = false"
    >
      <SpatialInput label="الاسم الكامل" model-value="أحمد السالم" required />
      <SpatialInput label="البريد الإلكتروني" model-value="ahmed@example.com" required />
      <SpatialDropdown label="الموقع الميداني" :options="cityOptions" model-value="طرابلس - المركز" required />
      <button @click="showDrawer = false; notify('success', 'تم حفظ التعديلات بنجاح!')" class="w-full h-12 rounded-[16px] bg-primary hover:bg-blue-600 text-white font-bold shadow-md mt-4 cursor-pointer">
        حفظ التغييرات
      </button>
    </SpatialDrawer>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import SpatialCard from '@/Components/Spatial/SpatialCard.vue';
import SpatialButton from '@/Components/Spatial/SpatialButton.vue';
import SpatialInput from '@/Components/Spatial/SpatialInput.vue';
import SpatialDropdown from '@/Components/Spatial/SpatialDropdown.vue';
import SpatialModal from '@/Components/Spatial/SpatialModal.vue';
import SpatialDrawer from '@/Components/Spatial/SpatialDrawer.vue';
import SpatialToast from '@/Components/Spatial/SpatialToast.vue';
import SpatialStatusPill from '@/Components/Spatial/SpatialStatusPill.vue';
import SpatialProgressBar from '@/Components/Spatial/SpatialProgressBar.vue';
import SpatialSkeleton from '@/Components/Spatial/SpatialSkeleton.vue';
import SpatialEmptyState from '@/Components/Spatial/SpatialEmptyState.vue';
import SpatialCheckbox from '@/Components/Spatial/SpatialCheckbox.vue';
import SpatialFloatingBulkBar from '@/Components/Spatial/SpatialFloatingBulkBar.vue';
import SpatialMultiSelect from '@/Components/Spatial/SpatialMultiSelect.vue';
import SpatialImageUpload from '@/Components/Spatial/SpatialImageUpload.vue';

const toastRef = ref(null);
const isDark = ref(true);
const activeTab = ref('tab1');

const formName = ref('أحمد السالم');
const formEmail = ref('ahmed@example.com');
const formPhone = ref('051-234-5678');
const formCity = ref('طرابلس - المركز');
const formStatus = ref('نشط وميداني');

const taskChoice = ref('yes');
const fieldItemCode = ref('B-1024-TR');
const notesText = ref('');
const stepperVal = ref(5);
const surveyDate = ref('2026-08-04');
const workDoneVal = ref('yes');
const failReason = ref('');

const checklistItems = ref([
  { title: 'فحص صب الخرسانة المسلحة', checked: true },
  { title: 'معاينة عزل القواعد السفلية', checked: true },
  { title: 'اختبار عينات حديد التسليح', checked: true },
  { title: 'اعتماد مخططات التكييف والتهوية', checked: false }
]);

const checklistPct = computed(() => {
  const checked = checklistItems.value.filter(i => i.checked).length;
  return Math.round((checked / checklistItems.value.length) * 100);
});

const btnLoading = ref(false);

const selectedTags = ref(['active', 'featured']);
const tagOptions = [
  { label: 'نشط', value: 'active' },
  { label: 'متميز', value: 'featured' },
  { label: 'ميداني', value: 'field' },
];

const showSkeleton = ref(false);
const showEmptyState = ref(false);
const showConfirmModal = ref(false);
const showDeleteModal = ref(false);
const showDetailModal = ref(false);
const showDrawer = ref(false);

const searchQuery = ref('');
const selectAllMaster = ref(false);
const selectedRows = ref([]);

const cityOptions = [
  { label: 'طرابلس - المركز', value: 'طرابلس - المركز' },
  { label: 'بنغازي - الكويفية', value: 'بنغازي - الكويفية' },
  { label: 'مصراتة - المنطقة الحرة', value: 'مصراتة - المنطقة الحرة' },
];

const statusOptions = [
  { label: 'نشط وميداني', value: 'نشط وميداني' },
  { label: 'موقوف مؤقتاً', value: 'موقوف مؤقتاً' },
];

const consultants = ref([
  { id: 1, code: '#101', name: 'أحمد السالم', city: 'طرابلس', pct: 94, statusType: 'completed', statusText: 'نشط' },
  { id: 2, code: '#102', name: 'محمد الفيتوري', city: 'بنغازي', pct: 78, statusType: 'pending', statusText: 'قيد' },
  { id: 3, code: '#103', name: 'علي بن عمر', city: 'مصراتة', pct: 52, statusType: 'overdue', statusText: 'موقوف' },
  { id: 4, code: '#104', name: 'سامي التومي', city: 'سبها', pct: 100, statusType: 'completed', statusText: 'متميز' },
]);

const filteredConsultants = computed(() => {
  if (!searchQuery.value) return consultants.value;
  return consultants.value.filter(c =>
    c.name.includes(searchQuery.value) || c.city.includes(searchQuery.value) || c.code.includes(searchQuery.value)
  );
});

function toggleTheme() {
  isDark.value = !isDark.value;
  if (isDark.value) {
    document.documentElement.classList.add('dark');
    document.body.classList.remove('body-bg-light');
    document.body.classList.add('body-bg-dark');
  } else {
    document.documentElement.classList.remove('dark');
    document.body.classList.remove('body-bg-dark');
    document.body.classList.add('body-bg-light');
  }
}

onMounted(() => {
  document.documentElement.classList.add('dark');
  document.body.classList.add('body-bg-dark');
});

function notify(type, msg) {
  toastRef.value?.addToast(type, msg);
}

function simulateSubmit() {
  btnLoading.value = true;
  setTimeout(() => {
    btnLoading.value = false;
    notify('success', 'تم الحفظ والإرسال بنجاح! 🎉');
  }, 1000);
}

function toggleSelectAll(val) {
  if (val) {
    selectedRows.value = consultants.value.map(c => c.id);
  } else {
    selectedRows.value = [];
  }
}

function toggleRow(id) {
  if (selectedRows.value.includes(id)) {
    selectedRows.value = selectedRows.value.filter(rId => rId !== id);
  } else {
    selectedRows.value.push(id);
  }
  selectAllMaster.value = selectedRows.value.length === consultants.value.length;
}

function unselectAllRows() {
  selectedRows.value = [];
  selectAllMaster.value = false;
}

function handleUploadedImage(file) {
  if (file) {
    notify('success', 'تم رفع الملف بنجاح ✓');
  }
}
</script>
