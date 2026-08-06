<template>
  <HRLayout>
    <!-- Spatial Toast Notifications -->
    <SpatialToast ref="toastRef" />

    <div class="space-y-6">

      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-black text-slate-900 dark:text-white flex items-center gap-2.5">
            <span>🗄️</span>
            <span>نظام النسخ الاحتياطي والحوكمة</span>
          </h1>
          <p class="text-xs font-bold text-slate-500 dark:text-white/60 mt-1">
            إدارة، إنشاء، واستعادة نسخ قاعدة البيانات للتعافي من الكوارث وحفظ سجلات FCPMS
          </p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
          <SpatialButton variant="secondary" @click="showUpload = true" class="font-black">
            <svg class="w-4 h-4 text-slate-600 dark:text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
            <span>رفع نسخة خارجية</span>
          </SpatialButton>

          <SpatialButton variant="primary" @click="showCreate = true" class="font-black shadow-lg shadow-blue-500/20">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            <span>إنشاء نسخة احتياطية جديدة</span>
          </SpatialButton>
        </div>
      </div>

      <!-- Overview KPI Stats Grid -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <SpatialCard padding="p-4" class="space-y-1 border-l-4 border-l-blue-500">
          <span class="text-[11px] font-bold text-slate-400 dark:text-white/50 block">إجمالي النسخ المحفوظة</span>
          <span class="text-2xl font-black text-slate-900 dark:text-white">{{ backups.length }}</span>
        </SpatialCard>

        <SpatialCard padding="p-4" class="space-y-1 border-l-4 border-l-emerald-500">
          <span class="text-[11px] font-bold text-slate-400 dark:text-white/50 block">حالة قاعدة البيانات</span>
          <div class="flex items-center gap-1.5 pt-0.5">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping shrink-0"></span>
            <span class="text-sm font-black text-emerald-600 dark:text-emerald-400">نشطة وسليمة 🟢</span>
          </div>
        </SpatialCard>

        <SpatialCard padding="p-4" class="space-y-1 border-l-4 border-l-purple-500">
          <span class="text-[11px] font-bold text-slate-400 dark:text-white/50 block">أحدث نسخة احتياطية</span>
          <span class="text-xs font-black text-slate-900 dark:text-white font-mono block truncate">
            {{ latestBackupDate }}
          </span>
        </SpatialCard>

        <SpatialCard padding="p-4" class="space-y-1.5 border-l-4 border-l-amber-500">
          <div class="flex items-center justify-between">
            <span class="text-[11px] font-bold text-slate-400 dark:text-white/50 block">موقع التخزين التشغيلي</span>
            <span class="text-[10px] font-black text-amber-600 dark:text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded-md">انقر للفتح 📁</span>
          </div>
          <button
            type="button"
            @click="handleOpenFolder"
            title="انقر لفتح مجلد النسخ الاحتياطية مباشرة ونسخ مساره"
            class="w-full text-right group flex items-center justify-between p-2 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 text-amber-700 dark:text-amber-300 font-mono text-xs font-black border border-amber-500/20 transition-all cursor-pointer shadow-xs active:scale-95"
          >
            <span class="dir-ltr truncate">storage/app/backups</span>
            <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 group-hover:scale-110 transition-transform shrink-0 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
            </svg>
          </button>
        </SpatialCard>
      </div>

      <!-- Main Content Container: High-Fidelity Empty State OR Data Table -->
      <SpatialCard padding="p-0 overflow-hidden" class="relative z-10">

        <!-- 🌟 High-Fidelity Glassmorphism Empty State -->
        <div v-if="backups.length === 0" class="p-8 sm:p-14 text-center relative overflow-hidden bg-gradient-to-b from-slate-50/60 via-white to-slate-50/40 dark:from-slate-800/80 dark:via-slate-800/40 dark:to-slate-900/90">
          <!-- Background Subtle Glow Effect -->
          <div class="absolute -top-24 left-1/2 -translate-x-1/2 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

          <!-- Empty State Core Content -->
          <div class="relative z-10 max-w-xl mx-auto space-y-6">
            
            <!-- Animated Glowing Database Icon Ring -->
            <div class="relative flex items-center justify-center w-24 h-24 mx-auto rounded-3xl bg-gradient-to-tr from-blue-600/20 via-indigo-500/15 to-purple-500/20 border border-blue-500/30 shadow-2xl shadow-blue-500/15 group">
              <div class="absolute inset-0 rounded-3xl bg-blue-500/10 blur-xl group-hover:bg-blue-500/20 transition-all duration-300"></div>
              <svg class="w-12 h-12 text-blue-600 dark:text-blue-400 relative z-10 transform group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21 3.582 4 8 4s8-1.79 8-4" />
              </svg>
            </div>

            <!-- Title & Narrative Description -->
            <div class="space-y-2">
              <h3 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                لا توجد نسخ احتياطية مسجلة بالنظام بعد
              </h3>
              <p class="text-xs sm:text-sm font-bold text-slate-600 dark:text-white/60 leading-relaxed max-w-lg mx-auto">
                قم بإنشاء أول نسخة احتياطية لقاعدة البيانات وحفظها بأمان للحفاظ على سجلات الزيارات الميدانية، المستندات، واستجابات الاستشاريين من أي فقدان.
              </p>
            </div>

            <!-- Feature Guarantee Badges -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 text-xs">
              <div class="p-3 rounded-2xl bg-white/80 dark:bg-white/5 border border-slate-200/80 dark:border-white/10 shadow-2xs flex flex-col items-center gap-1.5">
                <span class="text-base">🛡️</span>
                <span class="font-black text-slate-800 dark:text-white">حماية وتشفير كاملا</span>
                <span class="text-[10px] text-slate-500 dark:text-white/50 font-bold">تغطية كافّة الجداول</span>
              </div>

              <div class="p-3 rounded-2xl bg-white/80 dark:bg-white/5 border border-slate-200/80 dark:border-white/10 shadow-2xs flex flex-col items-center gap-1.5">
                <span class="text-base">⚡</span>
                <span class="font-black text-slate-800 dark:text-white">تصدير مضغوط .zip</span>
                <span class="text-[10px] text-slate-500 dark:text-white/50 font-bold">ملفات خفيفة ومحمية</span>
              </div>

              <div class="p-3 rounded-2xl bg-white/80 dark:bg-white/5 border border-slate-200/80 dark:border-white/10 shadow-2xs flex flex-col items-center gap-1.5">
                <span class="text-base">🔄</span>
                <span class="font-black text-slate-800 dark:text-white">استعادة فورية سهلة</span>
                <span class="text-[10px] text-slate-500 dark:text-white/50 font-bold">بضغطة زر واحدة</span>
              </div>
            </div>

            <!-- Prominent Primary Call to Action Button -->
            <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-3">
              <SpatialButton
                variant="primary"
                @click="showCreate = true"
                class="w-full sm:w-auto h-13 px-8 font-black text-sm shadow-xl shadow-blue-500/25 active:scale-95 transition-all"
              >
                <svg class="w-5 h-5 ml-1 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>إنشاء أول نسخة احتياطية الآن</span>
              </SpatialButton>

              <SpatialButton
                variant="secondary"
                @click="showUpload = true"
                class="w-full sm:w-auto h-13 px-6 font-black text-xs"
              >
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                <span>رفع نسخة احتياطية من جهازك</span>
              </SpatialButton>
            </div>

          </div>
        </div>

        <!-- 📊 Backups Data Table (When backups exist) -->
        <div v-else class="overflow-x-auto custom-scroll">
          <table class="w-full text-right border-collapse">
            <thead>
              <tr class="border-b border-slate-200 dark:border-white/10 text-xs font-black text-slate-600 dark:text-white/70 bg-slate-100/90 dark:bg-white/5">
                <th class="p-4">اسم ملف النسخة (.zip)</th>
                <th class="p-4">تاريخ الإنشاء والتوقيت</th>
                <th class="p-4">الحجم التخزيني</th>
                <th class="p-4">ملاحظة مرجعية</th>
                <th class="p-4 text-center w-40">الإجراءات المتاحة</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-sm font-bold">
              <tr
                v-for="backup in backups"
                :key="backup.filename"
                class="transition-colors hover:bg-slate-50/80 dark:hover:bg-white/5"
              >
                <!-- Filename -->
                <td class="p-4">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-2xl bg-blue-500/10 dark:bg-blue-400/15 border border-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7M9 11l3 3 3-3M12 3v11"/>
                      </svg>
                    </div>
                    <div>
                      <span class="font-mono text-xs font-black text-slate-900 dark:text-white dir-ltr inline-block">
                        {{ backup.filename }}
                      </span>
                    </div>
                  </div>
                </td>

                <!-- Date -->
                <td class="p-4">
                  <span class="inline-block px-3 py-1 rounded-xl bg-slate-100 dark:bg-white/10 text-slate-800 dark:text-white/90 text-xs font-mono font-black border border-black/5 dark:border-white/10 dir-ltr">
                    {{ backup.date ?? '—' }}
                  </span>
                </td>

                <!-- Size -->
                <td class="p-4">
                  <span class="inline-block px-3 py-1 rounded-xl bg-blue-500/10 text-blue-700 dark:text-blue-300 font-mono font-black text-xs border border-blue-500/20">
                    {{ backup.size }}
                  </span>
                </td>

                <!-- Note -->
                <td class="p-4 text-slate-600 dark:text-white/70 text-xs max-w-[220px] truncate">
                  {{ backup.note ?? 'بدون ملاحظات' }}
                </td>

                <!-- Actions -->
                <td class="p-4 text-center">
                  <div class="flex items-center justify-center gap-2">
                    <!-- Download Link -->
                    <a
                      :href="route('admin.backups.download', backup.filename)"
                      title="تحميل الملف إلى جهازك"
                      class="spatial-icon-button w-9 h-9 transition-all duration-200 flex items-center justify-center rounded-2xl border border-blue-500/30 bg-blue-500/10 text-blue-600 dark:text-blue-400 hover:bg-blue-600 hover:text-white dark:hover:bg-blue-500 shadow-xs active:scale-95 cursor-pointer"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                      </svg>
                    </a>

                    <!-- Restore Button -->
                    <SpatialIconButton
                      variant="warning"
                      title="استعادة هذه النسخة"
                      @click="openRestoreModal(backup)"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                      </svg>
                    </SpatialIconButton>

                    <!-- Delete Button -->
                    <SpatialIconButton
                      variant="danger"
                      title="حذف النسخة الاحتياطية"
                      @click="openDeleteModal(backup)"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
    </div>

    <!-- ─── Modal: إنشاء نسخة احتياطية ─────────────────────────────────── -->
    <SpatialModal
      :isOpen="showCreate"
      title="إنشاء نسخة احتياطية جديدة"
      maxWidth="md"
      @close="showCreate = false"
    >
      <div class="space-y-4 text-right">
        <p class="text-xs font-bold text-slate-600 dark:text-white/70 leading-relaxed">
          سيتم إنشاء نسخة احتياطية مضغوطة شاملة لكافّة جداول وبيانات قاعدة البيانات وحفظها بأمان داخل النظام.
        </p>

        <div class="space-y-1.5">
          <label class="text-xs font-black text-slate-700 dark:text-white/90">
            ملاحظة مرجعية <span class="text-slate-400 dark:text-white/40 font-bold">(اختياري)</span>
          </label>
          <textarea
            v-model="createNote"
            rows="3"
            placeholder="مثال: نسخة احتياطية قبل تعديل الجداول أو ترقية النظام..."
            class="spatial-input w-full p-3.5 rounded-2xl text-xs font-bold bg-transparent resize-none"
          ></textarea>
        </div>
      </div>

      <template #footer>
        <div class="flex items-center justify-end gap-3">
          <SpatialButton variant="ghost" @click="showCreate = false">إلغاء</SpatialButton>
          <SpatialButton variant="primary" :loading="createForm.processing" @click="submitCreate" class="font-black">
            <svg v-if="!createForm.processing" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
            <span>{{ createForm.processing ? 'جارٍ الإنشاء...' : 'بدء الإنشاء والحفظ' }}</span>
          </SpatialButton>
        </div>
      </template>
    </SpatialModal>

    <!-- ─── Modal: رفع نسخة خارجية ──────────────────────────────────────── -->
    <SpatialModal
      :isOpen="showUpload"
      title="رفع نسخة احتياطية خارجية"
      maxWidth="md"
      @close="showUpload = false"
    >
      <div class="space-y-4 text-right">
        <p class="text-xs font-bold text-slate-600 dark:text-white/70 leading-relaxed">
          قم برفع ملف <span class="font-black text-blue-600 dark:text-blue-400 font-mono">.zip</span> نسخة احتياطية سابقة. الحد الأقصى للملف 512MB.
        </p>

        <!-- Drop Zone -->
        <div
          @click="fileInputRef.click()"
          class="flex flex-col items-center justify-center gap-3 p-8 rounded-3xl border-2 border-dashed border-slate-300 dark:border-white/20 hover:border-blue-500/60 dark:hover:border-blue-400/60 hover:bg-blue-500/5 transition-all cursor-pointer text-center"
        >
          <div class="w-12 h-12 rounded-2xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
          </div>

          <div v-if="uploadFile" class="text-center">
            <p class="font-black text-slate-800 dark:text-white text-xs font-mono break-all">{{ uploadFile.name }}</p>
            <p class="text-[11px] font-mono font-bold text-blue-600 dark:text-blue-400 mt-1">{{ (uploadFile.size / 1048576).toFixed(2) }} MB</p>
          </div>
          <p v-else class="font-black text-slate-600 dark:text-white/60 text-xs">اضغط لاختيار ملف النسخة الاحتياطية (.zip)</p>
          <input ref="fileInputRef" type="file" accept=".zip" class="hidden" @change="onFileChange" />
        </div>
      </div>

      <template #footer>
        <div class="flex items-center justify-end gap-3">
          <SpatialButton variant="ghost" @click="showUpload = false">إلغاء</SpatialButton>
          <SpatialButton
            variant="primary"
            :disabled="!uploadFile"
            :loading="uploadForm.processing"
            @click="submitUpload"
            class="font-black"
          >
            <span>{{ uploadForm.processing ? 'جارٍ الرفع...' : 'تأكيد الرفع' }}</span>
          </SpatialButton>
        </div>
      </template>
    </SpatialModal>

    <!-- ─── Modal: استعادة نسخة احتياطية ────────────────────────────────── -->
    <SpatialModal
      :isOpen="showRestore"
      :title="restoreStep === 'loading' ? 'جارٍ تنفيذ استعادة قاعدة البيانات...' : 'تأكيد استعادة النسخة الاحتياطية'"
      maxWidth="md"
      @close="closeRestoreModal"
    >
      <!-- Step: loading -->
      <div v-if="restoreStep === 'loading'" class="flex flex-col items-center gap-4 py-4 text-center">
        <div class="w-16 h-16 rounded-3xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center">
          <svg class="w-8 h-8 text-amber-500 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
          </svg>
        </div>
        <p class="text-xs font-black text-amber-600 dark:text-amber-400">يرجى الانتظار وعدم إغلاق المتصفح أثناء الاستعادة...</p>
        <SpatialProgressBar :value="restoreProgress" class="w-full" />
        <span class="text-xs font-mono font-black text-blue-600 dark:text-blue-400">{{ Math.round(restoreProgress) }}%</span>
      </div>

      <!-- Step: confirm1 -->
      <div v-else-if="restoreStep === 'confirm1'" class="space-y-4 text-right">
        <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-700 dark:text-amber-300 text-xs font-bold leading-relaxed">
          ⚠️ سيتم استبدال كافّة البيانات الحالية في النظام بالكامل ببيانات هذه النسخة. هذا الإجراء حسّاس جداً.
        </div>
        <div class="p-3.5 rounded-2xl bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10">
          <span class="text-xs font-black text-slate-500 dark:text-white/50 block mb-1">الملف المحدد للاستعادة:</span>
          <p class="font-mono font-black text-slate-900 dark:text-white text-xs break-all">{{ activeBackup?.filename }}</p>
        </div>
      </div>

      <!-- Step: confirm2 -->
      <div v-else-if="restoreStep === 'confirm2'" class="space-y-4 text-right">
        <div class="p-4 rounded-2xl bg-rose-500/15 border border-rose-500/30 text-rose-700 dark:text-rose-300 text-xs font-black leading-relaxed">
          🚨 تأكيد نهائي — ستُحذف البيانات الحالية كلياً ويحل محلها المحتوى المخزن في النسخة المحددة.
        </div>
      </div>

      <template #footer>
        <div class="flex items-center justify-end gap-3">
          <!-- footer: confirm1 -->
          <template v-if="restoreStep === 'confirm1'">
            <SpatialButton variant="ghost" @click="closeRestoreModal">إلغاء</SpatialButton>
            <SpatialButton variant="warning" @click="restoreStep = 'confirm2'" class="font-black">
              <span>متابعة للاستعادة</span>
            </SpatialButton>
          </template>

          <!-- footer: confirm2 -->
          <template v-else-if="restoreStep === 'confirm2'">
            <SpatialButton variant="ghost" @click="restoreStep = 'confirm1'">رجوع</SpatialButton>
            <SpatialButton variant="danger" @click="submitRestore" class="font-black">
              <span>تأكيد وتنفيذ الاستعادة</span>
            </SpatialButton>
          </template>
        </div>
      </template>
    </SpatialModal>

    <!-- ─── Modal: حذف نسخة احتياطية ────────────────────────────────────── -->
    <SpatialModal
      :isOpen="showDelete"
      title="تأكيد حذف النسخة الاحتياطية"
      maxWidth="md"
      @close="showDelete = false"
    >
      <div class="space-y-4 text-right">
        <p class="text-xs font-bold text-slate-700 dark:text-white/90 leading-relaxed">
          هل أنت متأكد من رغبتك في حذف ملف النسخة الاحتياطية
          <span class="font-mono font-black text-rose-600 dark:text-rose-400 break-all">{{ activeBackup?.filename }}</span>؟
          لا يمكن التراجع عن هذا الإجراء بعد الحذف.
        </p>
      </div>

      <template #footer>
        <div class="flex items-center justify-end gap-3">
          <SpatialButton variant="ghost" @click="showDelete = false">إلغاء</SpatialButton>
          <SpatialButton variant="danger" @click="submitDelete" class="font-black">
            <span>تأكيد الحذف النهائي</span>
          </SpatialButton>
        </div>
      </template>
    </SpatialModal>

  </HRLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import HRLayout from '@/Layouts/HRLayout.vue';
import SpatialCard from '@/Components/Spatial/SpatialCard.vue';
import SpatialButton from '@/Components/Spatial/SpatialButton.vue';
import SpatialIconButton from '@/Components/Spatial/SpatialIconButton.vue';
import SpatialModal from '@/Components/Spatial/SpatialModal.vue';
import SpatialToast from '@/Components/Spatial/SpatialToast.vue';
import SpatialProgressBar from '@/Components/Spatial/SpatialProgressBar.vue';

const props = defineProps({
  backups: {
    type: Array,
    default: () => [],
  },
});

const toastRef = ref(null);
const fileInputRef = ref(null);

const latestBackupDate = computed(() => {
  if (!props.backups || props.backups.length === 0) return 'لا يوجد بعد';
  return props.backups[0].date || 'غير حدد';
});

function handleOpenFolder() {
  if (navigator.clipboard) {
    navigator.clipboard.writeText('/home/hammam/Desktop/work/fcpms_v2/storage/app/backups/database').catch(() => {});
  }
  router.post(route('admin.backups.open-folder'), {}, {
    preserveScroll: true,
    onSuccess: () => {
      toastRef.value?.addToast('success', 'تم فتح مجلد النسخ الاحتياطية بنجاح ونَسخ المسار للحافظة.');
    },
    onError: () => {
      toastRef.value?.addToast('info', 'تم نسخ مسار المجلد إلى الحافظة.');
    },
  });
}

// ─── Create ───────────────────────────────────────────────────────────────────
const showCreate = ref(false);
const createNote = ref('');
const createForm = useForm({ note: '' });

function submitCreate() {
  createForm.note = createNote.value;
  createForm.post(route('admin.backups.create'), {
    onSuccess: () => {
      showCreate.value = false;
      createNote.value = '';
      toastRef.value?.addToast('success', 'تم إنشاء النسخة الاحتياطية بنجاح.');
    },
    onError: () => {
      toastRef.value?.addToast('error', 'تعذّر إنشاء النسخة الاحتياطية.');
    },
  });
}

// ─── Upload ───────────────────────────────────────────────────────────────────
const showUpload = ref(false);
const uploadFile = ref(null);
const uploadForm = useForm({ file: null });

function onFileChange(e) {
  uploadFile.value = e.target.files?.[0] ?? null;
}

function submitUpload() {
  if (!uploadFile.value) return;
  uploadForm.file = uploadFile.value;
  uploadForm.post(route('admin.backups.upload'), {
    forceFormData: true,
    onSuccess: () => {
      showUpload.value = false;
      uploadFile.value = null;
      toastRef.value?.addToast('success', 'تم رفع النسخة الاحتياطية بنجاح.');
    },
    onError: () => {
      toastRef.value?.addToast('error', 'تعذّر رفع الملف. تأكد من أنه ملف .zip صالح.');
    },
  });
}

// ─── Restore ──────────────────────────────────────────────────────────────────
const showRestore = ref(false);
const restoreStep = ref('confirm1'); // 'confirm1' | 'confirm2' | 'loading'
const restoreProgress = ref(0);
const activeBackup = ref(null);
let progressInterval = null;

function openRestoreModal(backup) {
  activeBackup.value = backup;
  restoreStep.value = 'confirm1';
  restoreProgress.value = 0;
  showRestore.value = true;
}

function closeRestoreModal() {
  if (restoreStep.value === 'loading') return;
  showRestore.value = false;
}

function submitRestore() {
  restoreStep.value = 'loading';
  restoreProgress.value = 0;
  progressInterval = setInterval(() => {
    restoreProgress.value = Math.min(restoreProgress.value + Math.random() * 12, 90);
  }, 400);

  router.post(route('admin.backups.restore', activeBackup.value.filename), {}, {
    onSuccess: () => {
      clearInterval(progressInterval);
      restoreProgress.value = 100;
      setTimeout(() => {
        showRestore.value = false;
        toastRef.value?.addToast('success', 'تمت استعادة النسخة الاحتياطية بنجاح.');
      }, 500);
    },
    onError: () => {
      clearInterval(progressInterval);
      showRestore.value = false;
      toastRef.value?.addToast('error', 'فشل استعادة قاعدة البيانات.');
    },
  });
}

// ─── Delete ───────────────────────────────────────────────────────────────────
const showDelete = ref(false);

function openDeleteModal(backup) {
  activeBackup.value = backup;
  showDelete.value = true;
}

function submitDelete() {
  router.delete(route('admin.backups.delete', activeBackup.value.filename), {
    onSuccess: () => {
      showDelete.value = false;
      toastRef.value?.addToast('success', 'تم حذف النسخة الاحتياطية.');
    },
    onError: () => {
      showDelete.value = false;
      toastRef.value?.addToast('error', 'تعذّر حذف النسخة الاحتياطية.');
    },
  });
}

// ─── Flash Messages ───────────────────────────────────────────────────────────
onMounted(() => {
  const flash = usePage().props.flash;
  if (flash?.success) toastRef.value?.addToast('success', flash.success);
  if (flash?.error) toastRef.value?.addToast('error', flash.error);
});
</script>
