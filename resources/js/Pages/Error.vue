<template>
  <div class="min-h-screen w-full flex items-center justify-center font-sans relative overflow-hidden bg-[#090d16] text-white p-4 dir-rtl">
    
    <!-- Spatial Glow Orbs -->
    <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-red-600/15 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-blue-600/15 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-lg relative z-10">
      <!-- Glassmorphic Error Card -->
      <div class="p-8 sm:p-10 rounded-3xl bg-slate-900/80 border border-white/10 backdrop-blur-2xl shadow-2xl space-y-6 text-center">
        
        <!-- Icon & Status Badge -->
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-red-500/15 border border-red-500/30 text-red-400 mb-2 shadow-lg shadow-red-500/10">
          <svg v-if="status === 403" class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
          </svg>
          <svg v-else-if="status === 404" class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <svg v-else class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
        </div>

        <div class="space-y-2">
          <span class="inline-block px-3.5 py-1 rounded-full text-xs font-mono font-black bg-red-500/20 text-red-300 border border-red-500/30">
            خطأ {{ status }} - غير مصرح
          </span>
          <h1 class="text-2xl sm:text-3xl font-black text-white leading-tight">
            {{ title }}
          </h1>
          <p class="text-sm font-bold text-slate-300/80 leading-relaxed">
            {{ description }}
          </p>
        </div>

        <!-- Logged in User info if available -->
        <div v-if="$page.props.auth?.user" class="p-3.5 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-between text-right text-xs">
          <div class="space-y-0.5 min-w-0 flex-1">
            <span class="font-black text-white block truncate">{{ $page.props.auth.user.name }}</span>
            <span class="text-[11px] font-bold text-slate-400 block truncate">{{ $page.props.auth.user.email }}</span>
          </div>
          <span class="px-2.5 py-1 rounded-lg bg-white/10 text-[10px] font-black text-slate-300 shrink-0">حساب مسجّل</span>
        </div>

        <!-- Action Buttons -->
        <div class="pt-2 flex flex-col sm:flex-row items-center gap-3">
          <Link
            href="/dashboard"
            class="w-full sm:flex-1 py-3.5 px-4 rounded-2xl bg-blue-600 hover:bg-blue-500 text-white font-black text-xs transition-all shadow-lg shadow-blue-600/30 flex items-center justify-center gap-2 cursor-pointer active:scale-95"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span>العودة للبوابة الرئيسية</span>
          </Link>

          <button
            @click="logout"
            class="w-full sm:flex-1 py-3.5 px-4 rounded-2xl bg-red-500/20 hover:bg-red-600 text-red-300 hover:text-white border border-red-500/30 font-black text-xs transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-95 shadow-sm"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <span>تسجيل الخروج 🚪</span>
          </button>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
  status: { type: Number, required: true },
  message: { type: String, default: null },
});

const title = computed(() => {
  return {
    403: 'غير مصرح لك بالوصول لهذه الصفحة',
    404: 'الصفحة غير موجودة',
    500: 'حدث خطأ غير متوقع في الخادم',
    503: 'الخدمة قيد الصيانة مؤقتاً',
  }[props.status] || 'تعذر الوصول للخدمة';
});

const description = computed(() => {
  if (props.message && props.status === 403 && !props.message.includes('User does not have')) {
    return props.message;
  }
  return {
    403: 'عذراً، حسابك الحالي لا يمتلك الأدوار أو الصلاحيات الكافية لدخول هذه البوابة. يمكنك تسجيل الخروج والدخول بالحساب المناسب.',
    404: 'عذراً، الرابط الذي تحاول الوصول إليه غير موجود أو تم نقله.',
    500: 'نأسف لذلك، يرجى المحاولة لاحقاً أو التواصل مع الدعم الفني.',
    503: 'نعمل حالياً على تحسين النظام، يرجى العودة بعد قليل.',
  }[props.status] || 'تعذر إكمال الطلب الحالي.';
});

const logout = () => {
  router.post('/logout');
};
</script>
