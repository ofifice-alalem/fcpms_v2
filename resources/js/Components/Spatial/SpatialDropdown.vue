<template>
  <div class="flex flex-col gap-1.5">
    <label v-if="label" class="text-sm font-medium text-gray-300">
      {{ label }}
      <span v-if="required" class="text-red-400">*</span>
    </label>
    <select
      :value="modelValue"
      @change="$emit('update:modelValue', $event.target.value)"
      class="spatial-dropdown w-full appearance-none cursor-pointer"
    >
      <option value="" disabled selected>{{ placeholder || 'اختر من القائمة' }}</option>
      <slot />
    </select>
    <span v-if="error" class="text-xs text-red-400 mt-1">{{ error }}</span>
  </div>
</template>

<script setup>
defineProps({
  modelValue: [String, Number],
  label: String,
  placeholder: String,
  required: Boolean,
  error: String
});

defineEmits(['update:modelValue']);
</script>
