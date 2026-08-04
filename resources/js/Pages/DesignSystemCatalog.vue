<template>
  <div class="min-h-screen body-bg-dark text-white p-6 lg:p-10 space-y-12 custom-scroll">
    
    <!-- ===== TOAST CONTAINER COMPONENT ===== -->
    <SpatialToast ref="toastRef" />

    <!-- ===== FLOATING BULK BAR COMPONENT ===== -->
    <SpatialFloatingBulkBar
      :selected-count="selectedRows.length"
      @clear="unselectAllRows"
    >
      <SpatialButton variant="primary" class="!py-1.5 !px-3.5 !text-xs" @click="notify('success', 'تم طباعة العناصر المحددة')">
        طباعة
      </SpatialButton>
      <SpatialButton variant="success" class="!py-1.5 !px-3.5 !text-xs" @click="notify('info', 'تم تصدير البيانات بنجاح')">
        تصدير
      </SpatialButton>
      <SpatialButton variant="danger" class="!py-1.5 !px-3.5 !text-xs" @click="showDeleteModal = true">
        حذف مجمع
      </SpatialButton>
    </SpatialFloatingBulkBar>

    <!-- ===== HEADER BANNER ===== -->
    <header class="sticky top-0 z-40 backdrop-blur-2xl bg-[#0f172a]/80 border-b border-white/10 px-6 py-4 rounded-2xl flex items-center justify-between shadow-xl mb-8">
      <div class="flex items-center gap-3">
        <div class="w-3 h-3 rounded-full bg-primary animate-pulse"></div>
        <h1 class="text-xl font-black">FCPMS <span class="text-xs px-2.5 py-1 rounded-full bg-primary/20 text-primary font-bold mr-2">Spatial UI v3.0 Vue Components Catalog</span></h1>
      </div>
      <div class="flex items-center gap-3">
        <span class="text-xs font-mono bg-white/10 px-3 py-1.5 rounded-xl border border-white/15 text-blue-400 font-bold">
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
            <h2 class="text-3xl font-black leading-tight text-white">
              كتالوج المكونات الفاخر بتدرجات وأيقونات فائقة الدقة<br>
              <span class="text-base font-medium text-white/50">تم بناء وتحويل كافة مكونات components_catalog.html إلى مكونات Vue 3 مستحدثة وجاهزة للاستخدام التام في المشروع</span>
            </h2>
          </div>
          <SpatialButton @click="showDrawer = true">
            📂 فتح اللوحة الجانبية (Drawer)
          </SpatialButton>
        </div>
      </SpatialCard>

      <!-- ===== SECTION 1: CARDS & KPI ===== -->
      <section class="space-y-6">
        <div class="flex items-center gap-3">
          <span class="w-3.5 h-9 rounded-full bg-primary shadow-lg shadow-primary/40"></span>
          <div>
            <h2 class="text-2xl font-black">1. البطاقات ومؤشرات الأداء (KPI Cards)</h2>
            <p class="text-xs font-bold text-white/50">بطاقات قياس الأداء المتدرجة مع شريط الإنجاز وقالب بطاقة الاستشاري الكامل</p>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <SpatialCard class="flex flex-col justify-between space-y-4">
            <div class="flex items-center justify-between">
              <div class="w-12 h-12 rounded-[18px] bg-primary/15 border border-primary/25 flex items-center justify-center text-primary">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
              </div>
              <span class="text-xs font-black px-3 py-1 rounded-full bg-emerald-500/15 text-emerald-400">+12% ↗</span>
            </div>
            <div><span class="text-xs font-bold text-white/50 uppercase block">الاستشاريون النشطون</span><h3 class="text-2xl font-black">48</h3></div>
            <SpatialProgressBar :percentage="85" color="primary" />
          </SpatialCard>

          <SpatialCard class="flex flex-col justify-between space-y-4">
            <div class="flex items-center justify-between">
              <div class="w-12 h-12 rounded-[18px] bg-emerald-500/15 border border-emerald-500/25 flex items-center justify-center text-emerald-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              </div>
              <span class="text-xs font-black px-3 py-1 rounded-full bg-emerald-500/15 text-emerald-400">+8% ↗</span>
            </div>
            <div><span class="text-xs font-bold text-white/50 uppercase block">نسبة الإنجاز اليومي</span><h3 class="text-2xl font-black">94%</h3></div>
            <SpatialProgressBar :percentage="94" color="success" />
          </SpatialCard>

          <SpatialCard class="flex flex-col justify-between space-y-4">
            <div class="flex items-center justify-between">
              <div class="w-12 h-12 rounded-[18px] bg-amber-500/15 border border-amber-500/25 flex items-center justify-center text-amber-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
              </div>
              <span class="text-xs font-black px-3 py-1 rounded-full bg-amber-500/15 text-amber-400">+3% ↗</span>
            </div>
            <div><span class="text-xs font-bold text-white/50 uppercase block">زيارات اليوم</span><h3 class="text-2xl font-black">142</h3></div>
            <SpatialProgressBar :percentage="78" color="warning" />
          </SpatialCard>

          <SpatialCard class="flex flex-col justify-between space-y-4">
            <div class="flex items-center justify-between">
              <div class="w-12 h-12 rounded-[18px] bg-red-500/15 border border-red-500/25 flex items-center justify-center text-red-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              </div>
              <span class="text-xs font-black px-3 py-1 rounded-full bg-red-500/15 text-red-400">12 غائب</span>
            </div>
            <div><span class="text-xs font-bold text-white/50 uppercase block">الغياب اليوم</span><h3 class="text-2xl font-black">12</h3></div>
            <SpatialProgressBar :percentage="15" color="danger" />
          </SpatialCard>
        </div>

        <!-- FULL CONSULTANT CARD FROM CATALOG -->
        <SpatialCard padding="p-6">
          <div class="flex flex-col md:flex-row items-center gap-6">
            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 text-white flex items-center justify-center text-3xl font-black border-4 border-primary/30 shrink-0 shadow-lg">
              أ
            </div>
            <div class="flex-1 space-y-2">
              <div class="flex items-center gap-3 flex-wrap">
                <h3 class="text-xl font-black">أحمد السالم</h3>
                <SpatialStatusPill type="completed" pulse>نشط وميداني</SpatialStatusPill>
                <span class="text-xs font-mono text-white/50">#EMP-1024</span>
              </div>
              <div class="flex flex-wrap gap-4 text-sm font-bold text-white/70">
                <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg> 051-234-5678</span>
                <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg> طرابلس</span>
                <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> 01-01-2024</span>
              </div>
            </div>
            <div class="flex gap-2 shrink-0">
              <SpatialButton @click="showDetailModal = true">عرض الملف</SpatialButton>
              <SpatialButton variant="secondary" @click="showDrawer = true">تعديل</SpatialButton>
            </div>
          </div>
        </SpatialCard>
      </section>

      <!-- ===== SECTION 2: INTERACTIVE DATA TABLE & SKELETON ===== -->
      <section class="space-y-6">
        <div class="flex items-center gap-3">
          <span class="w-3.5 h-9 rounded-full bg-primary shadow-lg shadow-primary/40"></span>
          <div>
            <h2 class="text-2xl font-black">2. الجداول التفاعلية وشريط التحديد العائم</h2>
            <p class="text-xs font-bold text-white/50">جدول البيانات التفاعلي مع تظليل الصفوف المحددة وشريط الإجراءات المجمعة Floating Bulk Bar</p>
          </div>
        </div>

        <SpatialCard padding="p-0 overflow-hidden">
          <!-- Table Header controls -->
          <div class="p-6 border-b border-white/10 flex flex-col md:flex-row items-center justify-between gap-4">
            <h3 class="font-black text-lg">قائمة الاستشاريين الميدانيين</h3>
            <div class="flex flex-wrap items-center gap-3">
              <input
                type="text"
                v-model="searchQuery"
                placeholder="بحث عن استشاري..."
                class="spatial-input h-10 rounded-[14px] px-4 text-xs font-bold w-56"
              />
              <SpatialButton variant="secondary" class="!py-1.5 !px-3 !text-xs" @click="showSkeleton = !showSkeleton">
                💀 Skeleton Toggle
              </SpatialButton>
              <SpatialButton variant="secondary" class="!py-1.5 !px-3 !text-xs" @click="showEmptyState = !showEmptyState">
                📂 Empty State Toggle
              </SpatialButton>
            </div>
          </div>

          <!-- Skeleton Loader Display -->
          <div v-if="showSkeleton" class="p-6 space-y-3">
            <SpatialSkeleton height="h-8" />
            <SpatialSkeleton height="h-14" />
            <SpatialSkeleton height="h-14" />
            <SpatialSkeleton height="h-14" />
          </div>

          <!-- Empty State Display -->
          <div v-else-if="showEmptyState" class="p-8">
            <SpatialEmptyState
              title="لا توجد نتائج متطابقة"
              description="جرب البحث بكلمات مختلفة أو إعادة ضبط عوامل التصفية."
            >
              <template #action>
                <SpatialButton @click="showEmptyState = false">إعادة تعيين</SpatialButton>
              </template>
            </SpatialEmptyState>
          </div>

          <!-- Real Table Content -->
          <div v-else class="overflow-x-auto">
            <table class="w-full text-right text-sm">
              <thead class="bg-white/5 text-white/60 font-bold uppercase text-xs">
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
              <tbody class="divide-y divide-white/5 font-bold text-white">
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
                      <span class="font-black" :class="row.pct >= 80 ? 'text-emerald-400' : 'text-amber-400'">{{ row.pct }}%</span>
                      <SpatialProgressBar :percentage="row.pct" :color="row.pct >= 80 ? 'success' : 'warning'" class="w-20" />
                    </div>
                  </td>
                  <td class="p-4">
                    <SpatialStatusPill :type="row.statusType">{{ row.statusText }}</SpatialStatusPill>
                  </td>
                  <td class="p-4 text-center">
                    <SpatialButton variant="icon" class="!w-9 !h-9" @click="showDetailModal = true">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </SpatialButton>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </SpatialCard>
      </section>

      <!-- ===== SECTION 3: FORMS, DROPDOWNS (56px) & BUTTONS ===== -->
      <section class="space-y-6">
        <div class="flex items-center gap-3">
          <span class="w-3.5 h-9 rounded-full bg-primary shadow-lg shadow-primary/40"></span>
          <div>
            <h2 class="text-2xl font-black">3. النماذج، القوائم المنسدلة (56px)، والأزرار</h2>
            <p class="text-xs font-bold text-white/50">تفاعل القوائم المنسدلة القياسية وحقول الإدخال مع شارات التحقق وحالات الأزرار</p>
          </div>
        </div>

        <SpatialCard class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <SpatialInput
              v-model="formName"
              label="الاسم الكامل"
              placeholder="مثال: أحمد السالم"
              required
              successMsg="تم التحقق"
            />
            <SpatialInput
              v-model="formEmail"
              label="البريد الإلكتروني"
              placeholder="name@domain.com"
              required
              error="البريد الإلكتروني غير صالح"
            />
            <SpatialInput
              v-model="formPhone"
              label="رقم الهاتف"
              placeholder="051-234-5678"
            />
            <SpatialDropdown
              v-model="formCity"
              label="الموقع الميداني (56px)"
              :options="cityOptions"
              required
            />
          </div>

          <div class="flex flex-wrap items-center gap-4 pt-4 border-t border-white/10">
            <SpatialButton @click="notify('success', 'تم حفظ البيانات بنجاح!')">
              حفظ البيانات
            </SpatialButton>
            <SpatialButton variant="success" :loading="btnLoading" @click="simulateSubmit">
              حفظ وإرسال
            </SpatialButton>
            <SpatialButton variant="danger" @click="showConfirmModal = true">
              تأكيد العملية
            </SpatialButton>
            <SpatialButton variant="secondary" @click="notify('warning', 'تحذير من نظام الإشعارات')">
              تنبيه تحذيري
            </SpatialButton>
          </div>
        </SpatialCard>
      </section>

      <!-- ===== SECTION 4: DYNAMIC TASK BUILDER COMPONENTS ===== -->
      <section class="space-y-6">
        <div class="flex items-center gap-3">
          <span class="w-3.5 h-9 rounded-full bg-emerald-500 shadow-lg shadow-emerald-500/40"></span>
          <div>
            <h2 class="text-2xl font-black">4. مكونات المهام الديناميكية الميدانية</h2>
            <p class="text-xs font-bold text-white/50">بطاقات الخيارات، القوائم التفقدية، رفع الصور، والحقول الشرطية الذكية</p>
          </div>
        </div>

        <SpatialCard class="space-y-8">
          <!-- 4.1 Task Choice Option Cards -->
          <div class="space-y-4 border-b border-white/10 pb-6">
            <h3 class="font-black text-lg text-white flex items-center gap-2">
              <span class="w-3 h-3 rounded-full bg-primary"></span>
              1. اختيار حالة التنفيذ (TaskOptionCard)
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <SpatialTaskOptionCard
                title="تم التنفيذ بالكامل"
                subtitle="الأعمال مطابقة للمواصفات"
                pillText="مكتمل"
                pillType="completed"
                :selected="selectedTaskOpt === 'yes'"
                @select="selectedTaskOpt = 'yes'"
              />
              <SpatialTaskOptionCard
                title="تنفيذ جزئي"
                subtitle="تتطلب التقرير القادم"
                pillText="جزئي"
                pillType="pending"
                :selected="selectedTaskOpt === 'partial'"
                @select="selectedTaskOpt = 'partial'"
              />
              <SpatialTaskOptionCard
                title="لم يتم التنفيذ"
                subtitle="يتطلب السبب والصورة"
                pillText="متوقف"
                pillType="overdue"
                :selected="selectedTaskOpt === 'no'"
                @select="selectedTaskOpt = 'no'"
              />
            </div>
          </div>

          <!-- 4.2 Interactive Checklist -->
          <div class="space-y-4 border-b border-white/10 pb-6">
            <div class="flex items-center justify-between">
              <h3 class="font-black text-lg text-white flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                2. قائمة المراجعة التفقدية (Checklist)
              </h3>
              <span class="text-xs font-black px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400">
                الإنجاز: {{ checklistPct }}%
              </span>
            </div>
            <SpatialProgressBar :percentage="checklistPct" color="success" />
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
              <label
                v-for="(item, idx) in checklistItems"
                :key="idx"
                class="flex items-center justify-between p-3.5 rounded-[16px] bg-white/5 border border-white/10 cursor-pointer"
              >
                <span class="flex items-center gap-3 text-sm font-bold">
                  <SpatialCheckbox v-model="item.checked" />
                  {{ item.label }}
                </span>
                <span class="text-xs font-bold" :class="item.checked ? 'text-emerald-400' : 'text-amber-400'">
                  {{ item.checked ? 'مكفول ✓' : 'قيد الفحص ⏱' }}
                </span>
              </label>
            </div>
          </div>

          <!-- 4.3 Image Upload Dropzone -->
          <div class="space-y-4 border-b border-white/10 pb-6">
            <h3 class="font-black text-lg text-white flex items-center gap-2">
              <span class="w-3 h-3 rounded-full bg-amber-500"></span>
              3. مرفقات الصور والإثباتات الميدانية (Image Upload)
            </h3>
            <SpatialImageUpload @file-selected="file => notify('success', `تم رفع الصورة: ${file.name}`)" />
          </div>

          <!-- 4.4 Smart Conditional Field -->
          <div class="space-y-4">
            <h3 class="font-black text-lg text-white flex items-center gap-2">
              <span class="w-3 h-3 rounded-full bg-red-500"></span>
              4. الحقول الشرطية الذكية (Adaptive Conditional Fields)
            </h3>
            <div class="p-5 rounded-[24px] bg-white/5 space-y-4">
              <SpatialDropdown
                v-model="conditionalChoice"
                label="هل توجد ملاحظات أو توقف بالموقع؟"
                :options="[
                  { label: 'لا توجد ملاحظات', value: 'no' },
                  { label: 'نعم، يوجد عائق ميداني يمنع العمل', value: 'yes' }
                ]"
              />
              <div v-if="conditionalChoice === 'yes'" class="p-4 rounded-[20px] bg-red-500/10 border-r-4 border-red-500 space-y-3 animate-spatial-in">
                <div class="text-red-400 font-black text-xs">⚠️ يجب كتابة السبب وإرفاق إثبات بالصورة قبل الحفظ</div>
                <SpatialInput label="سبب التوقف بالتفصيل" placeholder="اكتب عائق العمل الميداني..." required />
              </div>
            </div>
          </div>
        </SpatialCard>
      </section>

      <!-- ===== SECTION 5: MODALS, DRAWERS & TOAST TRIGGER ===== -->
      <section class="space-y-6">
        <div class="flex items-center gap-3">
          <span class="w-3.5 h-9 rounded-full bg-purple-500 shadow-lg shadow-purple-500/40"></span>
          <div>
            <h2 class="text-2xl font-black">5. النوافذ واللوحات الإشعارية (Modals & Drawers)</h2>
            <p class="text-xs font-bold text-white/50">تأكيد العمليات، النوافذ المنبثقة، واللوحة المنزلقة السريعة</p>
          </div>
        </div>

        <SpatialCard class="flex flex-wrap gap-4">
          <SpatialButton @click="showConfirmModal = true">فتح نافذة التأكيد</SpatialButton>
          <SpatialButton variant="danger" @click="showDeleteModal = true">فتح نافذة الحذف النهائي</SpatialButton>
          <SpatialButton variant="secondary" @click="showDetailModal = true">عرض تفاصيل الاستشاري</SpatialButton>
          <SpatialButton variant="success" @click="showDrawer = true">فتح Drawer تعديل البيانات</SpatialButton>
        </SpatialCard>
      </section>

    </main>

    <!-- ===== MODALS COMPONENTS ===== -->
    <SpatialModal
      :show="showConfirmModal"
      title="تأكيد العملية"
      @close="showConfirmModal = false"
    >
      <p class="text-sm font-bold text-white/70">هل أنت متأكد من تنفيذ هذه العملية وحفظ البيانات بالكامل؟</p>
      <template #footer>
        <SpatialButton variant="secondary" class="flex-1" @click="showConfirmModal = false">إلغاء</SpatialButton>
        <SpatialButton variant="primary" class="flex-1" @click="showConfirmModal = false; notify('success', 'تم تأكيد العملية بنجاح')">تأكيد</SpatialButton>
      </template>
    </SpatialModal>

    <SpatialModal
      :show="showDeleteModal"
      title="حذف سجل السجلات"
      @close="showDeleteModal = false"
    >
      <p class="text-sm font-bold text-red-400">سيتم حذف العناصر ولا يمكن التراجع بعد ذلك.</p>
      <template #footer>
        <SpatialButton variant="secondary" class="flex-1" @click="showDeleteModal = false">إلغاء</SpatialButton>
        <SpatialButton variant="danger" class="flex-1" @click="showDeleteModal = false; notify('error', 'تم الحذف نهائياً')">حذف الآن</SpatialButton>
      </template>
    </SpatialModal>

    <SpatialModal
      :show="showDetailModal"
      title="تفاصيل ملف الاستشاري"
      maxWidth="max-w-lg"
      @close="showDetailModal = false"
    >
      <div class="space-y-3 text-sm font-bold">
        <div class="flex justify-between border-b border-white/10 pb-2"><span class="text-white/50">الاسم الكامل:</span><span>أحمد السالم</span></div>
        <div class="flex justify-between border-b border-white/10 pb-2"><span class="text-white/50">الرقم الوظيفي:</span><span class="font-mono">#EMP-1024</span></div>
        <div class="flex justify-between border-b border-white/10 pb-2"><span class="text-white/50">الموقع الميداني:</span><span>طرابلس</span></div>
        <div class="flex justify-between border-b border-white/10 pb-2"><span class="text-white/50">نسبة الإنجاز اليومي:</span><span class="text-emerald-400 font-black">94%</span></div>
        <div class="flex justify-between"><span class="text-white/50">الحالة:</span><SpatialStatusPill type="completed">نشط</SpatialStatusPill></div>
      </div>
      <template #footer>
        <SpatialButton class="w-full" @click="showDetailModal = false">إغلاق</SpatialButton>
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
      <SpatialInput label="رقم الهاتف" model-value="051-234-5678" />
      <SpatialDropdown label="الموقع الميداني" :options="cityOptions" model-value="tripoli" required />
      <SpatialButton class="w-full mt-4" @click="showDrawer = false; notify('success', 'تم حفظ التعديلات بنجاح!')">
        حفظ التغييرات
      </SpatialButton>
    </SpatialDrawer>

  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
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
import SpatialSwitch from '@/Components/Spatial/SpatialSwitch.vue';
import SpatialCheckbox from '@/Components/Spatial/SpatialCheckbox.vue';
import SpatialTaskOptionCard from '@/Components/Spatial/SpatialTaskOptionCard.vue';
import SpatialImageUpload from '@/Components/Spatial/SpatialImageUpload.vue';
import SpatialFloatingBulkBar from '@/Components/Spatial/SpatialFloatingBulkBar.vue';

const toastRef = ref(null);

const formName = ref('أحمد السالم');
const formEmail = ref('ahmed@example.com');
const formPhone = ref('051-234-5678');
const formCity = ref('tripoli');

const selectedTaskOpt = ref('yes');
const conditionalChoice = ref('no');
const switchVal = ref(true);
const btnLoading = ref(false);

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
  { label: 'طرابلس', value: 'tripoli' },
  { label: 'بنغازي', value: 'benghazi' },
  { label: 'مصراتة', value: 'misrata' },
  { label: 'سبها', value: 'sebha' },
];

const checklistItems = ref([
  { label: 'فحص صب الخرسانة المسلحة', checked: true },
  { label: 'معاينة عزل القواعد السفلية', checked: true },
  { label: 'اختبار عينات حديد التسليح', checked: true },
  { label: 'اعتماد مخططات التكييف والتهوية', checked: false },
]);

const checklistPct = computed(() => {
  const checkedCount = checklistItems.value.filter(i => i.checked).length;
  return Math.round((checkedCount / checklistItems.value.length) * 100);
});

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

function notify(type, msg) {
  toastRef.value?.addToast(type, msg);
}

function simulateSubmit() {
  btnLoading.value = true;
  setTimeout(() => {
    btnLoading.value = false;
    notify('success', 'تم الحفظ والإرسال بنجاح!');
  }, 1200);
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
</script>
