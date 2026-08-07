<template>
  <VisitDetailModal
    :is-open="isOpen"
    :visit="visitData"
    :read-only="true"
    @close="$emit('close')"
  />
</template>

<script setup>
import VisitDetailModal from '@/Components/Consultant/VisitDetailModal.vue';

defineProps({
  isOpen: Boolean,
  visitData: Object,
});

defineEmits(['close']);
</script>
