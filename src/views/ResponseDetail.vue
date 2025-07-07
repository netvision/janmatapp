<template>
  <div>
    <div class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900">Response Details</h1>
      <p class="text-gray-600">View survey response information</p>
    </div>

    <div v-if="loading" class="card">
      <div class="text-center py-8">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600 mx-auto"></div>
        <p class="mt-2 text-gray-600">Loading response...</p>
      </div>
    </div>

    <div v-else-if="response" class="space-y-6">
      <!-- Response Information -->
      <div class="card">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Response Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700">Survey</label>
            <p class="mt-1 text-sm text-gray-900">{{ response.survey_title }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Agent</label>
            <p class="mt-1 text-sm text-gray-900">{{ response.agent_name }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Respondent</label>
            <p class="mt-1 text-sm text-gray-900">{{ response.respondent_name || 'N/A' }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Date</label>
            <p class="mt-1 text-sm text-gray-900">{{ formatDate(response.created_at) }}</p>
          </div>
          <div v-if="response.location_address || (response.latitude && response.longitude)">
            <label class="block text-sm font-medium text-gray-700">Location</label>
            <p class="mt-1 text-sm text-gray-900">
              <span v-if="response.location_address">{{ response.location_address }}</span>
              <span v-else>{{ response.latitude.toFixed(6) }}, {{ response.longitude.toFixed(6) }}</span>
            </p>
            <a
              v-if="response.latitude && response.longitude"
              :href="`https://maps.google.com/?q=${response.latitude},${response.longitude}`"
              target="_blank"
              class="text-primary-600 hover:text-primary-900 text-sm"
            >
              View on Google Maps
            </a>
          </div>
          <div v-if="response.has_voice_recording">
            <label class="block text-sm font-medium text-gray-700">Voice Recording</label>
            <div class="mt-1">
              <audio 
                ref="audioPlayer"
                controls 
                class="w-full"
                @error="handleAudioError"
              >
                <source :src="`https://janmat.netserve.in/${response.voice_recording}`" type="audio/webm">
                Your browser does not support the audio element.
              </audio>
            </div>
          </div>
        </div>
      </div>

      <!-- Answers -->
      <div class="card">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Survey Answers</h3>
        <div class="space-y-6">
          <div
            v-for="answer in answers"
            :key="answer.id"
            class="border border-gray-200 rounded-lg p-4"
          >
            <div class="mb-3">
              <h4 class="text-sm font-medium text-gray-900">{{ answer.question_text }}</h4>
              <p class="text-xs text-gray-500">{{ getQuestionTypeText(answer.question_type) }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
              <p class="text-sm text-gray-900">{{ answer.answer_text || 'No answer provided' }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex justify-end">
        <router-link to="/responses" class="btn-secondary">
          Back to Responses
        </router-link>
      </div>
    </div>

    <div v-else class="card">
      <div class="text-center py-8">
        <p class="text-gray-600">Response not found</p>
        <router-link to="/responses" class="btn-primary mt-4">
          Back to Responses
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/services/api'
import { format } from 'date-fns'

const route = useRoute()

const response = ref(null)
const answers = ref([])
const loading = ref(true)

const formatDate = (timestamp) => {
  return format(new Date(timestamp * 1000), 'MMM dd, yyyy HH:mm')
}

const getQuestionTypeText = (type) => {
  const types = {
    text: 'Text Input',
    number: 'Number Input',
    select: 'Dropdown Selection',
    radio: 'Single Choice',
    checkbox: 'Multiple Choice'
  }
  return types[type] || type
}

const handleAudioError = (event) => {
  console.error('Audio loading error:', event)
  alert('Error loading voice recording. Please try again later.')
}

const loadResponse = async () => {
  try {
    const responseData = await api.get(`/responses/${route.params.id}`)
    response.value = responseData.data.data
    
    // Load answers
    const answersData = await api.get(`/responses/${route.params.id}/answers`)
    answers.value = answersData.data.data
  } catch (error) {
    console.error('Error loading response:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadResponse()
})
</script> 