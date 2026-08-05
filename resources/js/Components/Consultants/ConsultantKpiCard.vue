<template>
  <SpatialCard class="relative overflow-hidden border-primary/20 bg-gradient-to-br from-blue-600/10 via-indigo-600/5 to-purple-600/10 backdrop-blur-xl">
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 p-2">
      <!-- Left side: Avatar + info -->
      <div class="flex items-center gap-4">
        <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-black text-xl flex items-center justify-center shadow-lg shadow-blue-500/25 shrink-0">
          {{ getInitials(consultant?.full_name || 'استشاري ميداني') }}
        </div>
        <div class="space-y-1">
          <div class="flex items-center gap-3">
            <h3 class="text-lg font-black text-slate-900 dark:text-white">
              {{ consultant?.full_name || 'أحمد السالم' }}
            </h3>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-black bg-blue-500/15 text-blue-600 dark:text-blue-400 border border-blue-500/20">
              {{ consultant?.employee_number || '#EMP-1024' }}
            </span>
            <SpatialStatusPill
              :type="consultant?.employment_status === 'active' ? 'completed' : (consultant?.employment_status === 'suspended' ? 'overdue' : 'pending')"
              :pulse="consultant?.employment_status === 'active'"
            >
              {{ consultant?.employment_status === 'active' ? 'نشط' : (consultant?.employment_status === 'suspended' ? 'موقوف مؤقتاً' : 'في إجازة') }}
            </SpatialStatusPill>
          </div>
          <p class="text-xs font-bold text-slate-500 dark:text-white/60 flex items-center gap-4 flex-wrap mt-1">
            <span class="flex items-center gap-1.5">
              <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
              </svg>
              {{ consultant?.phone || '051-234-5678' }}
            </span>
            <span class="flex items-center gap-1.5">
              <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
              {{ consultant?.specialization || 'هندسة مدنية وشبكات' }}
            </span>
            <span class="flex items-center gap-1.5">
              <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              {{ formatDate(consultant?.hire_date) }}
            </span>
          </p>
        </div>
      </div>

      <!-- Right side: Actions with clean SVG icons -->
      <div class="flex items-center gap-3 w-full lg:w-auto justify-end border-t lg:border-t-0 pt-4 lg:pt-0 border-black/5 dark:border-white/10">
        <button
          @click="$emit('view', consultant)"
          class="px-4 py-2.5 rounded-xl bg-black/5 dark:bg-white/10 hover:bg-black/10 dark:hover:bg-white/20 text-slate-900 dark:text-white text-xs font-bold transition-all cursor-pointer flex items-center gap-2 border border-black/10 dark:border-white/10"
        >
          <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
          </svg>
          <span>عرض الملف</span>
        </button>

        <button
          @click="$emit('edit', consultant)"
          class="px-4 py-2.5 rounded-xl bg-primary text-white hover:bg-primary/90 text-xs font-bold transition-all cursor-pointer shadow-md flex items-center gap-2"
        >
          <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
          </svg>
          <span>تعديل البيانات</span>
        </button>
      </div>
    </div>
  </SpatialCard>
</template>

<script setup>
import SpatialCard from '@/Components/Spatial/SpatialCard.vue';
import SpatialStatusPill from '@/Components/Spatial/SpatialStatusPill.vue';

defineProps({
  consultant: {
    type: Object,
    default: null,
  },
});

defineEmits(['view', 'edit']);

function getInitials(name) {
  if (!name) return 'CS';
  const parts = name.split(' ');
  return parts.length >= 2 ? (parts[0][0] + parts[1][0]) : name.substring(0, 2);
}

function formatDate(dateStr) {
  if (!dateStr) return '01-01-2026';
  return dateStr.substring(0, 10);
}
</script>
