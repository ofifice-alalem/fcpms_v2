<template>
  <div class="space-y-1.5 w-full relative transition-all" :class="isOpen ? 'z-[999]' : 'z-10'" ref="dropdownRef">
    <label v-if="label" class="text-xs font-black tracking-wide text-slate-700 dark:text-white/85 flex items-center gap-1">
      {{ label }}
      <span v-if="required" class="required-star">*</span>
    </label>

    <div class="relative">
      <!-- Trigger Bar -->
      <div
        @click="toggleDropdown"
        class="spatial-input spatial-dropdown-trigger flex items-center justify-between cursor-pointer select-none"
        :class="{ 'open': isOpen }"
      >
        <span class="font-bold text-sm text-slate-900 dark:text-white truncate max-w-[88%]">
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

      <!-- Dropdown Menu -->
      <div
        v-if="isOpen"
        class="spatial-dropdown-menu animate-spatial-in z-[100] shadow-2xl space-y-2 p-2 min-w-[220px]"
        :class="openUpward ? 'bottom-full mb-2' : 'top-full mt-2'"
      >
        <!-- Search Input Box -->
        <div v-if="searchable" class="relative px-1 pt-1 space-y-1.5">
          <input
            ref="searchInputRef"
            v-model="searchQuery"
            type="text"
            placeholder="🔍 ابحث بالكلمة أو اختر القيمة..."
            class="w-full px-3 py-2 text-xs font-bold rounded-xl bg-slate-100 dark:bg-white/10 text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 focus:outline-none focus:border-primary transition-all placeholder:text-slate-400 dark:placeholder:text-white/40"
            @click.stop
            @keydown.enter.prevent="selectCustomSearchText"
          />

          <!-- Quick Option for Custom Keyword Filter -->
          <div
            v-if="searchQuery.trim() && allowCustomText"
            @click.stop="selectCustomSearchText"
            class="p-2 rounded-xl bg-primary/10 hover:bg-primary/20 text-primary dark:text-blue-400 text-xs font-black cursor-pointer flex items-center justify-between transition-all border border-primary/20"
          >
            <span class="truncate">🔍 تصفية مباشرة بالكلمة: "{{ searchQuery }}"</span>
            <span class="text-[10px] bg-primary/20 px-1.5 py-0.5 rounded font-mono shrink-0">Enter ↵</span>
          </div>
        </div>

        <!-- Options List -->
        <ul class="space-y-1 max-h-56 overflow-y-auto custom-scroll">
          <template v-if="filteredOptions.length > 0">
            <li
              v-for="option in filteredOptions"
              :key="option.value"
              @click.stop="selectOption(option)"
              :class="[
                'spatial-dropdown-item flex items-center justify-between cursor-pointer',
                isSelected(option.value) ? 'bg-primary/10 text-primary dark:bg-primary/20 dark:text-blue-400 font-black' : ''
              ]"
            >
              <div class="flex items-center gap-2 truncate">
                <SpatialCheckbox v-if="multiple" :model-value="isSelected(option.value)" class="pointer-events-none scale-90" />
                <span class="truncate">{{ option.label }}</span>
              </div>
              <span v-if="!multiple && isSelected(option.value)" class="text-xs text-primary font-black">✓</span>
            </li>
          </template>

          <li v-else-if="!searchQuery.trim()" class="p-3 text-center text-xs font-bold text-slate-400 dark:text-white/40">
            لا توجد خيارات المتاحة
          </li>
          <li v-else class="p-3 text-center text-xs font-bold text-slate-400 dark:text-white/40">
            اضغط Enter أعلاه للتصفية حسب النص "{{ searchQuery }}"
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue';
import SpatialCheckbox from '@/Components/Spatial/SpatialCheckbox.vue';

const props = defineProps({
  modelValue: [String, Number, Array, Boolean],
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
  multiple: {
    type: Boolean,
    default: false,
  },
  searchable: {
    type: Boolean,
    default: true,
  },
  allowCustomText: {
    type: Boolean,
    default: true,
  },
});

const emit = defineEmits(['update:modelValue', 'change']);

const isOpen = ref(false);
const dropdownRef = ref(null);
const searchInputRef = ref(null);
const searchQuery = ref('');

const safeOptions = computed(() => {
  return Array.isArray(props.options) ? props.options : [];
});

const filteredOptions = computed(() => {
  if (!searchQuery.value) return safeOptions.value;
  const q = searchQuery.value.toLowerCase().trim();
  return safeOptions.value.filter((opt) => {
    if (!opt) return false;
    const label = String(opt.label || '').toLowerCase();
    const val = String(opt.value || '').toLowerCase();
    return label.includes(q) || val.includes(q);
  });
});

function isSelected(val) {
  if (props.multiple) {
    return Array.isArray(props.modelValue) && props.modelValue.includes(val);
  }
  return props.modelValue === val;
}

const selectedLabel = computed(() => {
  if (props.multiple) {
    if (!Array.isArray(props.modelValue) || props.modelValue.length === 0) return '';
    const labels = props.modelValue.map((val) => {
      const found = safeOptions.value.find((opt) => opt && opt.value === val);
      return found ? found.label : String(val);
    });
    return labels.join(' ، ');
  }

  const found = safeOptions.value.find((opt) => opt && opt.value === props.modelValue);
  return found ? found.label : String(props.modelValue || '');
});

const openUpward = ref(false);

function checkPosition() {
  if (!dropdownRef.value) return;
  const rect = dropdownRef.value.getBoundingClientRect();
  const spaceBelow = window.innerHeight - rect.bottom;
  const menuEstimatedHeight = 280;
  openUpward.value = spaceBelow < menuEstimatedHeight && rect.top > menuEstimatedHeight;
}

function toggleDropdown() {
  isOpen.value = !isOpen.value;
  if (isOpen.value) {
    checkPosition();
    if (props.searchable) {
      searchQuery.value = '';
      nextTick(() => {
        searchInputRef.value?.focus();
      });
    }
  }
}

function selectOption(option) {
  if (props.multiple) {
    const current = Array.isArray(props.modelValue) ? [...props.modelValue] : [];
    const idx = current.indexOf(option.value);
    if (idx > -1) {
      current.splice(idx, 1);
    } else {
      current.push(option.value);
    }
    emit('update:modelValue', current);
    emit('change', current);
  } else {
    emit('update:modelValue', option.value);
    emit('change', option);
    isOpen.value = false;
  }
}

function selectCustomSearchText() {
  const text = searchQuery.value.trim();
  if (!text) return;

  if (props.multiple) {
    const current = Array.isArray(props.modelValue) ? [...props.modelValue] : [];
    if (!current.includes(text)) {
      current.push(text);
    }
    emit('update:modelValue', current);
    emit('change', current);
  } else {
    emit('update:modelValue', text);
    emit('change', text);
    isOpen.value = false;
  }
  searchQuery.value = '';
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
