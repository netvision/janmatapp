<template>
  <div class="space-y-2">
    <div v-for="option in question.options" :key="option" class="flex items-center">
      <input
        type="radio"
        :id="optionId(option)"
        :name="'radio-' + question.id"
        :value="option"
        :checked="modelValue === option"
        @change="$emit('update:modelValue', option)"
        :required="question.required"
        class="form-radio h-5 w-5 text-primary-600 focus:ring-primary-500"
      />
      <label :for="optionId(option)" class="ml-2 text-gray-700">{{ option }}</label>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  modelValue: String,
  question: { type: Object, required: true }
})
const optionId = (option) => `radio-${props.question.id}-${option}`
</script> 