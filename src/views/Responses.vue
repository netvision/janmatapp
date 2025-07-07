<template>
  <div>
    <div class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900">Survey Responses</h1>
      <p class="text-gray-600">View and analyze survey responses</p>
    </div>

    <!-- Filters -->
    <div class="card mb-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Survey
          </label>
          <select
            v-model="filters.survey_id"
            class="input-field"
            @change="loadResponses"
          >
            <option value="">All Surveys</option>
            <option
              v-for="survey in surveys"
              :key="survey.id"
              :value="survey.id"
            >
              {{ survey.title }}
            </option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Date Range
          </label>
          <select
            v-model="filters.date_range"
            class="input-field"
            @change="loadResponses"
          >
            <option value="">All Time</option>
            <option value="today">Today</option>
            <option value="week">This Week</option>
            <option value="month">This Month</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Agent
          </label>
          <select
            v-model="filters.agent_id"
            class="input-field"
            @change="loadResponses"
          >
            <option value="">All Agents</option>
            <option
              v-for="agent in agents"
              :key="agent.id"
              :value="agent.id"
            >
              {{ agent.username }}
            </option>
          </select>
        </div>
      </div>
    </div>

    <!-- Responses List -->
    <div class="card">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Survey
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Agent
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Respondent
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Location
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Voice
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Date
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="response in responses" :key="response.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                {{ response.survey_title }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ response.agent_name }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ response.respondent_name || 'N/A' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span v-if="response.location_address" class="text-gray-900">
                  {{ response.location_address }}
                </span>
                <span v-else-if="response.latitude && response.longitude" class="text-gray-600">
                  {{ response.latitude.toFixed(4) }}, {{ response.longitude.toFixed(4) }}
                </span>
                <span v-else class="text-gray-400">No location</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <div v-if="response.has_voice_recording" class="flex items-center space-x-2">
                  <button 
                    @click="playVoiceRecording(response)"
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 hover:bg-green-200 cursor-pointer"
                  >
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
                    </svg>
                    Play Voice
                  </button>
                </div>
                <span v-else class="text-gray-400">No voice</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(response.created_at) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <router-link
                  :to="`/responses/${response.id}`"
                  class="text-primary-600 hover:text-primary-900"
                >
                  View Details
                </router-link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.total > pagination.per_page" class="px-6 py-3 border-t border-gray-200">
        <div class="flex items-center justify-between">
          <div class="text-sm text-gray-700">
            Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} results
          </div>
          <div class="flex space-x-2">
            <button
              v-if="pagination.current_page > 1"
              @click="changePage(pagination.current_page - 1)"
              class="btn-secondary"
            >
              Previous
            </button>
            <button
              v-if="pagination.current_page < pagination.last_page"
              @click="changePage(pagination.current_page + 1)"
              class="btn-secondary"
            >
              Next
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Voice Recording Modal -->
    <div v-if="showVoiceModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-bold text-gray-900">Voice Recording</h3>
          <button @click="closeVoiceModal" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        
        <div v-if="selectedVoiceResponse" class="space-y-4">
          <div class="text-sm text-gray-600">
            <p><strong>Survey:</strong> {{ selectedVoiceResponse.survey_title }}</p>
            <p><strong>Respondent:</strong> {{ selectedVoiceResponse.respondent_name || 'N/A' }}</p>
            <p><strong>Agent:</strong> {{ selectedVoiceResponse.agent_name }}</p>
            <p><strong>Date:</strong> {{ formatDate(selectedVoiceResponse.created_at) }}</p>
          </div>
          
          <div class="bg-gray-50 rounded-lg p-4">
            <audio 
              ref="audioPlayer"
              controls 
              class="w-full"
              @error="handleAudioError"
            >
              <source :src="`https://janmat.netserve.in/${selectedVoiceResponse.voice_recording}`" type="audio/webm">
              Your browser does not support the audio element.
            </audio>
            <p class="text-xs text-gray-500 mt-2">
              If the audio doesn't play, the file might be corrupted or in an unsupported format.
            </p>
          </div>
        </div>
        
        <div class="flex justify-end mt-6">
          <button @click="closeVoiceModal" class="btn-secondary">
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'
import { format } from 'date-fns'

const responses = ref([])
const surveys = ref([])
const agents = ref([])
const filters = ref({
  survey_id: '',
  date_range: '',
  agent_id: ''
})

const pagination = ref({
  current_page: 1,
  per_page: 20,
  total: 0,
  from: 0,
  to: 0,
  last_page: 1
})

const showVoiceModal = ref(false)
const selectedVoiceResponse = ref(null)
const audioPlayer = ref(null)

const formatDate = (timestamp) => {
  return format(new Date(timestamp * 1000), 'MMM dd, yyyy HH:mm')
}

const playVoiceRecording = (response) => {
  selectedVoiceResponse.value = response
  showVoiceModal.value = true
}

const closeVoiceModal = () => {
  showVoiceModal.value = false
  selectedVoiceResponse.value = null
  // Stop any playing audio
  if (audioPlayer.value) {
    audioPlayer.value.pause()
    audioPlayer.value.currentTime = 0
  }
}

const handleAudioError = (event) => {
  console.error('Audio playback error:', event)
  // You could show a toast notification here
}

const loadSurveys = async () => {
  try {
    const response = await api.get('/surveys')
    surveys.value = response.data.data
  } catch (error) {
    console.error('Error loading surveys:', error)
  }
}

const loadAgents = async () => {
  try {
    const response = await api.get('/agents')
    agents.value = response.data.data
  } catch (error) {
    console.error('Error loading agents:', error)
  }
}

const loadResponses = async () => {
  try {
    const params = {
      page: pagination.value.current_page,
      ...filters.value
    }
    
    const response = await api.get('/responses', { params })
    responses.value = response.data.data
    pagination.value = response.data.pagination || pagination.value
  } catch (error) {
    console.error('Error loading responses:', error)
  }
}

const changePage = (page) => {
  pagination.value.current_page = page
  loadResponses()
}

onMounted(() => {
  loadSurveys()
  loadAgents()
  loadResponses()
})
</script> 