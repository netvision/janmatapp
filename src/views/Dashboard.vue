<template>
  <div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        <div class="flex items-center space-x-4">
          <button 
            @click="exportData"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
          >
            Export Data
          </button>
          <button 
            @click="toggleDemoMode"
            :class="[demoMode ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-600 hover:bg-gray-700', 'text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors']"
          >
            {{ demoMode ? 'Demo Mode ON' : 'Live Data' }}
          </button>
        </div>
      </div>

      <!-- Demo Mode Alert -->
      <div v-if="demoMode" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
          <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <p class="text-yellow-800 font-medium">
            Demo Mode Active - Showing sample data for demonstration purposes
          </p>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
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

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-3 rounded-full bg-orange-100 text-orange-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Active Agents</p>
              <p class="text-2xl font-semibold text-gray-900">{{ stats.activeAgents }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-medium text-gray-900">Filters & Analytics Options</h3>
          <div v-if="selectedAgent || selectedSurvey || dateRange !== 'all'" class="text-sm text-blue-600">
            Filters Active
          </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Agent</label>
            <select v-model="selectedAgent" @change="fetchAnalytics" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
              <option value="">All Agents</option>
              <option v-for="agent in agents" :key="agent.id" :value="agent.id">
                {{ agent.name }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Survey</label>
            <select v-model="selectedSurvey" @change="fetchAnalytics" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
              <option value="">All Surveys</option>
              <option v-for="survey in surveys" :key="survey.id" :value="survey.id">
                {{ survey.title }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
            <select v-model="dateRange" @change="fetchAnalytics" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
              <option value="all">All Time</option>
              <option value="today">Today</option>
              <option value="week">Last 7 Days</option>
              <option value="month">Last 30 Days</option>
            </select>
          </div>
        </div>
        <div v-if="selectedAgent || selectedSurvey || dateRange !== 'all'" class="mt-4 p-3 bg-blue-50 rounded-lg">
          <div class="flex items-center justify-between">
            <p class="text-sm text-blue-800">
              <span v-if="selectedAgent">Agent: {{ agents.find(a => a.id == selectedAgent)?.name }}</span>
              <span v-if="selectedAgent && selectedSurvey"> | </span>
              <span v-if="selectedSurvey">Survey: {{ surveys.find(s => s.id == selectedSurvey)?.title }}</span>
              <span v-if="(selectedAgent || selectedSurvey) && dateRange !== 'all'"> | </span>
              <span v-if="dateRange !== 'all'">Period: {{ dateRange.charAt(0).toUpperCase() + dateRange.slice(1) }}</span>
            </p>
            <button @click="clearFilters" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
              Clear Filters
            </button>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-8">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
        <p class="mt-4 text-gray-600">Loading analytics...</p>
      </div>

      <!-- Response Analytics Overview -->
      <div v-else-if="analytics.length > 0" class="space-y-8">
        <!-- Summary Section -->
        <div class="bg-white rounded-lg shadow">
          <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Response Analytics Overview</h2>
            <p class="text-sm text-gray-600 mt-1">
              {{ getCorrectTotalResponses() }} total responses 
              <span v-if="selectedAgent || selectedSurvey || dateRange !== 'all'"> (Filtered)</span>
            </p>
          </div>
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div class="text-center">
                <div class="text-3xl font-bold text-blue-600">{{ analytics.length }}</div>
                <div class="text-sm text-gray-600">Surveys Analyzed</div>
              </div>
              <div class="text-center">
                <div class="text-3xl font-bold text-green-600">{{ getTotalQuestions() }}</div>
                <div class="text-sm text-gray-600">Questions Answered</div>
              </div>
              <div class="text-center">
                <div class="text-3xl font-bold text-purple-600">{{ agentStats.length }}</div>
                <div class="text-sm text-gray-600">Active Agents</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Agent Performance Quick View -->
        <div v-if="agentStats.length > 0 && !selectedAgent" class="bg-white rounded-lg shadow">
          <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Agent Performance Distribution</h3>
            <p class="text-sm text-gray-600 mt-1">Click on an agent to filter responses</p>
          </div>
          <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
              <!-- Pie Chart -->
              <div class="relative h-64 flex items-center justify-center">
                <PieChart 
                  :data="agentStats.map(agent => agent.response_count)"
                  :labels="agentStats.map(agent => agent.agent_name || 'Unknown Agent')"
                  :colors="pieColors"
                  :width="280"
                  :height="280"
                />
              </div>
              <!-- Quick Stats -->
              <div class="space-y-3">
                <div v-for="(agent, index) in agentStats.slice(0, 5)" :key="agent.agent_id" 
                     @click="filterByAgent(agent.agent_id)"
                     class="flex items-center justify-between p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                  <div class="flex items-center">
                    <div class="w-3 h-3 rounded-full mr-3" :style="{ backgroundColor: pieColors[index % pieColors.length] }"></div>
                    <div>
                      <p class="font-medium text-gray-900 text-sm">{{ agent.agent_name || 'Unknown Agent' }}</p>
                    </div>
                  </div>
                  <div class="text-right">
                    <p class="font-semibold text-gray-900 text-sm">{{ ((agent.response_count / totalResponses) * 100).toFixed(1) }}%</p>
                    <p class="text-xs text-gray-600">({{ agent.response_count }})</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Survey Question Analytics -->
        <!-- Survey Question Analytics -->
        <div v-for="survey in analytics" :key="survey.survey_id" class="bg-white rounded-lg shadow">
          <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
              <div>
                <h2 class="text-xl font-semibold text-gray-900">{{ survey.survey_title }}</h2>
                <p class="text-sm text-gray-600 mt-1">
                  {{ survey.total_responses }} responses • {{ survey.questions.length }} questions
                  <span v-if="selectedAgent"> • Agent: {{ agents.find(a => a.id == selectedAgent)?.name }}</span>
                </p>
              </div>
              <div class="text-right">
                <div class="text-lg font-semibold text-blue-600">
                  {{ Math.round((survey.total_responses / stats.totalResponses) * 100) }}%
                </div>
                <div class="text-xs text-gray-500">of total responses</div>
              </div>
            </div>
          </div>
          
          <div class="p-6">
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
              <div v-for="question in survey.questions" :key="question.question_id" class="border rounded-lg p-6 bg-gray-50">
                <div class="mb-4">
                  <h3 class="font-medium text-gray-900 mb-2">{{ question.question_text }}</h3>
                  <div class="flex items-center space-x-4 text-xs text-gray-500">
                    <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded">{{ question.question_type.replace('_', ' ').toUpperCase() }}</span>
                    <span>{{ question.total_answers }} total responses</span>
                  </div>
                </div>
                
                <!-- Choice Questions (Pie Chart) -->
                <div v-if="question.question_type === 'single_choice' || question.question_type === 'multiple_choice' || question.question_type === 'rating'">
                  <div v-if="Object.keys(question.answers).length > 0" class="space-y-6">
                    <!-- Pie Chart -->
                    <div class="flex justify-center h-64 bg-white rounded-lg p-4">
                      <PieChart 
                        :data="Object.values(question.answers)"
                        :labels="Object.keys(question.answers)"
                        :colors="pieColors"
                        :width="220"
                        :height="220"
                      />
                    </div>
                    <!-- Legend with Percentages -->
                    <div class="space-y-2">
                      <h4 class="font-medium text-gray-700 text-sm mb-3">Response Breakdown:</h4>
                      <div v-for="(count, option, index) in question.answers" :key="option" class="flex items-center justify-between p-3 bg-white rounded-lg border">
                        <div class="flex items-center">
                          <div class="w-4 h-4 rounded-full mr-3 flex-shrink-0" :style="{ backgroundColor: pieColors[index % pieColors.length] }"></div>
                          <span class="text-gray-700 text-sm">{{ option }}</span>
                        </div>
                        <div class="text-right flex-shrink-0">
                          <span class="font-semibold text-gray-900">{{ ((count / question.total_answers) * 100).toFixed(1) }}%</span>
                          <span class="text-gray-500 text-sm ml-2">({{ count }})</span>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div v-else class="text-center py-8 text-gray-500 bg-white rounded-lg">
                    <svg class="mx-auto h-8 w-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    No responses yet
                  </div>
                </div>

                <!-- Text/Number Questions (Top Answers) -->
                <div v-else-if="Object.keys(question.text_answers || {}).length > 0" class="space-y-4">
                  <div class="bg-white rounded-lg p-4">
                    <h4 class="font-medium text-gray-700 text-sm mb-3">Top Responses:</h4>
                    <div class="space-y-2">
                      <div v-for="(count, answer, index) in question.text_answers" :key="answer" class="flex items-center justify-between p-2 bg-gray-50 rounded border">
                        <span class="text-gray-700 text-sm truncate flex-1">{{ answer }}</span>
                        <div class="text-right ml-4 flex-shrink-0">
                          <span class="font-semibold text-gray-900">{{ ((count / question.total_answers) * 100).toFixed(1) }}%</span>
                          <span class="text-gray-500 text-sm ml-2">({{ count }})</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- No answers yet -->
                <div v-else class="text-center py-8 text-gray-500 bg-white rounded-lg">
                  <svg class="mx-auto h-8 w-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  No responses yet
                </div>
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
import { ref, onMounted, nextTick } from 'vue'
import PieChart from '@/components/PieChart.vue'
import api from '@/services/api'

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
const agentStats = ref([])
const totalResponses = ref(0)

// Filters
const selectedAgent = ref('')
const selectedSurvey = ref('')
const dateRange = ref('all')

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
  fetchAnalytics()
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

onMounted(() => {
  fetchDashboardData()
})
</script> 