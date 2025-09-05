<template>
  <div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Survey Responses</h1>
        <p class="text-gray-600">View and analyze survey responses</p>
      </div>

      <!-- Summary Stats -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Total Responses</p>
              <p class="text-2xl font-semibold text-gray-900">{{ summary.totalResponses }}</p>
              <p v-if="hasActiveFilters" class="text-xs text-blue-600">Filtered Results</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">With Voice</p>
              <p class="text-2xl font-semibold text-gray-900">{{ summary.voiceResponses }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">With Location</p>
              <p class="text-2xl font-semibold text-gray-900">{{ summary.locationResponses }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-orange-100 text-orange-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Today</p>
              <p class="text-2xl font-semibold text-gray-900">{{ summary.todayResponses }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-medium text-gray-900">Filters</h3>
          <div v-if="hasActiveFilters" class="text-sm text-blue-600">
            Filters Active
          </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Survey
            </label>
            <select
              v-model="filters.survey_id"
              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
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
              Agent
            </label>
            <select
              v-model="filters.agent_id"
              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
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

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Date Range
            </label>
            <select
              v-model="filters.date_range"
              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              @change="handleDateRangeChange"
            >
              <option value="">All Time</option>
              <option value="today">Today</option>
              <option value="week">Last 7 Days</option>
              <option value="month">Last 30 Days</option>
              <option value="custom">Custom Range</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Location
            </label>
            <select
              v-model="filters.has_location"
              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              @change="loadResponses"
            >
              <option value="">All Responses</option>
              <option value="yes">With Location</option>
              <option value="no">Without Location</option>
            </select>
          </div>
        </div>
        
        <!-- Custom Date Range Inputs -->
        <div v-if="filters.date_range === 'custom'" class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
            <input
              v-model="customDateFrom"
              type="date"
              @change="loadResponses"
              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
            <input
              v-model="customDateTo"
              type="date"
              @change="loadResponses"
              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
        </div>
        
        <div v-if="hasActiveFilters" class="mt-4 p-3 bg-blue-50 rounded-lg">
          <div class="flex items-center justify-between">
            <p class="text-sm text-blue-800">
              <span v-if="filters.survey_id">Survey: {{ surveys.find(s => s.id == filters.survey_id)?.title }}</span>
              <span v-if="filters.survey_id && filters.agent_id"> | </span>
              <span v-if="filters.agent_id">Agent: {{ agents.find(a => a.id == filters.agent_id)?.username }}</span>
              <span v-if="(filters.survey_id || filters.agent_id) && filters.date_range"> | </span>
              <span v-if="filters.date_range && filters.date_range !== 'custom'">Period: {{ filters.date_range.charAt(0).toUpperCase() + filters.date_range.slice(1) }}</span>
              <span v-if="filters.date_range === 'custom' && customDateFrom && customDateTo">
                Period: {{ formatDateInput(customDateFrom) }} to {{ formatDateInput(customDateTo) }}
              </span>
              <span v-if="(filters.survey_id || filters.agent_id || filters.date_range) && filters.has_location"> | </span>
              <span v-if="filters.has_location">Location: {{ filters.has_location === 'yes' ? 'With Location' : 'Without Location' }}</span>
            </p>
            <button @click="clearFilters" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
              Clear Filters
            </button>
          </div>
        </div>
      </div>

      <!-- Responses List -->
      <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900">
            Survey Responses 
            <span class="text-sm font-normal text-gray-500">({{ pagination.total }} total)</span>
          </h3>
        </div>
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
              <!-- Loading State -->
              <tr v-if="loading">
                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                  <div class="flex flex-col items-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-4"></div>
                    <p class="text-lg font-medium text-gray-400">Loading responses...</p>
                  </div>
                </td>
              </tr>
              
              <!-- Data Rows -->
              <tr v-else v-for="response in responses" :key="response.id" class="hover:bg-gray-50">
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
                    class="text-blue-600 hover:text-blue-900 font-medium"
                  >
                    View Details
                  </router-link>
                </td>
              </tr>
              
              <!-- No Data State -->
              <tr v-if="!loading && responses.length === 0">
                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                  <div class="flex flex-col items-center">
                    <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-lg font-medium text-gray-400">No responses found</p>
                    <p class="text-sm text-gray-400">Try adjusting your filters or check back later</p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="pagination.total > 0" class="px-6 py-4 border-t border-gray-200 bg-gray-50">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <div class="text-sm text-gray-700">
                Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} results
                <span v-if="pagination.last_page > 1" class="text-blue-600 font-medium ml-2">
                  (Page {{ pagination.current_page }} of {{ pagination.last_page }})
                </span>
              </div>
              <div class="flex items-center space-x-2">
                <label class="text-sm text-gray-700">Show:</label>
                <select
                  v-model="pagination.per_page"
                  @change="changePerPage"
                  class="border border-gray-300 rounded px-2 py-1 text-sm"
                >
                  <option value="10">10</option>
                  <option value="20">20</option>
                  <option value="50">50</option>
                  <option value="100">100</option>
                </select>
                <span class="text-sm text-gray-700">per page</span>
              </div>
            </div>
            <div v-if="pagination.last_page > 1" class="flex space-x-2">
              <button
                v-if="pagination.current_page > 1"
                @click="changePage(pagination.current_page - 1)"
                class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-md text-sm font-medium"
              >
                Previous
              </button>
              
              <!-- Page numbers -->
              <div class="flex items-center space-x-1">
                <button
                  v-if="pagination.current_page > 3"
                  @click="changePage(1)"
                  class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-3 py-2 rounded-md text-sm font-medium"
                >
                  1
                </button>
                <span v-if="pagination.current_page > 4" class="px-2 text-gray-500">...</span>
                
                <button
                  v-for="page in visiblePages"
                  :key="page"
                  @click="changePage(page)"
                  :class="[
                    page === pagination.current_page 
                      ? 'bg-blue-600 text-white border-blue-600' 
                      : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50',
                    'px-3 py-2 rounded-md text-sm font-medium border'
                  ]"
                >
                  {{ page }}
                </button>
                
                <span v-if="pagination.current_page < pagination.last_page - 3" class="px-2 text-gray-500">...</span>
                <button
                  v-if="pagination.current_page < pagination.last_page - 2"
                  @click="changePage(pagination.last_page)"
                  class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-3 py-2 rounded-md text-sm font-medium"
                >
                  {{ pagination.last_page }}
                </button>
              </div>
              
              <button
                v-if="pagination.current_page < pagination.last_page"
                @click="changePage(pagination.current_page + 1)"
                class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-md text-sm font-medium"
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
            <button @click="closeVoiceModal" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md text-sm font-medium">
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/services/api'
import { format } from 'date-fns'

const responses = ref([])
const surveys = ref([])
const agents = ref([])
const loading = ref(false)
const summaryLoading = ref(false)
const summary = ref({
  totalResponses: 0,
  voiceResponses: 0,
  locationResponses: 0,
  todayResponses: 0
})

const filters = ref({
  survey_id: '',
  date_range: '',
  agent_id: '',
  has_location: ''
})

const customDateFrom = ref('')
const customDateTo = ref('')

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

// Computed property to check if any filters are active
const hasActiveFilters = computed(() => {
  return filters.value.survey_id || 
         filters.value.date_range || 
         filters.value.agent_id || 
         filters.value.has_location ||
         (filters.value.date_range === 'custom' && (customDateFrom.value || customDateTo.value))
})

// Computed property for visible page numbers
const visiblePages = computed(() => {
  const current = pagination.value.current_page
  const total = pagination.value.last_page
  const pages = []
  
  // Show 3 pages around current page
  let start = Math.max(1, current - 1)
  let end = Math.min(total, current + 1)
  
  // Adjust if we're near the beginning or end
  if (current <= 2) {
    end = Math.min(total, 3)
  }
  if (current >= total - 1) {
    start = Math.max(1, total - 2)
  }
  
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  
  return pages
})

// Normalize various timestamp formats (seconds, milliseconds, numeric strings, or ISO strings)
const toDate = (ts) => {
  if (ts === undefined || ts === null) return null
  if (typeof ts === 'number') return ts > 1e12 ? new Date(ts) : new Date(ts * 1000)
  const n = Number(ts)
  if (!Number.isNaN(n)) return n > 1e12 ? new Date(n) : new Date(n * 1000)
  const d = new Date(ts)
  return isNaN(d.getTime()) ? null : d
}

const formatDate = (timestamp) => {
  const d = toDate(timestamp)
  if (!d) return ''
  return format(d, 'MMM dd, yyyy HH:mm')
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

const clearFilters = () => {
  filters.value = {
    survey_id: '',
    date_range: '',
    agent_id: '',
    has_location: ''
  }
  customDateFrom.value = ''
  customDateTo.value = ''
  pagination.value.current_page = 1
  loadResponses()
}

const handleDateRangeChange = () => {
  if (filters.value.date_range !== 'custom') {
    customDateFrom.value = ''
    customDateTo.value = ''
  }
  loadResponses()
}

const formatDateInput = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric' 
  })
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

const buildDateParams = () => {
  const params = {}
  
  if (filters.value.date_range && filters.value.date_range !== '') {
    const now = new Date()
    let dateFrom, dateTo
    
    if (filters.value.date_range === 'custom') {
      // Use custom date range
      if (customDateFrom.value) {
        dateFrom = new Date(customDateFrom.value)
      }
      if (customDateTo.value) {
        dateTo = new Date(customDateTo.value)
        dateTo.setHours(23, 59, 59, 999) // Set to end of day
      }
    } else {
      switch (filters.value.date_range) {
        case 'today':
          // Today: from start of today to end of today
          dateFrom = new Date(now.getFullYear(), now.getMonth(), now.getDate())
          dateTo = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59, 59)
          break
        case 'week':
          // Last 7 days: from 7 days ago to now
          dateFrom = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000)
          dateTo = now
          break
        case 'month':
          // Last 30 days: from 30 days ago to now
          dateFrom = new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000)
          dateTo = now
          break
      }
    }
    
    if (dateFrom) {
      params.date_from = dateFrom.toISOString().split('T')[0]
    }
    if (dateTo) {
      params.date_to = dateTo.toISOString().split('T')[0]
    }
  }
  
  return params
}

const loadResponses = async () => {
  loading.value = true
  try {
    const dateParams = buildDateParams()
    const params = {
      page: pagination.value.current_page,
      per_page: parseInt(pagination.value.per_page),
      ...dateParams
    }
    
    // Add other filters
    if (filters.value.survey_id) params.survey_id = filters.value.survey_id
    if (filters.value.agent_id) params.agent_id = filters.value.agent_id
    if (filters.value.has_location) params.has_location = filters.value.has_location
    
    const response = await api.get('/responses', { params })
    responses.value = response.data.data || []
    
    // Debug: log the API response structure
    if (process.env.NODE_ENV === 'development') {
      console.log('API Response for pagination:', {
        params,
        responseKeys: Object.keys(response.data),
        hasPagination: !!response.data.pagination,
        hasMeta: !!response.data.meta,
        responseLength: responses.value.length
      })
    }
    
    // Update pagination from API response - try multiple formats
    if (response.data.pagination) {
      pagination.value = {
        ...pagination.value,
        ...response.data.pagination
      }
    } else if (response.data.meta) {
      // Handle different pagination response format
      const meta = response.data.meta
      pagination.value = {
        current_page: meta.current_page || 1,
        per_page: parseInt(meta.per_page) || 20,
        total: meta.total || 0,
        from: meta.from || 0,
        to: meta.to || 0,
        last_page: meta.last_page || 1
      }
    } else if (response.data.current_page !== undefined) {
      // Handle pagination data at root level
      pagination.value = {
        current_page: response.data.current_page || 1,
        per_page: parseInt(response.data.per_page) || 20,
        total: response.data.total || 0,
        from: response.data.from || 0,
        to: response.data.to || 0,
        last_page: response.data.last_page || 1
      }
    } else {
      // Fallback: Get all responses and implement client-side pagination
      const allParams = { ...params }
      delete allParams.page
      delete allParams.per_page
      
      const allResponse = await api.get('/responses', { params: allParams })
      const allResponses = allResponse.data.data || []
      
      // Calculate pagination manually
      const total = allResponses.length
      const perPage = parseInt(pagination.value.per_page)
      const currentPage = pagination.value.current_page
      const startIndex = (currentPage - 1) * perPage
      const endIndex = startIndex + perPage
      
      responses.value = allResponses.slice(startIndex, endIndex)
      
      pagination.value = {
        current_page: currentPage,
        per_page: perPage,
        total: total,
        from: total > 0 ? startIndex + 1 : 0,
        to: Math.min(endIndex, total),
        last_page: Math.ceil(total / perPage) || 1
      }
      
      if (process.env.NODE_ENV === 'development') {
        console.log('Using client-side pagination:', pagination.value)
      }
    }
    
    // Calculate summary statistics based on current filters
    await calculateFilteredSummary()
    
  } catch (error) {
    console.error('Error loading responses:', error)
    responses.value = []
    pagination.value = {
      current_page: 1,
      per_page: 20,
      total: 0,
      from: 0,
      to: 0,
      last_page: 1
    }
  } finally {
    loading.value = false
  }
}

const calculateFilteredSummary = async () => {
  summaryLoading.value = true
  try {
    // Get summary stats for the current filters (without pagination)
    const dateParams = buildDateParams()
    const summaryParams = { ...dateParams }
    
    // Add other filters
    if (filters.value.survey_id) summaryParams.survey_id = filters.value.survey_id
    if (filters.value.agent_id) summaryParams.agent_id = filters.value.agent_id
    if (filters.value.has_location) summaryParams.has_location = filters.value.has_location
    
    // Try to get summary from API or calculate from current responses
    try {
      const summaryResponse = await api.get('/responses/summary', { params: summaryParams })
      
      if (summaryResponse.data.success) {
        summary.value = summaryResponse.data.data
        return
      }
    } catch (summaryError) {
      console.log('Summary endpoint not available, fetching all filtered data')
    }
    
    // Fallback: Get all filtered responses (without pagination) to calculate accurate summary
    const allFilteredParams = { ...summaryParams }
    // Don't include pagination parameters for summary calculation
    delete allFilteredParams.page
    delete allFilteredParams.per_page
    
    const allFilteredResponse = await api.get('/responses', { params: allFilteredParams })
    const allFilteredResponses = allFilteredResponse.data.data || []
    
    // Calculate summary from all filtered responses
    const total = allFilteredResponses.length
    const withVoice = allFilteredResponses.filter(r => r.has_voice_recording || r.voice_recording).length
    const withLocation = allFilteredResponses.filter(r => 
      (r.latitude && r.longitude) || r.location_address
    ).length
    
    // Calculate today's responses from all filtered data
    const today = new Date()
    today.setHours(0, 0, 0, 0)
    const todayTimestamp = Math.floor(today.getTime() / 1000)
    const todayCount = allFilteredResponses.filter(r => {
      const rd = toDate(r.created_at)
      if (!rd) return false
      return Math.floor(rd.getTime() / 1000) >= todayTimestamp
    }).length
    
    summary.value = {
      totalResponses: total,
      voiceResponses: withVoice,
      locationResponses: withLocation,
      todayResponses: todayCount
    }
    
  } catch (error) {
    console.error('Error calculating summary:', error)
    // Fallback to calculating from pagination total and current page data
    calculateSummaryFromResponses()
  } finally {
    summaryLoading.value = false
  }
}

const calculateSummaryFromResponses = () => {
  // Use pagination total for accurate count if available, otherwise current responses
  const total = pagination.value.total || responses.value.length
  
  // For voice and location stats, we need to calculate from current page data
  // This is a limitation when we don't have access to all filtered data
  const currentPageVoice = responses.value.filter(r => r.has_voice_recording || r.voice_recording).length
  const currentPageLocation = responses.value.filter(r => 
    (r.latitude && r.longitude) || r.location_address
  ).length
  
  // Estimate total voice/location based on current page ratio if we have pagination
  let voiceEstimate = currentPageVoice
  let locationEstimate = currentPageLocation
  
  if (pagination.value.total > responses.value.length && responses.value.length > 0) {
    // Estimate based on current page ratio
    const voiceRatio = currentPageVoice / responses.value.length
    const locationRatio = currentPageLocation / responses.value.length
    voiceEstimate = Math.round(pagination.value.total * voiceRatio)
    locationEstimate = Math.round(pagination.value.total * locationRatio)
  }
  
  // Calculate today's responses from current data (limited to current page)
  const today = new Date()
  today.setHours(0, 0, 0, 0)
  const todayTimestamp = Math.floor(today.getTime() / 1000)
  const todayCount = responses.value.filter(r => {
    const rd = toDate(r.created_at)
    if (!rd) return false
    return Math.floor(rd.getTime() / 1000) >= todayTimestamp
  }).length
  
  summary.value = {
    totalResponses: total,
    voiceResponses: voiceEstimate,
    locationResponses: locationEstimate,
    todayResponses: todayCount // Note: This is limited to current page data
  }
}

const changePage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page && page !== pagination.value.current_page) {
    pagination.value.current_page = page
    loadResponses()
  }
}

const changePerPage = () => {
  // Ensure per_page is a number
  pagination.value.per_page = parseInt(pagination.value.per_page)
  pagination.value.current_page = 1 // Reset to first page
  loadResponses()
}

const route = useRoute()

// Initialize filters from route query (so /responses?survey_id=123 loads that survey only)
if (route && route.query) {
  if (route.query.survey_id) filters.value.survey_id = route.query.survey_id
  if (route.query.agent_id) filters.value.agent_id = route.query.agent_id
}

watch(() => route.query, (q) => {
  if (q.survey_id !== undefined) {
    filters.value.survey_id = q.survey_id
    pagination.value.current_page = 1
    loadResponses()
  }
})

onMounted(() => {
  // Apply route query as initial filters before loading data
  const q = route && route.query ? route.query : {}
  if (q.survey_id) filters.value.survey_id = q.survey_id
  if (q.agent_id) filters.value.agent_id = q.agent_id

  loadSurveys()
  loadAgents()
  // Now load responses with filters applied
  loadResponses()
})
</script> 