<template>
  <GuestLayout>
    <!-- Spatial Toast Container -->
    <SpatialToast ref="toastRef" />

    <!-- Spatial Glass Card -->
    <SpatialCard padding="p-8 sm:p-10 space-y-6">
      
      <!-- Brand & Header -->
      <div class="text-center space-y-3">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-[22px] bg-gradient-to-br from-blue-600 to-indigo-700 text-white font-black text-2xl shadow-xl border border-blue-400/30">
          F
        </div>
        <h1 class="text-2xl font-black text-slate-900 dark:text-white">
          تسجيل الدخول للنظام
        </h1>
        <p class="text-xs font-bold text-slate-500 dark:text-white/60">
          نظام متابعة وتقييم أداء الاستشاريين الميدانيين (FCPMS)
        </p>
      </div>

      <!-- General Error Alert Banner -->
      <div v-if="form.errors.login" class="p-4 rounded-[18px] bg-red-500/15 border border-red-500/30 text-red-500 text-xs font-bold flex items-start gap-2 animate-spatial-in">
        <span class="text-base">⚠️</span>
        <div class="leading-relaxed">{{ form.errors.login }}</div>
      </div>

      <!-- Login Form -->
      <form @submit.prevent="submit" class="space-y-5">
        
        <!-- Username or Email Input -->
        <SpatialInput
          v-model="form.login"
          label="البريد الإلكتروني أو اسم المستخدم"
          placeholder="ادخل البريد أو اسم المستخدم..."
          required
          :error="form.errors.login && !form.errors.login.includes('الحساب') ? form.errors.login : null"
        />

        <!-- Password Input with Eye Toggle -->
        <div class="space-y-1.5 w-full relative">
          <label class="text-xs font-bold text-slate-700 dark:text-white/75 flex items-center gap-1">
            كلمة المرور
            <span class="required-star">*</span>
          </label>
          <div class="relative">
            <input
              :type="showPassword ? 'text' : 'password'"
              v-model="form.password"
              placeholder="••••••••"
              class="spatial-input h-14 rounded-[18px] px-5 pl-12 w-full text-sm font-bold"
              :class="{ 'error': form.errors.password }"
              required
            />
            <button
              type="button"
              @click="showPassword = !showPassword"
              class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-primary transition-colors cursor-pointer"
            >
              <svg v-if="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1l22 22"/>
              </svg>
              <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
              </svg>
            </button>
          </div>
          <div v-if="form.errors.password" class="text-[12px] font-bold text-red-500 flex items-center gap-1">
            <span>✗</span> {{ form.errors.password }}
          </div>
        </div>

        <!-- Remember Me Checkbox -->
        <div class="flex items-center justify-between pt-1">
          <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-700 dark:text-white/80">
            <SpatialCheckbox v-model="form.remember" />
            <span>تذكرني على هذا الجهاز</span>
          </label>
        </div>

        <!-- Submit Button -->
        <button
          type="submit"
          :disabled="form.processing"
          class="spatial-button w-full h-14 text-base font-black shadow-lg cursor-pointer mt-2"
        >
          <span v-if="form.processing" class="flex items-center gap-2">
            <span class="animate-spin w-5 h-5 border-2 border-white border-t-transparent rounded-full"></span>
            جاري التحميل...
          </span>
          <span v-else class="flex items-center gap-2">
            تسجيل الدخول
            <svg class="w-5 h-5 transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
            </svg>
          </span>
        </button>

      </form>

    </SpatialCard>
  </GuestLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import SpatialCard from '@/Components/Spatial/SpatialCard.vue';
import SpatialInput from '@/Components/Spatial/SpatialInput.vue';
import SpatialCheckbox from '@/Components/Spatial/SpatialCheckbox.vue';
import SpatialToast from '@/Components/Spatial/SpatialToast.vue';

const toastRef = ref(null);
const showPassword = ref(false);

const form = useForm({
  login: '',
  password: '',
  remember: false,
});

function submit() {
  form.post(route('login.submit'), {
    onError: (errors) => {
      if (errors.login) {
        toastRef.value?.addToast('error', errors.login);
      } else {
        toastRef.value?.addToast('error', 'يرجى مراجعة الحقول وإصلاح الأخطاء.');
      }
    },
    onFinish: () => {
      form.reset('password');
    },
  });
}
</script>
