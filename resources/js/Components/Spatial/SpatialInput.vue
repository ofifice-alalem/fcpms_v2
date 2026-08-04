<template>
  <div class="space-y-1.5 w-full">
    <label v-if="label" class="text-xs font-bold text-white/75 flex items-center gap-1">
      {{ label }}
      <span v-if="required" class="required-star">*</span>
    </label>
    <div class="relative">
      <input
        :type="type"
        :value="modelValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :class="[
          'spatial-input h-14 rounded-[18px] px-5 w-full text-sm font-bold',
          error ? 'error' : '',
          success ? 'success' : '',
          inputClass
        ]"
        @input="$emit('update:modelValue', $event.target.value)"
      />
    </div>
    <div v-if="error" class="text-[12px] font-bold text-red-500 flex items-center gap-1">
      <span>✗</span> {{ error }}
    </div>
    <div v-else-if="successMsg" class="text-[12px] font-bold text-emerald-500 flex items-center gap-1">
      <span>✓</span> {{ successMsg }}
    </div>
  </div>
</template>

<script setup>
defineProps({
  modelValue: [String, Number],
  label: String,
  type: {
    type: String,
    default: 'text',
  },
  placeholder: String,
  required: Boolean,
  disabled: Boolean,
  error: String,
  success: Boolean,
  successMsg: String,
  inputClass: String,
});

defineEmits(['update:modelValue']);
</script>
