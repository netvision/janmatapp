<template>
  <input
    :value="modelValue"
    @input="$emit('update:modelValue', $event.target.value.replace(/[^0-9.]/g, ''))"
    :placeholder="question.placeholder || 'Enter a number'"
    :required="question.required"
    class="input"
    type="number"
    inputmode="decimal"
    autocomplete="off"
  />
</template>

<script setup>
defineProps({
  modelValue: [String, Number],
  question: { type: Object, required: true }
})
</script> 