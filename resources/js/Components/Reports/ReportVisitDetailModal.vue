<template>
  <Teleport to="body">
    <div
      v-if="isOpen"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md transition-opacity"
    >
      <div
        class="relative w-full max-w-3xl bg-white/95 dark:bg-slate-900/95 border border-slate-200/80 dark:border-white/10 rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]"
      >
        <!-- Header -->
        <div class="p-6 border-b border-slate-200/80 dark:border-white/10 flex items-center justify-between bg-slate-50/50 dark:bg-white/5">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-primary/10 border border-primary/20 flex items-center justify-center text-primary">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
              </svg>
            </div>
            <div>
              <h3 class="text-base font-black text-slate-900 dark:text-white">تفاصيل الزيرة الميدانية والإجابات</h3>
              <p class="text-xs font-bold text-slate-500 dark:text-white/60">
                سجل رقم #{{ visitData?.id }} - {{ visitData?.site?.name || 'موقع غير محدد' }}
              </p>
            </div>
          </div>
          <button
            @click="$emit('close')"
            class="w-9 h-9 rounded-xl bg-slate-200/60 dark:bg-white/10 hover:bg-slate-300 dark:hover:bg-white/20 text-slate-600 dark:text-white/80 transition-all flex items-center justify-center cursor-pointer"
          >
            ✕
          </button>
        </div>

        <!-- Body -->
        <div class="p-6 overflow-y-auto custom-scroll space-y-6 flex-1">
          <!-- Meta Grid -->
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 rounded-2xl bg-slate-100/60 dark:bg-white/5 border border-slate-200/60 dark:border-white/10 text-xs font-bold">
            <div>
              <div class="text-slate-400">الاستشاري</div>
              <div class="font-black text-slate-900 dark:text-white mt-0.5">{{ visitData?.daily_record?.consultant?.full_name || 'غير محدد' }}</div>
            </div>
            <div>
              <div class="text-slate-400">المدينة</div>
              <div class="font-black text-slate-900 dark:text-white mt-0.5">{{ visitData?.site?.city || 'غير محدد' }}</div>
            </div>
            <div>
              <div class="text-slate-400">وقت الدخول / الخروج</div>
              <div class="font-mono font-black text-slate-900 dark:text-white mt-0.5">
                {{ formatTime(visitData?.check_in_time) }} ⬅️ {{ formatTime(visitData?.check_out_time) }}
              </div>
            </div>
            <div>
              <div class="text-slate-400">حالة الزيارة</div>
              <div class="mt-0.5">
                <SpatialStatusPill :type="visitData?.status === 'completed' ? 'completed' : 'pending'">
                  {{ visitData?.status === 'completed' ? 'مكتملة 🟢' : 'قيد التنفيذ 🟡' }}
                </SpatialStatusPill>
              </div>
            </div>
          </div>

          <!-- Task Responses List -->
          <div class="space-y-4">
            <h4 class="text-sm font-black text-slate-900 dark:text-white flex items-center gap-2">
              <span>📋 النماذج والمهام المنجزة</span>
              <span class="text-xs font-mono font-bold text-slate-400">({{ visitData?.task_responses?.length || 0 }})</span>
            </h4>

            <div v-if="!visitData?.task_responses || visitData.task_responses.length === 0" class="text-center p-8 bg-slate-50 dark:bg-white/5 rounded-2xl border border-dashed border-slate-200 dark:border-white/10 text-xs font-bold text-slate-400">
              لا توجد مهام مسجلة لهذه الزيارة.
            </div>

            <div
              v-for="resp in visitData?.task_responses"
              :key="resp.id"
              class="p-4 rounded-2xl bg-white dark:bg-white/5 border border-slate-200/80 dark:border-white/10 space-y-3"
            >
              <div class="flex items-center justify-between border-b border-slate-100 dark:border-white/5 pb-2">
                <div class="font-black text-sm text-slate-900 dark:text-white">
                  {{ resp.task_definition?.title || 'مهمة ميدانية' }}
                </div>
                <SpatialStatusPill :type="resp.status === 'submitted' ? 'completed' : 'pending'">
                  {{ resp.status === 'submitted' ? 'مُعتمدة 🟢' : 'مسودة 🟡' }}
                </SpatialStatusPill>
              </div>

              <!-- Response Values -->
              <div class="space-y-2 text-xs font-bold">
                <div v-for="val in resp.response_values" :key="val.id" class="flex justify-between bg-slate-50 dark:bg-white/5 p-2.5 rounded-xl border border-slate-100 dark:border-white/5">
                  <span class="text-slate-500 dark:text-white/60">حقل الإجابة:</span>
                  <span class="font-mono text-slate-900 dark:text-white font-black">{{ val.value }}</span>
                </div>
              </div>

              <!-- Attachments if any -->
              <div v-if="resp.attachments && resp.attachments.length > 0" class="space-y-1 pt-1">
                <div class="text-[11px] font-black text-slate-400">المرفقات والصور الميدانية:</div>
                <div class="flex flex-wrap gap-2">
                  <a
                    v-for="att in resp.attachments"
                    :key="att.id"
                    :href="'/storage/' + att.file_path"
                    target="_blank"
                    class="px-3 py-1.5 rounded-xl bg-primary/10 hover:bg-primary/20 text-primary text-xs font-bold border border-primary/20 transition-all flex items-center gap-1"
                  >
                    📎 <span>مشاهدة المرفق</span>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="p-4 border-t border-slate-200/80 dark:border-white/10 flex justify-end bg-slate-50/50 dark:bg-white/5">
          <SpatialButton variant="secondary" @click="$emit('close')">
            إغلاق
          </SpatialButton>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import SpatialButton from '@/Components/Spatial/SpatialButton.vue';
import SpatialStatusPill from '@/Components/Spatial/SpatialStatusPill.vue';

defineProps({
  isOpen: Boolean,
  visitData: Object,
});

defineEmits(['close']);

function formatTime(timeStr) {
  if (!timeStr) return '--:--';
  return String(timeStr).substring(11, 16) || String(timeStr).substring(0, 5);
}
</script>
