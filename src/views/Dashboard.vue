<template>
  <div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
      <h1 class="text-3xl font-bold text-gray-900 mb-8">Dashboard</h1>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Total Surveys</p>
              <p class="text-2xl font-semibold text-gray-900">{{ stats.totalSurveys }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Total Responses</p>
              <p class="text-2xl font-semibold text-gray-900">{{ stats.totalResponses }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Today's Responses</p>
              <p class="text-2xl font-semibold text-gray-900">{{ stats.todayResponses }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-8">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
        <p class="mt-4 text-gray-600">Loading analytics...</p>
      </div>

      <!-- Analytics Charts -->
      <div v-else-if="analytics.length > 0" class="space-y-8">
        <div v-for="survey in analytics" :key="survey.survey_id" class="bg-white rounded-lg shadow">
          <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">{{ survey.survey_title }}</h2>
            <p class="text-sm text-gray-600 mt-1">{{ survey.total_responses }} total responses</p>
          </div>
          
          <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <div v-for="question in survey.questions" :key="question.question_id" class="border rounded-lg p-4">
                <h3 class="font-medium text-gray-900 mb-3">{{ question.question_text }}</h3>
                
                <!-- Choice Questions (Bar Chart) -->
                <div v-if="question.question_type === 'single_choice' || question.question_type === 'multiple_choice' || question.question_type === 'rating'">
                  <div v-if="Object.keys(question.answers).length > 0" class="space-y-2">
                    <div v-for="(count, option) in question.answers" :key="option" class="flex items-center">
                      <div class="flex-1">
                        <div class="flex justify-between text-sm">
                          <span class="text-gray-700">{{ option }}</span>
                          <span class="text-gray-500">{{ count }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                          <div 
                            class="bg-blue-600 h-2 rounded-full" 
                            :style="{ width: `${(count / question.total_answers) * 100}%` }"
                          ></div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div v-else class="text-gray-500 text-sm">No responses yet</div>
                </div>

                <!-- Text/Number Questions (Top Answers) -->
                <div v-else-if="Object.keys(question.text_answers).length > 0" class="space-y-2">
                  <div v-for="(count, answer) in question.text_answers" :key="answer" class="flex items-center">
                    <div class="flex-1">
                      <div class="flex justify-between text-sm">
                        <span class="text-gray-700 truncate">{{ answer }}</span>
                        <span class="text-gray-500 ml-2">{{ count }}</span>
                      </div>
                      <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                        <div 
                          class="bg-green-600 h-2 rounded-full" 
                          :style="{ width: `${(count / question.total_answers) * 100}%` }"
                        ></div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- No answers yet -->
                <div v-else class="text-gray-500 text-sm">No responses yet</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- No Analytics -->
      <div v-else class="text-center py-8">
        <div class="text-gray-500">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">No analytics available</h3>
          <p class="mt-1 text-sm text-gray-500">Start collecting survey responses to see analytics here.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'

const loading = ref(true)
const stats = ref({
  totalSurveys: 0,
  totalResponses: 0,
  todayResponses: 0
})
const analytics = ref([])

const fetchDashboardData = async () => {
  loading.value = true
  try {
    // Fetch basic stats
    const [surveysRes, responsesRes, analyticsRes] = await Promise.all([
      api.get('/surveys'),
      api.get('/responses'),
      api.get('/responses/dashboard-analytics')
    ])

    // Update stats
    stats.value.totalSurveys = surveysRes.data.data.length
    stats.value.totalResponses = responsesRes.data.data.length
    
    // Calculate today's responses
    const today = new Date()
    today.setHours(0, 0, 0, 0)
    const todayTimestamp = Math.floor(today.getTime() / 1000)
    stats.value.todayResponses = responsesRes.data.data.filter(
      response => response.created_at >= todayTimestamp
    ).length

    // Update analytics
    analytics.value = analyticsRes.data.data

  } catch (e) {
    console.error('Error fetching dashboard data:', e.response?.data || e.message)
    alert('Error loading dashboard data: ' + (e.response?.data?.message || e.message))
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchDashboardData()
})
</script> 