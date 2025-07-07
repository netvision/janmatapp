<template>
  <select
    :value="modelValue"
    @change="$emit('update:modelValue', $event.target.value)"
    :required="question.required"
    class="input"
  >
    <option value="" disabled selected>Select an option</option>
    <option v-for="option in question.options" :key="option" :value="option">{{ option }}</option>
  </select>
</template>

<script setup>
defineProps({
  modelValue: String,
  question: { type: Object, required: true }
})
</script> 