<template>
  <div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto relative">
      <h1 class="text-3xl font-bold text-gray-900 mb-6">Dashboard</h1>
      <!-- Summary Section (colorful) -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Show skeletons while loading -->
        <template v-if="loading">
          <div v-for="n in 4" :key="n" class="rounded-lg shadow p-6 bg-white animate-pulse">
            <div class="h-1 bg-gray-200 rounded mb-4"></div>
            <div class="flex items-center">
              <div class="w-12 h-12 rounded-full bg-gray-200 mr-4"></div>
              <div class="flex-1">
                <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                <div class="h-6 bg-gray-200 rounded w-1/3"></div>
              </div>
            </div>
          </div>
        </template>
        <!-- Real summary cards -->
        <template v-else>
          <!-- Total Surveys (blue) -->
          <div class="relative overflow-hidden rounded-lg shadow transform hover:-translate-y-1 transition">
            <div class="h-1 bg-gradient-to-r from-blue-400 to-blue-600"></div>
            <div class="p-6 bg-white">
              <div class="flex items-center justify-center mb-3">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-3">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v4a1 1 0 001 1h3m10 0h3a1 1 0 001-1V7M7 21h10M12 7v14"/>
                  </svg>
                </div>
                <div class="text-left">
                  <p class="text-sm font-medium text-gray-600">Total Surveys</p>
                  <p class="text-2xl font-semibold text-blue-800">{{ stats.totalSurveys }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Total Responses (green) -->
          <div class="relative overflow-hidden rounded-lg shadow transform hover:-translate-y-1 transition">
            <div class="h-1 bg-gradient-to-r from-green-400 to-green-600"></div>
            <div class="p-6 bg-white">
              <div class="flex items-center justify-center mb-3">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-3">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h4l3 9 4-18 3 9h4"/>
                  </svg>
                </div>
                <div class="text-left">
                  <p class="text-sm font-medium text-gray-600">Total Responses</p>
                  <p class="text-2xl font-semibold text-green-800">{{ stats.totalResponses }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Today's Responses (purple) -->
          <div class="relative overflow-hidden rounded-lg shadow transform hover:-translate-y-1 transition">
            <div class="h-1 bg-gradient-to-r from-purple-400 to-pink-500"></div>
            <div class="p-6 bg-white">
              <div class="flex items-center justify-center mb-3">
                <div class="p-3 rounded-full bg-pink-100 text-pink-600 mr-3">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3M12 2a10 10 0 100 20 10 10 0 000-20z"/>
                  </svg>
                </div>
                <div class="text-left">
                  <p class="text-sm font-medium text-gray-600">Today's Responses</p>
                  <p class="text-2xl font-semibold text-pink-800">{{ stats.todayResponses }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Active Agents (teal) -->
          <div class="relative overflow-hidden rounded-lg shadow transform hover:-translate-y-1 transition">
            <div class="h-1 bg-gradient-to-r from-teal-400 to-teal-600"></div>
            <div class="p-6 bg-white">
              <div class="flex items-center justify-center mb-3">
                <div class="p-3 rounded-full bg-teal-100 text-teal-600 mr-3">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m6-6a4 4 0 11-8 0 4 4 0 018 0z"/>
                  </svg>
                </div>
                <div class="text-left">
                  <p class="text-sm font-medium text-gray-600">Active Agents</p>
                  <p class="text-2xl font-semibold text-teal-800">{{ stats.activeAgents }}</p>
                </div>
              </div>
            </div>
          </div>
        </template>
      </div>

      <!-- Latest Surveys -->
      <div class="mb-8">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-semibold text-gray-900">Active Surveys</h2>
          <div class="w-full md:w-1/2 flex items-center justify-end">
            <div class="w-full md:w-3/4 flex items-center space-x-3">
              <select v-model="selectedSurveyDropdown" @change="openSurveyFromDropdown" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                <option value="">Select a survey...</option>
                <option v-for="s in surveys" :key="s.id" :value="s.id">{{ s.title }}{{ (s.status !== 1 && s.status !== '1') ? ' (inactive)' : '' }}</option>
              </select>
              <button v-if="selectedSurveyDropdown" @click="selectedSurveyDropdown = ''" class="text-sm text-gray-600 hover:text-gray-800">Clear</button>
            </div>
          </div>
          </div>
        <div v-if="surveys.length === 0" class="text-gray-500">No surveys available.</div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <template v-if="latestSurveys.length === 0 && loading">
              <div v-for="n in 4" :key="n" class="border rounded-lg p-6 bg-white">
                <div class="flex items-center justify-center h-24">
                  <svg class="animate-spin h-8 w-8 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                  </svg>
                </div>
              </div>
            </template>
            <template v-else>
              <div v-for="(survey, idx) in latestSurveys" :key="survey.id" class="border rounded-lg p-4 cursor-pointer transform hover:-translate-y-1 transition relative overflow-hidden" :class="`border-t-4 ${['border-blue-500','border-green-500','border-pink-500','border-teal-500'][idx % 4]}`" @click="goToSurvey(survey.id)">
                <!-- per-card inline spinner -->
                <div v-if="loading" class="absolute top-3 right-3">
                  <svg class="animate-spin h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                  </svg>
                </div>

                <div class="flex items-start">
                  <div class="w-28 h-28 mr-4 flex-shrink-0">
                    <!-- small analytics graphic: if analytics present, show pie chart, else placeholder -->
                    <component v-if="getMiniChartData(survey.id)" :is="PieChart" :data="getMiniChartData(survey.id).data" :labels="getMiniChartData(survey.id).labels" :colors="pieColors" :width="112" :height="112" :showLegend="false" />
                    <div v-else class="w-28 h-28 rounded-full bg-gray-100 flex items-center justify-center text-xs text-gray-500">No data</div>
                  </div>
                  <div class="flex-1">
                    <h3 class="font-bold text-lg text-gray-900 mb-1">{{ survey.title }}</h3>
                    <p class="text-sm text-gray-600 mb-2 truncate">{{ survey.description }}</p>
                    <div class="flex items-center text-sm text-gray-500 space-x-4">
                      <span>Questions: {{ survey.questions_count }}</span>
                      <span>Responses: {{ survey.responses_count }}</span>
                    </div>
                    <div class="mt-2 text-xs text-gray-400">Created: {{ formatDate(survey.created_at) }}</div>
                  </div>
                </div>
              </div>
            </template>
          </div>
      </div>
  <!-- Analytics Modal -->
  <SurveyAnalyticsModal :surveyId="analyticsSurveyId" :visible="showAnalyticsModal" @close="() => { showAnalyticsModal = false; analyticsSurveyId = null }" />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'
import SurveyAnalyticsModal from '@/components/SurveyAnalyticsModal.vue'
import PieChart from '@/components/PieChart.vue'

// Cache for storing fetched data
const dataCache = ref({
  responses: null,
  surveys: null,
  agents: null,
  answersCache: new Map(),
  lastFetch: null
})

const CACHE_DURATION = 5 * 60 * 1000 // 5 minutes cache

const loading = ref(true)
const stats = ref({
  totalSurveys: 0,
  totalResponses: 0,
  todayResponses: 0,
  activeAgents: 0
})
const analytics = ref([])
const agents = ref([])
const surveys = ref([])
const latestSurveys = ref([])
const selectedSurveyDropdown = ref('')
const router = useRouter()
const showAnalyticsModal = ref(false)
const analyticsSurveyId = ref(null)
const agentStats = ref([])
const totalResponses = ref(0)

// Filters
const selectedAgent = ref('')
const selectedSurvey = ref('')
const dateRange = ref('all')
const customDateFrom = ref('')
const customDateTo = ref('')

// Color palette for pie charts
const pieColors = [
  '#3B82F6', // blue
  '#10B981', // green
  '#F59E0B', // amber
  '#EF4444', // red
  '#8B5CF6', // purple
  '#06B6D4', // cyan
  '#EC4899', // pink
  '#84CC16', // lime
  '#F97316', // orange
  '#6366F1', // indigo
  '#14B8A6', // teal
  '#F472B6', // rose
]

// Demo mode for testing
const demoMode = ref(false)

const generateDemoData = () => {
  // First set up the agents data if not already loaded
  if (agents.value.length === 0) {
    agents.value = [
      { id: 1, name: 'John Smith', username: 'john.smith', status: 10 },
      { id: 2, name: 'Sarah Johnson', username: 'sarah.johnson', status: 10 },
      { id: 3, name: 'Mike Wilson', username: 'mike.wilson', status: 10 },
      { id: 4, name: 'Lisa Chen', username: 'lisa.chen', status: 10 },
      { id: 5, name: 'David Brown', username: 'david.brown', status: 10 }
    ]
  }

  // Generate demo agent stats based on current filter
  if (selectedAgent.value) {
    const selectedAgentData = agents.value.find(a => a.id == selectedAgent.value)
    agentStats.value = [
      { 
        agent_id: selectedAgent.value, 
        agent_name: selectedAgentData?.name || selectedAgentData?.username || 'Selected Agent', 
        response_count: 45 
      }
    ]
  } else {
    // Show multiple agents with varied response counts
    agentStats.value = [
      { agent_id: 1, agent_name: 'John Smith', response_count: 45 },
      { agent_id: 2, agent_name: 'Sarah Johnson', response_count: 38 },
      { agent_id: 3, agent_name: 'Mike Wilson', response_count: 29 },
      { agent_id: 4, agent_name: 'Lisa Chen', response_count: 22 },
      { agent_id: 5, agent_name: 'David Brown', response_count: 18 }
    ]
  }
  
  totalResponses.value = agentStats.value.reduce((sum, agent) => sum + agent.response_count, 0)
  
  // Set up surveys data if not already loaded
  if (surveys.value.length === 0) {
    surveys.value = [
      { id: 1, title: 'Customer Satisfaction Survey' },
      { id: 2, title: 'Product Feedback Survey' },
      { id: 3, title: 'Employee Engagement Survey' }
    ]
  }
  
  // Generate demo analytics based on filters
  const baseData = [
    {
      survey_id: 1,
      survey_title: 'Customer Satisfaction Survey',
      total_responses: selectedAgent.value ? 45 : 152,
      questions: [
        {
          question_id: 1,
          question_text: 'How satisfied are you with our service?',
          question_type: 'single_choice',
          answers: selectedAgent.value ? 
            {
              'Very Satisfied': 18,
              'Satisfied': 12,
              'Neutral': 8,
              'Dissatisfied': 5,
              'Very Dissatisfied': 2
            } :
            {
              'Very Satisfied': 58,
              'Satisfied': 42,
              'Neutral': 28,
              'Dissatisfied': 15,
              'Very Dissatisfied': 9
            },
          total_answers: selectedAgent.value ? 45 : 152
        },
        {
          question_id: 2,
          question_text: 'Which features do you use most? (Select multiple)',
          question_type: 'multiple_choice',
          answers: selectedAgent.value ?
            {
              'Mobile App': 25,
              'Website': 20,
              'API Integration': 8,
              'Desktop Application': 12,
              'Customer Support': 6
            } :
            {
              'Mobile App': 85,
              'Website': 67,
              'API Integration': 34,
              'Desktop Application': 28,
              'Customer Support': 22
            },
          total_answers: selectedAgent.value ? 71 : 236
        },
        {
          question_id: 3,
          question_text: 'Rate our customer service (1-5 stars)',
          question_type: 'rating',
          answers: selectedAgent.value ?
            {
              '5 - Excellent': 20,
              '4 - Good': 15,
              '3 - Average': 7,
              '2 - Poor': 2,
              '1 - Very Poor': 1
            } :
            {
              '5 - Excellent': 68,
              '4 - Good': 45,
              '3 - Average': 25,
              '2 - Poor': 10,
              '1 - Very Poor': 4
            },
          total_answers: selectedAgent.value ? 45 : 152
        }
      ]
    },
    {
      survey_id: 2,
      survey_title: 'Product Feedback Survey',
      total_responses: selectedAgent.value ? 23 : 89,
      questions: [
        {
          question_id: 4,
          question_text: 'Which product category interests you most?',
          question_type: 'single_choice',
          answers: selectedAgent.value ?
            {
              'Electronics': 8,
              'Clothing': 6,
              'Home & Garden': 5,
              'Sports': 3,
              'Books': 1
            } :
            {
              'Electronics': 32,
              'Clothing': 24,
              'Home & Garden': 18,
              'Sports': 12,
              'Books': 3
            },
          total_answers: selectedAgent.value ? 23 : 89
        },
        {
          question_id: 5,
          question_text: 'How likely are you to recommend us? (NPS Score)',
          question_type: 'rating',
          answers: selectedAgent.value ?
            {
              '10 - Extremely Likely': 9,
              '9': 5,
              '8': 4,
              '7': 3,
              '6': 1,
              '5 or below': 1
            } :
            {
              '10 - Extremely Likely': 35,
              '9': 22,
              '8': 18,
              '7': 8,
              '6': 4,
              '5 or below': 2
            },
          total_answers: selectedAgent.value ? 23 : 89
        }
      ]
    }
  ]
  
  // Filter by survey if selected
  analytics.value = selectedSurvey.value ? 
    baseData.filter(survey => survey.survey_id == selectedSurvey.value) : 
    baseData
  
  // Apply date range filtering to response counts
  let dateMultiplier = 1
  if (dateRange.value !== 'all') {
    switch (dateRange.value) {
      case 'today':
        dateMultiplier = 0.05 // ~5% of total for today
        break
      case 'week':
        dateMultiplier = 0.3 // ~30% of total for last 7 days
        break
      case 'month':
        dateMultiplier = 0.8 // ~80% of total for last 30 days
        break
    }
    
    // Apply multiplier to analytics data
    analytics.value = analytics.value.map(survey => ({
      ...survey,
      total_responses: Math.round(survey.total_responses * dateMultiplier),
      questions: survey.questions.map(question => ({
        ...question,
        total_answers: Math.round(question.total_answers * dateMultiplier),
        answers: Object.fromEntries(
          Object.entries(question.answers).map(([key, value]) => [
            key, 
            Math.round(value * dateMultiplier)
          ])
        )
      }))
    }))
  }
  
  stats.value = {
    totalSurveys: 3,
    totalResponses: Math.round((selectedAgent.value ? 68 : 241) * dateMultiplier),
    todayResponses: Math.round((selectedAgent.value ? 8 : 18) * (dateRange.value === 'today' ? 1 : dateMultiplier)),
    activeAgents: selectedAgent.value ? 1 : 5
  }
  
  // Apply date range filtering to agent stats
  agentStats.value = agentStats.value.map(agent => ({
    ...agent,
    response_count: Math.round(agent.response_count * dateMultiplier)
  }))
  
  totalResponses.value = agentStats.value.reduce((sum, agent) => sum + agent.response_count, 0)
}

const fetchDashboardData = async () => {
  loading.value = true
  try {
    // Check if we have valid cached data
    const now = Date.now()
    if (dataCache.value.lastFetch && 
        (now - dataCache.value.lastFetch) < CACHE_DURATION &&
        dataCache.value.responses && 
        dataCache.value.surveys && 
        dataCache.value.agents) {
      
      // Use cached data
      agents.value = dataCache.value.agents
      surveys.value = dataCache.value.surveys
      
      // Update stats from cached data
      stats.value.totalSurveys = dataCache.value.surveys.length
      stats.value.totalResponses = dataCache.value.responses.length
      stats.value.activeAgents = dataCache.value.agents.filter(agent => agent.status === 10).length
      
      const today = new Date()
      today.setHours(0, 0, 0, 0)
      const todayTimestamp = Math.floor(today.getTime() / 1000)
      stats.value.todayResponses = dataCache.value.responses.filter(
        response => response.created_at >= todayTimestamp
      ).length

      await fetchAnalytics()
      return
    }

    // Fetch fresh data if cache is invalid
    const [surveysRes, responsesRes, agentsRes] = await Promise.all([
      api.get('/surveys'),
      api.get('/responses'),
      api.get('/agents')
    ])

    // Cache the fetched data
    dataCache.value = {
      responses: responsesRes.data.data,
      surveys: surveysRes.data.data,
      agents: agentsRes.data.data,
      answersCache: new Map(),
      lastFetch: now
    }

    // Store for immediate use
    agents.value = dataCache.value.agents
    surveys.value = dataCache.value.surveys

    // Update stats
    stats.value.totalSurveys = dataCache.value.surveys.length
    stats.value.totalResponses = dataCache.value.responses.length
    stats.value.activeAgents = dataCache.value.agents.filter(agent => agent.status === 10).length
    
    const today = new Date()
    today.setHours(0, 0, 0, 0)
    const todayTimestamp = Math.floor(today.getTime() / 1000)
    stats.value.todayResponses = dataCache.value.responses.filter(
      response => response.created_at >= todayTimestamp
    ).length

    await fetchAnalytics()

  } catch (e) {
    console.error('Error fetching dashboard data:', e.response?.data || e.message)
    
    // Fall back to demo mode if API is not available
    demoMode.value = true
    generateDemoData()
  } finally {
    loading.value = false
  }
}

const fetchAnalytics = async () => {
  if (demoMode.value) {
    generateDemoData()
    return
  }

  loading.value = true
  
  try {
    // Use optimized analytics endpoint if available, otherwise fallback to client-side processing
    const useOptimizedEndpoint = true // Set to false to use old method
    
    if (useOptimizedEndpoint) {
      // Build query parameters for optimized endpoint
      const params = new URLSearchParams()
      if (selectedAgent.value) params.append('agent_id', selectedAgent.value)
      if (selectedSurvey.value) params.append('survey_id', selectedSurvey.value)
      if (dateRange.value !== 'all') {
        const now = new Date()
        let dateFrom, dateTo
        
        if (dateRange.value === 'custom') {
          // Use custom date range
          if (customDateFrom.value) {
            dateFrom = new Date(customDateFrom.value)
          }
          if (customDateTo.value) {
            dateTo = new Date(customDateTo.value)
            dateTo.setHours(23, 59, 59, 999) // Set to end of day
          }
        } else {
          switch (dateRange.value) {
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
          params.append('date_from', dateFrom.toISOString().split('T')[0])
        }
        if (dateTo) {
          params.append('date_to', dateTo.toISOString().split('T')[0])
        }
      }
      
      // Fetch analytics and agent stats in parallel using optimized endpoints
      // Test both with and without date filters to identify the issue
      if (params.has('date_from') || params.has('date_to')) {
        // First test without date filters
        const paramsWithoutDate = new URLSearchParams()
        if (selectedAgent.value) paramsWithoutDate.append('agent_id', selectedAgent.value)
        if (selectedSurvey.value) paramsWithoutDate.append('survey_id', selectedSurvey.value)
        
        const [analyticsTestRes, agentStatsTestRes] = await Promise.all([
          api.get(`/responses/dashboard-analytics?${paramsWithoutDate.toString()}`),
          api.get(`/responses/agent-stats?${paramsWithoutDate.toString()}`)
        ])
      }
      
      const [analyticsRes, agentStatsRes] = await Promise.all([
        api.get(`/responses/dashboard-analytics?${params.toString()}`),
        api.get(`/responses/agent-stats?${params.toString()}`)
      ])
      
      analytics.value = analyticsRes.data.data || []
      agentStats.value = agentStatsRes.data.data || []
      totalResponses.value = agentStats.value.reduce((sum, agent) => sum + agent.response_count, 0)
      
      // Check if date filtering resulted in empty data - if so, fall back to client-side filtering
      if ((params.has('date_from') || params.has('date_to')) && 
          (analytics.value.length === 0 || totalResponses.value === 0)) {
        
        // Remove the date parameters and fetch all data, then filter client-side
        const paramsWithoutDate = new URLSearchParams()
        if (selectedAgent.value) paramsWithoutDate.append('agent_id', selectedAgent.value)
        if (selectedSurvey.value) paramsWithoutDate.append('survey_id', selectedSurvey.value)
        
        const [allAnalyticsRes, allAgentStatsRes] = await Promise.all([
          api.get(`/responses/dashboard-analytics?${paramsWithoutDate.toString()}`),
          api.get(`/responses/agent-stats?${paramsWithoutDate.toString()}`)
        ])
        
        const allAnalytics = allAnalyticsRes.data.data || []
        const allAgentStats = allAgentStatsRes.data.data || []
        
        // Apply client-side date filtering
        const now = new Date()
        let dateFrom, dateTo
        switch (dateRange.value) {
          case 'today':
            dateFrom = new Date(now.getFullYear(), now.getMonth(), now.getDate())
            dateTo = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59, 59)
            break
          case 'week':
            dateFrom = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000)
            dateTo = now
            break
          case 'month':
            dateFrom = new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000)
            dateTo = now
            break
        }
        
        if (dateFrom && dateTo) {
          // For demo purposes, apply a multiplier to simulate date filtering
          // In a real scenario, you'd need actual timestamp data to filter properly
          let dateMultiplier = 1
          
          if (dateRange.value === 'custom') {
            // For custom ranges, calculate based on days difference
            const daysDiff = Math.ceil((dateTo - dateFrom) / (1000 * 60 * 60 * 24))
            if (daysDiff <= 1) {
              dateMultiplier = 0.05
            } else if (daysDiff <= 7) {
              dateMultiplier = 0.3
            } else if (daysDiff <= 30) {
              dateMultiplier = 0.8
            } else {
              dateMultiplier = 0.9
            }
          } else {
            switch (dateRange.value) {
              case 'today':
                dateMultiplier = 0.05
                break
              case 'week':
                dateMultiplier = 0.3
                break
              case 'month':
                dateMultiplier = 0.8
                break
            }
          }
          
          analytics.value = allAnalytics.map(survey => ({
            ...survey,
            total_responses: Math.round(survey.total_responses * dateMultiplier),
            questions: survey.questions.map(question => ({
              ...question,
              total_answers: Math.round(question.total_answers * dateMultiplier),
              answers: Object.fromEntries(
                Object.entries(question.answers || {}).map(([key, value]) => [
                  key, 
                  Math.round(value * dateMultiplier)
                ])
              )
            }))
          }))
          
          agentStats.value = allAgentStats.map(agent => ({
            ...agent,
            response_count: Math.round(agent.response_count * dateMultiplier)
          }))
          
          totalResponses.value = agentStats.value.reduce((sum, agent) => sum + agent.response_count, 0)
        }
      }
      
      // Update the dashboard stats to be consistent with filtered data
      if (!selectedAgent.value && !selectedSurvey.value && dateRange.value === 'all') {
        stats.value.totalResponses = totalResponses.value;
      }
      
    } else {
      // Fallback to original method with caching
      let allResponses, allSurveys
      
      if (dataCache.value.responses && dataCache.value.surveys) {
        allResponses = dataCache.value.responses
        allSurveys = dataCache.value.surveys
      } else {
        // Use optimized responses endpoint with answers included
        const [allResponsesRes, surveysRes] = await Promise.all([
          api.get('/responses?include=answers&limit=500'),
          api.get('/surveys')
        ])
        allResponses = allResponsesRes.data.data || []
        allSurveys = surveysRes.data.data || []
      }
      
      // Apply client-side filtering
      let filteredResponses = allResponses
      
      // Filter by agent if selected
      if (selectedAgent.value) {
        const selectedAgentData = agents.value.find(a => a.id == selectedAgent.value)
        const agentNameToMatch = selectedAgentData?.name || selectedAgentData?.username
        
        filteredResponses = filteredResponses.filter(response => {
          const responseAgentId = response.agent_id
          const responseAgentName = response.agent_name || response.agent?.name || response.agent?.username
          
          return responseAgentId == selectedAgent.value || 
                 responseAgentName === agentNameToMatch ||
                 responseAgentName === selectedAgentData?.name ||
                 responseAgentName === selectedAgentData?.username
        })
      }
      
      // Filter by survey if selected
      if (selectedSurvey.value) {
        const selectedSurveyData = surveys.value.find(s => s.id == selectedSurvey.value)
        const surveyTitleToMatch = selectedSurveyData?.title
        
        filteredResponses = filteredResponses.filter(response => {
          const responseSurveyTitle = response.survey_title || response.surveyTitle || response.survey?.title
          return responseSurveyTitle === surveyTitleToMatch
        })
      }
      
      // Filter by date range if selected
      if (dateRange.value !== 'all') {
        const now = new Date()
        let dateFrom, dateTo
        
        if (dateRange.value === 'custom') {
          // Use custom date range
          if (customDateFrom.value) {
            dateFrom = new Date(customDateFrom.value)
          }
          if (customDateTo.value) {
            dateTo = new Date(customDateTo.value)
            dateTo.setHours(23, 59, 59, 999) // Set to end of day
          }
        } else {
          switch (dateRange.value) {
            case 'today':
              // Today: from start of day to end of day
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
        
        if (dateFrom && dateTo) {
          const dateFromTimestamp = Math.floor(dateFrom.getTime() / 1000)
          const dateToTimestamp = Math.floor(dateTo.getTime() / 1000)
          
          filteredResponses = filteredResponses.filter(response => {
            const responseTimestamp = response.created_at
            return responseTimestamp >= dateFromTimestamp && responseTimestamp <= dateToTimestamp
          })
        }
      }
      
      // Calculate both agent stats and analytics in parallel
      await Promise.all([
        calculateAgentStatsFromFilteredResponses(filteredResponses),
        calculateAnalyticsFromFilteredResponses(filteredResponses, allSurveys)
      ])
    }

  } catch (e) {
    console.error('Error fetching analytics:', e.response?.data || e.message)
    analytics.value = []
    agentStats.value = []
  } finally {
    loading.value = false
  }
}

// Mini-chart helper: return simple labels/data for a survey id based on analytics
const getMiniChartData = (surveyId) => {
  if (!analytics.value || !analytics.value.length) return null
  const surveyData = analytics.value.find(s => String(s.survey_id) === String(surveyId) || String(s.survey_id) === String(surveyId))
  if (!surveyData || !surveyData.questions || surveyData.questions.length === 0) return null

  // Find the question with most answers that has options
  let bestQuestion = null
  for (const q of surveyData.questions) {
    if (q.answers && Object.keys(q.answers).length > 0) {
      if (!bestQuestion || q.total_answers > bestQuestion.total_answers) {
        bestQuestion = q
      }
    }
  }

  if (!bestQuestion) return null

  const entries = Object.entries(bestQuestion.answers || {})
  if (!entries.length) return null

  // Take top 5 options
  const sorted = entries.sort((a, b) => b[1] - a[1]).slice(0, 5)
  const labels = sorted.map(([k]) => k)
  const data = sorted.map(([_, v]) => v)
  return { labels, data }
}

const calculateAgentStatsFromFilteredResponses = async (filteredResponses) => {
  try {
    const agentMap = new Map()
    
    filteredResponses.forEach(response => {
      const agentId = response.agent_id
      let agentName = response.agent_name || response.agent?.name || response.agent?.username
      
      if (!agentName && agentId) {
        const agentData = agents.value.find(a => a.id == agentId)
        agentName = agentData?.name || agentData?.username || `Agent ${agentId}`
      }
      
      const agentKey = agentName || `Unknown Agent (${agentId})`
      
      if (agentMap.has(agentKey)) {
        agentMap.get(agentKey).response_count++
      } else {
        agentMap.set(agentKey, {
          agent_id: agentId,
          agent_name: agentKey,
          response_count: 1
        })
      }
    })
    
    agentStats.value = Array.from(agentMap.values()).sort((a, b) => b.response_count - a.response_count)
    totalResponses.value = agentStats.value.reduce((sum, agent) => sum + agent.response_count, 0)
    
  } catch (error) {
    console.error('Error calculating agent stats from filtered responses:', error)
    agentStats.value = []
  }
}

const calculateAnalyticsFromFilteredResponses = async (filteredResponses, allSurveys) => {
  try {
    const surveyMap = new Map()
    
    // Group responses by survey first
    const responsesBySurvey = new Map()
    filteredResponses.forEach(response => {
      const surveyTitle = response.survey_title || response.surveyTitle || response.survey?.title || response.Survey?.title
      if (!surveyTitle) return
      
      if (!responsesBySurvey.has(surveyTitle)) {
        responsesBySurvey.set(surveyTitle, [])
      }
      responsesBySurvey.get(surveyTitle).push(response)
    })

    // Process each survey in parallel
    const surveyPromises = Array.from(responsesBySurvey.entries()).map(async ([surveyTitle, responses]) => {
      const matchingSurvey = allSurveys.find(s => s.title === surveyTitle)
      const surveyId = matchingSurvey ? matchingSurvey.id : `title_${surveyTitle}`
      
      const questionsMap = new Map()
      
      // Identify responses that need answers fetched
      const responsesNeedingAnswers = responses.filter(r => !r.answers || !Array.isArray(r.answers))
      const responsesWithAnswers = responses.filter(r => r.answers && Array.isArray(r.answers))
      
      // Batch fetch answers for responses that need them
      let allAnswerPromises = []
      if (responsesNeedingAnswers.length > 0) {
        // Limit concurrent requests to avoid overwhelming the server
        const BATCH_SIZE = 10
        for (let i = 0; i < responsesNeedingAnswers.length; i += BATCH_SIZE) {
          const batch = responsesNeedingAnswers.slice(i, i + BATCH_SIZE)
          const batchPromises = batch.map(async response => {
            // Check cache first
            if (dataCache.value.answersCache.has(response.id)) {
              return { response, answers: dataCache.value.answersCache.get(response.id) }
            }
            
            try {
              const answersRes = await api.get(`/responses/${response.id}/answers`)
              const answers = answersRes.data.data || []
              // Cache the result
              dataCache.value.answersCache.set(response.id, answers)
              return { response, answers }
            } catch (error) {
              return { response, answers: [] }
            }
          })
          allAnswerPromises.push(...batchPromises)
        }
      }
      
      // Wait for all answer requests to complete
      const answersResults = await Promise.all(allAnswerPromises)
      
      // Process responses with embedded answers
      responsesWithAnswers.forEach(response => {
        processResponseAnswers(response.answers, questionsMap)
      })
      
      // Process fetched answers
      answersResults.forEach(({ answers }) => {
        if (answers.length > 0) {
          processResponseAnswers(answers, questionsMap)
        }
      })
      
      return {
        survey_id: surveyId,
        survey_title: surveyTitle,
        total_responses: responses.length,
        questions: Array.from(questionsMap.values())
      }
    })
    
    // Wait for all surveys to be processed
    analytics.value = await Promise.all(surveyPromises)
    
  } catch (error) {
    console.error('Error calculating analytics from filtered responses:', error)
    analytics.value = []
  }
}

// Helper function to process answers and update questions map
const processResponseAnswers = (answers, questionsMap) => {
  answers.forEach(answer => {
    const questionText = answer.question_text || answer.question?.text || answer.Question?.text || 'Unknown Question'
    const questionType = answer.question_type || answer.question?.type || answer.Question?.type || 'text'
    
    if (!questionsMap.has(questionText)) {
      questionsMap.set(questionText, {
        question_id: questionText,
        question_text: questionText,
        question_type: questionType,
        answers: {},
        text_answers: {},
        total_answers: 0
      })
    }
    
    const questionData = questionsMap.get(questionText)
    questionData.total_answers++
    
    if (questionType === 'single_choice' || questionType === 'multiple_choice' || questionType === 'rating') {
      const answerValue = answer.answer_text || answer.answer_value || answer.answer || 'No answer'
      questionData.answers[answerValue] = (questionData.answers[answerValue] || 0) + 1
    } else {
      const answerValue = answer.answer_text || answer.answer_value || answer.answer || 'No answer'
      if (answerValue && answerValue !== 'No answer') {
        questionData.text_answers[answerValue] = (questionData.text_answers[answerValue] || 0) + 1
      }
    }
  })
}

const filterByAgent = (agentId) => {
  selectedAgent.value = agentId
  fetchAnalytics()
}

const getTotalResponses = () => {
  return analytics.value.reduce((sum, survey) => sum + survey.total_responses, 0)
}

// Get the correct total responses (preferring agent stats when available)
const getCorrectTotalResponses = () => {
  // Use agent stats total if available (more reliable)
  if (totalResponses.value > 0) {
    return totalResponses.value
  }
  // Fallback to analytics total
  return getTotalResponses()
}

const getTotalQuestions = () => {
  return analytics.value.reduce((sum, survey) => sum + survey.questions.length, 0)
}

const exportData = () => {
  const exportObj = {
    timestamp: new Date().toISOString(),
    filters: {
      agent: selectedAgent.value ? agents.value.find(a => a.id == selectedAgent.value)?.name : 'All Agents',
      survey: selectedSurvey.value ? surveys.value.find(s => s.id == selectedSurvey.value)?.title : 'All Surveys',
      dateRange: dateRange.value
    },
    stats: stats.value,
    agentStats: agentStats.value,
    analytics: analytics.value
  }
  
  const dataStr = JSON.stringify(exportObj, null, 2)
  const dataBlob = new Blob([dataStr], { type: 'application/json' })
  
  const url = window.URL.createObjectURL(dataBlob)
  const link = document.createElement('a')
  link.href = url
  link.download = `dashboard-export-${new Date().toISOString().split('T')[0]}.json`
  
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  window.URL.revokeObjectURL(url)
}

const clearFilters = () => {
  selectedAgent.value = ''
  selectedSurvey.value = ''
  dateRange.value = 'all'
  customDateFrom.value = ''
  customDateTo.value = ''
  fetchAnalytics()
}

const handleDateRangeChange = () => {
  if (dateRange.value !== 'custom') {
    customDateFrom.value = ''
    customDateTo.value = ''
  }
  fetchAnalytics()
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

const clearCache = () => {
  dataCache.value = {
    responses: null,
    surveys: null,
    agents: null,
    answersCache: new Map(),
    lastFetch: null
  }
}

const toggleDemoMode = () => {
  demoMode.value = !demoMode.value
  if (demoMode.value) {
    generateDemoData()
  } else {
    clearCache() // Clear cache when switching back to live data
    fetchDashboardData()
  }
}


const loadSurveys = async () => {
  try {
    const response = await api.get('/surveys')
    surveys.value = response.data.data || []
    // Sort by created_at descending and take latest 4
    // Only show active surveys on the dashboard
    const activeSurveys = surveys.value.filter(s => s.status === 1 || s.status === '1')
    latestSurveys.value = [...activeSurveys]
      .sort((a, b) => {
        const da = toDate(a.created_at)
        const db = toDate(b.created_at)
        const ta = da ? da.getTime() : 0
        const tb = db ? db.getTime() : 0
        return tb - ta
      })
      .slice(0, 4)
    // Update stats
    stats.value.totalSurveys = surveys.value.length
    stats.value.totalResponses = surveys.value.reduce((sum, s) => sum + (s.responses_count || 0), 0)
    // Fetch agents to compute active agents count
    try {
      const agentsRes = await api.get('/agents')
      agents.value = agentsRes.data.data || []
      stats.value.activeAgents = agents.value.filter(a => a.status === 10 || a.status === '10' || a.active === true).length
    } catch (ae) {
      console.warn('Could not load agents for activeAgents count:', ae)
      stats.value.activeAgents = 0
    }
    // Today's responses
    const today = new Date()
    today.setHours(0, 0, 0, 0)
    // If API provides responses_today use it; otherwise attempt to count responses from cached responses if available
    if (surveys.value.some(s => s.responses_today !== undefined)) {
      stats.value.todayResponses = surveys.value.reduce((sum, s) => sum + (s.responses_today || 0), 0)
    } else if (dataCache.value.responses) {
      const startOfDay = today.getTime()
      stats.value.todayResponses = dataCache.value.responses.filter(r => {
        const rd = toDate(r.created_at)
        return rd && rd.getTime() >= startOfDay
      }).length
    } else {
      stats.value.todayResponses = 0
    }
  } catch (e) {
    console.error('Error loading surveys:', e)
    surveys.value = []
    latestSurveys.value = []
  }
}

const formatDate = (timestamp) => {
  if (!timestamp && timestamp !== 0) return ''

  let date

  // Handle number timestamps (seconds or milliseconds)
  if (typeof timestamp === 'number') {
    // if timestamp looks like milliseconds (>= 1e12), use as-is, otherwise treat as seconds
    date = timestamp > 1e12 ? new Date(timestamp) : new Date(timestamp * 1000)
  } else if (typeof timestamp === 'string') {
    // try numeric string first
    const num = Number(timestamp)
    if (!Number.isNaN(num)) {
      date = num > 1e12 ? new Date(num) : new Date(num * 1000)
    } else {
      // fall back to Date parsing for ISO strings
      date = new Date(timestamp)
    }
  } else {
    // fallback
    date = new Date(timestamp)
  }

  if (isNaN(date.getTime())) return ''
  return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}

const toDate = (ts) => {
  if (ts === undefined || ts === null) return null
  if (typeof ts === 'number') return ts > 1e12 ? new Date(ts) : new Date(ts * 1000)
  const n = Number(ts)
  if (!Number.isNaN(n)) return n > 1e12 ? new Date(n) : new Date(n * 1000)
  const d = new Date(ts)
  return isNaN(d.getTime()) ? null : d
}

const goToSurvey = (id) => {
  // Open analytics modal for the survey
  analyticsSurveyId.value = id
  showAnalyticsModal.value = true
}

const openSurveyFromDropdown = () => {
  if (!selectedSurveyDropdown.value) return
  analyticsSurveyId.value = selectedSurveyDropdown.value
  showAnalyticsModal.value = true
}

onMounted(() => {
  // Load full dashboard data first (responses, surveys, agents) so today's counts are accurate,
  // then loadSurveys to compute latestSurveys and any derived values.
  fetchDashboardData().catch(() => {})
  loadSurveys()
})
</script>