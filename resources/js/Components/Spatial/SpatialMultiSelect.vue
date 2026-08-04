<template>
  <div class="space-y-1.5 w-full relative" ref="containerRef">
    <label v-if="label" class="text-xs font-bold text-slate-700 dark:text-white/75 flex items-center gap-1">
      {{ label }}
      <span v-if="required" class="required-star">*</span>
    </label>

    <div class="relative">
      <div
        @click="isOpen = !isOpen"
        class="spatial-input min-h-[56px] rounded-[20px] px-4 py-2.5 flex items-center justify-between cursor-pointer flex-wrap gap-2"
        :class="{ 'border-primary/50': isOpen }"
      >
        <div class="flex flex-wrap gap-1.5 flex-1 items-center">
          <span
            v-for="item in selectedItems"
            :key="item.value"
            class="flex items-center gap-1.5 px-3 py-1 rounded-[10px] bg-primary/15 text-primary text-xs font-bold border border-primary/30"
          >
            {{ item.label }}
            <button
              @click.stopPropagation="removeTag(item.value)"
              class="hover:bg-primary/20 rounded-full w-4 h-4 inline-flex items-center justify-center text-[10px]"
            >
              ✕
            </button>
          </span>

          <span v-if="selectedItems.length === 0" class="text-slate-400 dark:text-white/40 text-sm font-bold">
            {{ placeholder }}
          </span>
        </div>

        <svg
          class="spatial-dropdown-arrow text-primary transition-transform duration-250"
          :class="{ 'rotate-180': isOpen }"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
        </svg>
      </div>

      <!-- Dropdown Menu -->
      <div v-if="isOpen" class="spatial-dropdown-menu animate-spatial-in">
        <ul class="p-2 space-y-1 max-h-56 overflow-y-auto custom-scroll">
          <li
            v-for="opt in options"
            :key="opt.value"
            @click="toggleItem(opt.value)"
            class="spatial-dropdown-item"
          >
            <span>{{ opt.label }}</span>
            <span v-if="modelValue.includes(opt.value)" class="text-xs text-primary font-bold">✓</span>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  modelValue: {
    type: Array,
    default: () => [],
  },
  label: String,
  placeholder: {
    type: String,
    default: 'اختر عناصر متعددة...',
  },
  options: {
    type: Array,
    default: () => [], // [{ label, value }]
  },
  required: Boolean,
});

const emit = defineEmits(['update:modelValue']);

const isOpen = ref(false);
const containerRef = ref(null);

const selectedItems = computed(() => {
  return props.options.filter(opt => props.modelValue.includes(opt.value));
});

function toggleItem(val) {
  let updated = [...props.modelValue];
  if (updated.includes(val)) {
    updated = updated.filter(v => v !== val);
  } else {
    updated.push(val);
  }
  emit('update:modelValue', updated);
}

function removeTag(val) {
  emit('update:modelValue', props.modelValue.filter(v => v !== val));
}

function handleClickOutside(e) {
  if (containerRef.value && !containerRef.value.contains(e.target)) {
    isOpen.value = false;
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>
