<template>
  <div class="space-y-1.5 w-full relative" :class="{ 'z-50': isOpen }" ref="dropdownRef">
    <label v-if="label" class="text-xs font-black tracking-wide text-slate-700 dark:text-white/85 flex items-center gap-1">
      {{ label }}
      <span v-if="required" class="required-star">*</span>
    </label>
    <div class="relative">
      <div
        @click="isOpen = !isOpen"
        class="spatial-input spatial-dropdown-trigger"
        :class="{ 'open': isOpen }"
      >
        <span class="font-bold text-sm text-slate-900 dark:text-white">
          {{ selectedLabel || placeholder }}
        </span>
        <svg
          class="spatial-dropdown-arrow text-primary transition-transform duration-250 shrink-0"
          :class="{ 'rotate-180': isOpen }"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
        </svg>
      </div>

      <div
        v-if="isOpen"
        class="spatial-dropdown-menu animate-spatial-in z-[100] shadow-2xl"
      >
        <ul class="p-2 space-y-1 max-h-60 overflow-y-auto custom-scroll">
          <li
            v-for="option in options"
            :key="option.value"
            @click="selectOption(option)"
            :class="[
              'spatial-dropdown-item',
              option.value === modelValue ? 'bg-primary/10 text-primary dark:bg-primary/20 dark:text-blue-400 font-black' : ''
            ]"
          >
            <span>{{ option.label }}</span>
            <span v-if="option.value === modelValue" class="text-xs text-primary font-black">✓</span>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  modelValue: [String, Number],
  label: String,
  placeholder: {
    type: String,
    default: 'اختر القيمة...',
  },
  options: {
    type: Array,
    default: () => [],
  },
  required: Boolean,
});

const emit = defineEmits(['update:modelValue', 'change']);

const isOpen = ref(false);
const dropdownRef = ref(null);

const selectedLabel = computed(() => {
  const found = props.options.find(opt => opt.value === props.modelValue);
  return found ? found.label : '';
});

function selectOption(option) {
  emit('update:modelValue', option.value);
  emit('change', option);
  isOpen.value = false;
}

function handleClickOutside(e) {
  if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
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
