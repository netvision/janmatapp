<template>
  <input
    :value="modelValue"
    @input="$emit('update:modelValue', $event.target.value)"
    :placeholder="question.placeholder || 'Enter your answer'"
    :required="question.required"
    class="input"
    type="text"
    autocomplete="off"
  />
</template>

<script setup>
defineProps({
  modelValue: String,
  question: { type: Object, required: true }
})
</script> 