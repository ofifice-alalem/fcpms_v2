<template>
  <div class="space-y-6">
    <!-- Tabs Header Buttons -->
    <div class="flex flex-wrap gap-2 p-1.5 rounded-[22px] bg-black/5 dark:bg-white/5 border border-black/5 dark:border-white/10 w-fit">
      <button
        v-for="tab in tabs"
        :key="tab.id"
        @click="activeTab = tab.id"
        :class="[
          'px-6 py-3 rounded-[16px] font-bold text-sm transition-all duration-200',
          activeTab === tab.id
            ? 'bg-primary text-white shadow-md shadow-primary/30'
            : 'text-slate-600 dark:text-white/70 hover:text-slate-900 dark:hover:text-white'
        ]"
      >
        {{ tab.label }}
      </button>
    </div>

    <!-- Active Tab Panel Content -->
    <div class="p-6 rounded-[26px] bg-black/5 dark:bg-white/5 space-y-4 animate-spatial-in">
      <slot :name="activeTab" />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  tabs: {
    type: Array,
    required: true, // [{ id: 'tab1', label: 'المهام اليومية' }, ...]
  },
  defaultTab: String,
});

const activeTab = ref(props.defaultTab || (props.tabs[0] ? props.tabs[0].id : ''));
</script>
