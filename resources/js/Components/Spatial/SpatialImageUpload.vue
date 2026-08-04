<template>
  <div class="space-y-4">
    <div class="image-upload-area" @click="triggerFileInput">
      <div v-if="!previewUrl" class="flex flex-col items-center gap-2">
        <div class="w-14 h-14 rounded-full bg-primary/10 text-primary flex items-center justify-center">
          <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
        </div>
        <span class="text-sm font-black text-white">انقر لرفع صورة إثبات الموقع أو اسحب الصورة هنا</span>
        <span class="text-xs text-white/40">صيغ مدعومة: JPG, PNG, WEBP — الحد الأقصى 10MB</span>
      </div>

      <input
        type="file"
        ref="fileInput"
        accept="image/*"
        class="hidden"
        @change="onFileSelected"
      />

      <div v-if="previewUrl" class="mt-4 pt-4 border-t border-white/10 flex flex-col items-center gap-3 animate-spatial-in">
        <img :src="previewUrl" alt="معاينة المرفق" class="preview-img" />
        <div class="flex items-center gap-4">
          <span class="text-xs font-bold text-emerald-500 bg-emerald-500/15 px-3 py-1 rounded-full">✓ تم رفع الملف بنجاح</span>
          <button @click.stopPropagation="removeFile" class="text-xs font-black text-red-500 hover:underline">إزالة المرفق ✕</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const fileInput = ref(null);
const previewUrl = ref(null);

const emit = defineEmits(['file-selected', 'file-removed']);

function triggerFileInput() {
  fileInput.value?.click();
}

function onFileSelected(e) {
  const file = e.target.files[0];
  if (file) {
    previewUrl.value = URL.createObjectURL(file);
    emit('file-selected', file);
  }
}

function removeFile() {
  previewUrl.value = null;
  if (fileInput.value) fileInput.value.value = '';
  emit('file-removed');
}
</script>
