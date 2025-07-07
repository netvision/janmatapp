<template>
  <div class="min-h-screen bg-gray-50">
    <header class="bg-white shadow-sm border-b border-gray-200">
      <div class="px-4 py-4 flex items-center justify-between">
        <h1 class="text-lg font-semibold text-gray-900">Surveys</h1>
        <button @click="router.back()" class="btn btn-secondary text-sm">Back</button>
      </div>
    </header>
    <main class="px-4 py-6">
      <div v-if="loading" class="text-center py-8">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600 mx-auto"></div>
        <p class="mt-2 text-sm text-gray-600">Loading surveys...</p>
      </div>
      <div v-else-if="surveys.length === 0" class="text-center py-8">
        <p class="text-gray-600">No surveys available</p>
      </div>
      <div v-else class="space-y-3">
        <div v-for="survey in surveys" :key="survey.id" @click="goToSurvey(survey.id)" class="p-4 card hover:bg-primary-50 cursor-pointer transition-colors">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="font-medium text-gray-900">{{ survey.title }}</h2>
              <p class="text-sm text-gray-600">{{ survey.question_count }} questions</p>
            </div>
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'

const router = useRouter()
const loading = ref(false)
const surveys = ref([])

const fetchSurveys = async () => {
  loading.value = true
  try {
    const res = await api.get('/surveys')
    if (res.data.success) {
      surveys.value = res.data.data
    }
  } catch (e) {
    surveys.value = []
  } finally {
    loading.value = false
  }
}

const goToSurvey = (id) => {
  router.push(`/survey/${id}`)
}

onMounted(() => {
  fetchSurveys()
})
</script> 