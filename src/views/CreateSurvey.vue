<template>
  <div>
    <div class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900">Create Survey</h1>
      <p class="text-gray-600">Create a new survey questionnaire</p>
    </div>

    <div class="card">
      <form @submit.prevent="handleSubmit">
        <div class="space-y-6">
          <!-- Basic Information -->
          <div>
            <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
            <div class="grid grid-cols-1 gap-6">
              <div>
                <label for="title" class="block text-sm font-medium text-gray-700">
                  Survey Title
                </label>
                <input
                  id="title"
                  v-model="form.title"
                  type="text"
                  required
                  class="input-field"
                  placeholder="Enter survey title"
                />
              </div>

              <div>
                <label for="description" class="block text-sm font-medium text-gray-700">
                  Description
                </label>
                <textarea
                  id="description"
                  v-model="form.description"
                  rows="3"
                  class="input-field"
                  placeholder="Enter survey description"
                ></textarea>
              </div>

              <div>
                <label for="status" class="block text-sm font-medium text-gray-700">
                  Status
                </label>
                <select
                  id="status"
                  v-model="form.status"
                  class="input-field"
                >
                  <option value="0">Draft</option>
                  <option value="1">Active</option>
                  <option value="2">Inactive</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Questions -->
          <!-- Removed: Questions section for separate management -->

          <!-- Submit -->
          <div class="flex justify-end space-x-3">
            <router-link
              to="/surveys"
              class="btn-secondary"
            >
              Cancel
            </router-link>
            <button
              type="submit"
              :disabled="loading"
              class="btn-primary"
            >
              {{ loading ? 'Creating...' : 'Create Survey' }}
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'

const router = useRouter()

const form = ref({
  title: '',
  description: '',
  status: '0',
  questions: [
    {
      text: '',
      type: 'text',
      required: false,
      options: ''
    }
  ]
})

const loading = ref(false)

const addQuestion = () => {
  form.value.questions.push({
    text: '',
    type: 'text',
    required: false,
    options: ''
  })
}

const removeQuestion = (index) => {
  if (form.value.questions.length > 1) {
    form.value.questions.splice(index, 1)
  }
}

const handleSubmit = async () => {
  loading.value = true

  try {
    // Prepare questions data
    const questionsData = form.value.questions.map(q => ({
      text: q.text,
      type: q.type,
      required: q.required,
      options: q.options ? q.options.split('\n').filter(opt => opt.trim()) : []
    }))

    const surveyData = {
      title: form.value.title,
      description: form.value.description,
      status: parseInt(form.value.status),
      questions: questionsData
    }

    await api.post('/surveys/create', surveyData)
    router.push('/surveys')
  } catch (error) {
    console.error('Error creating survey:', error)
  } finally {
    loading.value = false
  }
}
</script> 