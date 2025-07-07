<template>
  <div class="space-y-2">
    <div v-for="option in question.options" :key="option" class="flex items-center">
      <input
        type="checkbox"
        :id="optionId(option)"
        :value="option"
        :checked="modelValue.includes(option)"
        @change="onChange(option, $event.target.checked)"
        class="form-checkbox h-5 w-5 text-primary-600 focus:ring-primary-500"
      />
      <label :for="optionId(option)" class="ml-2 text-gray-700">{{ option }}</label>
    </div>
  </div>
</template>

<script setup>
import { toRefs } from 'vue'
const props = defineProps({
  modelValue: Array,
  question: { type: Object, required: true }
})
const { modelValue, question } = toRefs(props)
const onChange = (option, checked) => {
  let newValue = [...modelValue.value]
  if (checked) {
    if (!newValue.includes(option)) newValue.push(option)
  } else {
    newValue = newValue.filter(o => o !== option)
  }
  // Emit updated array
  emit('update:modelValue', newValue)
}
const emit = defineEmits(['update:modelValue'])
const optionId = (option) => `checkbox-${question.value.id}-${option}`
</script> 